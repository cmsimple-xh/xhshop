<?php

use Xhshop\PaymentModule;

global $xhsController;
   
class XHS_On_Account extends PaymentModule {
    
    function __construct(){
     
        $this->name = 'on_account';
        $this->loadLanguage();
        $this->loadSettings();
    }
   
}
$xhsOnAccount = new XHS_On_Account();
$xhsController->addPaymentModule($xhsOnAccount);
?>
