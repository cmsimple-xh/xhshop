<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

class CashInAdvance extends PaymentModule {

    function __construct(){
        $this->name = 'cash_in_advance';
        $this->loadLanguage();
        $this->loadSettings();
    }

}

?>