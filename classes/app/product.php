<?php
class Product {
//    var $counter;
    var $names;
    var $descriptions;
    var $teasers;
    var $price;
    var $productPages;
    var $previewPicture;
    var $vat;
    var $separator;
    var $categories;
    var $stock_on_hand;
    var $weight;
    var $variants;
    var $uid;
    var $sortIndex;
    var $image;
    private $imageFolder;
    private $previewFolder;

    function __construct() {
        global $pth, $plugin_cf;

        $this->names = array();
        $this->descriptions = array();
        $this->teasers = array();
        $this->productPages = array();
        $this->categories = array();
        $this->variants = array();
        $this->uid = uniqid('p');
        $this->imageFolder = "{$pth['folder']['images']}{$plugin_cf['xhshop']['shop_image_folder']}";
        $this->previewFolder = "{$pth['folder']['images']}{$plugin_cf['xhshop']['shop_preview_folder']}";
    }

    function getWeight(){
        if(!isset($this->weight)){$this->weight = 0;}
        return (float)$this->weight;
    }
    function getNet($vatRate){
        $net = $this->price/(100 + $vatRate)*100;
        return (float)$net;
    }

    function getGross(){
        return (float)$this->price;
    }

    function getName($language = null, $variant = null) {
        if($language === null){ $language = XHS_LANGUAGE; }
        $variantName = '';
        //     var_dump($this);
        if(isset($this->variants[$language][$variant])){
            $variantName = ' ('.$this->variants[$language][$variant]. ')';
        }
        if(!isset($this->names[$language])) {
            $langs = array_keys($this->names);
            $language = $langs[0];
        }

        return $this->names[$language]. $variantName;
    }
    function getVariantName($variant = 0){
        if(array_key_exists($variant, $this->variants[XHS_LANGUAGE])){
            return $this->variants[XHS_LANGUAGE][$variant];
        }
     }

     function getBestPicture(){
         if(isset($this->image) && strlen($this->image) > 0 && file_exists($this->imageFolder . $this->image)){
             return $this->imageFolder . $this->image;
         }
         if(isset(   $this->previewPicture)
                  && strlen($this->previewPicture) > 0
                  && file_exists($this->imageFolder . $this->previewPicture)){


             return $this->imageFolder . $this->previewPicture;
         }
        return  false;
     }


     function getImage(){
         if((isset($this->image)) && strlen($this->image) > 0 && ($this->image <> '')) {

            $image = '<a href="' . $this->imageFolder.$this->image . '" title="'.$this->getName(XHS_LANGUAGE). '" class="zoom"><img src="' . $this->imageFolder.$this->image . '" alt="' .$this->getName(XHS_LANGUAGE) . '"  title="'.$this->getName(XHS_LANGUAGE). '"></a>';
            return $image;
        }
        return '';
     }
    
    function getDescription($language = null) {
        $language = ($language === null) ? XHS_LANGUAGE : $language;
        return isset($this->descriptions[$language]) ? $this->descriptions[$language] : '' ;
    }
     function getTeaser($language = null) {
        $language = ($language === null) ? XHS_LANGUAGE : $language;
       
        return isset($this->teasers[$language]) ? $this->teasers[$language] : '' ;
    }

    function getPageLink($language, $label = null){
        if(!$label){$label = $this->getName($language);}
        if($this->getPage($language)) {
            $label = "<a href=\"" . $this->productPages[$language][0] . "\">$label</a>";
        }
        return $label;
    }

    function getDetailsLink($language = null){
        $lang = ($language === null) ? XHS_LANGUAGE : $language;
         if(isset($this->descriptions[$lang]) && trim($this->descriptions[$lang]) != ''){
            return XHS_URL . '&xhsProduct=' . $this->uid;
        }
        return $this->getPage($language);
    }
    
    function getPage($language = null){
        if($language == null){
            $language = XHS_LANGUAGE;
        }

        if(isset($this->productPages[$language][0]) && trim($this->productPages[$language][0]) <> ''){
            return $this->productPages[$language][0];
        }
        return false;
    }

