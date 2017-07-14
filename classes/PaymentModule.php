<?php

namespace Xhshop;

abstract class PaymentModule
{
    protected $name = null;
    protected $language = array();
    protected $settings = array();
    protected $cartItems = array();
    private $shopCurrency; // unused?
    protected $shipping = 0.0;

    public function __construct()
    {
        $this->loadLanguage();
        $this->loadSettings();
    }

    // apparently unused
    private function isAvailable()
    {
        return true;
    }

    public function getLabel()
    {
        return isset($this->language['label']) ? $this->language['label'] : '* ' . $this->getName() . ' *';
    }

    /**
     *
     * @return <string>
     *
     * Do not overwrite this in subclasses - or at least return a short <string> that can be used in text emails
     */
    public function getLabelString()
    {
        return isset($this->language['label']) ? $this->language['label'] : '* ' . $this->getName() . ' *';
    }

    public function getName()
    {
        if (isset($this->name)) {
            return $this->name;
        }
        return 'Error: *' . get_class($this) . '* has to provide a name.';
    }

    public function getFee()
    {
        return isset($this->settings['fee']) ? (float)$this->settings['fee'] : 0.00;
    }

    public function orderSubmitForm()
    {
        return false;
    }

    public function wantsCartItems()
    {
        false;
    }

    public function setCartItems(array $cartItems)
    {
        $this->cartItems = $cartItems;
    }

    // apparently unused
    private function choosePaymentRadio()
    {
    }

    protected function loadLanguage()
    {
        global $plugin_tx;

        $lang = array();
        $prefix = str_replace('_', '-', $this->name) . '_';
        foreach ($plugin_tx['xhshop'] as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $lang[substr($key, strlen($prefix))] = $value;
            }
        }
        $this->language = $lang;
        return false;
    }

    protected function loadSettings()
    {
        global $plugin_cf;

        $config = array();
        $prefix = str_replace('_', '-', $this->name) . '_';
        foreach ($plugin_cf['xhshop'] as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $config[substr($key, strlen($prefix))] = $value;
            }
        }
        $this->settings = $config;
        return false;
    }

    public function setShopCurrency($currency = null)
    {
        $this->shopCurrency = $currency;
    }

    public function setShipping($shipping = 0.0)
    {
        $this->shipping = $shipping;
    }
}
