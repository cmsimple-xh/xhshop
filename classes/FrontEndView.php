<?php

namespace Xhshop;

class FrontEndView extends View
{
    private $paymentHint; // apparently unused

    public function __construct()
    {
        parent::__construct();
        $this->templatePath = XHS_TEMPLATES_PATH. 'frontend/';
        $this->themePath = XHS_BASE_PATH . 'theme/frontend/';
    }

    protected function paymentHint()
    {
        if (count($this->payments) == 1) {
            $hint = '';
        } else {
            $hint = $this->labels['choose_payment_mode'];
        }
        if (in_array('payment_mode', $this->missingData)) {
            $hint = '<span class="xhsRequired">'.$hint.'</span>';
        }
        return $hint;
    }

    protected function contactInput($field)
    {
        $html = '';
        $value = '';
        $label = $this->labels[$field];
        if ($field == 'city') {
            $label = '<label for="'.$field.'" class="xhsFormLabel">'.$label. ':</label>';
        } else {
            $label = '<label for="'.$field.'" class="xhsFormLabel">'.$label. ':</label>';
        }
        if (in_array($field, $this->missingData)) {
            $label = '<span class="xhsRequired">'.$label.'</span>';
        }
        if (isset($_SESSION['xhsCustomer']->$field)) {
            $value = $_SESSION['xhsCustomer']->$field;
        }
        switch ($field) {
            case 'zip_code':
                $params['size'] = 6;
//                $params['placeholder'] = $this->labels[$field];
//                $params['class'] = '';
                break;
//            case 'city':
//                $params['class'] = 'xhsContInp';
//                break;
            default:
                $params['class'] = 'xhsContInp';
//                $params['placeholder'] = $this->labels[$field];
                break;
        }
        $params['id'] = $field;
        $html .= $label;
        $html .= $this->textinputNameValueLabel($field, $value, $field, $params);
        return $html;
    }

    protected function countriesSelectbox($delivery = false)
    {
        if (empty($this->shippingCountries)) {
            return '';
        }
        $html = '<label for="xhsCountries" class="xhsFormLabel">' . $this->labels['country'] . ':</label>'
        . '<select name="' . ($delivery ? 'delivery_' : '') . 'country" id="xhsCountries">';
        foreach ($this->shippingCountries as $country) {
            $html .= "\n\t".'<option';
            if ($_SESSION['xhsCustomer']->country == trim($country)) {
                $html .= ' selected="selected"';
            }
            $html .= '>'.trim($country).'</option>';
        }
        $html .= "\n" . '</select>';
        return $html;
    }

    /**
     *
     * @return <string>
     *
     * TODO: leave url preparation to cms_bridge
     */
    protected function cosHint()
    {
        $cos_url = $this->cosUrl;
        $hint = $this->hints['cos_confirmation'];
        $name = $this->labels['cos_name'];
        if (strlen($cos_url) > 0) {
// COS-Link in new window
//            $link = "<a href=\"$cos_url\" target=\"_blank\">$name</a>";
// COS-Link in fancybox
//            $link = "<a href=\"$cos_url\" class=\"zoom_i\">$name</a>";
            $link = "<a href=\"$cos_url&print\" class=\"zoom_i xhsCosLnk\" target=\"_blank\">$name</a>"; //cmb
            $hint = str_replace($name, $link, $hint);
        }
        return $hint;
    }

    // apparently unused
    private function defaultOrderSubmitForm()
    {
//just for fun
        printf(<<< STOP_FORM
<form method="post" style="display: inline;">
    <input type="hidden" name="xhsCheckout" value="finish" />
    <input type="submit" class="shopButton"  value="%s" />
</form>
STOP_FORM
            , $this->buttons['send_order']);
    }
}
