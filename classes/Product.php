<?php

namespace Xhshop;

class Product
{
//    var $counter;
    public $names;
    public $descriptions;
    public $teasers;
    public $price;
    public $productPages;
    public $previewPicture;
    public $vat;
    public $separator;
    public $categories;
    public $stock_on_hand;
    public $weight;
    public $variants;
    public $uid;
    public $sortIndex;
    public $image;
    private $imageFolder;
    private $previewFolder;

    public function __construct()
    {
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

    public function getWeight()
    {
        if (!isset($this->weight)) {
            $this->weight = 0;
        }
        return (float)$this->weight;
    }

    public function getNet($vatRate)
    {
        $net = $this->price/(100 + $vatRate)*100;
        return (float)$net;
    }

    public function getGross()
    {
        return (float)$this->price;
    }

    public function getName($language = null, $variant = null)
    {
        if ($language === null) {
            $language = XHS_LANGUAGE;
        }
        $variantName = '';
        //     var_dump($this);
        if (isset($this->variants[$language][$variant])) {
            $variantName = ' ('.$this->variants[$language][$variant]. ')';
        }
        if (!isset($this->names[$language])) {
            $langs = array_keys($this->names);
            $language = $langs[0];
        }

        return $this->names[$language]. $variantName;
    }

    public function getVariantName($variant = 0)
    {
        if (array_key_exists($variant, $this->variants[XHS_LANGUAGE])) {
            return $this->variants[XHS_LANGUAGE][$variant];
        }
    }

    public function getDescription($language = null)
    {
        $language = ($language === null) ? XHS_LANGUAGE : $language;
        return isset($this->descriptions[$language]) ? $this->descriptions[$language] : '' ;
    }

    public function getTeaser($language = null)
    {
        $language = ($language === null) ? XHS_LANGUAGE : $language;
       
        return isset($this->teasers[$language]) ? $this->teasers[$language] : '' ;
    }

    // appears to be unused
    private function getPageLink($language, $label = null)
    {
        if (!$label) {
            $label = $this->getName($language);
        }
        if ($this->getPage($language)) {
            $label = "<a href=\"" . $this->productPages[$language][0] . "\">$label</a>";
        }
        return $label;
    }

    public function getDetailsLink($language = null)
    {
        $lang = ($language === null) ? XHS_LANGUAGE : $language;
        if (isset($this->descriptions[$lang]) && trim($this->descriptions[$lang]) != '') {
            return XHS_URL . '&xhsProduct=' . $this->uid;
        }
        return $this->getPage($language);
    }

    public function getPage($language = null)
    {
        if ($language == null) {
            $language = XHS_LANGUAGE;
        }

        if (isset($this->productPages[$language][0]) && trim($this->productPages[$language][0]) <> '') {
            return $this->productPages[$language][0];
        }
        return false;
    }


    public function getPreviewPictureName()
    {
        if ((isset($this->previewPicture))) {
            return $this->previewPicture;
        }
        return '';
    }

    public function getPreviewPicturePath()
    {
        if (isset($this->previewPicture)) {
            return $this->previewFolder . $this->previewPicture;
        }
        return '';
    }

    public function getImageName()
    {
        if (isset($this->image)) {
            return $this->image;
        }
        return '';
    }

    public function getImagePath()
    {
        if (isset($this->image)) {
            return $this->imageFolder . $this->image;
        }
        return '';
    }

    public function getVariants($language = null)
    {
        if ($language === null) {
            $language = XHS_LANGUAGE;
        }
        return isset($this->variants[XHS_LANGUAGE]) ? $this->variants[XHS_LANGUAGE] : array();
    }

    public function getCategories($language = null)
    {
        if ($language === null) {
            $language = XHS_LANGUAGE;
        }
        return isset($this->categories[$language]) ? $this->categories[$language] : array();
    }

    public function getProductPages($language = null)
    {
        if ($language === null) {
            $language = XHS_LANGUAGE;
        }
        if (isset($this->productPages[$language])) {
            return $this->productPages[$language];
        }
        return array();
    }

    public function hasVariants()
    {
        // return true;
        return (isset($this->variants[XHS_LANGUAGE]) && is_array($this->variants[XHS_LANGUAGE]))
            ? count($this->variants[XHS_LANGUAGE]) > 0
            : null;
    }

    public function setName($name = 'No Name!', $language = null)
    {
        if ($language === null) {
            $language = XHS_LANGUAGE;
        }
        $this->names[$language] = $name;
        return;
    }

    public function setDescription($description = '', $language = null)
    {
        if ($language === null) {
            $language = XHS_LANGUAGE;
        }
        $this->descriptions[$language] = $description;
        return;
    }

    public function setTeaser($description = '', $language = null)
    {
        if ($language === null) {
            $language = XHS_LANGUAGE;
        }
        $this->teasers[$language] = $description;
        return;
    }

    public function setPrice($price = 0.00)
    {
        $price = str_replace(',', '.', $price);
        $this->price = (float)$price;
        return;
    }

    public function setWeight($weight = 0.00)
    {
        $weight = str_replace(',', '.', $weight);
        $this->weight = (float)$weight;
        return;
    }

    public function setStockOnHand($quantity = 1)
    {
        $this->stock_on_hand = (int)$quantity;
        return;
    }

    public function setVat($rate = 'full')
    {
        $this->vat = (string)$rate;
        return;
    }

    public function setVariants($variants = array(), $language = null)
    {
        if ($language === null) {
            $language = XHS_LANGUAGE;
        }
        if (is_array($variants)) {
            if (count(($variants)) == 1) {
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

    public function setProductPages(array $pages = array(), $language = null)
    {
        if ($language === null) {
            $language = XHS_LANGUAGE;
        }
        $this->productPages[$language] = $pages;
    }

    public function setCategories(array $categories = array(), $language = null)
    {
        if ($language === null) {
            $language = XHS_LANGUAGE;
        }
         
        $this->categories[$language] = $categories;
    }

    public function setPreviewPic($pic = '')
    {
        $this->previewPicture = (string)$pic;
    }

    public function setImage($pic = '')
    {
        $this->image = (string)$pic;
    }

    public function isAvailable()
    {
        return isset($this->stock_on_hand) ? $this->stock_on_hand > 0 : true;
    }
}
