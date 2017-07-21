<?php

namespace Xhshop;

use PHPMailer;

class FrontEndController extends Controller
{

    private $requiredCustomerData = array();

    public function __construct()
    {
        parent::__construct();
        $this->splitForwardingExpenses();
        $this->requiredCustomerData = array('first_name', 'last_name',
            'street', 'zip_code', 'city', 'country', 'cos_confirmed',
            'email', 'payment_mode');
    }

    private function splitForwardingExpenses()
    {
        $this->settings['weightRange'] = array();
        foreach (explode(';', $this->settings['forwarding_expenses']) as $grade) {
            $parts = explode('=', trim($grade));
            if (count($parts) === 2) {
                $this->settings['weightRange'][trim($parts[0])] = (float) $parts[1];
            } else {
                $this->settings['shipping_max'] = $parts[0];
            }
        }
    }

    /**
     *
     * @return string returns info string about handling v.a.t
     *
     */
    private function vatInfo()
    {
        if ($this->settings['dont_deal_with_taxes']) {
            $info = 'price_info_no_vat';
        } else {
            $info = 'price_info_vat';
        }
        return $info;
    }

    public function addToCartButton(Product $product)
    {
        $params = array('productName' => $product->getName(XHS_LANGUAGE),
            'product'     => $product,
            'vatInfo'     => $this->vatInfo(),
            'vatRate'     => $this->settings['vat_' . $product->getVat()]);
        if ($product->hasVariants()) {
            $params['variants'] = $product->getVariants(XHS_LANGUAGE);
        }
        $params['shippingCostsUrl'] = $this->settings['shipping_costs_page'];
        $params['csrf_token_input'] = $this->csrfProtector->tokenInput();
        $this->csrfProtector->store();
        return $this->render('addToCartButton', $params);
    }

    public function updateCart()
    {
        $this->csrfProtector->check();
        $variant = null;

        if ($this->catalog->getProduct($_POST['cartItem'])->hasVariants()) {
            if (isset($_POST['xhsVariant'])) {
                $variant = (string) $_POST['xhsVariant'];
            } else {
                $variant = 0;
            }
        }

        if (!isset($_SESSION['xhsOrder'])) {
            $_SESSION['xhsOrder'] = new Order($this->settings['vat_full'], $this->settings['vat_reduced']);
        }

        if ((int) $_POST['xhsAmount'] > 0) {
            $_SESSION['xhsOrder']->addItem($this->catalog->getProduct($_POST['cartItem']), $_POST['xhsAmount'], $variant);
        } else {
            $_SESSION['xhsOrder']->removeItem($this->catalog->getProduct($_POST['cartItem']), $variant);
        }
        $_SESSION['xhsOrder']->setShipping($this->calculateShipping());

        $url = CMSIMPLE_URL . '?' . $_SERVER['QUERY_STRING'];
        header("Location: $url", true, 303);
        exit;
    }

    private function calculateShipping()
    {
        if (!$this->settings['charge_for_shipping']) {
            return 0;
        }
        if ($this->settings['shipping_up_to'] == 'true' &&
                $_SESSION['xhsOrder']->cartGross >= (float) $this->settings['forwarding_expenses_up_to']) {
            return 0;
        }
        if (empty($this->settings['weightRange'])) {
            return (float) $this->settings['shipping_max'];
        }
        $weight = $_SESSION['xhsOrder']->units;

        if (isset($this->settings['weightRange'])) {
            foreach ($this->settings['weightRange'] as $key => $value) {
                if ($weight <= (float) $key) {
                    return (float) $value;
                }
            }
        }
        return (float) $this->settings['shipping_max'];
    }

    private function calculatePaymentFee()
    {
        if (isset($_SESSION['xhsCustomer']->payment_mode)) {
            if ($this->loadPaymentModule($_SESSION['xhsCustomer']->payment_mode)) {
                return $this->paymentModules[$_SESSION['xhsCustomer']->payment_mode]->getFee();
            }
        }
        return 0.0;
    }

