<?php

global $xhsController;

class XHS_Paypal extends XHS_Payment_Module {

    var $currency_codes = array('AUD'         => 'Australian Dollar',
        'CAD'         => 'Canadian Dollar',
        'EUR'         => 'Euros',
        'GBP'         => 'Pound Sterling',
        'JPY'         => 'Yen',
        'USD'         => 'U.S. Dollar',
        'NZD'         => 'New Zealand Dollar',
        'CHF'         => 'Swiss Franc',
        'HKD'         => 'Honkong Dollar',
        'SGD'         => 'Singapore Dollar',
        'SEK'         => 'Swedish Krona',
        'DKK'         => 'Danish Krone',
        'PLN'         => 'Polish Zloty',
        'NOK'         => 'Norwegian Krone',
        'HUF'         => 'Hungarian Forint',
        'CZK'         => 'Czech Koruna',
        'ILS'         => 'Israeli Shekel',
        'MXN'         => 'Mexican Peso',
        'PHP'         => 'Philippine Pesos',
        'TWD'         => 'Taiwan New Dollars',
        'THB'         => 'Thai Baht',
        'BRL'         => 'Brazilian Real (only for Brazilian users)',
        'MYR'         => 'Malaysian Ringgits (only for Malaysian users)'
    );
    var $name         = 'paypal';
    var $requiredData = array('business'      => null, // e-mail adress
        'currency_code' => null, // EUR, USD ...
        'lc'            => null         // language code (EN, DE, ...)
    );
    var $urls           = array(
        'development' => 'https://www.sandbox.paypal.com/cgi-bin/webscr',
        'production'  => 'https://www.paypal.com/cgi-bin/webscr'
    );

    function __construct() {

        $this->loadLanguage();
        $this->loadSettings();
    }

    function isAvailable() {
        return strlen(trim($this->settings['currency_code'])) === 3 && strlen(trim($this->settings['business'])) > 5;
    }

    function wantsCartItems() {
        return true;
    }

    function getLabel() {
        return '<img src="' . XHS_BASE_PATH . 'classes/paymentmodules/paypal/images/paypal_solutions.gif">
';
    }

    function orderSubmitForm() {
        $name = 'pp_' . session_id() . '.temp';
        //$name = 'test';
        $fh   = fopen(XHS_BASE_PATH . 'classes/paymentmodules/paypal/tmp_orders/' . $name, "w");
        if (!$fh)
        {
            die("could not open ");
        }
        $temp = serialize($_SESSION);

        fwrite($fh, $temp) or die("could not write");
        fclose($fh);

        $form = '
<form action="' . $this->urls[$this->settings['environment']] . '" method="post">
    <input type="hidden" name="cmd" value="_cart" />
	<input type="hidden" name="upload" value="1" />
	<input type="hidden" name="business" value="' . $this->settings['business'] . '">
	<input type="hidden" name="currency_code" value="' . $this->settings['currency_code'] . '" />
    <input type="hidden" name="lc" value="' . strtoupper(XHS_LANGUAGE) . '" />
	<input type="hidden" name="rm" value="2" />
    <input type="hidden" name="custom" value="' . session_id() . '" />
	<input type="hidden" name="handling_cart" value="' . ($this->settings['fee'] + $this->shipping) . '" />
    <input type="hidden" name="cancel_return" value="' . $_SERVER['HTTP_REFERER'] . '" />
          <input type="hidden" name="notify_url" value="' . $_SERVER['HTTP_REFERER'] . '" />
	<input type="hidden" name="return" value="' . $_SERVER['HTTP_REFERER'] . '" />';

        foreach ($this->cartItems as $item)
        {
            $name = strip_tags($item['name']);
            $name .= isset($item['variantName']) ? ', ' . $item['variantName'] : '';
            $form .= '
     <input type="hidden" name="item_name_' . $item['itemCounter'] . '" value="' . $name . '" />
     <input type="hidden" name="quantity_' . $item['itemCounter'] . '" value="' . $item['amount'] . '" />
     <input type="hidden" name="amount_' . $item['itemCounter'] . '" value="' . number_format((float) $item['price'], 2, '.', '') . '" />
     <input type="hidden" name="item_number_' . $item['itemCounter'] . '" value="' . $item['itemCounter'] . '" />';
        }
        $form .= '
     <button class="xhsShopButton"><span class="fa fa-paypal fa-fw"></span> ' . $this->language['go_to_paypal'] . '</button>
</form>
';

        return $form;
    }

