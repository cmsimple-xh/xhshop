<?php

use Xhshop\PaymentModule;

global $xhsController;

class XHS_On_Delivery extends PaymentModule {

    function __construct(){
        $this->name = 'cash_on_delivery';
        $this->loadLanguage();
        $this->loadSettings();
    }

}
$xhsOnDelivery = new XHS_On_Delivery();
$xhsController->addPaymentModule($xhsOnDelivery);
?>