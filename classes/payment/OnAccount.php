<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

class OnAccount extends PaymentModule
{
    
    public function __construct()
    {
        $this->loadLanguage();
        $this->loadSettings();
    }

    public function getName()
    {
        return 'on_account';
    }
}