    function getPreviewPicture(){
        if((isset($this->previewPicture)) && ($this->previewPicture <> '')) {
            
            $image = '<a href="' . $this->imageFolder.$this->image . '" title="' . $this->getName(XHS_LANGUAGE) . '" class="zoom"><img src="'. $this->previewFolder . $this->previewPicture. '" alt="' .$this->getName(XHS_LANGUAGE) .' - Preview"  title="'.$this->getName(XHS_LANGUAGE). '"></a>';
            return $image;
        }
        return '';
    }

    function getPreviewPictureName(){
        if((isset($this->previewPicture))) {
            return $this->previewPicture;
        }
        return '';
    }

    function getImageName(){
        if((isset($this->image))) {
            return $this->image;
        }
        return '';
    }
    
    function getVariants($language = null){
        if($language === null){ $language = XHS_LANGUAGE; }
        return isset($this->variants[XHS_LANGUAGE]) ? $this->variants[XHS_LANGUAGE] : array();
    }

    function getCategories($language = null){
        if($language === null){ $language = XHS_LANGUAGE; }
        return isset($this->categories[$language]) ? $this->categories[$language] : array();
    }

    function getProductPages($language = null){
        if($language === null){ $language = XHS_LANGUAGE; }
        if(isset($this->productPages[$language])){
            return $this->productPages[$language];
        }
        return array();
    }
    function hasVariants(){
        // return true;
        return (isset($this->variants[XHS_LANGUAGE]) && is_array($this->variants[XHS_LANGUAGE])) 
                ?  count($this->variants[XHS_LANGUAGE]) > 0
                : null;
    }

    function setName($name = 'No Name!', $language = null){
        if($language === null){ $language = XHS_LANGUAGE; }
        $this->names[$language] = $name;
        return;
    }

    function setDescription($description = '', $language = null){
        if($language === null){ $language = XHS_LANGUAGE; }
        $this->descriptions[$language] = $description;
        return;
    }

    function setTeaser($description = '', $language = null){
        if($language === null){ $language = XHS_LANGUAGE; }
        $this->teasers[$language] = $description;
        return;
    }

    function setPrice($price = 0.00){
        $price = str_replace(',', '.', $price);
        $this->price = (float)$price;
        return;
    }

    function setWeight($weight = 0.00){
        $weight = str_replace(',', '.', $weight);
        $this->weight = (float)$weight;
        return;
    }

    function setStockOnHand($quantity = 1){
        $this->stock_on_hand = (int)$quantity;
        return;
    }

    function setVat($rate = 'full'){
        $this->vat = (string)$rate;
        return;
    }

    function setVariants($variants = array(), $language = null){
         if($language === null){ $language = XHS_LANGUAGE; }
         if(is_array($variants)){
             if(count(($variants)) == 1){
                 trigger_error('Product:setVariants() only 1 variant has been passed - and ignored.');
                 $this->variants[$language] = array();
                 return;
             }
             $this->variants[$language] = $variants;
             return;
         }
         trigger_error('Product:setVariants() expects an array as first argument.');
         return;
    }

    function setProductPages($pages = array(), $language = null){
         if($language === null){ $language = XHS_LANGUAGE; }
         if(is_array($pages)){
             $this->productPages[$language] = $pages;
             return;
         }
         trigger_error('Product::setProductPages() expects an array as first argument.');
         return;
    }

    function setCategories($categories = array(), $language = null){
         if($language === null){ $language = XHS_LANGUAGE; }
         
         if(is_array($categories)){
             $this->categories[$language] = $categories;
             return;
         }
         trigger_error('Product:setCategories() expects an array as first argument.');
         return;
    }

    function setPreviewPic($pic = ''){
        $this->previewPicture = (string)$pic;
    }

    function setImage($pic = ''){
        $this->image = (string)$pic;
    }

    function isAvailable(){
        return isset($this->stock_on_hand) ? $this->stock_on_hand > 0 : true;
    }
}
?>
