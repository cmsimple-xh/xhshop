<?php

namespace Xhshop;

class Catalogue
{
    public $products;
    private $cf; // apparently unused
    private $separator;
    public $categories;
    public $category_for_the_left_overs;
    public $default_category;
    private $version;
    private $cms; // apparently unused

    public function __construct($separator, $version)
    {
        $this->version = $version;
        $this->cms = 'CMSimple_XH';
        $this->separator = $separator;
        $this->products = array();
        $this->categories = array();

        $this->loadArray();
    }

    private function loadArray()
    {
        include XHS_CATALOG;

        $this->categories = $categories;
        $this->category_for_the_left_overs = $category_for_the_left_overs;
        $this->default_category = $default_category;

        if (!isset($products) || !is_array($products)) {
            $products = array();
        }
        $i = count($products);
        foreach ($products as $temp) {
            $product = new Product();
            $product->names = $temp['names'];
            $product->price = $temp['price'];
            $product->vat = $temp['vat'];
            $product->variants = isset($temp['variants']) ? $temp['variants'] : array(XHS_LANGUAGE => '');
            $product->previewPicture = isset($temp['previewPicture']) ? $temp['previewPicture'] : '';
            $product->image = isset($temp['image']) ? $temp['image'] : '';
            $product->weight = $temp['weight'];
            $product->setStockOnHand(isset($temp['stock_on_hand']) ? $temp['stock_on_hand'] : 1);
            $product->teasers = isset($temp['teasers']) ? $temp['teasers'] : array(XHS_LANGUAGE => '');
            $product->descriptions = isset($temp['descriptions']) ? $temp['descriptions'] :array(XHS_LANGUAGE => '');
            $product->categories = isset($temp['categories']) ? $temp['categories'] : array(XHS_LANGUAGE => '');
            $product->productPages = isset($temp['productPages'])
                ? $temp['productPages']
                : array(XHS_LANGUAGE => array());

            if ($temp['separator'] <> $this->separator) {
                $new_links = array();
                foreach ($temp['productPages'][XHS_LANGUAGE] as $page) {
                    $new_links[] = str_replace($temp['separator'], $this->separator, $page);
                }
                $product->productPages[XHS_LANGUAGE] = $new_links;
            }


            $product->sortIndex = isset($temp['sortIndex']) ? $temp['sortIndex'] : $i;
            $i--;
            $product->uid = isset($temp['uid']) ? $temp['uid'] :uniqid('p');
            $product->separator = $this->separator;
            $this->products[$product->uid] = $product;
        }
    }

