<?php

use Xhshop\PaymentModule;

global $xhsController;

class XHS_In_Advance extends PaymentModule {

    function __construct(){
        $this->name = 'cash_in_advance';
        $this->loadLanguage();
        $this->loadSettings();
    }

}

$xhsInAdvance = new XHS_In_Advance();
$xhsController->addPaymentModule($xhsInAdvance);

?>