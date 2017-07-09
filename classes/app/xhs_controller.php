<?php
/**
 * Description of xhscontroller
 *
 * @author Moritz
 */
class XHS_Controller {
    var $viewProvider;
    var $catalog;
    var $settings;
    var $appName = 'XH-Shop';
    var $version = '1alpha-preview';
    var $payments ;
    var $paymentModules;
    var $bridge;
    var $errors = array();

    function __construct(){
        global $pth, $plugin_cf, $plugin_tx;

        include XHS_CONFIG_FILE;
        $this->settings = $zShopSettings;
        foreach ($plugin_cf['xhshop'] as $key => $value) {
            if (strpos($key, 'shop_') === 0) {
                $this->settings[substr($key, 5)] = $value;
            } elseif (strpos($key, 'contact_') === 0) {
                $this->settings[substr($key, 8)] = $value;
            } elseif (strpos($key, 'taxes_') === 0) {
                $this->settings[substr($key, 6)] =$value;
            }
        }
        $this->settings['image_folder'] = "{$pth['folder']['images']}{$this->settings['image_folder']}";
        $this->settings['preview_folder'] = "{$pth['folder']['images']}{$this->settings['preview_folder']}";
        $this->settings['url'] = ltrim($plugin_tx['xhshop']['config_shop_page'], '?');
        $this->settings['cos_page'] = $plugin_tx['xhshop']['config_cos_page'];
        $this->getPaymentModules();
        $this->paymentModules = array();
        $this->payments = $this->getPaymentModules();
        /**
         * TODO: eliminate need of that CMSimple-separator, leave it to the bridge
         */

        if(!defined('XHS_URL') && isset($this->settings['url'])){
            define('XHS_URL',$this->settings['url']);
        }
        $this->bridge = new XHS_CMS_Bridge();
        $this->catalog = new Catalogue(XHS_URI_SEPARATOR);

        $viewProvider = (explode('_',get_class($this)));
        array_pop($viewProvider);
        $viewProvider = implode('_', $viewProvider).'_View';
        $this->viewProvider = new $viewProvider();
        $this->viewProvider->setCurrency($this->settings['default_currency']);
    }

    function render($template, $params = null){
        if(!is_a($this->viewProvider, 'XHS_View')){
            return "XHSController:render no view provider!";
        }
        if(is_array($params)){
            foreach($params as $key => $value){
                $this->viewProvider->assignParam($key, $value);
            }
        }
        return $this->viewProvider->loadTemplate($template);
    }

    function categories(){
        if(!isset($this->catalog->categories[XHS_LANGUAGE])){
            $this->catalog->categories[XHS_LANGUAGE] = array();
            $this->catalog->save();
        }
        return $this->catalog->categories[XHS_LANGUAGE];
    }

    function categoryOptions(){
        $options = array();

        if($this->settings['allow_show_all'] == 'true' || is_a($this, 'XHS_Backend_Controller')){
            $options[] = array('value' => '', 'label' => $this->viewProvider->labels['all_categories']);
        }
        foreach($this->categories() as $category){
            $options[] = array('value' => $category, 'label' => $category);
        }
        if($this->catalog->hasUncategorizedProducts()){
            $options[] = array('value' => 'left_overs', 'label' => $this->catalog->category_for_the_left_overs[XHS_LANGUAGE]);
        }
        return $options;
    }

