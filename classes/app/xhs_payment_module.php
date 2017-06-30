<?php

class XHS_Payment_Module {
    var $name = null;
    var $language = array();
    var $settings = array();
    var $requiredData = array();
    var $cartItems = array();
    var $shopCurrency;
    var $shipping = 0.0;
    

    function __construct(){
        
        $this->loadLanguage();
        $this->loadSettings();


    }

    function isAvailable(){
        
        return true;
    }

    function isActive(){

        return $this->settings['isActive'] == 'true' ? true : false;

    }

    function setActive($active = true){
        $active == true ?   $this->settings['isActive'] = 'true' : $this->settings['isActive'] = 'false';
        //   var_dump($this->settings);
    }
    function setFee($fee){
        $this->settings['fee'] = (float)str_replace(',', '.', $fee);
    }
    function needsSettings(){
        return true;
    }

    function getLabel(){
        return isset($this->language['label']) ? $this->language['label'] : '* ' . $this-> getName() . ' *';
    }
    /**
     *
     * @return <string>
     *
     * Do not overwrite this in subclasses - or at least return a short <string> that can be used in text emails
     */
    function getLabelString(){
        return isset($this->language['label']) ? $this->language['label'] : '* ' . $this-> getName() . ' *';
    }

    function getName(){
        if(isset($this->name)){
            return $this->name;
        }
        return 'Error: *' . get_class($this) . '* has to provide a name.';
    }

    function getFee(){
        return isset($this->settings['fee']) ? (float)$this->settings['fee'] : 0.00;
    }

    function orderSubmitForm(){
        return false;

    }

    function settingInputs(){
        return '';
    }

    function wantsCartItems(){
       false;
    }

    function setCartItems($cartItems){
        $this->cartItems = $cartItems;
    }

    function choosePaymentRadio(){

    }

    function loadLanguage(){
        if(file_exists(XHS_BASE_PATH . 'classes/paymentmodules/' . $this->name . '/lang.php')){
            include_once XHS_BASE_PATH . 'classes/paymentmodules/' . $this->name . '/lang.php';
            $this->language = $lang[XHS_LANGUAGE];
            return true;
        }
        return false;
    }

    function loadSettings(){
        if(file_exists(XHS_BASE_PATH . 'classes/paymentmodules/' . $this->name . '/settings.php')){
            include_once XHS_BASE_PATH . 'classes/paymentmodules/' . $this->name . '/settings.php';
            $this->settings = $config;
            return true;
        }
        return false;

    }

    function saveSettings(){
        $this->setActive(isset($_POST[$this->name.'_checked']));
        $this->setFee(isset($_POST[$this->name.'_fee']) ? (float)str_replace(",", ".", $_POST[$this->name.'_fee']) : 0.00);
        $file = XHS_BASE_PATH . 'classes/paymentmodules/' . $this->name . '/settings.php';

        $handle = fopen($file, 'w');
        if(!$handle){ 
            return false;
        }
        $string = '<?php' . "\n";
        foreach($this->settings as $key => $value){
            if(is_string($value)){$value = '"' . $value . '"'; }
            $string .=  '$config[\'' . $key .'\'] = '.  $value . ';' . "\n";
        }
        $string .= '?>';
        fwrite($handle, $string);
        fclose($handle);
        return true;
    }
    function needsConfig(){
        return count($this->requiredData) > 0 ? true : false;
    }
    function setShopCurrency($currency = null){
        $this->shopCurrency = $currency;
    }
    function setShipping($shipping = 0.0){
        $this->shipping = $shipping;
    }
}
?>
