<?php

namespace Xhshop;

abstract class View
{
    protected $templatePath;
    protected $themePath = null;
    private $currency;
    private $params = array();
    public $hints = array();
    public $labels = array();
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

    public function resetParams()
    {
        $this->params = array();
        unset($this->variants);
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

    protected function radioNameValueLabel($name = null, $value = '', $label = null, $params = null)
    {
        $html = '';
        if (isset($label)) {
            $html .= '<label> ';
        }
        
        $html .= '<input type="radio" name="'.$name. '" value="'.$value.'"';
        if (isset($name) && $this->$name == $value) {
            $html .= ' checked="checked"';
        }
        if (isset($params)) {
            $html .= $this->injectParams($params);
        }
        $html .= '>';
        if (isset($label)) {
               $html .= "\xC2\xA0" . $this->labels[$label] . '</label>';
        }
        return $html;
    }

    protected function textInputNameValueLabel($name = '', $value = '', $params = array())
    {
        $html = '<input name="'.$name.'" value="'. $value . '"';
        if (!isset($params['type'])) {
            $params['type'] = 'text';
        }
        $html .= $this->injectParams($params);
        $html .= '>';
        return $html;
    }

    private function floatInputNameValueLabel($name, Decimal $value, $params = null)
    {
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

        return $this->textInputNameValueLabel($name, $value->toString(), $params);
    }

    protected function moneyInputNameValueLabel($name, Decimal $value, $params = array())
    {
        return $this->floatinputNameValueLabel($name, $value, $params) . " ". $this->currency;
    }

    /**
     * @return string
     */
    public function formatDecimal(Decimal $value)
    {
        global $plugin_tx;

        if (!preg_match('/^(-?\d{1,3})((?:\d{3})*)\.(\d{2})$/', $value->toString(), $matches)) {
            trigger_error('unexpected decimal format', E_USER_WARNING);
        }
        $dec_sep = trim($plugin_tx['xhshop']['config_decimal_separator']);
        $thousands_sep = trim($plugin_tx['xhshop']['config_thousands_separator']);
        list(, $lead, $triplets, $decimals) = $matches;
        $triplets = preg_replace('/(\d{3})/', "$thousands_sep\$1", $triplets);
        return $lead . $triplets . $dec_sep . $decimals;
    }

    /**
     * @return string
     */
    public function formatCurrency(Decimal $value)
    {
        return $this->formatDecimal($value)  . ' ' . $this->currency;
    }

    /**
     * @return string
     */
    public function formatPercentage(Decimal $value)
    {
        global $plugin_tx;

        $decsep = trim($plugin_tx['xhshop']['config_decimal_separator']);
        return str_replace('.', $decsep, rtrim(rtrim($value->toString(), '0'), '.')) . ' %';
    }

    private function hint($key)
    {
        if (isset($this->hints[$key])) {
            echo($this->hints[$key]);
             return;
        }
        echo $key . ' - missing in language file ([\'hints\'])';
    }

    protected function label($key)
    {
        $key = str_replace("'", "", $key);
        if (isset($this->labels[$key])) {
            echo $this->labels[$key];
            return;
        }
        echo $key . ' - missing in language file ([\'labels\'])';
    }

    protected function syscheck($label, $stateLabel)
    {
        global $plugin_tx;

        return sprintf($plugin_tx['xhshop']['syscheck_message'], $label, $stateLabel);
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
        if ($href && $href !== '?') {
            $html = sprintf('<a href="%s" title="%s" class="%s">%s</a>', $href, $alt, $class, $html);
        }
        return $html;
    }

    protected function categorySelect()
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
              . "\n\t" . '<input type="submit" class="xhsShopButton" value="'.$this->labels['select'] .'">'
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
}
