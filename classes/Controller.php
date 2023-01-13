<?php

namespace Xhshop;

use XH\CSRFProtection;

abstract class Controller
{
    protected $viewProvider;
    /** @var Catalogue */
    protected $catalog;
    /** @var array */
    public $settings;
    protected $appName = 'XH-Shop';
    protected $version = '@VERSION@';
    /** @var array */
    protected $payments;
    /** @var array */
    protected $paymentModules;
    /** @var CmsBridge */
    protected $bridge;
    /** @var bool */
    protected $shopIsOn1stPage;

    /**
     * @var CSRFProtection
     */
    protected $csrfProtector;

    public function __construct()
    {
        global $pth, $plugin_cf, $plugin_tx, $u, $_XH_csrfProtection, $xh_publisher;

        $this->settings = array();
        foreach ($plugin_cf['xhshop'] as $key => $value) {
            if (strpos($key, 'shop_') === 0) {
                $this->settings[substr($key, 5)] = $value;
            } elseif (strpos($key, 'contact_') === 0) {
                $this->settings[substr($key, 8)] = $value;
            } elseif (strpos($key, 'taxes_') === 0) {
                $this->settings[substr($key, 6)] =$value;
            } elseif (strpos($key, 'shipping_') === 0) {
                $this->settings[substr($key, 9)] = $value;
            } elseif (strpos($key, 'categories_') === 0) {
                $this->settings[substr($key, 11)] = $value;
            }
        }
        $this->settings['vat_full'] = new Decimal($this->settings['vat_full']);
        $this->settings['vat_reduced'] = new Decimal($this->settings['vat_reduced']);
        $this->settings['image_folder'] = "{$pth['folder']['images']}{$this->settings['image_folder']}";
        $this->settings['preview_folder'] = "{$pth['folder']['images']}{$this->settings['preview_folder']}";
        $this->settings['url'] = ltrim($plugin_tx['xhshop']['config_shop_page'], '?');
        $this->settings['gtc_page'] = $plugin_tx['xhshop']['config_gtc_page'];
        $this->settings['shipping_costs_page'] = $plugin_tx['xhshop']['config_shipping_costs_page'];
        $this->settings['shipping_unit'] = $plugin_tx['xhshop']['config_shipping_unit'];
        $this->settings['shipping_countries'] = $this->getShippingCountries();
        $this->settings['bill_dateformat'] = $plugin_tx['xhshop']['config_bill_dateformat'];
        $this->settings['email_bills'] = $plugin_tx['xhshop']['config_email_bills'];
        $this->settings['email_attachment'] = $plugin_tx['xhshop']['config_email_attachment'];
        $this->paymentModules = array();
        $this->payments = $this->getPaymentModules();
        /**
         * TODO: eliminate need of that CMSimple-separator, leave it to the bridge
         */

         $this->shopIsOn1stPage = !XH_ADM && $this->settings['url'] === $u[$xh_publisher->getFirstPublishedPage()];

        if (!defined('XHS_URL') && isset($this->settings['url'])) {
            define('XHS_URL', $this->isShopOn1stPage() === false ? $this->settings['url'] : '');
        }
        $this->bridge = new CmsBridge();
        $this->catalog = new Catalogue(XHS_URI_SEPARATOR, $this->version, $this->settings['allow_show_all']);
        if (!class_exists('XH_CSRFProtection')) {
            include_once "{$pth['folder']['classes']}CsrfProtection.php";
        }
        $this->csrfProtector = isset($_XH_csrfProtection) ? $_XH_csrfProtection : new CSRFProtection('xhs_csrf_token');

        $viewProvider = preg_replace('/Controller$/', 'View', get_class($this));
        $this->viewProvider = new $viewProvider();
        $this->viewProvider->setCurrency($this->settings['default_currency']);
        $this->viewProvider->setShippingCountries($this->settings['shipping_countries']);

        if (isset($_SESSION['xhsToken']) && $_SESSION['xhsToken'] !== $this->settings['token']) {
            unset($_SESSION['xhsOrder'], $_SESSION['xhsCustomer']);
        }
        $_SESSION['xhsToken'] = $this->settings['token'];
    }

    /**
     * @return bool
     */
    public function hasSystemCheckFailure()
    {
        $systemCheckService = new SystemCheckService();
        foreach ($systemCheckService->getChecks() as $check) {
            if ($check->state === 'fail') {
                return true;
            }
        }
        return false;
    }

