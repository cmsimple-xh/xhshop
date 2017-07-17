<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

class CashOnDelivery extends PaymentModule
{
    public function getName()
    {
        return 'cash_on_delivery';
    }
}
