<?php

namespace Xhshop;

class BackEndView extends View
{
    public function __construct()
    {
        parent::__construct();
        $this->templatePath = XHS_TEMPLATES_PATH. '/backend/';
    }

    public function picSelector($path, array $imageArray, $selectedPic, $element)
    {
        if (empty($imageArray)) {
            return '<p class="xhsWarn">Sorry, no pictures found in '. $path .'!</p>';
        }
        $js = ' data-xhs="' . XH_hsc(json_encode(compact('path', 'element'))) . '"';
                          
        $html = "\n" . '<select class="xhsPicSelector" name="' . $element .'"' . $js . '>';
        $html .= "\n\t" . '<option>' . $this->labels['no_pic'] . '</option>';
        foreach ($imageArray as $pic) {
            $selected = ($pic == $selectedPic) ? ' selected="selected"' : '';
            $html .= "\n\t" . '<option' . $selected . '>' . $pic . '</option>';
        }
        $html .= "\n" . '</select>';
        return $html;
    }

    protected function productPageSelector()
    {
        $html =  '<select name="xhsProductPages[]" multiple size="5">';
      
        foreach ($this->pageLinks as $key => $value) {
            $spacer = str_repeat('&emsp;', $this->pageLevels[$key] - 1);
            /**
             * TODO: get rid of the '?' here
             */
            $selected = (in_array($value, $this->pages)) ? ' selected="selected" ' : '';
            $html .=  '<option ' .$selected . 'value="' . $value . '">' . $spacer . $this->pageNames[$key] .'</option>';
        }
        $html .= '</select>';
        return $html;
    }

    protected function productCategorySelector()
    {
        if (count($this->categories) === 0) {
            return $this->hint('no_categories');
        }
        $html =  '<select name="xhsCategories[]" multiple size="5">';
        foreach ($this->categories as $value) {
            $selected = in_array($value, $this->productCats) ? ' selected="selected" ' : '';
            $html .=  '<option ' .$selected . 'value="' . $value . '">' . $value .'</option>';
        }
        $html .= '</select>';
        return $html;
    }

    protected function productErrors(array $errors = array())
    {
        if (count($errors) === 0) {
            return '';
        }
        $html = "\n" .  '<ul class="xhsUL">';
        foreach ($errors as $error) {
            $html .= "\n" . '<li class="xhsErr">' . $this->labels[$error] . '</li>';
        }
        $html .= "\n\t" . '</ul>' . "\n" ;
        return $html;
    }

    protected function productHints(array $caveats = array())
    {
        if (count($caveats) === 0) {
            return '';
        }
        $html = "\n" .  '<ul class="xhsUL">';
        foreach ($caveats as $hint) {
            $html .= "\n" . '<li>' . $this->labels[$hint] . '</li>';
        }
        $html .= "\n\t" . '</ul>' . "\n";
        return $html;
    }
}