    function products($category = null, $collectAll = false){
        if($category !== null){
            $category = $this->tidyPostString($category);
        }

        $productList = $this->catalog->getProducts($category);
        uasort($productList, array($this, 'compareProducts'));
        $products = array();

        foreach($productList as $index => $product){
            if($collectAll === false && $product->isAvailable() === false){continue;}
            $name = $product->getName(XHS_LANGUAGE);
            $detailLink = '';
            $page = $product->getDetailsLink(XHS_LANGUAGE);
            if($page){
                $page = $this->bridge->translateUrl($page);
                $name = $this->viewProvider->link($page, $name);
                $detailLink = $this->viewProvider->link($page, $this->viewProvider->labels['product_info']);
            }
            $products[$index]['pages'] = $product->getProductPages();
            //  $products[$index]['id'] = $index;
            $products[$index]['name'] = $name;
            $products[$index]['description'] = $product->getDescription(XHS_LANGUAGE);
            $products[$index]['teaser'] = $product->getTeaser();
            $products[$index]['detailLink'] = $detailLink;
            $products[$index]['price'] = $product->getGross();
            $products[$index]['sortIndex'] = $product->sortIndex;
            $products[$index]['isAvailable'] = $product->isAvailable();
            $products[$index]['previewPicture'] = $product->getPreviewPicture();
            $products[$index]['categories'] = $product->getCategories();
            $image = $product->getBestPicture();
            $image = '';
            $pic = $product->getBestPicture();
            if($pic){
                $info = getimagesize($pic);
                $image = '<a href="' . $pic . '" ' . $info[3] . ' title="' . $product->getName() . '" class="zoom"><img src="' . $pic . '" ' . $info[3] . ' title="' . $product->getName() . '"></a>';
            }
            $products[$index]['image'] = $image;
            if($product->hasVariants()){
                $products[$index]['variants'] = $product->getVariants();
            }
            //  $products[$index]['variants'] = $product->hasVariants() ? $product->getVariants() : false;
            //  var_dump($products[$index]['variants']);
        }
        return $products;
    }

    function getCurrentProduct(){
        $xhs_page_name = $_SERVER['QUERY_STRING'];
        $productPages = array();
        foreach($this->catalog->products as $product) {
            foreach($product->productPages[XHS_LANGUAGE] as $page) {
                $productPages[] = $page;
                if($page == $xhs_page_name){
                    return $product;
                }
            }
        }
        return false;
    }

    function getPagesProducts(){
        $url = $this->bridge->getCurrentPage();
        $products = array();
        foreach($this->catalog->products as $product) {
            if(!isset($product->productPages[XHS_LANGUAGE])){
                continue;
            }
            if(isset($product->stock_on_hand) && $product->stock_on_hand < 1){
                continue;
            }

            foreach($product->productPages[XHS_LANGUAGE] as $page) {
                if($page == $this->bridge->translateUrl($url) || $page == $url){
                    $products[] = $product;
                }
            }
        }

        return $products;
    }

    function handleRequest($request = null){
        if(!$request){
            return "No request";
        }
        if(!method_exists($this, $request)){
            return get_class($this) . ' does not understand: '. $request;
        }
        return $this->$request();
    }
    function productList($collectAll = true){
        $category = $this->catalog->default_category[XHS_LANGUAGE];
        if(isset($_GET['xhsCategory'])){
            $category = $_GET['xhsCategory'];
        }
        if(isset($_POST['xhsCategory'])){
            $category = $_POST['xhsCategory'];
        }
        $showCats = true;
        if(    $this->settings['use_categories'] === 'false'
            || $this->settings['use_categories'] === false
            || $this->settings['use_categories'] ===  '0'
        )
        { $showCats = false;
            $category = null;
        }

        $params['products'] = $this->products($category, $collectAll);
        $params['selectedCategory'] = $category;
        $params['categoryOptions'] = $this->categoryOptions();
        switch ($category) {
            case 'left_overs':
                $params['categoryHeader'] = $this->catalog->category_for_the_left_overs[XHS_LANGUAGE];
                break;
            default:  $params['categoryHeader'] = $category;
                break;
        }
        return $params;
    }

    function productSearchList($needle = ''){
        $showCats = true;
        if(    $this->settings['use_categories'] === 'false'
            || $this->settings['use_categories'] === false
            || $this->settings['use_categories'] ===  '0'
        )
        { $showCats = false;
        }
        $category = null;
        $products = array();
        // do not collect not available products for visitor
        $collectAll = is_a($this, 'XHS_Backend_Controller') ? true : false;
        $temp = $this->products(null, $collectAll);
        $needles = explode(' ', trim($needle));
        foreach($temp as $uid => $product){
            $gotIt = true;
            foreach($needles as $needle){
                 if(
                       stristr($product['name'], $needle)   == false
                    && stristr($product['teaser'], $needle) == false
                    && stristr($product['description'], $needle) == false
                    && stristr(implode(' ', $product['categories']), $needle) == false
                ){
                    $gotIt = false;
                    break;
                }
            }
            if($gotIt === false ){
                continue;
            }
            $products[$uid] = $product;
        }
        $params['products'] = $products;
        $params['selectedCategory'] = null;
        $params['categoryOptions'] = $this->categoryOptions();
        $params['categoryHeader'] = '';

        return $params;
    }