    public function cartPreview()
    {
        $cartItems = $this->collectCartItems();
        if ($cartItems) {
            $params = array();
            $params['xhs_url']   = XHS_URL;
            $params['cartItems'] = $cartItems;
            $params['cartSum']   = $_SESSION['xhsOrder']->cartGross;
            $params['count']     = count($cartItems);
            return $this->render('cartPreview', $params);
        }
        return false;
    }

    /**
     *
     * @return array|bool either an array of products or false if cart is empty
     */
    private function collectCartItems()
    {
        $cartItems = array();
        if (isset($_SESSION['xhsOrder']) && $_SESSION['xhsOrder']->hasItems()) {
            $i = 1;
            foreach ($_SESSION['xhsOrder']->items as $index => $product) {
                $test = explode('_', $index);  // variants are marked as uid_variant
                if (!key_exists($test[0], $this->catalog->getProducts())) {
                    continue;
                } // if someone comes from another xhshopShop
                $cartItems[$index]['itemCounter'] = $i;
                $cartItems[$index]['id']          = $index;
                $cartItems[$index]['amount']      = $product['amount'];
                $productKey                       = $index;
                $variantName                      = '';
                $variantKey                       = '';
                if (strstr($index, '_')) {
                    $array       = explode('_', $index);
                    $productKey  = $array[0];
                    $variantKey  = $array[1];
                    $variantName = $this->catalog->getProduct($productKey)->getVariantName($variantKey);
                }

                $name       = $this->catalog->getProduct($productKey)->getName(XHS_LANGUAGE);
                $detailLink = '';
                $page       = $this->catalog->getProduct($productKey)->getDetailsLink(XHS_LANGUAGE);
                if ($page) {
                    $page       = $this->bridge->translateUrl($page);
                    $name       = $this->viewProvider->link($page, $name);
                    $detailLink = $this->viewProvider->link($page, $this->viewProvider->labels['product_info']);
                }
                $vatRate                          = 'vat_' . $this->catalog->getProduct($productKey)->getVat();
                $vatRate                          = $this->settings[$vatRate];
                $cartItems[$index]['name']        = $name;
                $cartItems[$index]['key']         = $productKey;
                $cartItems[$index]['variantName'] = $variantName;
                $cartItems[$index]['variantKey']  = $variantKey;
                $cartItems[$index]['productPage'] = $this->catalog->getProduct($productKey)->getPage(XHS_LANGUAGE);
                $cartItems[$index]['description'] = $this->catalog->getProduct($productKey)->getTeaser(XHS_LANGUAGE);
                $cartItems[$index]['detailLink']  = $detailLink;
                $cartItems[$index]['price']       = $product['gross'];
                $cartItems[$index]['vatRate']     = $vatRate;
                $cartItems[$index]['sum']         = $product['gross'] * $product['amount'];
                if ($this->catalog->getProduct($productKey)->getPreviewPictureName()) {
                    $cartItems[$index]['previewPicture'] =
                            $this->catalog->getProduct($productKey)->getPreviewPicturePath();
                }

                $i++;
            }
            return $cartItems;
        }

        return false;
    }

    private function cart()
    {
        $cartItems = $this->collectCartItems();
        if (!$cartItems) {
            $this->relocateToCheckout(null, 302);
        }
        foreach ($cartItems as $key => $item) {
            if (strlen(trim($item['variantName'])) > 0) {
                $cartItems[$key]['variantName'] = ', ' . $item['variantName'];
            }
        }
        if ($cartItems) {
            $params = array();
            $params['cartItems']        = $cartItems;
            $params['shipping_limit']   = $this->settings['shipping_up_to'];
            $params['cartSum']          = $_SESSION['xhsOrder']->cartGross;
            $params['units']            = $_SESSION['xhsOrder']->units;
            $params['unitName']         = $this->settings['shipping_unit'];
            $params['shipping']         = $_SESSION['xhsOrder']->shipping;
            $params['total']            = $_SESSION['xhsOrder']->shipping + $_SESSION['xhsOrder']->cartGross;
            $params['vatTotal']         = $_SESSION['xhsOrder']->getVat();
            $params['vatFull']          = $_SESSION['xhsOrder']->getVatFull();
            $params['vatReduced']       = $_SESSION['xhsOrder']->getVatReduced();
            $params['minimum_order']    = $this->settings['minimum_order'];
            $params['no_shipping_from'] = $this->settings['forwarding_expenses_up_to'];
            $params['canOrder']         = $this->canOrder();
            $params['xhs_url']          = XHS_URL;
            $params['xhs_checkout_url'] = '?' . XHS_URL . '&xhsCheckout=cart';
            $params['csrf_token_input'] = $this->csrfProtector->tokenInput();
            $this->csrfProtector->store();

            return $this->render('cart', $params);
        }
        return false;
    }

