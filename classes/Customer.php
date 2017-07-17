<?php

namespace Xhshop;

class Customer
{
    public $first_name;
    public $last_name;
    public $street;
    public $zip_code;
    public $city;
    public $country;
    public $delivery_first_name;
    public $delivery_last_name;
    public $delivery_street;
    public $delivery_zip_code;
    public $delivery_city;
    public $delivery_country;
    public $cos_confirmed;
    public $annotation;
    public $payment_mode;
    public $email;
    public $phone;

    public function hasDifferingDeliveryAddress()
    {
        return !empty($this->delivery_first_name) && !empty($this->delivery_last_name)
            && !empty($this->delivery_street) && !empty($this->delivery_zip_code) && !empty($this->delivery_country);
    }
}
