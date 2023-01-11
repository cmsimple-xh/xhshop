<?php

namespace Xhshop;

use stdClass;

class CatalogWriter
{
    /** @var stdClass */
    private $catalog;

    public function __construct(stdClass $catalog)
    {
        $this->catalog = $catalog;
    }

    /** @return bool */
    public function write()
    {
        return XH_writeFile(XHS_CATALOG, $this->emitCatalog()) !== false;
    }

    /** @return string */
    private function emitCatalog()
    {
        $string = "<?php \n";
        $string .= '################### Catalog ###############################' . ";\n";
        $string .= '$version = \'' . $this->catalog->version . "';\n";
        $string .= '$separator = \'' . $this->catalog->separator . "';\n\n";
        $string .= '################### Categories ###############################' . ";\n";
        foreach ($this->catalog->categories as $lang => $categories) {
            if (!is_array($categories)) {
                $string .= '$categories[\'' . $lang . '\'] = array();' . ";\n";
            } else {
                foreach ($categories as $key => $category) {
                    $string .= '$categories[\'' . $lang . '\'][' . $key . '] = \''
                        . $this->cleanString($category) . "';\n";
                }
            }
        }
        foreach ($this->catalog->default_category as $lang => $cat) {
            $string .= '$default_category[\'' . $lang . '\'] = \'' . $this->cleanString($cat) . "';\n";
        }
        foreach ($this->catalog->category_for_the_left_overs as $lang => $cat) {
            $string .= '$category_for_the_left_overs[\'' . $lang . '\'] = \'' . $this->cleanString($cat) . "';\n";
        }
        $string .= "\n\n" . '################### Products ######################' . ";\n";
        foreach ($this->catalog->products as $uid => $product) {
            $string .= $this->emitProduct($uid, $product->getInternalState());
        }

        $string .= '?>';
        return $string;
    }

    /**
     * @param string $uid
     * @return string
     */
    private function emitProduct($uid, stdClass $product)
    {
        $string = '';
        foreach ($product->names as $lang => $name) {
            $string .= '$products[\'' . $uid . '\'][\'names\'][\'' . $lang . '\'] = \''
                . $this->cleanString($name) . "';\n";
        }
        $string .= '$products[\'' . $uid . '\'][\'price\'] = \'' . $product->price->toString() . "';\n";
        $string .= '$products[\'' . $uid . '\'][\'vat\'] = \'' . $this->cleanString($product->vat) . "';\n";
        $string .= '$products[\'' . $uid . '\'][\'sortIndex\'] = ' . (int)$product->sortIndex . ";\n";
        $string .= '$products[\'' . $uid . '\'][\'previewPicture\'] = \'' . $this->cleanString($product->previewPicture) . "';\n";
        $string .= '$products[\'' . $uid . '\'][\'image\'] = \'' . $this->cleanString($product->image) . "';\n";
        $string .= '$products[\'' . $uid . '\'][\'weight\'] = \'' . $product->weight->toString() . "';\n";
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
        
        $string .= $this->emitVariants($uid, $product);
        $string .= $this->emitCategories($uid, $product);
        $string .= $this->emitProductPages($uid, $product);
        $string .= '$products[\'' . $uid . '\'][\'uid\'] = \'' . $this->cleanString($uid) . "';\n";
        $string .= "\n#-----------------------------------------------------\n\n";
        return $string;
    }

    /**
     * @param string $uid
     * @return string
     */
    private function emitVariants($uid, stdClass $product)
    {
        $string = '';
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
        return $string;
    }

    /**
     * @param string $uid
     * @return string
     */
    private function emitCategories($uid, stdClass $product)
    {
        $string = '';
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
        return $string;
    }

    /**
     * @param string $uid
     * @return string
     */
    private function emitProductPages($uid, stdClass $product)
    {
        $string = '';
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
        return $string;
    }

    /**
     * @param string $string
     * @return string
     */
    private function cleanString($string)
    {
        $string = str_replace(array('./', '<?php', '<?', '?>'), '', $string);
        return addcslashes($string, '\'\\');
    }
}