    private function canOrder()
    {
        return isset($_SESSION['xhsOrder']) && $_SESSION['xhsOrder']->hasItems()
            && (float) $this->settings['minimum_order'] <= $_SESSION['xhsOrder']->getTotal();
    }

    private function customersData(array $missingData = array())
    {
        if (!$this->canOrder()) {
            $this->relocateToCheckout('cart', 302);
        }

        if (!isset($_SESSION['xhsCustomer'])) {
            $customer                = new Customer();
            $_SESSION['xhsCustomer'] = $customer;
        }

        foreach ($this->payments as $name) {
            $this->loadPaymentModule($name);
        }
        if (!isset($_SESSION['xhsCustomer']->payment_mode)) {
            foreach ($this->paymentModules as $module) {
                $_SESSION['xhsCustomer']->payment_mode = $module->getName();
                break;
            }
        }
        $params['payments']    = $this->paymentModules;
        $params['missingData'] = $missingData;

        $params['xhs_url'] = XHS_URL;
        $params['xhs_checkout_url'] = '?' . XHS_URL . '&xhsCheckout=checkCustomersData';
        $params['gtcUrl'] = ($this->settings['gtc_page']);
        $params['csrf_token_input'] = $this->csrfProtector->tokenInput();
        $this->csrfProtector->store();

        return $this->render('customersData', $params);
    }

    private function checkCustomersData()
    {
        if (!$this->canOrder()) {
            $this->relocateToCheckout('cart', 302);
        }
        $this->csrfProtector->check();
        $missingData = array();
        $postArray = array();
        foreach ($_POST as $key => $value) {
            $postArray[$key] = trim($value);
        }
        foreach ($_SESSION['xhsCustomer'] as $field => $value) {
            if (key_exists($field, $postArray)) {
                $_SESSION['xhsCustomer']->$field = $postArray[$field];
                if (in_array($field, $this->requiredCustomerData)
                        && (strlen($postArray[$field]) == 0 || !isset($postArray[$field]))) {
                    $missingData[] = $field;
                }
            }
        }
        if (!isset($_SESSION['xhsCustomer']->cos_confirmed)) {
            $missingData[] = 'cos_confirmed';
        }
        if (!isset($_SESSION['xhsCustomer']->payment_mode)) {
            $missingData[] = 'payment_mode';
        }
        if (!in_array($_SESSION['xhsCustomer']->country, $this->settings['shipping_countries'], true)) {
            $missingData[] = 'country';
        }
        if (count($missingData) > 0) {
            return $this->customersData($missingData);
        } else {
            $this->relocateToCheckout('finalConfirmation', 303);
        }
    }

    private function isValidCustomer()
    {
        if (!isset($_SESSION['xhsCustomer'])) {
            return false;
        }
        $customer = $_SESSION['xhsCustomer'];
        foreach ($this->requiredCustomerData as $field) {
            if (!isset($customer->$field) || $customer->$field == '') {
                return false;
            }
        }
        if (!in_array($_SESSION['xhsCustomer']->country, $this->settings['shipping_countries'], true)) {
            return false;
        }
        return true;
    }

