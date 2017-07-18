<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;
use Xhshop\Decimal;

class PaypalEc extends PaymentModule
{
    public function __construct()
    {
        global $plugin_cf;

        parent::__construct();
        $this->settings['currency_code'] = $plugin_cf['xhshop']['shop_currency_code'];
    }

    public function getName()
    {
        return 'paypal_ec';
    }

    public function orderSubmitForm()
    {
        global $hjs;

        $hjs .= '<script src="https://www.paypalobjects.com/api/checkout.js"></script>';
        $transaction = array(
            'client' => array(
                'sandbox' => $this->settings['sandbox_id']
            ),
            'transaction' =>  array(
                'amount' => array(
                    'total' => (string) $this->calculateTotal(),
                    'currency' => $this->settings['currency_code']
                )
            )
        );
        return '<div id="xhsPaypalButton" data-transaction="' . XH_hsc(json_encode($transaction)) . '"></div>';
    }

    /**
     * @return Decimal
     */
    private function calculateTotal()
    {
        $sum = Decimal::zero();
        foreach ($this->cartItems as $item) {
            $sum = $sum->plus($item['sum']);
        }
        return $sum->plus($this->shipping)->plus(new Decimal($this->settings['fee']));
    }
}
