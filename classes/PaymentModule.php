<?php

namespace Xhshop;

/**
 * The abstract base class of all payment modules
 *
 * Payment modules implement different payment methods.  In the simplest case
 * a payment method is a rather abstract concept and doesn't require special
 * implementation, such as the `CashInAdvance` and `OnAccount` payment modules.
 * Such simple payment modules just have to define the `::getName()` method,
 * which is supposed to return the snake-cased name of the payment module which
 * is used, converted to lisp-case, as prefix of the required configuration and
 * language keys.
 *
 * For example, the payment module `FooBar`'s `::getName()` method usually
 * returns `foo_bar`, and so there have to be the configuration options
 * `foo-bar_is_active` (to enable/disable the module) and `foo-bar_fee` (to
 * determine the fee for this payment method). Furthermore, there has to be a
 * language text for `foo-bar_label` (what is used as label of the payment
 * method during checkout).  If there is no need for the fee and/or the label
 * to be configurable, `::getFee()` and `::getLabel()` can be overriden in the
 * child class.
 *
 * However, there may be the need for further automation so other methods
 * may need to be overriden as well.  See `Paypal` for an example.
 *
 * Anyhow, all PHP files in the `payment/` subfolder are supposed to extend
 * `PaymentModule` and all payment modules have to be placed in this folder.
 */
abstract class PaymentModule
{
    protected $language = array();
    protected $settings = array();
    protected $cartItems = array();
    private $shopCurrency; // unused?

    /**
     * @var Decimal
     */
    protected $shipping;

    public function __construct()
    {
        $this->loadLanguage();
        $this->loadSettings();
        $this->shipping = Decimal::zero();
    }

    /** @return string */
    public function getLabel()
    {
        return isset($this->language['label']) ? $this->language['label'] : '* ' . $this->getName() . ' *';
    }

    /**
     * @return string
     *
     * Do not overwrite this in subclasses - or at least return a short <string> that can be used in text emails
     */
    public function getLabelString()
    {
        return isset($this->language['label']) ? $this->language['label'] : '* ' . $this->getName() . ' *';
    }

    /**
     * Returns the name of the payment module
     *
     * The name is used as prefix for configuration and language keys.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * @return Decimal
     */
    public function getFee()
    {
        return isset($this->settings['fee']) ? new Decimal($this->settings['fee']) : Decimal::zero();
    }

    /** @return string|false */
    public function orderSubmitForm()
    {
        return false;
    }

    /** @todo isn't this supposed to return false? */
    public function wantsCartItems()
    {
        false;
    }

    /** @return void */
    public function setCartItems(array $cartItems)
    {
        $this->cartItems = $cartItems;
    }

    /** @return false */
    protected function loadLanguage()
    {
        global $plugin_tx;

        $lang = array();
        $prefix = str_replace('_', '-', $this->getName()) . '_';
        foreach ($plugin_tx['xhshop'] as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $lang[substr($key, strlen($prefix))] = $value;
            }
        }
        $this->language = $lang;
        return false;
    }

    /** @return false */
    protected function loadSettings()
    {
        global $plugin_cf;

        $config = array();
        $prefix = str_replace('_', '-', $this->getName()) . '_';
        foreach ($plugin_cf['xhshop'] as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $config[substr($key, strlen($prefix))] = $value;
            }
        }
        $this->settings = $config;
        return false;
    }

    /**
     * @param ?string $currency
     * @return void
     */
    public function setShopCurrency($currency = null)
    {
        $this->shopCurrency = $currency;
    }

    /** @return void */
    public function setShipping(Decimal $shipping)
    {
        $this->shipping = $shipping;
    }
}
