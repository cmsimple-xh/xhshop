<?php

namespace Xhshop;

class PaymentModule
{
    var $name = null;
    var $language = array();
    var $settings = array();
    var $cartItems = array();
    var $shopCurrency;
    var $shipping = 0.0;

    function __construct()
    {
        $this->loadLanguage();
        $this->loadSettings();
    }

    function isAvailable()
    {
        return true;
    }

    function getLabel()
    {
        return isset($this->language['label']) ? $this->language['label'] : '* ' . $this->getName() . ' *';
    }

    /**
     *
     * @return <string>
     *
     * Do not overwrite this in subclasses - or at least return a short <string> that can be used in text emails
     */
    function getLabelString()
    {
        return isset($this->language['label']) ? $this->language['label'] : '* ' . $this->getName() . ' *';
    }

    function getName()
    {
        if (isset($this->name)) {
            return $this->name;
        }
        return 'Error: *' . get_class($this) . '* has to provide a name.';
    }

    function getFee()
    {
        return isset($this->settings['fee']) ? (float)$this->settings['fee'] : 0.00;
    }

    function orderSubmitForm()
    {
        return false;
    }

    function wantsCartItems()
    {
        false;
    }

    function setCartItems($cartItems)
    {
        $this->cartItems = $cartItems;
    }

    function choosePaymentRadio()
    {
    }

    function loadLanguage()
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

    function loadSettings()
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

    function setShopCurrency($currency = null)
    {
        $this->shopCurrency = $currency;
    }

    function setShipping($shipping = 0.0)
    {
        $this->shipping = $shipping;
    }
}