    private function htmlConfirmation()
    {
        foreach ($_SESSION['xhsCustomer'] as $field => $value) {
            $params[$field]       = $value;
        }
        if (isset($params['annotation'])) {
            $params['annotation'] = nl2br($params['annotation']);
        }
        $params['fee']        = $this->calculatePaymentFee();
        $params['cartItems']  = $this->collectCartItems();
        $params['cartSum']    = $_SESSION['xhsOrder']->getCartSum();
        $params['shipping']   = $_SESSION['xhsOrder']->getShipping();
        $params['total']      = $_SESSION['xhsOrder']->getTotal();
        $params['vatTotal']   = $_SESSION['xhsOrder']->getVat();
        $params['vatFull']    = $_SESSION['xhsOrder']->getVatFull();
        $params['vatReduced'] = $_SESSION['xhsOrder']->getVatReduced();
        $params['contact_name']    = $this->settings['name'];
        $params['payment']    = $this->paymentModules[$_SESSION['xhsCustomer']->payment_mode]->getLabelString();
        if ($this->settings['dont_deal_with_taxes']) {
            $params['hideVat'] = true;
        } else {
            $params['hideVat']     = false;
            $params['fullRate']    = $this->settings['vat_full'];
            $params['reducedRate'] = $this->settings['vat_reduced'];
        }
        return $this->render('confirmation_email/html', $params);
    }

    private function textConfirmation()
    {
        foreach ($_SESSION['xhsCustomer'] as $field => $value) {
            $params[$field]       = $value;
        }
        $params['fee']        = $this->calculatePaymentFee();
        $params['cartItems']  = $this->collectCartItems();
        $params['cartSum']    = $_SESSION['xhsOrder']->getCartSum();
        $params['shipping']   = $_SESSION['xhsOrder']->getShipping();
        $params['total']      = $_SESSION['xhsOrder']->getTotal();
        $params['vatTotal']   = $_SESSION['xhsOrder']->getVat();
        $params['vatFull']    = $_SESSION['xhsOrder']->getVatFull();
        $params['vatReduced'] = $_SESSION['xhsOrder']->getVatReduced();
        $params['contact_name']    = $this->settings['name'];
        $params['payment']    = $this->paymentModules[$_SESSION['xhsCustomer']->payment_mode]->getLabelString();
        if ($this->settings['dont_deal_with_taxes']) {
            $params['hideVat'] = true;
        } else {
            $params['hideVat']     = false;
            $params['fullRate']    = $this->settings['vat_full'];
            $params['reducedRate'] = $this->settings['vat_reduced'];
        }
        return $this->render('confirmation_email/text', $params);
    }

    private function finalConfirmation()
    {
        if (!$this->canOrder()) {
            $this->relocateToCheckout('cart', 302);
        } elseif (!$this->isValidCustomer()) {
            $this->relocateToCheckout('customersData', 302);
        }
        $fee           = $this->calculatePaymentFee();
        $paymentModule = $this->paymentModules[$_SESSION['xhsCustomer']->payment_mode];
        if ($paymentModule->wantsCartItems() !== false) {
            $paymentModule->setCartItems($this->collectCartItems());
            $paymentModule->setShipping($_SESSION['xhsOrder']->getShipping());
        }

        foreach ($_SESSION['xhsCustomer'] as $field => $value) {
            $params[$field]       = isset($value) ? $value : '';
        }
        if (isset($params['annotation'])) {
            $params['annotation'] = nl2br($params['annotation']);
        }
        $_SESSION['xhsOrder']->setFee($fee);
        $params['xhs_url']    = XHS_URL;
        $params['xhs_checkout_url'] = '?' . XHS_URL . '&xhsCheckout=finish';
        $params['payment']    = $paymentModule;
        $params['fee']        = $fee;
        $params['cartItems']  = $this->collectCartItems();
        $params['cartSum']    = $_SESSION['xhsOrder']->getCartSum();
        $params['shipping']   = $_SESSION['xhsOrder']->getShipping();
        $params['total']      = $_SESSION['xhsOrder']->getTotal();
        $params['vatTotal']   = $_SESSION['xhsOrder']->getVat();
        $params['vatFull']    = $_SESSION['xhsOrder']->getVatFull();
        $params['vatReduced'] = $_SESSION['xhsOrder']->getVatReduced();
        if ($this->settings['dont_deal_with_taxes']) {
            $params['hideVat'] = true;
        } else {
            $params['hideVat']     = false;
            $params['fullRate']    = $this->settings['vat_full'];
            $params['reducedRate'] = $this->settings['vat_reduced'];
        }
        $params['csrf_token_input'] = $this->csrfProtector->tokenInput();
        $this->csrfProtector->store();

        return $this->render('finalConfirmation', $params);
    }

