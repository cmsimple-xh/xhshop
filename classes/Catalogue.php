<?php

namespace Xhshop;

class Catalogue
{
    /** @var array */
    private $products;
    /** @var string */
    private $separator;
    /** @var array */
    private $categories;
    /** @var array */
    private $category_for_the_left_overs;
    /** @var array */
    private $default_category;
    /** @var string */
    private $version;
    /** @var bool */
    private $allowShowAll;

    /**
     * @param string $separator
     * @param string $version
     * @param bool $allowShowAll
     */
    public function __construct($separator, $version, $allowShowAll)
    {
        $this->version = $version;
        $this->separator = $separator;
        $this->allowShowAll = (bool) $allowShowAll;
        $this->products = array();
        $this->categories = array();

        $this->loadArray();
    }

    /** @return void */
    private function loadArray()
    {
        $separator = '/';
        $categories = $category_for_the_left_overs = $default_category = array();
        include XHS_CATALOG;

        $this->categories = $categories;
        $this->category_for_the_left_overs = $category_for_the_left_overs;
        if (!$this->allowShowAll) {
            foreach ($default_category as $lang => $cat) {
                if (!in_array($cat, $categories[$lang], true)) {
                    $default_category[$lang] = $categories[$lang][0];
                }
            }
        }
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

    /** @return void */
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

        uasort($products, function (Product $productA, Product $productB) {
            return $productA->getSortIndex() - $productB->getSortIndex();
        });

        $this->products = isset($products) ? $products : array();

        $writer = new CatalogWriter((object) get_object_vars($this));
        $writer->write();
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(XHS_CATALOG);
        }
        $this->loadArray();
        return;
    }

    /**
     * @param ?string $name
     * @param ?string $index
     * @return void
     */
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

    /**
     * @param ?string $index
     * @return void
     */
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

    /**
     * @param string $name
     * @return void
     */
    public function setLeftOverCategory($name)
    {
        $this->category_for_the_left_overs[XHS_LANGUAGE] = $name;
        $this->save();
    }

    /** @return string */
    public function getDefaultCategory()
    {
        return $this->default_category[XHS_LANGUAGE];
    }

    /**
     * @param string $name
     * @return void
     */
    public function setDefaultCategory($name)
    {
        $this->default_category[XHS_LANGUAGE] = $name;
        $this->save();
    }

    /** @return bool */
    public function hasUncategorizedProducts()
    {
        foreach ($this->products as $product) {
            if (count($product->getCategories()) == 0) {
                return true;
            }
        }
        return false;
    }

    /** @return array */
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

    /** @return string */
    public function getFallbackCategory()
    {
        return isset($this->category_for_the_left_overs[XHS_LANGUAGE]) ?
            $this->category_for_the_left_overs[XHS_LANGUAGE] :
            'N.N.';
    }

    /**
     * @param ?string $category
     * @return bool
     */
    public function isAnyProductAvailable($category = null)
    {
        foreach ($this->getProducts($category) as $product) {
            if ($product->isAvailable()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ?string $category
     * @return array
     */
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

    /** @return void */
    public function swapSortIndex(Product $productA, Product $productB)
    {
        $swap = $productA->getSortIndex();

        $productA->setSortIndex($productB->getSortIndex());
        $productB->setSortIndex($swap);
        $this->save();
    }

    /**
     * @param string $id
     * @return Product|false
     */
    public function getProduct($id)
    {
        if (!key_exists($id, $this->products)) {
            trigger_error('Catalogue::getProduct($id): No product with this id.');
            return false;
        } else {
            return $this->products[$id];
        }
    }

    /** @return string */
    public function getLastProductId()
    {
        return end($this->products)->getUid();
    }

    /**
     * @param string $language
     * @return array|void
     */
    public function getCategories($language = XHS_LANGUAGE)
    {
        if (!isset($this->categories[$language])) {
            $this->categories[$language] = array();
            $this->save();
        }
        return $this->categories[$language];
    }

    /**
     * @param string $direction
     * @param ?string $index
     * @return void
     */
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

    /**
     * @param string $name
     * @return void
     */
    public function addCategory($name = null)
    {
        if (!isset($name)) {
            return;
        }
        $this->categories[XHS_LANGUAGE][] = $name;
        $this->save();
    }

    /** @return void */
    public function addProduct(Product $product)
    {
        $product->setSortIndex(PHP_INT_MAX);
        $this->products[] = $product;
        $this->save();
    }

    /**
     * @param ?string $uid
     * @return void
     */
    public function updateProduct($uid = null)
    {
        if (!key_exists($uid, $this->products)) {
            trigger_error('Catalogue::updateProduct($uid) - no Product with this $uid');
            return;
        }
        $this->save();
    }

    /**
     * @param ?string $id
     * @return void|false
     */
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
