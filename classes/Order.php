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
     * @var Decimal
     */
    private $shipping;

    /**
     * @var Decimal
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
        $this->calculateTaxes();
    }

    private function calculateTaxes()
    {
        if (bccomp($this->cartGross, '0.00') <= 0) {
            $this->vatFull = $this->vatReduced = '0.00';
            return;
        }

        $fees = bcadd($this->shipping, $this->fee);
        $num = $this->grossReduced;
        $denom = $this->cartGross;

        $fullFee = new Decimal($fees * ($denom - $num) / $denom);
        $reducedFee = new Decimal($fees * $num / $denom);

        $fullTotal = bcadd($this->grossFull, $fullFee);
        $reducedTotal = bcadd($this->grossReduced, $reducedFee);

        $this->vatFull = $this->calculateVat($fullTotal, $this->vatFullRate);
        $this->vatReduced = $this->calculateVat($reducedTotal, $this->vatReducedRate);
    }

    /**
     * @param float $value
     * @param float $rate
     * @return string
     */
    private function calculateVat($value, $rate)
    {
        return (string) new Decimal($value - $value * 100 / (100 + $rate));
    }

    public function hasItems()
    {
        return count($this->items) > 0;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setShipping(Decimal $shipping)
    {
        $this->shipping = $shipping;
        $this->refresh();
    }

    /**
     * @return Decimal
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    public function setFee(Decimal $fee)
    {
        $this->fee = $fee;
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
