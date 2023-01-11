<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

class CashOnDelivery extends PaymentModule
{
    /** @return string */
    public function getName()
    {
        return 'cash_on_delivery';
    }
}
