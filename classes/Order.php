<?php

namespace Xhshop;

class Order
{
    private $items = array();
    private $cartGross;
    private $vatFull;
    private $vatReduced;
    private $grossFull;
    private $grossReduced;
    private $units;
    private $shipping;
    private $vatFullRate;
    private $vatReducedRate;
    private $total;
    private $fee;

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

    private function refresh()
    {
        $this->cartGross = 0.00;
        $this->units = 0.00;
        $this->grossFull = '0.00';
        $this->grossReduced = '0.00';
        foreach ($this->items as $product) {
            $amount = $product['amount'];
            $gross = (float)$product['gross'] * $amount;
            if ($product['vatRate'] == 'full') {
                $this->grossFull += $gross;
            } elseif ($product['vatRate'] == 'reduced') {
                $this->grossReduced += $gross;
            }
            $this->units +=  (float)$product['units'] * $amount;
            $this->cartGross += $gross;
        }
        $this->total = $this->cartGross + $this->shipping + $this->fee;
        $this->vatFull = $this->calculateVat($this->grossFull, $this->vatFullRate);
        $this->vatReduced = $this->calculateVat($this->grossReduced, $this->vatReducedRate);
        $this->vatForShippingAndFee();
    }

    private function vatForShippingAndFee()
    {
        if ($this->cartGross <= 0) {
            return;
        }
        $fees = $this->shipping + $this->fee;
        $ratio = $this->grossReduced / $this->cartGross;
        $feeVatFull = $this->calculateVat((1 - $ratio) * $fees, $this->vatFullRate);
        $feeVatReduced = $this->calculateVat($ratio * $fees, $this->vatReducedRate);
        $this->vatFull += $feeVatFull;
        $this->vatReduced += $feeVatReduced;
    }

    private function calculateVat($value, $rate)
    {
        return $value - $value * 100 / (100 + $rate);
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
        return $this->cartGross;
    }

    public function getVat()
    {
        return $this->vatReduced + $this->vatFull;
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
