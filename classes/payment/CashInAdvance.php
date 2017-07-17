<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

class CashInAdvance extends PaymentModule
{
    public function __construct()
    {
        $this->loadLanguage();
        $this->loadSettings();
    }

    public function getName()
    {
        return 'cash_in_advance';
    }
}
