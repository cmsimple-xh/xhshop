<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

class CashInAdvance extends PaymentModule
{
    /** @return string */
    public function getName()
    {
        return 'cash_in_advance';
    }
}
