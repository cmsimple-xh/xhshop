<?php

namespace Xhshop\Payment;

use Xhshop\PaymentModule;

class OnAccount extends PaymentModule {
    
    function __construct(){
     
        $this->name = 'on_account';
        $this->loadLanguage();
        $this->loadSettings();
    }
   
}

?>