    private function saveCatalogArray()
    {
        $string = "<?php \n";
        //      $string .= '$separator = \'' . $this->separator . "';\n\n";
        $string .= '################### Catalog ###############################' . ";\n";
        $string .= '$version = \'' . $this->version . "';\n\n";
        $string .= '################### Categories ###############################' . ";\n";
        foreach ($this->categories as $lang => $categories) {
            if (!is_array($categories)) {
                $string .= '$categories[\'' . $lang . '\'] = array();' . ";\n";
            } else {
                foreach ($categories as $key => $category) {
                    $string .= '$categories[\'' . $lang . '\'][' . $key . '] = \''
                        . $this->cleanString($category) . "';\n";
                }
            }
        }
        foreach ($this->default_category as $lang => $cat) {
            $string .= '$default_category[\'' . $lang . '\'] = \'' . $this->cleanString($cat) . "';\n";
        }
        foreach ($this->category_for_the_left_overs as $lang => $cat) {
            $string .= '$category_for_the_left_overs[\'' . $lang . '\'] = \'' . $this->cleanString($cat) . "';\n";
        }
        $string .= "\n\n" . '################### Products ######################' . ";\n";
        foreach ($this->products as $uid => $product) {
            foreach ($product->names as $lang => $name) {
                $string .= '$products[\'' . $uid . '\'][\'names\'][\'' . $lang . '\'] = \''
                    . $this->cleanString($name) . "';\n";
            }
            $string .= '$products[\'' . $uid . '\'][\'price\'] = ' . number_format($product->price, 2, '.', '') . ";\n";
            $string .= '$products[\'' . $uid . '\'][\'vat\'] = \'' . $this->cleanString($product->vat) . "';\n";
            $string .= '$products[\'' . $uid . '\'][\'sortIndex\'] = ' . (int)$product->sortIndex . ";\n";
            $string .= '$products[\'' . $uid . '\'][\'previewPicture\'] = \'' . $product->previewPicture . "';\n";
            $string .= '$products[\'' . $uid . '\'][\'image\'] = \'' . $product->image . "';\n";
            $string .= '$products[\'' . $uid . '\'][\'weight\'] = ' . number_format($product->weight, 2, '.', '') . ";\n";
            $string .= '$products[\'' . $uid . '\'][\'stock_on_hand\'] = ' . (int)$product->stock_on_hand . ";\n";
            if (!isset($product->teasers)) {
                $product->teasers = array(XHS_LANGUAGE => '');
            }
            foreach ($product->teasers as $lang => $teaser) {
                $string .= '$products[\'' . $uid . '\'][\'teasers\'][\'' . $lang . '\'] = \'' . $this->cleanString($teaser) . "';\n";
            }
            foreach ($product->descriptions as $lang => $description) {
                $string .= '$products[\'' . $uid . '\'][\'descriptions\'][\'' . $lang . '\'] = \''
                    . $this->cleanString($description) . "';\n";
            }
            
            if (!isset($product->variants) || !is_array($product->variants)) {
                $string .= '$products[\'' . $uid . '\'][\'variants\'] = array('.XHS_LANGUAGE .' => \'\')' . ";\n";
            } else {
                foreach ($product->variants as $lang => $variants) {
                    $string .= '$products[\'' . $uid . '\'][\'variants\'][\'' . $lang . '\'] = array(';
                    if (is_array($variants)) {
                        foreach ($variants as $variant) {
                            $string .= "'" . trim($this->cleanString($variant)) . "', ";
                        }
                    }
                    $string .= ');' . "\n";
                }
            }

            if (!isset($product->categories) || !is_array($product->categories)) {
                $string .= '$products[\'' . $uid . '\'][\'categories\'] = array()' . ";\n";
            } else {
                foreach ($product->categories as $lang => $categories) {
                    $string .= '$products[\'' . $uid . '\'][\'categories\'][\'' . $lang . '\'] = array(';
                    if (is_array($categories)) {
                        foreach ($categories as $cat) {
                            $string .= "'" . $this->cleanString($cat) . "', ";
                        }
                    }
                    $string .= ');' . "\n";
                }
            }

            if (!isset($product->productPages)) {
                $string .= '$products[\'' . $uid . '\'][\'productPages\'] = array()' . ";\n";
            } else {
                foreach ($product->productPages as $lang => $pages) {
                    $string .= '$products[\'' . $uid . '\'][\'productPages\'][\'' . $lang . '\'] = array(';
                    if (is_array($pages)) {
                        foreach ($pages as $page) {
                            $string .= "'" . $this->cleanString($page) . "', ";
                        }
                    }
                    $string .= ');' . "\n";
                }
            }
            $string .= '$products[\'' . $uid . '\'][\'separator\'] = \''
                . $this->cleanString($product->separator) . "';\n";
            $string .= '$products[\'' . $uid . '\'][\'uid\'] = \'' . $this->cleanString($uid) . "';\n";
            $string .= "\n#-----------------------------------------------------\n\n";
        }

        $string .= '?>';
        if (!file_exists(XHS_CATALOG)) {
            $handle = fopen(XHS_CATALOG, 'w');

            if ($handle) {
                fwrite($handle, $string);
                chmod(XHS_CATALOG, 0666);
                fclose($handle);
            } else {
                trigger_error('Catalogue::saveCatalogArray() - failed to create ' . XHS_CATALOG);
            }
        }
        $handle = fopen(XHS_CATALOG, 'w');
        if (!is_writeable(XHS_CATALOG)) {
            if (!chmod(XHS_CATALOG, 0666)) {
                trigger_error('Catalogue::saveCatalogArray() - can\'t write to ' . XHS_CATALOG);
                fclose($handle);
                return false;
            }
        }
        fwrite($handle, $string);
        fclose($handle);
        return true;
    }

    public function save()
    {
        $sortOrder = array();
        $products = array();

        foreach ($this->products as $product) {
            if ($product instanceof Product) {
                $product->separator = $this->separator;
                if (!isset($product->uid)) {
                    $product->uid = uniqid('p');
                }
                $products[$product->uid] = $product;
                $sortOrder[$product->uid] = $product->sortIndex;
            }
        }

        asort($sortOrder);


        $i = 1;
        foreach ($sortOrder as $key => $sort) {
            //     $products[$key]->sortIndex = $sort;
            $products[$key]->sortIndex = $i;
            $i++;
        }

        $this->products = isset($products) ? $products : array();

        $this->saveCatalogArray();
        $this->loadArray();
        return;
    }

    public function renameCategory($name = null, $index = null)
    {
        if (!isset($index) || !isset($name)) {
            return;
        }
        $products = $this->getProducts($this->categories[XHS_LANGUAGE][$index]);
        foreach ($products as $product) {
            foreach ($product->categories[XHS_LANGUAGE] as $key => $value) {
                if ($value == $this->categories[XHS_LANGUAGE][$index]) {
                    $product->categories[XHS_LANGUAGE][$key] = $name;
                }
            }
        }
        if ($this->default_category[XHS_LANGUAGE] == $this->categories[XHS_LANGUAGE][$index]) {
            $this->default_category[XHS_LANGUAGE] = $name;
        }
        $this->categories[XHS_LANGUAGE][$index] = $name;
        $this->save();
    }

