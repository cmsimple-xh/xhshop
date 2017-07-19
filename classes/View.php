<?php

namespace Xhshop;

abstract class View
{
    private $docType; // apparently unused
    private $endTag = ' />';
    protected $templatePath;
    protected $themePath = null;
    private $imagePath; // apparently unused
    private $currency;
    private $params = array();
    protected $hints = array();
    public $labels = array();
    private $lang = array(); // apparently unused
    protected $shippingCountries = array();

    public function __construct()
    {
        global $plugin_tx;

        $this->hints = array();
        $this->labels = array();
        $this->mail = array();
        foreach ($plugin_tx['xhshop'] as $key => $value) {
            if (strpos($key, 'hints_') === 0) {
                $this->hints[substr($key, 6)] = $value;
            } elseif (strpos($key, 'labels_') === 0) {
                $this->labels[substr($key, 7)] = $value;
            } elseif (strpos($key, 'mail_') === 0) {
                $this->mail[substr($key, 5)] = $value;
            }
        }
    }

    public function setShippingCountries(array $countries)
    {
        $this->shippingCountries = $countries;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function assignParam($key, $param)
    {
        if (is_string($param)) {
            $this->params[$key] = $param;
        }
        $this->$key = $param;
    }

    public function loadTemplate($template)
    {
        $template = str_replace(array('.',  '\\', '<', ' '), '', $template);
      
        if (isset($this->themePath) && file_exists($this->themePath . $template . '.tpl')) {
            $template = $this->themePath . $template . '.tpl';
        } else {
            $template = $this->templatePath . $template . '.tpl';
        }
        if (file_exists($template)) {
            ob_start();
            include $template;
            $html = ob_get_clean();
        } else {
            $html =  'Template-File ('. $this->templatePath . $template. '.tpl) not found';
        }
        foreach ($this->params as $placeholder => $value) {
            $html = str_replace('%'.strtoupper($placeholder).'%', $value, $html);
        }
        return $html;
    }

    private function injectParams($params)
    {
        if (is_string($params)) {
            return  ' ' . $params;
        }
        $html = '';
        foreach ($params as $param => $value) {
            $html .= ' '.$param.'="'.$value.'"';
        }
        return $html;
    }

    private function radioNameValueLabel($name = null, $value = '', $label = null, $params = null)
    {
        $html = '';
        if (isset($label)) {
            $html = '<label> ';
        }
        
        $html = '<input type="radio" name="'.$name. '" value="'.$value.'"';
        if (isset($name) && $this->$name == $value) {
            $html .= ' checked="checked"';
        }
        if (isset($params)) {
            $html .= $this->injectParams($params);
        }
        $html .= $this->endTag;
        if (isset($label)) {
               $html .= ' ' . $this->label($label) . '</label>';
        }
        return $html;
    }

    // apparently unused
    private function checkboxNameKeyValueOnLabel($name = '', $key = '', $value = '', $setting = '', $label = '', $params = array())
    {
        if (!isset($setting)) {
            $setting = 'on';
        }
        $html = '<label><input type="checkbox" name ="'.$name. '['.$key.']" value="'.$setting.'"';
        if ($this->settings[$name][$key] == $setting) {
            $html .= ' checked="checked"';
        }
        $html .= $this->endTag;
        if (isset($this->labels[$label])) {
            $html .= $this->labels[$label];
        } else {
            $html .= $label;
        }
        return $html . '</label>';
    }

    // apparently unsused
    private function checkboxNameValueLabel($name = '', $value = null, $label = null, $params = array())
    {
        if (!isset($value)) {
            $value = '';
        }
        $html = '';
        if (isset($label)) {
            $html .= '<label>' . $this->label($label) . ' ';
        }
        $html .= '<input type="checkbox" name ="'.$name. '" value="'.$value.'"';
        
        if ($this->$name == $value) {
            $html .= ' checked="checked"';
        }
        $html .= $this->endTag;
        if (isset($label)) {
            $html .=  '</label>';
        }
        return $html;
    }

    protected function textinputNameValueLabel($name = '', $value = '', $label = '', $params = array())
    {
        $html = '<input name="'.$name.'" value="'. $value . '" step="0.01"'; // steps!
        if (!isset($params['type'])) {
            $params['type'] = 'text';
        }
        $html .= $this->injectParams($params);
        $html .= $this->endTag;
        return $html;
    }

    private function floatInputNameValueLabel($name, $value = 0, $label = null, $params = null)
    {
        $value = (float)$value;
        if (is_array($params)) {
            if (!isset($params['style'])) {
                $params['style'] = 'text-align: right;';
            } else {
                $params['style'] = "text-align: right; ". $params['style'];
            }
            if (!isset($params['size'])) {
                $params['size']='5';
            }
        } else {
            $params = array('style'=> 'text-align: right;', 'size'=>'5');
        }

        $params['type'] = 'number';
        $value = number_format($value, 2);
        return $this->textinputNameValueLabel($name, $value, $label, $params);
    }

    private function moneyInputNameValueLabel($name, $value = 0, $label = '', $params = array())
    {
        return $this->floatinputNameValueLabel($name, $value, $label, $params) . " ". $this->currency;
    }

    // apparently unused
    private function hiddeninputNameValue($name, $value)
    {
        return '<input type="hidden" name="'. $name . '" value="'. $value .'"'. $this->endTag;
    }

    public function formatFloat($sum)
    {
        global $plugin_tx;

        $dec_sep = trim($plugin_tx['xhshop']['config_decimal_separator']);
        $thousands_sep = trim($plugin_tx['xhshop']['config_thousands_separator']);
        return number_format($sum, 2, $dec_sep, $thousands_sep);
    }

    private function formatCurrency($sum)
    {
        return $this->formatFloat($sum)  . ' ' . $this->currency;
    }

    private function hint($key)
    {
        if (isset($this->hints[$key])) {
            echo($this->hints[$key]);
             return;
        }
        echo $key . ' - missing in language file ([\'hints\'])';
    }

    private function label($key)
    {
        $key = str_replace("'", "", $key);
        if (isset($this->labels[$key])) {
            echo $this->labels[$key];
            return;
        }
        echo $key . ' - missing in language file ([\'labels\'])';
    }

    private function syscheck($label, $stateLabel)
    {
        global $plugin_tx;

        return sprintf($plugin_tx['xhshop']['syscheck_message'], $label, $stateLabel);
    }

    // apparently unused
    private function submitButton($value)
    {
        echo '<input type="submit" class="xhsShopButton" value="'.$this->buttons[$value].'"'. $this->endTag;
    }

    public function link($href, $text)
    {
        return '<a href="'.$href.'">'.$text.'</a>';
    }

    public function linkedImage($src, $href, $alt, $class = 'zoom')
    {
        if (!$src) {
            return '';
        }
        $html = sprintf('<img src="%s" alt="%s">', $src, $alt);
        if ($href) {
            $html = sprintf('<a href="%s" title="%s" class="%s">%s</a>', $href, $alt, $class, $html);
        }
        return $html;
    }

    // apparently unused
    private function br()
    {
        echo '<br'.$this->endTag;
    }

    private function categorySelect()
    {
        if (($this->showCategorySelect == false && !($this instanceof BackendView))
            || !isset($this->categoryOptions)
            || count($this->categoryOptions) == 0) {
            return '';
        }
        
        $html =    "\n\t" . '<select title="' . $this->labels['cat_select']
            . '" name="xhsCategory">';
        foreach ($this->categoryOptions as $category) {
            $selected = (html_entity_decode($category['value']) == html_entity_decode($this->selectedCategory))
                ? ' selected="selected"'
                : '';
            $html .= "\n\t\t" . '<option value="' . $category['value'] . '"' . $selected . '>'
                . $category['label'] . '</option>';
        }

        $html .= "\n\t" .'</select>';
        $html .= "\n\t" . '<noscript>'
              . "\n\t" . '<input type="submit" class="xhsShopButton" value="'.$this->labels['select'] .'" />'
             . "\n\t</noscript>\n";
        return $html;
    }

    protected function productCategorySelector()
    {
        if (count($this->categories) === 0) {
            return $this->hint('no_categories');
        }
        $html =  '<select name="xhsCategories[]" multiple size="5" style="min-width: 250px">';
        foreach ($this->categories as $value) {
            $selected = in_array($value, $this->productCats) ? ' selected="selected" ' : '';
            $html .=  '<option  style="min-width: 250px"' . $selected . 'value="' . $value . '">'
                . $value .'</option>';
        }
        $html .= '</select>';
        return $html;
    }

    // apparently unused
    private function mapProduct(Product $product, $buffer)
    {
        foreach ($product as $field => $value) {
            $placeHolder = '%'.strtoupper($field). '%';
            str_replace($placeHolder, $value, $buffer);
        }
        ob_end_flush();
        return $buffer;
    }
}
