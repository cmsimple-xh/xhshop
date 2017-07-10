<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

global $xhsController;

class CashInAdvance extends PaymentModule {

    function __construct(){
        $this->name = 'cash_in_advance';
        $this->loadLanguage();
        $this->loadSettings();
    }

}

$xhsInAdvance = new CashInAdvance();
$xhsController->addPaymentModule($xhsInAdvance);

?>