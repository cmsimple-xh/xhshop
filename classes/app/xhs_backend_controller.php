<?php
class XHS_Backend_Controller extends XHS_Controller {

    function __construct(){
        parent::__construct();
    }

    function handleRequest($request = null){
        $html = $this->tabs();
        $request = 'productList';
        if(isset($_POST['xhsTask'])){
            $request = $_POST['xhsTask'];
        }
        if(isset($_POST['xhsEPayment']) && in_array($_POST['xhsEPayment'], $this->payments)){
            $this->loadPaymentModule($_POST['xhsEPayment']);
            $this->paymentModules[$_POST['xhsEPayment']]->saveConfig();
        }
        if(method_exists($this, $request)){
            $html .=  $this->$request();
        } else{$html .=  parent::handleRequest($request);}
        return $html;
    }

    function productList($collectAll = true){
        if(isset($_POST['xhsProductSwapID']) && isset($_POST['xhsProductID'])  ){
            $myself = $this->catalog->getProduct($_POST['xhsProductID']);
            $swap = $this->catalog->getProduct($_POST['xhsProductSwapID']);
            $this->catalog->swapSortIndex($myself, $swap);
        }
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
			exit;
		}

        $indices    = array();
        $hints      = array();
        $errors     = array();

        $params = parent::productList();
        $params['category'] = isset($_POST['xhsCategory']) ? $_POST['xhsCategory'] : null;
        foreach($params['products'] as $index=>$product) {
            $indices[] = $index;
            if((float)$product['price'] <= 0){
                $errors[$index][] = 'no_price';
            }
            if(!$product['isAvailable']){
                $hints[$index][] = 'not_available';
            }
            if(strlen($product['previewPicture']) === 0){
                $hints[$index][] = 'no_preview_pic';
            }
            if(strlen($product['teaser']) === 0){
                $hints[$index][] = 'no_teaser';
            }
            if(strlen($product['description']) === 0 && count($product['pages']) === 0){
                $hints[$index][] = 'no_product_page';
            }
            foreach($product['pages'] as $temp){
                if(!$this->bridge->pageExists($temp)){
                    $errors[$index][] = 'page_not_found';
                }
            }
        }
        $params['indices'] = $indices;
        $params['caveats'] = $hints;
        $params['errors']  = $errors;
        $params['showCategorySelect'] = true;
        return $this->render('catalog', $params);
    }

    function editProduct($id = null){
        if(!isset($id)){
            $id = isset($_POST['xhsProductID']) ? $_POST['xhsProductID'] : 'new';
        }

        $params = array();

        $this->bridge->initProductDescriptionEditor();
        if(key_exists($id, $this->catalog->products)){
            $product = $this->catalog->products[$id];

            $params['product_ID']     = $id;
            $params['preview_selector'] = $this->viewProvider->picSelector(
				$this->settings['preview_folder'],
                $this->getImageFiles($this->settings['preview_folder']),
                $product->getPreviewPictureName(),
				'xhsPreviewPic');
            $params['image_selector'] = $this->viewProvider->picSelector($this->settings['image_folder'],
                $this->getImageFiles($this->settings['image_folder']),
                $product->getImageName(),
				'xhsImage');
            $params['variants']       = $product->hasVariants() ? implode('; ', $product->getVariants(XHS_LANGUAGE)) : '';
            $params['name']           = $product->names[XHS_LANGUAGE];
            $params['teaser']         = $product->getTeaser(XHS_LANGUAGE);
            $params['description']    = $product->getDescription(XHS_LANGUAGE);
            $params['price']          = $product->getGross();
            $params['weight']         = $product->getWeight();
            $params['stockOnHand']    = is_int($product->stock_on_hand) ? $product->stock_on_hand : 1;
            $params['preview']        = $product->getPreviewPicture();
            $params['image']          = $product->getImage();
            $params['vat']            = $product->vat;
            $params['pages']          = $product->getProductPages();
            $params['productCats']    = $product->getCategories();
        }
        else{
            $params['product_ID']     = 'new';
            $params['preview_selector'] = $this->viewProvider->picSelector(
				$this->settings['preview_folder'],
				$this->getImageFiles($this->settings['preview_folder']),
				null,
				'xhsPreviewPic');
            $params['image_selector'] = $this->viewProvider->picSelector(
				$this->settings['image_folder'],
				$this->getImageFiles($this->settings['image_folder']),
				null,
				'xhsImage');
            $params['name']           = 'N. N.';
            $params['teaser']         = '';
            $params['description']    = '';
            $params['variants']       = '';
            $params['price']          = 0.00;
            $params['weight']         = 1.00;
            $params['stockOnHand']    = 1;
            $params['preview']        = '';
            $params['image']          = '';
            $params['vat']            = $this->settings['vat_default_full'] ? 'full' : 'reduced';
            $params['pages']          = array();
            $params['productCats']    = array();
        }
        $params['shipping_unit']    = $this->settings['shipping_unit'];
        $params['categories']       = $this->catalog->getCategories();

        $level = 0;
        $params['pageLinks']     = $this->bridge->getUrls($level);
        $params['pageNames']     = $this->bridge->getHeadings($level);
        $params['pageLevels']    = $this->bridge->getLevels($level);

        return $this->render('productEdit', $params);
    }

    function deleteProduct(){
        if(!isset($_POST['xhsProductID'])){
            return false;
        }
        $this->catalog->deleteProduct($_POST['xhsProductID']);
        return $this->productList();
    }

    function productCategories(){
        $params['categories'] =  parent::categories();
        $params['leftOverCat'] = $this->catalog->category_for_the_left_overs[XHS_LANGUAGE];
        $params['xhsDefaultCat'] = $this->catalog->default_category[XHS_LANGUAGE];
        $params['xhsUseCats'] = $this->settings['use_categories'];
        $params['xhsAllowShowAll'] = $this->settings['allow_show_all'];

        return $this->render('categories', $params);
    }

    function tabs(){
        $params['SHOP_URL'] = $this->settings['url'];
        $params['app_name'] = $this->appName;
        $params['version'] = $this->version;
        $params['setting_tasks'] = 'xhsTaskTab';
        $params['product_tasks'] = 'xhsTaskTab';
        $params['help_tasks'] = 'xhsTaskTab';
        if(isset($_POST['xhsTaskCat'])){
            $params[$_POST['xhsTaskCat']] = 'xhsTaskTabActive';
        }else {$params['product_tasks'] = 'xhsTaskTabActive';}

		$screen = isset($_POST['xhsTask']) ? $_POST['xhsTask'] : 'productList'; 

       switch ($screen) {
            case 'editProduct' : $params['editProduct'] = 'xhsActiveSubtab';
                $params['editProductLabel'] = isset($_POST['xhsProductID']) ? 'edit_product' : 'new_product';
                break;

            default: $params[$screen] = 'xhsActiveSubtab';
                break;
        }
        return $this->render('tabs', $params);
    }

    function saveSettings(){
        $save = "<?php\n";
        foreach($this->settings as $key => $value){
			$exclude = array(
				'published', 'minimum_order', 'default_currency', 'shop_page', 'cos_page',
				'order_email', 'company_name', 'name', 'street', 'zip_code', 'city',
				'vat_full', 'vat_reduced', 'vat_default_full', 'dont_deal_with_taxes',
				'shipping_unit', 'forwarding_expenses_up_to', 'charge_for_shipping',
				'forwarding_expenses', 'shipping_max', 'weightRange'
			);
			if (in_array($key, $exclude, true)) {
				continue;
			}
            $row = '$zShopSettings' . "['" . $key . "']";
            if(!is_array($value)){
                $value = $this->tidyPostString($value, false);
                $row .= " = '" . $value . "';\n";
            }
            else {
                $row = '';
                foreach($value as $k => $t){
                    $row .= '$zShopSettings' . "['" . $key. "']" . "['$k'] = '" . addcslashes($t, '\'\\') . "';\n";
                }
            }
            $save .= $row;
        }
        $save .= '?>';
        $fh = fopen(XHS_CONFIG_FILE, 'w');
        fwrite($fh, $save);
        fclose($fh);
    }

    function saveProductCategories(){
        if(isset($_POST['xhsMoveCat'])){
            $this->catalog->moveCategory($_POST['xhsMoveDirection'], $_POST['xhsMoveCat']);
        }
        if(isset($_POST['xhsRenameCat'])){
            $newName = $this->tidyPostString($_POST['xhsCatName']);
            (strlen($newName) > 0) ?
            $this->catalog->renameCategory($newName, $_POST['xhsCatIndex']) : $this->catalog->deleteCategory($_POST['xhsCatIndex']) ;
        }
        if(isset($_POST['xhsAddCat'])){
            $newName = $this->tidyPostString($_POST['xhsAddCat']);
            if(strlen($newName) > 0) {
                $this->catalog->addCategory($newName);
            }
        }

        if(isset($_POST['xhsLeftOverCat'])){
            $leftOver = $this->tidyPostString($_POST['xhsLeftOverCat']);
            if(strlen($leftOver) > 0) {
                $this->catalog->setLeftOverCategory($leftOver);
            }
        }
        if(isset($_POST['xhsDefaultCat'])){
            $this->catalog->setDefaultCategory($_POST['xhsDefaultCat']);
        }
        if(isset($_POST['xhsUseCats'])){
            $this->settings['allow_show_all'] = $_POST['xhsAllowShowAll'];
            $this->settings['use_categories'] = $_POST['xhsUseCats'];
            $this->saveSettings();
        }

        return $this->productCategories();
    }

    function saveProduct(){
        $id = isset($_POST['xhsProductID']) ? $_POST['xhsProductID'] : 'new';
        if(key_exists($id, $this->catalog->products)){
            $product = $this->catalog->getProduct($id);
        }
        else{
            $product = new Product();
        }
        if(isset($_POST['xhsName'])){
            $product->setName($this->tidyPostString($_POST['xhsName']));
        }
        if(isset($_POST['xhsWeight'])){
            $product->setWeight($this->tidyPostString($_POST['xhsWeight']));
        }
        if(isset($_POST['xhsPrice'])){
            $product->setPrice($this->tidyPostString($_POST['xhsPrice']));
        }
        if(isset($_POST['xhsTeaser'])){
         $product->setTeaser($this->tidyPostString($_POST['xhsTeaser'], false));
        }
        if(isset($_POST['xhsDescription'])){
            $product->setDescription($this->tidyPostString($_POST['xhsDescription'], false));
        }
        if(isset($_POST['stockOnHand'])){
            $product->setStockOnHand($_POST['stockOnHand']);
        }
        if(isset($_POST['xhsCategories']) && is_array(($_POST['xhsCategories']))){
            $temp = array();
            foreach(($_POST['xhsCategories']) as $cat){
                $temp[] = $this->tidyPostString($cat);
            }
            $product->setCategories($temp);
        }else{
            $product->setCategories(array());
        }

        if(isset($_POST['xhsProductPages']) && is_array(($_POST['xhsProductPages']))){
            $temp = array();
            foreach(($_POST['xhsProductPages']) as $page){
                $temp[] = $page;
            }
            $product->setProductPages($temp);
        }else{
            $product->setProductPages(array());
        }

        if(isset($_POST['xhsPreviewPic'])){
            if($this->isAllowedImageFile($_POST['xhsPreviewPic'])){
                $product->setPreviewPic($_POST['xhsPreviewPic']);
            }else{
                $product->setPreviewPic();
            }
        }
        if(isset($_POST['xhsImage'])){

            if($this->isAllowedImageFile($_POST['xhsImage'])){
                $product->setImage($_POST['xhsImage']);
            }else{
                $product->setImage();
            }
        }

        if(isset($_POST['vat'])){
            $product->setVat($_POST['vat']);
        }
        if(isset($_POST['xhsVariants'])){
            $variants = array();
            $temp = explode(';', $_POST['xhsVariants']);
            foreach($temp as $variant){
                if(strlen($this->tidyPostString($variant)) > 0){
                    $variants[] = $this->tidyPostString($variant);
                }
            }
            $product->setVariants($variants);
        }

        if($id === 'new'){
            $this->catalog->addProduct($product);
            //$id = end($this->catalog->products)->uid;  // Parse error bei server4you ???
            $temp = end($this->catalog->products);
            $id = $temp->uid;
        }
        else {
            $this->catalog->updateProduct($id, $product);
        }

        return $this->editProduct($id);
    }

    function helpAbout(){
        $params['appName'] = $this->appName;
        $params['version'] = $this->version;
		$service = new XHS_SystemCheckService;
		$params['syschecks'] = $service->getChecks();
        return $this->render('help_about', $params);
    }
}
?>