    /**
     *
     * @return <string>
     */
    private function finishCheckOut()
    {
        if (!$this->canOrder()) {
            $this->relocateToCheckout('cart', 302);
        } elseif (!$this->isValidCustomer()) {
            $this->relocateToCheckout('customersData', 302);
        }
        $this->csrfProtector->check();
        if (!isset($_SESSION['xhsCustomer']) || !isset($_SESSION['xhsOrder'])) {
            return '';
        }
        $bill = $this->writeBill();

        $sent = $this->sendEmails($bill);

        if ($sent === true) {
            $this->relocateToCheckout('thankYou', 303);
        } else {
            return $sent;
        }
    }

    private function writeBill()
    {
        global $plugin_tx;

        $class = 'Xhshop\\' . ucfirst($this->settings['bill_format']) . 'BillWriter';
        $writer = new $class();
        $template = XHS_TEMPLATES_PATH . 'frontend/confirmation_email/billtemplate.' . $this->settings['bill_format'];
        if (!$writer->loadTemplate($template)) {
            return $plugin_tx['xhshop']['error_no_bill'];
        }
        $rows   = '';

        $currency = ' ' . $this->settings['default_currency'];
        foreach ($this->collectCartItems() as $product) {
            $name    = strip_tags($product['name']) . ' ' . $product['variantName'];
            $price   = $this->viewProvider->formatFloat($product['price']) . $currency;
            $sum     = $this->viewProvider->formatFloat($product['sum']) . $currency;
            $amount  = $product['amount'] . ' ';
            $vatRate = $this->viewProvider->labels['get_vat'] . $product['vatRate'] . ' %';
            if ($this->settings['dont_deal_with_taxes']) {
                $vatRate = '';
            }
            $rows .= $writer->writeProductRow($name, $amount, $price, $sum, $vatRate);
        }
        $fee     = $this->calculatePaymentFee();

        if ($fee < 0) {
            $feeLabel = $this->viewProvider->labels['reduction'];
        } else {
            $feeLabel = $this->viewProvider->labels['fee'];
        }

        if ($this->settings['dont_deal_with_taxes']) {
            $vat_hint = $this->viewProvider->hints['price_info_no_vat'];
        } else {
            $vat_hint = $this->viewProvider->labels['included_vat'] . ' '
                . $this->viewProvider->formatFloat($_SESSION['xhsOrder']->getVat()) . $currency;
            $vat_hint .= ' (' . $this->settings['vat_reduced'] . '%: '
                . $this->viewProvider->formatFloat($_SESSION['xhsOrder']->getVatReduced()) . $currency . ' - ';
            $vat_hint .= $this->settings['vat_full'] . '%: '
                . $this->viewProvider->formatFloat($_SESSION['xhsOrder']->getVatFull()) . $currency . ')';
        }

        $subtotal     = $_SESSION['xhsOrder']->getCartSum();
        $shipping     = $_SESSION['xhsOrder']->getShipping();
        $replacements = array('%Datum%'          => date($this->settings['bill_dateformat']),
            '%Vorname%'        => $_SESSION['xhsCustomer']->first_name,
            '%Nachname%'       => $_SESSION['xhsCustomer']->last_name,
            '%Strasse%'        => $_SESSION['xhsCustomer']->street,
            '%PLZ%'            => $_SESSION['xhsCustomer']->zip_code,
            '%Ort%'            => $_SESSION['xhsCustomer']->city,
            '%COMPANY_NAME%'   => $this->settings['company_name'],
            '%COMPANY_STREET%' => $this->settings['street'],
            '%COMPANY_ZIP%'    => $this->settings['zip_code'],
            '%COMPANY_CITY%'   => $this->settings['city'],
            '%SUMME%'          => $this->viewProvider->formatFloat($subtotal) . $currency,
            '%SHIPPING%'       => $this->viewProvider->formatFloat($shipping) . $currency,
            '%rows%'           => $rows,
            '%FEE_LABEL%'      => $feeLabel,
            '%FEE%'            => $this->viewProvider->formatFloat($fee) . $currency,
            '%ENDSUMME%'       => $this->viewProvider->formatFloat($subtotal + $shipping + $fee) . $currency,
            '%MWST_HINWEIS%'   => $vat_hint
        );

        return $writer->replace($replacements);
    }

