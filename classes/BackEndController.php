<?php

namespace Xhshop;

class BackEndController extends Controller
{
    public function handleRequest($request = null)
    {
        global $su;

        $html = '';
        if (isset($_REQUEST['xhsTask'])) {
            $request = $_REQUEST['xhsTask'];
        } elseif ($su !== $this->settings['url']) {
            $request = 'syscheck';
        } else {
            $request = 'productList';
        }
        if (method_exists($this, $request)) {
            $html .=  $this->$request();
        } else {
            $html .=  parent::handleRequest($request);
        }
        return $html;
    }

    protected function productList($collectAll = true)
    {
        if (isset($_POST['xhsProductSwapID']) && isset($_POST['xhsProductID'])) {
            $this->csrfProtector->check();
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
        foreach ($params['products'] as $index => $product) {
            $indices[] = $index;
            if (!$product['price']->isGreaterThan(Decimal::zero())) {
                $errors[$index][] = 'no_price';
            }
            if (!$product['isAvailable']) {
                $hints[$index][] = 'not_available';
            }
            if (strlen($product['previewPicture']) === 0) {
                $hints[$index][] = 'no_preview_pic';
            }
            if (strlen($product['teaser']) === 0) {
                $hints[$index][] = 'no_teaser';
            }
            if (strlen($product['description']) === 0 && count($product['pages']) === 0) {
                $hints[$index][] = 'no_product_page';
            }
            foreach ($product['pages'] as $temp) {
                if (!$this->bridge->pageExists($temp)) {
                    $errors[$index][] = 'page_not_found';
                }
            }
        }
        $params['indices'] = $indices;
        $params['caveats'] = $hints;
        $params['errors']  = $errors;
        $params['showCategorySelect'] = true;
        $params['csrf_token_input'] = $this->csrfProtector->tokenInput();
        return $this->render('catalog', $params);
    }

    private function editProduct($id = null)
    {
        if (!isset($id)) {
            $id = isset($_GET['xhsProductID']) ? $_GET['xhsProductID'] : 'new';
        }

        $params = array();

        $this->bridge->initProductDescriptionEditor();
        if (key_exists($id, $this->catalog->getProducts())) {
            $product = $this->catalog->getProduct($id);

            $params['product_ID']     = $id;
            $params['preview_selector'] = $this->viewProvider->picSelector(
                $this->settings['preview_folder'],
                $this->getImageFiles($this->settings['preview_folder']),
                $product->getPreviewPictureName(),
                'xhsPreviewPic'
            );
            $params['image_selector'] = $this->viewProvider->picSelector(
                $this->settings['image_folder'],
                $this->getImageFiles($this->settings['image_folder']),
                $product->getImageName(),
                'xhsImage'
            );
            $params['variants']    = $product->hasVariants() ? implode('; ', $product->getVariants(XHS_LANGUAGE)) : '';
            $params['name']        = $product->getName();
            $params['teaser']      = $product->getTeaser(XHS_LANGUAGE);
            $params['description'] = $product->getDescription(XHS_LANGUAGE);
            $params['price']       = $product->getGross();
            $params['weight']      = $product->getWeight();
            $params['stockOnHand'] = $product->isAvailable();
            $params['preview']     = $this->viewProvider->linkedImage(
                $product->getPreviewPicturePath(),
                $product->getPreviewPicturePath(),
                $product->getName(XHS_LANGUAGE)
            );
            $params['image']       = $this->viewProvider->linkedImage(
                $product->getImagePath(),
                $product->getImagePath(),
                $product->getName(XHS_LANGUAGE)
            );
            $params['vat']         = $product->getVat();
            $params['pages']       = $product->getProductPages();
            $params['productCats'] = $product->getCategories();
        } else {
            $params['product_ID']     = 'new';
            $params['preview_selector'] = $this->viewProvider->picSelector(
                $this->settings['preview_folder'],
                $this->getImageFiles($this->settings['preview_folder']),
                null,
                'xhsPreviewPic'
            );
            $params['image_selector'] = $this->viewProvider->picSelector(
                $this->settings['image_folder'],
                $this->getImageFiles($this->settings['image_folder']),
                null,
                'xhsImage'
            );
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
        $params['use_categories']   = $this->settings['use_categories'];
        $params['categories']       = $this->catalog->getCategories();

        $params['pageLinks']     = $this->bridge->getUrls();
        $params['pageNames']     = $this->bridge->getHeadings();
        $params['pageLevels']    = $this->bridge->getLevels();
        $params['csrf_token_input'] = $this->csrfProtector->tokenInput();

        return $this->render('productEdit', $params);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function deleteProduct()
    {
        $this->csrfProtector->check();
        if (!isset($_POST['xhsProductID'])) {
            return false;
        }
        $this->catalog->deleteProduct($_POST['xhsProductID']);
        return $this->productList();
    }

    private function productCategories()
    {
        $params['categories'] =  parent::categories();
        $params['leftOverCat'] = $this->catalog->getFallbackCategory();
        $params['xhsDefaultCat'] = $this->catalog->getDefaultCategory();
        $params['csrf_token_input'] = $this->csrfProtector->tokenInput();

        return $this->render('categories', $params);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function saveProductCategories()
    {
        $this->csrfProtector->check();
        if (isset($_POST['xhsMoveCat'])) {
            $this->catalog->moveCategory($_POST['xhsMoveDirection'], $_POST['xhsMoveCat']);
        }
        if (isset($_POST['xhsRenameCat'])) {
            $newName = $this->tidyPostString($_POST['xhsCatName']);
            if (strlen($newName) > 0) {
                $this->catalog->renameCategory($newName, $_POST['xhsCatIndex']);
            } else {
                $this->catalog->deleteCategory($_POST['xhsCatIndex']);
            }
        }
        if (isset($_POST['xhsAddCat'])) {
            $newName = $this->tidyPostString($_POST['xhsAddCat']);
            if (strlen($newName) > 0) {
                $this->catalog->addCategory($newName);
            }
        }

        if (isset($_POST['xhsLeftOverCat'])) {
            $leftOver = $this->tidyPostString($_POST['xhsLeftOverCat']);
            if (strlen($leftOver) > 0) {
                $this->catalog->setLeftOverCategory($leftOver);
            }
        }
        if (isset($_POST['xhsDefaultCat'])) {
            $this->catalog->setDefaultCategory($_POST['xhsDefaultCat']);
        }

        return $this->productCategories();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function saveProduct()
    {
        $this->csrfProtector->check();
        $html = '';
        $id = isset($_POST['xhsProductID']) ? $_POST['xhsProductID'] : 'new';
        if (key_exists($id, $this->catalog->getProducts())) {
            $product = $this->catalog->getProduct($id);
        } else {
            $product = new Product();
        }
        if (isset($_POST['xhsName'])) {
            $product->setName($this->tidyPostString($_POST['xhsName']));
        }
        if (isset($_POST['xhsWeight'])) {
            if (is_numeric(ltrim($_POST['xhsWeight']))) {
                $product->setWeight(new Decimal($this->tidyPostString($_POST['xhsWeight'])));
            } else {
                $html .= XH_message(
                    'fail',
                    $this->viewProvider->hints['wrong_format'],
                    sprintf($this->viewProvider->labels['shipping_unit'], $this->settings['shipping_unit'])
                );
                $product->setWeight(Decimal::zero());
            }
        }
        if (isset($_POST['xhsPrice'])) {
            if (is_numeric(ltrim($_POST['xhsPrice']))) {
                $product->setPrice(new Decimal($this->tidyPostString($_POST['xhsPrice'])));
            } else {
                $html .= XH_message(
                    'fail',
                    $this->viewProvider->hints['wrong_format'],
                    $this->viewProvider->labels['price']
                    
                );
                $product->setPrice(Decimal::zero());
            }
        }
        if (isset($_POST['xhsTeaser'])) {
            $product->setTeaser($this->tidyPostString($_POST['xhsTeaser'], false));
        }
        if (isset($_POST['xhsDescription'])) {
            $product->setDescription($this->tidyPostString($_POST['xhsDescription'], false));
        }
        if (isset($_POST['stockOnHand'])) {
            $product->setStockOnHand($_POST['stockOnHand']);
        }
        if (isset($_POST['xhsCategories']) && is_array(($_POST['xhsCategories']))) {
            $temp = array();
            foreach (($_POST['xhsCategories']) as $cat) {
                $temp[] = $this->tidyPostString($cat);
            }
            $product->setCategories($temp);
        } else {
            $product->setCategories(array());
        }

        if (isset($_POST['xhsProductPages']) && is_array(($_POST['xhsProductPages']))) {
            $temp = array();
            foreach (($_POST['xhsProductPages']) as $page) {
                $temp[] = $page;
            }
            $product->setProductPages($temp);
        } else {
            $product->setProductPages(array());
        }

        if (isset($_POST['xhsPreviewPic'])) {
            if ($this->isAllowedImageFile($_POST['xhsPreviewPic'])) {
                $product->setPreviewPic($_POST['xhsPreviewPic']);
            } else {
                $product->setPreviewPic();
            }
        }
        if (isset($_POST['xhsImage'])) {
            if ($this->isAllowedImageFile($_POST['xhsImage'])) {
                $product->setImage($_POST['xhsImage']);
            } else {
                $product->setImage();
            }
        }

        if (isset($_POST['vat'])) {
            $product->setVat($_POST['vat']);
        }
        if (isset($_POST['xhsVariants'])) {
            $variants = array();
            $temp = explode(';', $_POST['xhsVariants']);
            foreach ($temp as $variant) {
                if (strlen($this->tidyPostString($variant)) > 0) {
                    $variants[] = $this->tidyPostString($variant);
                }
            }
            $product->setVariants($variants);
        }

        if ($id === 'new') {
            $this->catalog->addProduct($product);
            $id = $this->catalog->getLastProductId();
        } else {
            $this->catalog->updateProduct($id, $product);
        }

        return $html . $this->editProduct($id);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function helpAbout()
    {
        $params['appName'] = $this->appName;
        $params['version'] = $this->version;
        return $this->render('help_about', $params);
    }

    protected function syscheck()
    {
        $service = new SystemCheckService;
        $params['syschecks'] = $service->getChecks();
        return $this->render('syscheck', $params);
    }
}
