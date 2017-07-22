<?php

namespace Xhshop;

class Order
{
    private $items = array();
    private $cartGross;
    private $cartNet;
    private $vatFull;
    private $vatReduced;
    private $units;
    private $shipping;
    private $vatFullRate;
    private $vatReducedRate;
    private $total;
    private $fee;
    private $showNet = false; // practically unused

    public function __construct($vatFullRate, $vatReducedRate)
    {
        $this->vatFullRate = (float)$vatFullRate;
        $this->vatReducedRate = (float)$vatReducedRate;
    }

    public function addItem(Product $product, $amount, $variant = null)
    {
        $index = $product->getUid();
        if (isset($variant)) {
            $index .= '_'.$variant;
        }
        $this->items[$index]['amount'] = (int)$amount;
        $this->items[$index]['variant'] = $variant;
        $this->items[$index]['net'] = (float)$this->getProductNet($product);
        $this->items[$index]['gross'] = $product->getGross();
        $this->items[$index]['vatRate'] = $product->getVat();
        $this->items[$index]['units'] = (float)$product->getWeight();
        $this->refresh();
    }

    public function removeItem(Product $product, $variant = null)
    {
        $index = $product->getUid();
        if (isset($variant)) {
            $index .= '_'.$variant;
        }
        unset($this->items[$index]);
        $this->refresh();
    }

    private function getProductNet(Product $product)
    {
        $rate = 0;
        if ($product->getVat() == 'full') {
            $rate = $this->vatFullRate;
        } elseif ($product->getVat() == 'reduced') {
            $rate = $this->vatReducedRate;
        }
        return $product->getNet($rate);
    }

    private function refresh()
    {
        $this->cartGross = 0.00;
        $this->cartNet = 0.00;
        $this->units = 0.00;
        $this->vatReduced = 0.00;
        $this->vatFull = 0.00;
        foreach ($this->items as $product) {
            $amount = $product['amount'];
            $gross = (float)$product['gross'] * $amount;
            $net = (float)$product['net'] * $amount;
            $tax = $gross - $net;
            if ($product['vatRate'] == 'full') {
                $this->vatFull += $tax;
            }
            if ($product['vatRate'] == 'reduced') {
                $this->vatReduced += $tax;
            }
            $this->units +=  (float)$product['units'] * $amount;
            $this->cartGross += $gross;
            $this->cartNet += $net;
        }
        $this->vatForShippingAndFee();
        $this->total = $this->cartGross + $this->shipping + $this->fee;
    }

    private function vatForShippingAndFee()
    {
        $fees = $this->shipping + $this->fee;
        
        if ($this->vatFull > 0) {
            $factor = (($this->vatFull/$this->vatFullRate) * (100  + $this->vatFullRate))/$this->cartGross;
            $temp = $fees * $factor;
            $temp = ($temp/(100 + $this->vatFullRate)) * $this->vatFullRate;
            $this->vatFull = $this->vatFull +  $temp;
        }
        if ($this->vatReduced > 0) {
            $factor = (($this->vatReduced/$this->vatReducedRate) * (100  + $this->vatReducedRate))/$this->cartGross;
            $temp = $fees * $factor;
            $temp = ($temp/(100 + $this->vatReducedRate)) * $this->vatReducedRate;
            $this->vatReduced = $this->vatReduced +  $temp;
        }
        return;
    }

    public function hasItems()
    {
        return count($this->items) > 0;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setShipping($shipping)
    {
        $this->shipping = $shipping;
        $this->refresh();
    }

    public function getShipping()
    {
        return $this->shipping;
    }

    public function setFee($fee = 0)
    {
        $this->fee = $fee;
        $this->refresh();
    }

    public function getUnits()
    {
        return $this->units;
    }

    public function getCartSum()
    {
        if ($this->showNet == true) {
            return $this->cartNet;
        }
        return $this->cartGross;
    }

    public function getVat()
    {
        return $this->vatFull + $this->vatReduced;
    }

    public function getVatReduced()
    {
        return $this->vatReduced;
    }

    public function getVatFull()
    {
        return $this->vatFull;
    }

    public function getTotal()
    {
        return $this->cartGross + $this->shipping + $this->fee;
    }
}