    private function sendEmails($bill)
    {
        global $plugin_tx;

        require_once(XHS_BASE_PATH . 'phpmailer/class.phpmailer.php');
        $mail = new PHPMailer();
        $mail->WordWrap = 60;
        $mail->IsHTML(true);
        $mail->set('CharSet', 'UTF-8');

        $customer     = $_SESSION['xhsCustomer']->email;
        $customerName = $_SESSION['xhsCustomer']->first_name . ' ' . $_SESSION['xhsCustomer']->last_name;

        $mail->From = $this->settings['order_email'];
        $mail->FromName = $this->settings['company_name'];
        $mail->AddReplyTo($this->settings['order_email'], $this->settings['company_name']);
        $mail->AddAddress($customer, $customerName);
        $mail->Subject = sprintf($this->viewProvider->mail['email_subject'], $this->settings['company_name']);

        $filename = XHS_TEMPLATES_PATH . "frontend/confirmation_email/{$this->settings['email_attachment']}";
        if ($this->settings['email_attachment'] !== '' && is_readable($filename)) {
            $mail->addAttachment($filename);
        }
        $mail->Body = $this->htmlConfirmation();
        $mail->AltBody = $this->textConfirmation();
        if (!$mail->Send()) {
            $message = sprintf($this->viewProvider->mail['confirmation_error_log'], $customer, $mail->ErrorInfo);
            XH_logMessage('error', 'xhshop', 'mail', $message);
            return sprintf($plugin_tx['xhshop']['mail_confirmation_error'], $this->settings['order_email']);
        } else {
            $message = sprintf($this->viewProvider->mail['confirmation_log'], $customer);
            XH_logMessage('info', 'xhshop', 'mail', $message);
        }

        $mail->ClearAddresses();
        $mail->clearAttachments();
        $mail->AddAddress($this->settings['order_email'], $this->settings['company_name']);
        $mail->Subject = sprintf($this->viewProvider->mail['notify'], $customerName, $this->settings['company_name']);
        $mail->AddStringAttachment($bill, "bill.{$this->settings['bill_format']}");
        $mail->Body = $this->htmlConfirmation();
        $mail->AltBody = $this->textConfirmation();
        if (!$mail->Send()) {
            $message = sprintf($this->viewProvider->mail['notification_error_log'], $customer, $mail->ErrorInfo);
            XH_logMessage('error', 'xhshop', 'mail', $message);
            return sprintf($plugin_tx['xhshop']['mail_notify_error'], $this->settings['order_email']);
        } else {
            $message = sprintf($this->viewProvider->mail['notification_log'], $customer);
            XH_logMessage('info', 'xhshop', 'mail', $message);
        }

        //  echo "Message has been sent";
        return true;
    }

    private function thankYou()
    {
        if (!$this->canOrder()) {
            $this->relocateToCheckout('cart', 302);
        } elseif (!$this->isValidCustomer()) {
            $this->relocateToCheckout('customersData', 302);
        }

        $params['name'] = $_SESSION['xhsCustomer']->first_name . ' ' . $_SESSION['xhsCustomer']->last_name;

        unset($_SESSION['xhsCustomer']);
        unset($_SESSION['xhsOrder']);

        return $this->render('thankYou', $params);
    }

    protected function productList($collectAll = true)
    {
        $params                       = parent::productList(false);
        $params['showCategorySelect'] = (bool) $this->settings['use_categories'];
        $params['csrf_token_input'] = $this->csrfProtector->tokenInput();
        $this->csrfProtector->store();

        return $this->render('catalog', $params);
    }

    /**
     *
     * @param <string> $needle
     * @return <string> the product list rendered in catalog.tpl
     */
    protected function productSearchList($needle = '')
    {
        $params                       = parent::productSearchList($needle);
        $params['showCategorySelect'] = (bool) $this->settings['use_categories'];
        $params['csrf_token_input'] = $this->csrfProtector->tokenInput();
        $this->csrfProtector->store();

        return $this->render('catalog', $params);
    }

