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

    private function loadArray()
    {
        $separator = '/';
        $categories = $category_for_the_left_overs = $default_category = array();
        include XHS_CATALOG;

        $this->categories = $categories;
        $this->category_for_the_left_overs = $category_for_the_left_overs;
        $this->default_category = $default_category;

        if (!isset($products) || !is_array($products)) {
            $products = array();
        }
        $i = count($products);
        foreach ($products as $record) {
            $product = Product::createFromRecord($record, $i, $this->separator, $separator);
            $this->products[$product->getUid()] = $product;
            $i--;
        }
    }

    public function save()
    {
        $sortOrder = array();
        $products = array();

        foreach ($this->products as $product) {
            if ($product instanceof Product) {
                $product->setSeparator($this->separator);
                $products[$product->getUid()] = $product;
                $sortOrder[$product->getUid()] = $product->getSortIndex();
            }
        }

        asort($sortOrder);


        $i = 1;
        foreach (array_keys($sortOrder) as $key) {
            $products[$key]->setSortIndex($i);
            $i++;
        }

        $this->products = isset($products) ? $products : array();

        $writer = new CatalogWriter((object) get_object_vars($this));
        $writer->write();
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(XHS_CATALOG);
        }
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
            foreach ($product->getCategories() as $key => $value) {
                if ($value == $this->categories[XHS_LANGUAGE][$index]) {
                    $product->addCategory($key, $name);
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
            if (count($product->getCategories()) == 0) {
                return true;
            }
        }
        return false;
    }

    private function getUncategorizedProducts()
    {
        $products = array();
        foreach ($this->products as $index => $product) {
            if (!count($product->getCategories())) {
                $products[$index] = $product;
            }
        }
        return $products;
    }

    public function getFallbackCategory()
    {
        return isset($this->category_for_the_left_overs[XHS_LANGUAGE]) ? $this->category_for_the_left_overs[XHS_LANGUAGE] : 'N.N.';
    }

    public function isAnyProductAvailable($category = null)
    {
        foreach ($this->getProducts($category) as $product) {
            if ($product->isAvailable()) {
                return true;
            }
        }
        return false;
    }

    public function getProducts($category = null)
    {
        if (isset($category)) {
            if (in_array($category, $this->categories[XHS_LANGUAGE])) {
                $products = array();
                foreach ($this->products as $index => $product) {
                    if (in_array($category, $product->getCategories())) {
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
        $swap = $productA->getSortIndex();

        $productA->setSortIndex($productB->getSortIndex());
        $productB->setSortIndex($swap);
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
        return end($this->products)->getUid();
    }

    public function getCategories($language = XHS_LANGUAGE)
    {
        if (!isset($this->categories[$language])) {
            $this->categories[$language] = array();
            $this->save();
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

    public function addProduct(Product $product)
    {
        $product->setSortIndex(0);
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
