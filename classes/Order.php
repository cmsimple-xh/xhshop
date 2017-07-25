<?php

namespace Xhshop;

class Order
{
    private $items = array();

    /**
     * @var Decimal
     */
    private $cartGross;

    /**
     * @var Decimal
     */
    private $vatFull;

    /**
     * @var Decimal
     */
    private $vatReduced;

    /**
     * @var Decimal
     */
    private $grossFull;

    /**
     * @var Decimal
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
     * @var Decimal
     */
    private $total;

    /**
     * @param float $vatFullRate
     * @param float $vatReducedRate
     */
    public function __construct($vatFullRate, $vatReducedRate)
    {
        $this->vatFullRate = (float)$vatFullRate;
        $this->vatReducedRate = (float)$vatReducedRate;
        $this->shipping = Decimal::zero();
        $this->fee = Decimal::zero();
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
        $this->cartGross = Decimal::zero();
        $this->units = 0.00;
        $this->grossFull = Decimal::zero();
        $this->grossReduced = Decimal::zero();
        foreach ($this->items as $product) {
            $amount = $product['amount'];
            $gross = $product['gross']->times(new Decimal($amount));
            if ($product['vatRate'] == 'full') {
                $this->grossFull = $this->grossFull->plus($gross);
            } elseif ($product['vatRate'] == 'reduced') {
                $this->grossReduced = $this->grossReduced->plus($gross);
            }
            $this->units +=  (float)$product['units'] * $amount;
            $this->cartGross = $this->cartGross->plus($gross);
        }
        $this->total = $this->cartGross->plus($this->shipping->plus($this->fee));
        $this->calculateTaxes();
    }

    private function calculateTaxes()
    {
        if (!$this->cartGross->isGreaterThan(Decimal::zero())) {
            $this->vatFull = $this->vatReduced = Decimal::zero();
            return;
        }

        $fees = $this->shipping->plus($this->fee);
        $num = $this->grossReduced;
        $denom = $this->cartGross;

        $fullFee = $fees->times($denom->minus($num))->dividedBy($denom);
        $reducedFee = $fees->times($num)->dividedBy($denom);

        $fullTotal = $this->grossFull->plus($fullFee);
        $reducedTotal = $this->grossReduced->plus($reducedFee);

        $this->vatFull = $this->calculateVat($fullTotal, $this->vatFullRate);
        $this->vatReduced = $this->calculateVat($reducedTotal, $this->vatReducedRate);
    }

    /**
     * @param float $rate
     * @return Decimal
     */
    private function calculateVat(Decimal $value, $rate)
    {
        return new Decimal((string) $value - (string) $value * 100 / (100 + $rate));
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
     * @return Decimal
     */
    public function getCartSum()
    {
        return $this->cartGross;
    }

    /**
     * @return Decimal
     */
    public function getVat()
    {
        return $this->vatReduced->plus($this->vatFull);
    }

    /**
     * @return Decimal
     */
    public function getVatReduced()
    {
        return $this->vatReduced;
    }

    /**
     * @return Decimal
     */
    public function getVatFull()
    {
        return $this->vatFull;
    }

    /**
     * @return Decimal
     */
    public function getTotal()
    {
        return $this->total;
    }
}
