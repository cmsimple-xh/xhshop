<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

class CashOnDelivery extends PaymentModule
{
    public function __construct()
    {
        $this->loadLanguage();
        $this->loadSettings();
    }

    public function getName()
    {
        return 'cash_on_delivery';
    }
}
