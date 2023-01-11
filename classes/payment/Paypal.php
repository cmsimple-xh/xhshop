<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

use Xhshop\Decimal;

class Paypal extends PaymentModule
{
    private $urls = array(
        'development' => 'https://www.sandbox.paypal.com/cgi-bin/webscr',
        'production'  => 'https://www.paypal.com/cgi-bin/webscr'
    );

    public function __construct()
    {
        global $plugin_cf;

        parent::__construct();
        $this->settings['currency_code'] = $plugin_cf['xhshop']['shop_currency_code'];
    }

    /** @return bool */
    public function wantsCartItems()
    {
        return true;
    }

    /** @return string */
    public function getLabel()
    {
        return '<img src="' . XHS_BASE_PATH . 'images/paypal-logo.png" alt="PayPal">
';
    }

    /** @return string */
    public function getName()
    {
        return 'paypal';
    }

    /**
     * @return string
     * @see https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/cart_upload/#implementing-the-cart-upload-command
     */
    public function orderSubmitForm()
    {
        global $plugin_tx;

        $filename = XHS_CONTENT_PATH . 'xhshop/tmp_orders/pp_' . session_id() . '.temp';
        if (!file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
            chmod(dirname($filename), 0777);
        }
        $data = array(
            'xhsOrder' => $_SESSION['xhsOrder'],
            'xhsCustomer' => $_SESSION['xhsCustomer']
        );
        XH_writeFile($filename, serialize($data));

        $shopUrl = CMSIMPLE_URL . $plugin_tx['xhshop']['config_shop_page'];
        $form = '
<form action="' . $this->urls[$this->settings['sandbox'] ? 'development' : 'production'] . '" method="post">
    <input type="hidden" name="cmd" value="_cart">
    <input type="hidden" name="upload" value="1">
    <input type="hidden" name="business" value="' . $this->settings['email'] . '">
    <input type="hidden" name="currency_code" value="' . $this->settings['currency_code'] . '">
    <input type="hidden" name="lc" value="' . strtoupper(XHS_LANGUAGE) . '">
    <input type="hidden" name="rm" value="2">
    <input type="hidden" name="custom" value="' . session_id() . '">
    <input type="hidden" name="handling_cart" value="' .
        $this->shipping->plus(new Decimal($this->settings['fee']))->toString() . '">
    <input type="hidden" name="cancel_return" value="' . "$shopUrl&xhsCheckout=customersData" . '">
    <input type="hidden" name="notify_url" value="' . "$shopUrl&xhsIpn" . '">
    <input type="hidden" name="return" value="' . "$shopUrl&xhsCheckout=thankYou" . '">';

        foreach ($this->cartItems as $item) {
            $name = strip_tags($item['name']);
            $name .= isset($item['variantName']) ? ', ' . $item['variantName'] : '';
            $form .= '
     <input type="hidden" name="item_name_' . $item['itemCounter'] . '" value="' . $name . '">
     <input type="hidden" name="quantity_' . $item['itemCounter'] . '" value="' . $item['amount'] . '">
     <input type="hidden" name="amount_' . $item['itemCounter'] . '" value="' . $item['price']->toString() . '">
     <input type="hidden" name="item_number_' . $item['itemCounter'] . '" value="' . $item['itemCounter'] . '">';
        }
        $form .= '
     <button class="xhsShopButton"><span class="fa fa-paypal fa-fw"></span> ' .
        $this->language['go_to_paypal'] . '</button>
</form>
';

        return $form;
    }

    /** @return never */
    public function ipn()
    {
        // read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';

        foreach ($_POST as $key => $value) {
            $value = urlencode($value);
            $req .= "&$key=$value";
        }

// post back to PayPal system to validate

        $header = "POST /cgi-bin/webscr HTTP/1.1\r\n";

        if ($this->settings['sandbox']) {
            $header .= "Host: ipnpb.sandbox.paypal.com:443\r\n";
        } else {
            $header .= "Host: ipnpb.paypal.com:443\r\n";
        }
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n";
        $header .= "User-Agent: XH-Shop-IPN-VerificationScript\r\n";
        $header .= "Connection: close\r\n";
        $header .= "\r\n";

        if ($this->settings['sandbox']) {
            $fp = fsockopen('ssl://ipnpb.sandbox.paypal.com', 443, $errno, $errstr, 30);
        } else {
            $fp = fsockopen('ssl://ipnpb.paypal.com', 443, $errno, $errstr, 30);
        }

        if (!$fp) {
            $this->handshakeFailed(sprintf('fsockopen returned %d: %s', $errno, trim($errstr)));
        }

        $payload = $header . $req;
        if (fwrite($fp, $payload) !== strlen($payload)) {
            $this->handshakeFailed('could not sent complete IPN pingback request');
        }
        $res = stream_get_contents($fp);
        list(, $body) = explode("\r\n\r\n", $res);
        $lines = explode("\r\n", $body);
        if (in_array('VERIFIED', $lines)) {
            $this->handleVerifiedIpn();
        } elseif (in_array('INVALID', $lines)) {
                // just ignore this IPN
        } else {
            $this->handshakeFailed(sprintf('unexpected response for IPN pingback request: %s', trim($body)));
        }
        fclose($fp);
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('HTTP/1.1 200 OK');
        exit;
    }

    /**
     * @param string $message
     * @return never
     */
    private function handshakeFailed($message)
    {
        XH_logMessage('error', 'xhshop', 'ipn', sprintf('Handshake failed! (%s)', $message));
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('HTTP/1.1 500 Internal Server Error');
        exit;
    }

    /** @return void */
    private function handleVerifiedIpn()
    {
        global $xhsController;

        $file = XHS_CONTENT_PATH . 'xhshop/tmp_orders/pp_' . $_POST['custom'] . '.temp';
        if ($_POST['receiver_email'] === $this->settings['email']
                && $_POST['payment_status'] === 'Completed'
                && file_exists($file)) {
            XH_logMessage('info', 'xhshop', 'ipn', 'processed: ' . serialize($_POST));
            $temp                    = XH_readFile($file);
            $temp                    = unserialize($temp);
            $_SESSION['xhsCustomer'] = $temp['xhsCustomer'];
            $_SESSION['xhsOrder']    = $temp['xhsOrder'];
            unlink($file);
            $xhsController->finishCheckout(true);
        } else {
            XH_logMessage('info', 'xhshop', 'ipn', 'ignored: ' . serialize($_POST));
        }
    }
}
