<?php
/**
 * Description of xhs_order
 *
 * @author Moritz
 */

namespace Xhshop;

class Order {
    //put your code here
    var $items = array();
    var $cartGross;
    var $cartNet;
    var $vatFull;
    var $vatReduced;
    var $units;
    var $shipping;
    var $area;
    var $vatFullRate;
    var $vatReducedRate;
    var $total;
    var $fee;
    var $showNet = false;

    function __construct($vatFullRate, $vatReducedRate){
        $this->vatFullRate = (float)$vatFullRate;
        $this->vatReducedRate = (float)$vatReducedRate;

    }

    function addItem($product, $amount, $variant = null){
        
        $index = $product->uid;
        if(isset($variant)){$index .= '_'.$variant;}
        $this->items[$index]['amount'] = (int)$amount;
        $this->items[$index]['variant'] = $variant;
        $this->items[$index]['net'] = (float)$this->getProductNet($product);
        $this->items[$index]['gross'] = (float)$product->price;
        $this->items[$index]['vatRate'] = $product->vat;
        $this->items[$index]['units'] = (float)$product->getWeight();
        $this->refresh();
        
    }

    function removeItem($product, $variant = null){
        $index = $product->uid;
        if(isset($variant)){$index .= '_'.$variant;}
        unset($this->items[$index]);
        $this->refresh();

    }

    function getProductNet($product){
        $rate = 0;
        if($product->vat == 'full'){
            $rate = $this->vatFullRate;
        }
        if($product->vat == 'reduced'){
            $rate = $this->vatReducedRate;
        }
        return $product->getNet($rate);
    }

    function refresh(){
        $this->cartGross = 0.00;
        $this->cartNet = 0.00;
        $this->units = 0.00;
        $this->vatReduced = 0.00;
        $this->vatFull = 0.00;
        foreach($this->items as $product){
            $amount = $product['amount'];
            $gross = (float)$product['gross'] * $amount;
            $net = (float)$product['net'] * $amount;
            $tax = $gross - $net;
            if($product['vatRate'] == 'full'){$this->vatFull += $tax;}
            if($product['vatRate'] == 'reduced'){$this->vatReduced += $tax;}
            $this->units +=  (float)$product['units'] * $amount;
            $this->cartGross += $gross;
            $this->cartNet += $net;

        }
        $this->vatForShippingAndFee();
        $this->total = $this->cartGross + $this->shipping + $this->fee;
    }

    function vatForShippingAndFee(){
        $fees = $this->shipping + $this->fee;
        
     //   if(true ||(float)$fees > 0){
            if($this->vatFull > 0){
                $factor = (($this->vatFull/$this->vatFullRate) * (100  + $this->vatFullRate))/$this->cartGross;
                $temp = $fees * $factor;
                $temp = ($temp/(100 + $this->vatFullRate)) * $this->vatFullRate;
                $this->vatFull = $this->vatFull +  $temp;
            }
             if($this->vatReduced > 0){
                $factor = (($this->vatReduced/$this->vatReducedRate) * (100  + $this->vatReducedRate))/$this->cartGross;
                $temp = $fees * $factor;
                $temp = ($temp/(100 + $this->vatReducedRate)) * $this->vatReducedRate;
                $this->vatReduced = $this->vatReduced +  $temp;

            }
       //     return;
       // }
        return;
    }
    function hasItems(){
        return count($this->items) > 0;
    }

    function setShipping($shipping){
        $this->shipping = $shipping;
        $this->refresh();
    }
    function getShipping(){
        return $this->shipping;
    }
    function setFee($fee = 0){
        $this->fee = $fee;
        $this->refresh();
    }
    function getCartSum(){
        if($this->showNet == true){
            return $this->cartNet;
        }
        return $this->cartGross;
    }
    function getVat(){
        return $this->vatFull + $this->vatReduced;
    }

    function getVatReduced(){
        return $this->vatReduced;
    }
    function getVatFull(){
        return $this->vatFull;
    }
    function getTotal(){
        return $this->cartGross + $this->shipping + $this->fee;
    }
}
?>
