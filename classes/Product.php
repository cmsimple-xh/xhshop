<?php

namespace Xhshop;

class Product
{
    private $names;
    private $descriptions;
    private $teasers;

    /**
     * @var Decimal
     */
    private $price;

    private $productPages;
    private $previewPicture;
    private $vat;
    private $separator;
    private $categories;
    private $stock_on_hand;

    /**
     * @var Decimal
     */
    private $weight;

    private $variants;
    private $uid;
    private $sortIndex;
    private $image;
    private $imageFolder;
    private $previewFolder;

    public static function createFromRecord(array $record, $index, $nominalsep, $actualsep)
    {
        $result = new self;
        $result->names = $record['names'];
        $price = $record['price'];
        // old catalog.php may store the price as float
        if (is_float($price)) {
            $price = number_format($price, 2, '.', '');
        }
        $result->setPrice(new Decimal($price));
        $result->vat = $record['vat'];
        $result->variants = isset($record['variants']) ? $record['variants'] : array(XHS_LANGUAGE => '');
        $result->previewPicture = isset($record['previewPicture']) ? $record['previewPicture'] : '';
        $result->image = isset($record['image']) ? $record['image'] : '';
        $weight = $record['weight'];
        // old catalog.php may store the weight as float
        if (is_float($weight)) {
            $weight = number_format($weight, 2, '.', '');
        }
        $result->weight = new Decimal($weight);
        $result->setStockOnHand(isset($record['stock_on_hand']) ? $record['stock_on_hand'] : 1);
        $result->teasers = isset($record['teasers']) ? $record['teasers'] : array(XHS_LANGUAGE => '');
        $result->descriptions = isset($record['descriptions']) ? $record['descriptions'] :array(XHS_LANGUAGE => '');
        $result->categories = isset($record['categories']) ? $record['categories'] : array(XHS_LANGUAGE => '');
        $result->productPages = isset($record['productPages'])
            ? $record['productPages']
            : array(XHS_LANGUAGE => array());

        $actualsep = isset($record['separator']) ? $record['separator'] : $actualsep;
        if ($actualsep !== $nominalsep) {
            $new_links = array();
            foreach ($record['productPages'][XHS_LANGUAGE] as $page) {
                $new_links[] = str_replace($actualsep, $nominalsep, $page);
            }
            $result->productPages[XHS_LANGUAGE] = $new_links;
        }

        $result->sortIndex = isset($record['sortIndex']) ? $record['sortIndex'] : $index;
        $result->uid = isset($record['uid']) ? $record['uid'] : uniqid('p');
        $result->separator = $nominalsep;
        return $result;
    }

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

    /**
     * For serialization only!
     */
    public function getInternalState()
    {
        return (object) get_object_vars($this);
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function getSortIndex()
    {
        return $this->sortIndex;
    }

    /**
     * @return Decimal
     */
    public function getWeight()
    {
        if (!isset($this->weight)) {
            $this->weight = Decimal::zero();
        }
        return $this->weight;
    }

    /**
     * @return Decimal
     */
    public function getGross()
    {
        return $this->price;
    }

    public function getName($language = XHS_LANGUAGE, $variant = null)
    {
        $variantName = '';
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

    public function getDescription($language = XHS_LANGUAGE)
    {
        return isset($this->descriptions[$language]) ? $this->descriptions[$language] : '' ;
    }

    public function getTeaser($language = XHS_LANGUAGE)
    {
        return isset($this->teasers[$language]) ? $this->teasers[$language] : '' ;
    }

    public function getDetailsLink($language = XHS_LANGUAGE)
    {
        if (isset($this->descriptions[$language]) && trim($this->descriptions[$language]) != '') {
            return XHS_URL . '&xhsProduct=' . $this->uid;
        }
        return $this->getPage($language);
    }

    public function getPage($language = XHS_LANGUAGE)
    {
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
        if (!empty($this->previewPicture)) {
            return $this->previewFolder . $this->previewPicture;
        }
        return '';
    }

    public function getVat()
    {
        return $this->vat;
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
        if (!empty($this->image)) {
            return $this->imageFolder . $this->image;
        }
        return '';
    }

    public function getVariants($language = XHS_LANGUAGE)
    {
        return isset($this->variants[$language]) ? $this->variants[$language] : array();
    }

    public function getCategories($language = XHS_LANGUAGE)
    {
        return isset($this->categories[$language]) ? $this->categories[$language] : array();
    }

    public function getProductPages($language = XHS_LANGUAGE)
    {
        if (isset($this->productPages[$language])) {
            return $this->productPages[$language];
        }
        return array();
    }

    public function hasVariants()
    {
        return (isset($this->variants[XHS_LANGUAGE]) && is_array($this->variants[XHS_LANGUAGE]))
            ? count($this->variants[XHS_LANGUAGE]) > 0
            : null;
    }

    public function setSortIndex($value)
    {
        $this->sortIndex = $value;
    }

    public function setSeparator($value)
    {
        $this->separator = $value;
    }

    public function setName($name = 'No Name!', $language = XHS_LANGUAGE)
    {
        $this->names[$language] = $name;
    }

    public function setDescription($description = '', $language = XHS_LANGUAGE)
    {
        $this->descriptions[$language] = $description;
    }

    public function setTeaser($description = '', $language = XHS_LANGUAGE)
    {
        $this->teasers[$language] = $description;
    }

    public function setPrice(Decimal $price)
    {
        $this->price = $price;
    }

    public function setWeight(Decimal $weight)
    {
        $this->weight = $weight;
    }

    public function setStockOnHand($quantity = 1)
    {
        $this->stock_on_hand = (int)$quantity;
    }

    public function setVat($rate = 'full')
    {
        $this->vat = (string)$rate;
    }

    public function setVariants($variants = array(), $language = XHS_LANGUAGE)
    {
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
    }

    public function setProductPages(array $pages = array(), $language = XHS_LANGUAGE)
    {
        $this->productPages[$language] = $pages;
    }

    public function setCategories(array $categories = array(), $language = XHS_LANGUAGE)
    {
        $this->categories[$language] = $categories;
    }

    public function addCategory($key, $value)
    {
        $this->categories[XHS_LANGUAGE][$key] = $value;
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
