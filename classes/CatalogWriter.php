<?php

namespace Xhshop;

use stdClass;

class CatalogWriter
{
    private $catalog;

    public function __construct(stdClass $catalog)
    {
        $this->catalog = $catalog;
    }

    public function write()
    {
        $string = "<?php \n";
        //      $string .= '$separator = \'' . $this->separator . "';\n\n";
        $string .= '################### Catalog ###############################' . ";\n";
        $string .= '$version = \'' . $this->catalog->version . "';\n\n";
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
            $product = $product->getInternalState();
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

    private function cleanString($string, $writeEntities = false)
    {
        $string = str_replace(array('./', '<?php', '<?', '?>'), '', $string);
        if ($writeEntities === true) {
            $string = htmlspecialchars($string);
        }

        return addcslashes($string, '\'\\');
    }
}