    public function deleteCategory($index = null)
    {
        if (!isset($index)) {
            return;
        }
        if (key_exists($index, $this->categories[XHS_LANGUAGE])) {
            unset($this->categories[XHS_LANGUAGE][$index]);
        }
        $this->categories[XHS_LANGUAGE] = array_values($this->categories[XHS_LANGUAGE]);
        $this->save();
    }

    public function setLeftOverCategory($name)
    {
        $this->category_for_the_left_overs[XHS_LANGUAGE] = $name;
        $this->save();
    }

    public function setDefaultCategory($name)
    {
        $this->default_category[XHS_LANGUAGE] = $name;
        $this->save();
    }

    public function hasUncategorizedProducts()
    {
        foreach ($this->products as $product) {
            if (!isset($product->categories[XHS_LANGUAGE]) || count($product->categories[XHS_LANGUAGE]) == 0) {
                return true;
            }
        }
        return false;
    }

    private function getUncategorizedProducts()
    {
        $products = array();
        foreach ($this->products as $index => $product) {
            if (!isset($product->categories[XHS_LANGUAGE]) || !$product->categories[XHS_LANGUAGE]) {
                $products[$index] = $product;
            }
        }
        return $products;
    }

    public function getFallbackCategory()
    {
        return isset($this->category_for_the_left_overs[XHS_LANGUAGE]) ? $this->category_for_the_left_overs[XHS_LANGUAGE] : 'N.N.';
    }

    public function getProducts($category = null)
    {
        if (isset($category)) {
            if (in_array($category, $this->categories[XHS_LANGUAGE])) {
                $products = array();
                foreach ($this->products as $index => $product) {
                    if (isset($product->categories[XHS_LANGUAGE])
                        && is_array($product->categories[XHS_LANGUAGE])
                        && in_array($category, $product->categories[XHS_LANGUAGE])) {
                        $products[$index] = $product;
                    }
                }

                return $products;
            }
            if ($category == 'left_overs') {
                return $this->getUncategorizedProducts();
            }
        }
        return $this->products;
    }
    public function swapSortIndex(Product $productA, Product $productB)
    {
        $swap = $productA->sortIndex;

        $productA->sortIndex = $productB->sortIndex;
        $productB->sortIndex = $swap;
        $this->save();
    }

    public function getProduct($id)
    {
        if (!key_exists($id, $this->products)) {
            trigger_error('Catalogue::getProduct($id): No product with this id.');
            return false;
        } else {
            return $this->products[$id];
        }
    }

    public function getCategories($language = null)
    {
        if ($language === null) {
            $language = XHS_LANGUAGE;
        }
        return $this->categories[$language];
    }

    public function moveCategory($direction = null, $index = null)
    {
        if (!isset($index)) {
            return;
        }
        $swap = null;
        if ($direction == 'up') {
            $swap = $this->categories[XHS_LANGUAGE][$index - 1];
            $this->categories[XHS_LANGUAGE][$index - 1] = $this->categories[XHS_LANGUAGE][$index];
        }
        if ($direction == 'down') {
            $swap = $this->categories[XHS_LANGUAGE][$index + 1 ];
            $this->categories[XHS_LANGUAGE][$index + 1] = $this->categories[XHS_LANGUAGE][$index];
        }
        if (!isset($swap)) {
            return;
        }
        $this->categories[XHS_LANGUAGE][$index] = $swap;
        $this->save();
    }

    public function addCategory($name = null)
    {
        if (!isset($name)) {
            return;
        }
        $this->categories[XHS_LANGUAGE][] = $name;
        $this->save();
    }

    private function cleanString($string, $writeEntities = false)
    {
        $string = str_replace(array('./', '<?php', '<?', '?>'), '', $string);
        if ($writeEntities === true) {
            $string = htmlspecialchars($string);
        }

        return addcslashes($string, '\'\\');
    }

    public function addProduct(Product $product)
    {
        $product->sortIndex = 0;
        $this->products[] = $product;
        $this->save();
    }

    /**
     *
     * @param <type> $uid
     * @return <type>
     */
    public function updateProduct($uid = null)
    {
        if (!key_exists($uid, $this->products)) {
            trigger_error('Catalogue::updateProduct($uid) - no Product with this $uid');
            return;
        }
        $this->save();
    }

    public function deleteProduct($id = null)
    {
        if (!key_exists($id, $this->products)) {
            trigger_error('Catalogue::deleteProduct($id): No product with this id.');
            return false;
        }
        unset($this->products[$id]);
        $this->save();
    }
}
