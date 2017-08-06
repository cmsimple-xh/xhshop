<?php

namespace Xhshop;

/**
 * Value objects supporting exact arithmetic on decimals with two decimal places
 *
 * Objects of this class are primarily meant for all monetary information
 * storage and calculations, but we're using them for weights as well. All
 * operations support arbitrary magnitude and are *exact*. Note that there is
 * no support for division, because that could yield inexact results. If
 * division is necessary, convert to `Rational` to do the calculations, and
 * convert back to `Decimal` as late as possible.
 */
class Decimal
{
    /**
     * @return Decimal
     */
    public static function zero()
    {
        return new self('0.00');
    }

    /**
     * @var string
     */
    private $value;

    public function __construct($value)
    {
        if ($value instanceof Decimal) {
            trigger_error('argument is already a Decimal', E_USER_WARNING);
        }
        if (is_string($value) && preg_match('/^-?(?:\d|[1-9]\d+)\.\d{2}$/', $value)) {
            $this->value = $value;
        } else {
            $this->value = number_format($value, 2, '.', '');
        }
    }

    /**
     * @return Decimal
     */
    public function plus(Decimal $other)
    {
        return new Decimal(bcadd($this->value, $other->value, 2));
    }

    /**
     * @return Decimal
     */
    public function minus(Decimal $other)
    {
        return new Decimal(bcsub($this->value, $other->value, 2));
    }

    /**
     * @return Decimal
     */
    public function times(Decimal $other)
    {
        return new Decimal(bcmul($this->value, $other->value, 2));
    }

    /**
     * @return bool
     */
    public function isEqualTo(Decimal $other)
    {
        return bccomp($this->value, $other->value, 2) === 0;
    }

    /**
     * @return bool
     */
    public function isLessThan(Decimal $other)
    {
        return bccomp($this->value, $other->value, 2) < 0;
    }

    /**
     * @return bool
     */
    public function isGreaterThan(Decimal $other)
    {
        return bccomp($this->value, $other->value, 2) > 0;
    }

    public function __toString()
    {
        return $this->value;
    }

    /**
     * @return Rational
     */
    public function toRational()
    {
        return new Rational(bcmul($this->value, '100', 0), '100');
    }
}