    /** @return array */
    protected function getShippingCountries()
    {
        global $plugin_tx;

        $countries = array();
        $pairs = preg_split('/\r\n|\r|\n/', $plugin_tx['xhshop']['config_shipping_countries']);
        foreach ($pairs as $pair) {
            $parts = explode('=', $pair);
            if (count($parts) === 2) {
                list($code, $country) = $parts;
                $countries[trim($code)] = trim($country);
            }
        }
        return $countries;
    }

    /**
     * @param string $template
     * @return string
     */
    protected function render($template, array $params = null)
    {
        if (!($this->viewProvider instanceof View)) {
            return "XHSController:render no view provider!";
        }
        $this->viewProvider->resetParams();
        if (isset($params)) {
            foreach ($params as $key => $value) {
                $this->viewProvider->assignParam($key, $value);
            }
        }
        return $this->viewProvider->loadTemplate($template);
    }

    /** @return array */
    protected function categories()
    {
        return $this->catalog->getCategories();
    }

    /** @return array */
    private function categoryOptions()
    {
        $options = array();

        if ($this->settings['allow_show_all'] || $this instanceof BackEndController) {
            $options[] = array(
                'value' => $this->viewProvider->labels['all_categories'],
                'label' => $this->viewProvider->labels['all_categories']
            );
        }
        foreach ($this->categories() as $category) {
            if (!$this instanceof FrontEndController || $this->catalog->isAnyProductAvailable($category)) {
                $options[] = array('value' => $category, 'label' => $category);
            }
        }
        if ($this->catalog->hasUncategorizedProducts()) {
            if (!$this instanceof FrontEndController || $this->catalog->isAnyProductAvailable('left_overs')) {
                $options[] = array(
                    'value' => 'left_overs',
                    'label' => $this->catalog->getFallbackCategory()
                );
            }
        }
        return $options;
    }