    function settingInputs() {
        $test = $this->shopCurrency;
        if ($test == '$')
        {
            $test = 'USD';
        }
        if (in_array($test, array('&euro;', '€', '&#x20AC;', '&#8364;')))
        {
            $test = 'EUR';
        }
        if (in_array($test, array('&pound;', '£', '&#163;', '&#x00A3;')))
        {
            $test = 'GBP';
        }
        if (in_array($test, array('&yen;', '¥', '&#165;', '&#x00A5;')))
        {
            $test = 'JPY';
        }


        $currency = strlen($this->settings['currency_code']) === 3 ? $this->settings['currency_code'] : 'none';

        $html  = '<p><label>';
        $html .= $this->language['business'] . ':<br />';
        $value = isset($this->settings['business']) ? $this->settings['business'] : '';

        $html .= '<input type="text" size="40" name="ppBusiness" value="' . $value . '" />';
        $html .= '</label></p><p>';
        if ($test !== $currency && key_exists($test, $this->currency_codes))
        {

            $chosen   = key_exists($currency, $this->currency_codes) ? $this->currency_codes[$currency] : '???';
            $html .= '<b>' . $this->language['currency_warning'] . ':</b> Shop: ' . $this->currency_codes[$test] . '(?) - Paypal: ' . $chosen . ')<br />';
            $currency = $test;
        }
        $html .= $this->language['currency_code'] . '<br /><select name="ppCurrencyCode">';
        $html .= '<option value="">Select Currency</option>';

        foreach ($this->currency_codes as $code => $value)
        {
            $selected = $code == $currency ? ' selected="selected"' : '';
            $html .= '<option value="' . $code . '"' . $selected . '>' . $value . '</option>';
        }
        $html .= '</select></p>';
        $html .= '<p><label>' . $this->language['environment'] . ':</label><br />';
        $selected = $this->settings['environment'] === 'development' ? ' checked="checked"' : '';
        $html .= '<input name="ppEnvironment" type="radio" value="development"' . $selected . ' /> ' . $this->language['environment_development'] . '<br />';
        $selected = $this->settings['environment'] === 'production' ? ' checked="checked"' : '';
        $html .= '<input name="ppEnvironment" type="radio" value="production"' . $selected . ' /> ' . $this->language['environment_production'] . '<br /></p>';

        return $html;
    }

    function saveConfig() {

        if (isset($_POST['ppCurrencyCode']))
        {
            $this->settings['currency_code'] = '' . $_POST['ppCurrencyCode'] . '';
        }
        if (isset($_POST['ppEnvironment']))
        {
            $this->settings['environment'] = '' . $_POST['ppEnvironment'] . '';
        }
        if (isset($_POST['ppBusiness']))
        {
            $business = trim($_POST['ppBusiness']);
            $this->settings['business'] = '' . $business . '';
        }
        $file     = XHS_BASE_PATH . 'classes/paymentmodules/' . $this->name . '/settings.php';
        $handle   = fopen($file, 'w');
        if (!$handle)
        {
            return false;
        }
        $string = '<?php' . "\n";
        foreach ($this->settings as $key => $value)
        {
            if (is_string($value))
            {
                $value = '"' . $value . '"';
            }
            $string .= '$config[\'' . $key . '\'] = ' . $value . ';' . "\n";
        }
        $string .= '?>';
        fwrite($handle, $string);
        fclose($handle);
        return true;
    }

    function ipn() {
        // read the post from PayPal system and add 'cmd'
        global $xhsController;
        $req = 'cmd=_notify-validate';

        foreach ($_POST as $key => $value)
        {
            $value = urlencode($value);
            $req .= "&$key=$value";
        }

// post back to PayPal system to validate

        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";

        if ($this->settings['environment'] === 'development')
        {
            $header .= "Host: www.sandbox.paypal.com:443\r\n";
        } else
        {
            $header .= "Host: www.paypal.com:443\r\n";
        }
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

        if ($this->settings['environment'] === 'development')
        {
            $fp = fsockopen('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
        } else
        {
            $fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 30);
        }

        if (!$fp)
        {

            /*
             * HTTP-ERROR: Was tun?
             */
            return;
        }


        fputs($fp, $header . $req);
        while (!feof($fp))
        {
            $res = fgets($fp, 1024);
            if (strcmp($res, "VERIFIED") == 0)
            {
                /*
                 *  bei Bedarf pruefen, ob die Bestellung ausgefuehrt werden soll. (Stimmt die Haendler-E-Mail, ...?
                 */
              
                $file = __DIR__ . '/tmp_orders/pp_' . $_POST['custom'];
                if (file_exists($file . '.temp'))
                {

                    if (!(bool) session_id())
                    {
                        session_id($_POST['custom']);
                        session_start();
                    }

                    $temp                    = implode("", file($file . '.temp'));
                    $temp                    = unserialize($temp);
                    $_SESSION['xhsCustomer'] = $temp['xhsCustomer'];
                    $_SESSION['xhsOrder']    = $temp['xhsOrder'];
                    rename($file . '.temp', $file . '.sent');
                    $xhsController->finishCheckout();
                } else
                {

                }
            } else if (strcmp($res, "INVALID") == 0)
            {
                /*
                 *  Fehlerbehandlung "ungueltig"
                 */
            }
        }
        fclose($fp);
    }

}

$xhsPaypal = new XHS_Paypal();
$xhsController->addPaymentModule($xhsPaypal);
?>
