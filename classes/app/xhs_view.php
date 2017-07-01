<?php
/**
 *
 */
class XHS_View{
    var $docType;
    var $endTag = ' />';
    var $templatePath;
    var $themePath = null;
    var $imagePath;
    var $currency;
    var $params = array();
    var $hints = array();
    var $labels = array();
    var $lang = array();

    function __construct(){
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
                $this->labels[substr($key, 5)] = $value;
            } else {
                assert(false);
            }
        }
    }

    function setDocType($docType){
        if($docType == 'html'){
            $this->endTag = '>';
        }
        if($docType == 'xhtml'){
            $this->endTag = ' />';
        }
    }
    function getDocType(){

    }

    function setTemplatePath(){

    }

    function getTemplatePath(){

    }

    function setCurrency($currency){
        $this->currency = $currency;

    }

    function assignParam($key, $param){
        if(is_string($param)){$this->params[$key] = $param;}
        $this->$key = $param;

    }

    function loadTemplate($template){
        
        $template = str_replace(array('.',  '\\', '<', ' '), '', $template);
      
        if(isset($this->themePath) && file_exists($this->themePath . $template . '.tpl')){
            $template = $this->themePath . $template . '.tpl';
        }
        else{
            $template = $this->templatePath . $template . '.tpl';
        }
        if(file_exists($template)){
            ob_start();
            include $template;
            $html = ob_get_clean();
        } else {$html =  'Template-File ('. $this->templatePath . $template. '.tpl) not found'; }
        foreach($this->params as $placeholder => $value){
            $html = str_replace('%'.strtoupper($placeholder).'%', $value, $html);
        }
        return $html;
    }

    function injectParams($params){
        if(is_string($params)){
            return  ' ' . $params;
        }
        $html = '';
        foreach($params as $param => $value){
            $html .= ' '.$param.'="'.$value.'"';
        }
        return $html;
    }
    function radioNameValueLabel($name = null, $value = '', $label = null, $params = null){
        $html = '';
        if(isset($label)){
            $html = '<label> ';
        }
        
        $html = '<input type="radio" name="'.$name. '" value="'.$value.'"';
        if(isset($name) && $this->$name == $value){
            $html .= ' checked="checked"';
           
        }
        if(isset($params)){
            $html .= $this->injectParams($params);
        }
        $html .= $this->endTag;
        if(isset($label)){
               $html .= ' ' . $this->label($label) . '</label>';
       }
        return $html;
    }

    function checkboxNameKeyValueOnLabel($name = '', $key = '', $value = '', $setting = '', $label = '', $params = array()){
        if(!isset($setting)){$setting = 'on';}
        $html = '<label><input type="checkbox" name ="'.$name. '['.$key.']" value="'.$setting.'"';
        if($this->settings[$name][$key] == $setting){
            $html .= ' checked="checked"';
        }
        $html .= $this->endTag;
        if(isset($this->labels[$label])){
            $html .= $this->labels[$label];
        } else {$html .= $label;}
        return $html . '</label>';
    }
  
    function checkboxNameValueLabel($name = '', $value = null, $label = null, $params = array()){
        if(!isset($value)){$value = '';}
        $html = '';
        if(isset($label)){
            $html .= '<label>' . $this->label($label) . ' ';
        }
        $html .= '<input type="checkbox" name ="'.$name. '" value="'.$value.'"';
        
        if($this->$name == $value){
            $html .= ' checked="checked"';
        }
        $html .= $this->endTag;
        if(isset($label)){
            $html .=  '</label>';
        } 
        return $html;
    }

/*    function textinputNameValueLabel($name = '', $value = '', $label = '', $params = array()){
        
        $html = '<input type="text" name="'.$name.'" value="'. $value . '"';
        if(isset($params)){$html .= $this->injectParams($params);}
        $html .= $this->endTag;
        return $html;
    }
*/

/*    function floatinputNameValueLabel($name, $value = 0, $label = null, $params = null){
        $value = (float)$value;
        if(is_array($params)){
            if(!isset($params['style'])){
                $params['style'] = 'text-align: right;';}
            else {$params['style'] = "text-align: right; ". $params['style'];
            }
            if(!isset($params['size'])){
                $params['size']='5';
            }
        }
        else {$params = array('style'=> 'text-align: right;', 'size'=>'5');
        }
         if(XHS_LANGUAGE == 'en'){
            $dec_sep = '.';
        }else {
            $dec_sep = ',';
        }

        $value = number_format($value, 2, $dec_sep, '');
        return $this->textinputNameValueLabel($name, $value, $label, $params);
    }
*/
/*** cmb input type number ***/
    function textinputNameValueLabel($name = '', $value = '', $label = '', $params = array(), $isFloat = false){
        $type = $isFloat ? 'number' : 'text';
        $html = '<input type="' . $type . '" name="'.$name.'" value="'. $value . '" step="0.01"'; // steps!
        if(isset($params)){$html .= $this->injectParams($params);}
        $html .= $this->endTag;
        return $html;
    }

    function floatinputNameValueLabel($name, $value = 0, $label = null, $params = null){
        $value = (float)$value;
        if(is_array($params)){
            if(!isset($params['style'])){
                $params['style'] = 'text-align: right;';}
            else {$params['style'] = "text-align: right; ". $params['style'];
            }
            if(!isset($params['size'])){
                $params['size']='5';
            }
        }
        else {$params = array('style'=> 'text-align: right;', 'size'=>'5');
        }
         if(XHS_LANGUAGE == 'en'){
            $dec_sep = '.';
        }else {
//test            $dec_sep = ',';
            $dec_sep = '.';
        }

        $value = number_format($value, 2, $dec_sep, '');
        return $this->textinputNameValueLabel($name, $value, $label, $params, true);
    }