    private function productDetails()
    {
        $product = $this->catalog->getProduct($_GET['xhsProduct']);
        if (!$product) {
            return $this->productList();
        }
        $params = array();
        $params['name']        = $product->getName();
        $params['teaser']      = $product->getTeaser();
        $params['description'] = $product->getDescription();
        $params['button']      = $this->addToCartButton($product);
        $params['variants']    = count($product->getVariants() > 0) ? $product->getVariants() : false;
        $params['price']       = $product->getGross();
        $params['uid']         = $product->getUid();
        $params['hideVat']     = (bool) $this->settings['dont_deal_with_taxes'];
        $params['vatRate']     = $this->settings['vat_' . $product->getVat()];
        $params['vatInfo']     = $this->vatInfo();
        $params['image']       = $this->viewProvider->linkedImage(
            $product->getPreviewPicturePath(),
            $product->getImagePath(),
            $product->getName(XHS_LANGUAGE),
            'zoom_g'
        );
        $params['shippingCostsUrl'] = $this->settings['shipping_costs_page'];
        $this->bridge->setTitle($params['name']);
        $this->bridge->setMeta('description', $params['teaser']);
        return $this->render('productDetails', $params);
    }

    private function closed()
    {
        $params = array();

        return $this->render('closed', $params);
    }

    public function shopToc($level = 6)
    {
        if (!$this->settings['use_categories']) {
            return;
        }
        $params = array();
        $url                   = $this->bridge->translateUrl(XHS_URL);
        $params['shopUrl']     = $url;
        $params['shopHeading'] = $this->bridge->getHeadingOfUrl(XHS_URL);
        $params['categories']  = array();
        if ($this->settings['allow_show_all']) {
            $params['categories'][0]['url']  = urlencode($this->viewProvider->labels['all_categories']);
            $params['categories'][0]['name'] = $this->viewProvider->labels['all_categories'];
        }
        $cats                            = $this->categories();
        $i                               = 1;
        foreach ($cats as $cat) {
            $params['categories'][$i]['url']  = urlencode($cat);
            $params['categories'][$i]['name'] = $cat;
            $i++;
        }

        if ($this->catalog->hasUncategorizedProducts()) {
            $i++;
            $params['categories'][$i]['url']  = 'left_overs';
            $params['categories'][$i]['name'] = $this->catalog->getFallbackCategory();
        }

        return $this->render('shopToc', $params);
    }

    public function handleRequest($request = null)
    {
        if (isset($_POST['ipn_track_id'])) {
            $this->loadPaymentModule('paypal');
            $this->paymentModules['paypal']->ipn();
        }
        if (file_exists(XHS_CONTENT_PATH . 'xhshop/tmp_orders/pp_' . session_id() . '.sent')) {
            unlink(XHS_CONTENT_PATH . 'xhshop/tmp_orders/pp_' . session_id() . '.sent');
            return $this->thankYou();
        }
        if (!$this->settings['published']) {
            return $this->closed();
        }
        if (isset($_GET['xhsProduct'])) {
            return $this->productDetails();
        }
        if (isset($_GET['xhsProductSearch'])) {
            return $this->productSearchList($_GET['xhsProductSearch']);
        }
        $checkOut = '';
        if (isset($_GET['xhsCheckout'])) {
            $checkOut = $_GET['xhsCheckout'];
        }

        switch ($checkOut) {
            case 'cart':
                return $this->cart();
            case 'customersData':
                return $this->customersData();
            case 'checkCustomersData':
                return $this->checkCustomersData();
            case 'finalConfirmation':
                return $this->finalConfirmation();
            case 'finish':
                return $this->finishCheckOut();
            case 'thankYou':
                return $this->thankYou();
            default:
                return $this->productList();
        }

        return;
    }

    private function relocateToCheckout($step, $status)
    {
        $url = CMSIMPLE_URL . '?' . XHS_URL;
        if (isset($step)) {
            $url .= "&xhsCheckout=$step";
        }
        header("Location: $url" , true, $status);
        exit;
    }
}
