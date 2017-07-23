<?php

namespace Xhshop;

class Order
{
    private $items = array();

    /**
     * @var string
     */
    private $cartGross;

    /**
     * @var string
     */
    private $vatFull;

    /**
     * @var string
     */
    private $vatReduced;

    /**
     * @var string
     */
    private $grossFull;

    /**
     * @var string
     */
    private $grossReduced;

    private $units;

    /**
     * @var string
     */
    private $shipping;

    /**
     * @var string
     */
    private $fee;

    /**
     * @var float
     */
    private $vatFullRate;

    /**
     * @var float
     */
    private $vatReducedRate;

    /**
     * @var string
     */
    private $total;

    /**
     * @param float $vatFullRate
     * @param float $vatReducedRate
     */
    public function __construct($vatFullRate, $vatReducedRate)
    {
        bcscale(2);

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
        $this->cartGross = '0.00';
        $this->units = 0.00;
        $this->grossFull = '0.00';
        $this->grossReduced = '0.00';
        foreach ($this->items as $product) {
            $amount = $product['amount'];
            $gross = bcmul($product['gross'], $amount);
            if ($product['vatRate'] == 'full') {
                $this->grossFull = bcadd($this->grossFull, $gross);
            } elseif ($product['vatRate'] == 'reduced') {
                $this->grossReduced = bcadd($this->grossReduced, $gross);
            }
            $this->units +=  (float)$product['units'] * $amount;
            $this->cartGross = bcadd($this->cartGross, $gross);
        }
        $this->total = bcadd($this->cartGross, bcadd($this->shipping, $this->fee));
        $this->vatFull = $this->calculateVat($this->grossFull, $this->vatFullRate);
        $this->vatReduced = $this->calculateVat($this->grossReduced, $this->vatReducedRate);
        $this->vatForShippingAndFee();
    }

    private function vatForShippingAndFee()
    {
        if (bccomp($this->cartGross, '0.00') <= 0) {
            return;
        }
        $fees = bcadd($this->shipping, $this->fee);
        $ratio = $this->grossReduced / $this->cartGross;
        $feeVatFull = $this->calculateVat((1 - $ratio) * $fees, $this->vatFullRate);
        $feeVatReduced = $this->calculateVat($ratio * $fees, $this->vatReducedRate);
        $this->vatFull = bcadd($this->vatFull, $feeVatFull);
        $this->vatReduced = bcadd($this->vatReduced, $feeVatReduced);
    }

    /**
     * @param float $value
     * @param float $rate
     * @return string
     */
    private function calculateVat($value, $rate)
    {
        return number_format($value - $value * 100 / (100 + $rate), 2, '.', '');
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
        if (is_string($shipping) && preg_match('/^[1-9]\d*\.\d{2}$/', $shipping)) {
            $this->shipping = $shipping;
        } else {
            $this->shipping = number_format($shipping, 2, '.', '');
        }
        $this->refresh();
    }

    /**
     * @return string
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    public function setFee($fee = '0.00')
    {
        if (is_string($fee) && preg_match('/^-?[1-9]\d*\.\d{2}$/', $fee)) {
            $this->fee = $fee;
        } else {
            $this->fee = number_format($fee, 2, '.', '');
        }
        $this->refresh();
    }

    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @return string
     */
    public function getCartSum()
    {
        return $this->cartGross;
    }

    /**
     * @return string
     */
    public function getVat()
    {
        return bcadd($this->vatReduced, $this->vatFull);
    }

    /**
     * @return string
     */
    public function getVatReduced()
    {
        return $this->vatReduced;
    }

    /**
     * @return string
     */
    public function getVatFull()
    {
        return $this->vatFull;
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }
}
