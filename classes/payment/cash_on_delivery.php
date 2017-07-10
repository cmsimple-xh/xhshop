<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

global $xhsController;

class CashOnDelivery extends PaymentModule {

    function __construct(){
        $this->name = 'cash_on_delivery';
        $this->loadLanguage();
        $this->loadSettings();
    }

}
$xhsOnDelivery = new CashOnDelivery();
$xhsController->addPaymentModule($xhsOnDelivery);
?>