    /**
     * @param ?string $category
     * @param bool $collectAll
     * @return array
     */
    private function products($category = null, $collectAll = false)
    {
        if ($category !== null) {
            $category = $this->tidyPostString($category);
        }

        $productList = $this->catalog->getProducts($category);
        $products = array();

        foreach ($productList as $index => $product) {
            if ($collectAll === false && $product->isAvailable() === false) {
                continue;
            }
            $name = $product->getName(XHS_LANGUAGE);
            $detailLink = '';
            $page = $product->getDetailsLink(XHS_LANGUAGE);
            if ($page) {
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
            $products[$index]['sortIndex'] = $product->getSortIndex();
            $products[$index]['isAvailable'] = $product->isAvailable();
            if ($detailLink!='' || !$product->getImageName()) {
                $products[$index]['previewPicture'] = $this->viewProvider->linkedImage(
                    $product->getPreviewPicturePath(),
                    $this->bridge->translateUrl($product->getDetailsLink(XHS_LANGUAGE)),
                    $product->getName(),
                    ''
                );
            } else {
                $products[$index]['previewPicture'] = $this->viewProvider->linkedImage(
                    $product->getPreviewPicturePath(),
                    $product->getImagePath(),
                    $product->getName(),
                    'zoom'
                );
            }
            $products[$index]['categories'] = $product->getCategories();
            if ($product->hasVariants()) {
                $products[$index]['variants'] = $product->getVariants();
            }
            //  $products[$index]['variants'] = $product->hasVariants() ? $product->getVariants() : false;
            //  var_dump($products[$index]['variants']);
        }
        return $products;
    }

    /** @return array */
    public function getPagesProducts()
    {
        $url = $this->bridge->getCurrentPage();
        $products = array();
        foreach ($this->catalog->getProducts() as $product) {
            if (!$product->isAvailable()) {
                continue;
            }

            foreach ($product->getProductPages() as $page) {
                if ($page == $this->bridge->translateUrl($url) || $page == $url) {
                    $products[] = $product;
                }
            }
        }

        return $products;
    }

    /**
     * @param ?string $request
     * @return string|void
     */
    public function handleRequest($request = null)
    {
        if (!$request) {
            return "No request";
        }
        if (!method_exists($this, $request)) {
            return get_class($this) . ' does not understand: '. $request;
        }
        return $this->$request();
    }

    /**
     * @param bool $collectAll
     * @return array
     */
    protected function productListArray($collectAll = true)
    {
        $category = $this->catalog->getDefaultCategory();
        if (!empty($_GET['xhsCategory'])) {
            $category = $_GET['xhsCategory'];
        }
        if (!empty($_POST['xhsCategory'])) {
            $category = $_POST['xhsCategory'];
        }
        $showCats = true;
        if (!$this->settings['use_categories']) {
            $showCats = false;
            $category = null;
        }

        $params['products'] = $this->products($category, $collectAll);
        $params['selectedCategory'] = $category;
        $params['categoryOptions'] = $this->categoryOptions();
        switch ($category) {
            case 'left_overs':
                $params['categoryHeader'] = $this->catalog->getFallbackCategory();
                break;
            default:
                $params['categoryHeader'] = $category;
                break;
        }
        $params['page_url'] = $this->bridge->getCurrentPage();
        return $params;
    }

    /**
     * @param string $needle
     * @return array|string
     */
    protected function productSearchList($needle = '')
    {
        $showCats = true;
        if (!$this->settings['use_categories']) {
            $showCats = false;
        }
        $products = array();
        // do not collect not available products for visitor
        $collectAll = $this instanceof BackendController;
        $temp = $this->products(null, $collectAll);
        $needles = explode(' ', trim($needle));
        foreach ($temp as $uid => $product) {
            $gotIt = true;
            foreach ($needles as $needle) {
                if (utf8_stripos($product['name'], $needle) === false
                    && utf8_stripos($product['teaser'], $needle) === false
                    && utf8_stripos($product['description'], $needle) === false
                    && utf8_stripos(implode(' ', $product['categories']), $needle) === false
                ) {
                    $gotIt = false;
                    break;
                }
            }
            if ($gotIt === false) {
                continue;
            }
            $products[$uid] = $product;
        }
        $params['products'] = $products;
        $params['selectedCategory'] = null;
        $params['categoryOptions'] = $this->categoryOptions();
        $params['categoryHeader'] = '';
        $params['page_url'] = $this->bridge->getCurrentPage();

        return $params;
    }

    /**
     * @param string $string
     * @param bool $writeEntities
     * @return string
     */
    protected function tidyPostString($string, $writeEntities = true)
    {
        $string = str_replace(array('./', '<?php', '<?', '?>'), '', $string);
        if ($writeEntities === true) {
            $string = htmlspecialchars($string);
        }
        return rtrim($string);
    }

    /**
     * @return true
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function addPaymentModule(PaymentModule $module)
    {
        $this->paymentModules[$module->getName()] = $module;
        $module->setShopCurrency(html_entity_decode($this->settings['default_currency']));
        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function loadPaymentModule($name)
    {
        $classname = '\\Xhshop\\Payment\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        if (class_exists($classname)) {
            $this->addPaymentModule(new $classname());
            return true;
        }
        return false;
    }

    /**
     * @param ?string $directory
     * @return array
     */
    protected function getImageFiles($directory = null)
    {
        if ($directory === null) {
            $directory = $this->settings['image_folder'];
        }
        $handle = opendir($directory);
        $files = array();
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if ($this->isAllowedImageFile($file)) {
                    $files[] = $file;
                }
            }
            closedir($handle);
        }
        natcasesort($files);
        return $files;
    }

    /**
     * @param string $file
     * @return bool
     */
    protected function isAllowedImageFile($file = '')
    {
        $extensions = preg_split('/\s*,\s*/', $this->settings['image_extensions']);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if ($extension == $file) {
            return false;
        }
        if (in_array(strtolower($extension), $extensions)) {
            return true;
        }
        return false;
    }

    /** @return array */
    private function getPaymentModules()
    {
        global $plugin_cf;

        $modules = preg_filter('/^([\w-]+)_is_active$/', '$1', array_keys($plugin_cf['xhshop']));
        return array_values(str_replace('-', '_', array_filter($modules, function ($module) use ($plugin_cf) {
            return $plugin_cf['xhshop']["{$module}_is_active"];
        })));
    }

    /**
     * @param int $level
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function shopToc($level = 6)
    {
        return '';
    }

    /** @return bool */
    public function isShopOn1stPage()
    {
        return $this->shopIsOn1stPage;
    }
}
