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

    /**
     * @var Decimal
     */
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
     * @var Decimal
     */
    private $vatFullRate;

    /**
     * @var Decimal
     */
    private $vatReducedRate;

    /**
     * @var Decimal
     */
    private $total;

    public function __construct(Decimal $vatFullRate, Decimal $vatReducedRate)
    {
        $this->vatFullRate = $vatFullRate;
        $this->vatReducedRate = $vatReducedRate;
        $this->shipping = Decimal::zero();
        $this->fee = Decimal::zero();
    }

    /**
     * @param string $amount
     * @param ?string $variant
     * @param bool $replace
     * @return void
     */
    public function addItem(Product $product, $amount, $variant = null, $replace = true)
    {
        $index = $product->getUid();
        if (isset($variant)) {
            $index .= '_'.$variant;
        }
        if ($replace) {
            $this->items[$index]['amount'] = (int)$amount;
        } else {
            $this->items[$index]['amount'] += (int)$amount;
            if ($this->items[$index]['amount'] <= 0) {
                $this->removeItem($product, $variant);
                return;
            }
        }
        $this->items[$index]['variant'] = $variant;
        $this->items[$index]['gross'] = $product->getGross();
        $this->items[$index]['vatRate'] = $product->getVat();
        $this->items[$index]['units'] = $product->getWeight();
        $this->refresh();
    }

    /**
     * @param ?string variant
     * @return void
     */
    public function removeItem(Product $product, $variant = null)
    {
        $index = $product->getUid();
        if (isset($variant)) {
            $index .= '_'.$variant;
        }
        unset($this->items[$index]);
        $this->refresh();
    }

    /** @return void */
    private function refresh()
    {
        $this->cartGross = Decimal::zero();
        $this->units = Decimal::zero();
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
            $this->units = $this->units->plus($product['units']->times(new Decimal($amount)));
            $this->cartGross = $this->cartGross->plus($gross);
        }
        $this->total = $this->cartGross->plus($this->shipping->plus($this->fee));
        $this->calculateTaxes();
    }

    /** @return void */
    private function calculateTaxes()
    {
        if (!$this->cartGross->isGreaterThan(Decimal::zero())) {
            $this->vatFull = $this->vatReduced = Decimal::zero();
            return;
        }

        $fees = $this->shipping->plus($this->fee)->toRational();
        $reducedRate = $this->grossReduced->toRational()->dividedBy($this->cartGross->toRational());

        $fullFee = $fees->times(Rational::one()->minus($reducedRate));
        $reducedFee = $fees->times($reducedRate);

        $fullTotal = $this->grossFull->toRational()->plus($fullFee);
        $reducedTotal = $this->grossReduced->toRational()->plus($reducedFee);

        $this->vatFull = $this->calculateVat($fullTotal, $this->vatFullRate);
        $this->vatReduced = $this->calculateVat($reducedTotal, $this->vatReducedRate);
    }

    /**
     * @return Decimal
     */
    private function calculateVat(Rational $value, Decimal $rate)
    {
        $hundred = Rational::hundred();
        $percentage = $hundred->dividedBy($hundred->plus($rate->toRational()));
        $net = $value->times($percentage);
        return $value->minus($net)->toDecimal();
    }

    /** @return bool */
    public function hasItems()
    {
        return count($this->items) > 0;
    }

    /** @return array */
    public function getItems()
    {
        return $this->items;
    }

    /** @return void */
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

    /** @return void */
    public function setFee(Decimal $fee)
    {
        $this->fee = $fee;
        $this->refresh();
    }

    /**
     * @return Decimal
     */
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
