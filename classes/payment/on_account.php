<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

global $xhsController;
   
class OnAccount extends PaymentModule {
    
    function __construct(){
     
        $this->name = 'on_account';
        $this->loadLanguage();
        $this->loadSettings();
    }
   
}
$xhsOnAccount = new OnAccount();
$xhsController->addPaymentModule($xhsOnAccount);
?>