/*** cmb input type number end ***/
	
    function moneyinputNameValueLabel($name, $value = 0, $label = '', $params = array()){
        
        return $this->floatinputNameValueLabel($name, $value, $label, $params) . " ". $this->currency;

    }

    function hiddeninputNameValue($name, $value){
        return '<input type="hidden" name="'. $name . '" value="'. $value .'"'. $this->endTag;
    }

    function formatFloat($sum){
        if(XHS_LANGUAGE == 'en'){
            $dec_sep = '.';
            $thousands_sep = ',';
        }else {
// test            $dec_sep = ',';
// test            $thousands_sep = '.';
            $dec_sep = '.';
            $thousands_sep = ',';
        }
        return number_format($sum, 2, $dec_sep, $thousands_sep);
    }

    function formatCurrency($sum){
        return $this->formatFloat($sum)  . ' ' . $this->currency;
    }

    function oddOrEven($index){
        if ($index % 2 != 0) {
            echo 'odd';
            return;
        }
        echo 'even';
    }

    function hint($key){
        if(isset($this->hints[$key])){
            echo($this->hints[$key]);
             return;
        }
        echo $key . ' - missing in language file ([\'hints\'])';
    }

    function heading($key){
        if(isset($this->headings[$key])){
            echo($this->headings[$key]);
            return;
        }
        echo $key . ' - missing in language file ([\'headings\'])';
    }

    function label($key){
        $key = str_replace("'", "", $key);
        if(isset($this->labels[$key])){
            echo $this->labels[$key];
            return;
        }
        echo $key . ' - missing in language file ([\'labels\'])';
    }

    function button($key){
        if(isset($this->buttons[$key])){
            echo($this->buttons[$key]);
            return;
        }
        echo $key . ' - missing in language file ([\'buttons\'])';
    }

    function submitButton($value){
        echo '<input type="submit" class="xhsShopButton" value="'.$this->buttons[$value].'"'. $this->endTag;
    }

    function link($href, $text){
        return '<a href="'.$href.'">'.$text.'</a>';

    }

    function br(){
        echo '<br'.$this->endTag;
    }

   function categorySelect(){
        if(    ($this->showCategorySelect == false && !is_a($this, 'XHS_Backend_View'))
            || !isset($this->categoryOptions) 
            || count($this->categoryOptions) == 0){

            return '';
        }
        
        $html =    "\n\t" . '<select title="' . $this->labels['cat_select'] . '" name="xhsCategory" onchange="this.parentNode.submit();">';
        foreach($this->categoryOptions as $category){
            $selected = (html_entity_decode($category['value']) == html_entity_decode($this->selectedCategory)) ? ' selected="selected"' : '';
            $html .= "\n\t\t" . '<option value="' . $category['value'] . '"' . $selected . '">' . $category['label'] . '</option>';
        }

        $html .= "\n\t" .'</select>';
        $html .= "\n\t" . '<noscript>'
              . "\n\t" . '<input type="submit" class="xhsShopButton" value="'.$this->labels['select'] .'" />'
             . "\n\t</noscript>\n";
        return $html;
    }
  function productCategorySelector(){
       if(count($this->categories) === 0){
           return $this->hint('no_categories');
       }
       $html =  '<select name="xhsCategories[]" multiple size="5" style="min-width: 250px">';
       foreach($this->categories as $value){
                $selected = in_array($value, $this->productCats) ? ' selected="selected" ' : '';
                $html .=  '<option  style="min-width: 250px"' .$selected . 'value="' . $value . '">' . $value .'</option>';
       }
       $html .= '</select>';
       return $html;
    }

    function mapProduct($product, $buffer){
        foreach($product as $field => $value){
            $placeHolder = '%'.strtoupper($field). '%';
            str_replace($placeHolder, $value, $buffer);
        }
        ob_end_flush();
        return $buffer;
    }


}
?>