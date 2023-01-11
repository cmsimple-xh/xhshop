<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

class OnAccount extends PaymentModule
{
    /** @return string */
    public function getName()
    {
        return 'on_account';
    }
}
