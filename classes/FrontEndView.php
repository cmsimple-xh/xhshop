<?php

namespace Xhshop;

class FrontEndView extends View
{
    /** @var array */
    protected $requiredCustomerData;

    public function __construct()
    {
        parent::__construct();
        $this->templatePath = XHS_TEMPLATES_PATH. 'frontend/';
        $this->themePath = XHS_BASE_PATH . 'theme/frontend/';
    }

    /** @return void */
    public function setRequiredCustomerData(array $value)
    {
        $this->requiredCustomerData = $value;
    }

    /** @return string */
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

    /**
     * @param string $field
     * @return string
     */
    protected function contactInput($field)
    {
        $html = '';
        $value = '';
        $label = $this->labels[$field];
        if (in_array($field, $this->requiredCustomerData)) {
            $class = 'xhsFormLabel xhsRequired';
        } else {
            $class = 'xhsFormLabel';
        }
        $label = '<label for="'.$field.'" class="' . $class . '">'.$label. ':</label>';
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
            case 'email':
                $params['type'] = 'email';
                /* fall through */
            default:
                $params['class'] = 'xhsContInp';
//                $params['placeholder'] = $this->labels[$field];
                break;
        }
        $params['id'] = $field;
        if (in_array($field, $this->requiredCustomerData, true)) {
            $params['required'] = 'required';
        }
        $html .= $label;
        $html .= $this->textInputNameValueLabel($field, $value, $params);
        return $html;
    }

    /** @return string */
    protected function salutationSelectbox()
    {
        $isRequired = in_array('salutation', $this->requiredCustomerData);
        if ($isRequired) {
            $class = 'xhsFormLabel xhsRequired';
        } else {
            $class = 'xhsFormLabel';
        }
        $label = '<label for="xhsSalutation" class="' . $class . '">' . $this->labels['salutation'] . ':</label>';
        if (in_array('salutation', $this->missingData)) {
            $label = '<span class="xhsRequired">' . $label . '</span>';
        }
        $html = $label . '<select name="salutation" id="xhsSalutation"';
        if ($isRequired) {
            $html .= ' required';
        }
        $html .= '>';
        $salutations = array('', $this->labels['salutation_misses'],
                $this->labels['salutation_mister'], $this->labels['salutation_x']);
        foreach ($salutations as $salutation) {
            $html .= '<option';
            if ($salutation === '') {
                $html .= ' value=""';
                $salutation = $this->labels['please_select'];
            }
            if ($_SESSION['xhsCustomer']->salutation === $salutation) {
                $html .= ' selected';
            }
            $html .= '>' . $salutation . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /** @return string */
    protected function countriesSelectbox()
    {
        $isRequired = in_array('country', $this->requiredCustomerData);
        if ($isRequired) {
            $class = 'xhsFormLabel xhsRequired';
        } else {
            $class = 'xhsFormLabel';
        }
        $label = '<label for="xhsCountries" class="' . $class . '">' . $this->labels['country'] . ':</label>';
        if (in_array('country', $this->missingData)) {
            $label = '<span class="xhsRequired">' . $label . '</span>';
        }
        $html = $label . '<select name="country" id="xhsCountries"';
        if ($isRequired) {
            $html .= ' required';
        }
        $html .= '>';
        $countries = $this->shippingCountries;
        array_unshift($countries, '');
        foreach ($countries as $country) {
            $html .= "\n\t".'<option';
            if ($country === '') {
                $html .= ' value=""';
                $country = $this->labels['please_select'];
            }
            if ($_SESSION['xhsCustomer']->country == trim($country)) {
                $html .= ' selected="selected"';
            }
            $html .= '>'.trim($country).'</option>';
        }
        $html .= "\n" . '</select>';
        return $html;
    }

    /**
     * @return string
     *
     * @todo leave url preparation to cms_bridge
     */
    protected function cosHint()
    {
        return $this->linkedPageHint($this->gtcUrl, $this->hints['gtc_confirmation']);
    }

    /** @return string */
    protected function shippingCostsHint()
    {
        return $this->linkedPageHint($this->shippingCostsUrl, $this->hints['price_info_shipping']);
    }

    /**
     * @param string $url
     * @param string $text
     * @return string
     */
    public function linkedPageHint($url, $text)
    {
        if ($url) {
            if (in_array('hi_fancybox', XH_plugins(), true)) {
                $starttag = sprintf('<a href="%s&print" class="zoom_i xhsCosLnk" target="_blank">', $url);
            } else {
                $starttag = sprintf('<a href="%s" class="xhsCosLnk">', $url);
            }
            $endtag = '</a>';
        } else {
            $starttag = $endtag = '';
        }
        return sprintf($text, $starttag, $endtag);
    }
}