    function tidyPostString($string, $writeEntities = true){
        $string = str_replace(array('./', '<?php', '<?', '?>'), '', $string);
        if($writeEntities === true){
            $string = htmlspecialchars($string);
        }
        return rtrim($string);
    }

    function addPaymentModule($module){
        if(is_a($module, 'XHS_Payment_Module')){
            $this->paymentModules[$module->getName()] = $module;
            $module->setShopCurrency(html_entity_decode($this->settings['default_currency']));
            return true;
        }
        return false;
    }

    function loadPaymentModule($name){
        $name = str_replace('.', '', $name);
        $file = XHS_BASE_PATH . 'classes/paymentmodules/' . $name . '/' . $name . '.php';
        if (file_exists($file)) {
            include_once $file;
            return true;
        }
        return false;
    }

    function removePaymentModule($name){
        if(key_exists($name, $this->paymentModules)){
            unset($this->paymentModules[$name]);
            return true;
        }
        return false;
    }
/*
    function changed(){
        if(isset($_POST['xhsPage']) && method_exists($this, $_POST['xhsPage'])){
            var_dump($_POST);
            unset($_POST['xhsTask']);
            var_dump($_POST);
            return  $this->$_POST['xhsPage']();
        }
        return;
    }
*/

    function getImageFiles($directory = null){
        if($directory === null){
            $directory = $this->settings['image_folder'];
        }
       $handle = opendir($directory);
        $files = array();
        if($handle){
            while (false !== ($file = readdir($handle))) {
                if ($file == '.' || $file == '..') {continue;}
                if($this->isAllowedImageFile($file)){
                    $files[] = $file;
                }
            }
            closedir($handle);
        }
        return $files;
    }

    function isAllowedImageFile($file = ''){
        $extensions = array('jpeg', 'jpg', 'gif', 'png', 'svg', 'tif', 'tiff');
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if($extension == $file){return false;}
        if(in_array($extension, $extensions)){
            return true;
        }
        return false;
    }

    function compareProducts($productA, $productB){
        /**
         *  By default the products are sorted by asscendent sortIndex => newest first
         */
        $field =  isset($_POST['xhsProductSortField']) ? $_POST['xhsSortField'] : 'sortIndex';
        $order =  isset($_POST['xhsProductSortOrder']) && $_POST['xhsProductSortOrder'] == 'DESC' ? $_POST['xhsProductSortOrder'] : 'ASC';

        if(!is_a($productA, 'Product') || !is_a($productB, 'Product') ){
            trigger_error('Catalog::compareProducts() - expects 2 Product-Objects');
            return 0;
        }
        if(!isset($productA->$field)){
            trigger_error('Catalog:compareProducts - cannot compare products by ' . $field);
            return 0;
        }
        if(is_array($productA->$field)){
            $temp = $productA->$field;
            $propertyA = $temp[XHS_LANGUAGE];
            $temp = $productB->$field;
            $propertyB = $temp[XHS_LANGUAGE];
        }
        else{
            $propertyA = $productA->$field;
            $propertyB = $productB->$field;
        }

        if($propertyA == $propertyB){
            return 0;
        }
        if($order == 'DESC'){
            return ($propertyA > $propertyB) ? -1 : 1;
        }
        if($order == 'ASC'){
            return ($propertyA < $propertyB) ? -1 : 1;
        }
        return 0;
    }

    function getPaymentModules(){
        return $this->getSubdirectoryNames(XHS_BASE_PATH . 'classes/paymentmodules/');
    }

    function getSubdirectoryNames($dir){
        if (is_dir($dir)) {
            $names = array();
            $handle = opendir($dir);
            if ($handle) {
                while (($file = readdir($handle)) !== false) {
                    if($file == '.' || $file == '..' || !filetype($dir . $file)){continue;}
                    $names[] = $file;
                }
                closedir($handle);
            }
            return $names;
        }
        trigger_error('XHS_Controller::getSubdirectoryNames($dir) - no $dir (directory name)  was passes');
    }
    function shopToc($level = 6){
        return '';
    }
}
?>