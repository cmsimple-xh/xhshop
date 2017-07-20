<?php

namespace Xhshop;

class Catalogue
{
    private $products;
    private $separator;
    private $categories;
    private $category_for_the_left_overs;
    private $default_category;
    private $version;

    public function __construct($separator, $version)
    {
        $this->version = $version;
        $this->separator = $separator;
        $this->products = array();
        $this->categories = array();

        $this->loadArray();
    }

    public function getVersion()
    {
        return $this->version;
    }

    private function loadArray()
    {
        $categories = $category_for_the_left_overs = $default_category = array();
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
        foreach (array_keys($sortOrder) as $key) {
            $products[$key]->sortIndex = $i;
            $i++;
        }

        $this->products = isset($products) ? $products : array();

        $writer = new CatalogWriter($this);
        $writer->write();
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

    public function getDefaultCategory()
    {
        return $this->default_category[XHS_LANGUAGE];
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

    public function getLastProductId()
    {
        return end($this->catalog->products)->uid;
    }

    public function getCategories($language = XHS_LANGUAGE)
    {
        if (!isset($this->categories[$language])) {
            $this->categories[$language] = array();
            $this->save();
        }
        return $this->categories[$language];
    }

    public function getAllCategories()
    {
        return $this->categories;
    }

    public function getAllDefaultCategories()
    {
        return $this->default_category;
    }

    public function getAllLeftOverCategories()
    {
        return $this->category_for_the_left_overs;
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
