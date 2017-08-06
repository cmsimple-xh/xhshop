<?php

namespace Xhshop;

use RangeException;

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
        if ((is_string($value) || is_int($value))
                && preg_match('/^\s*(-?(?:[0-9]|[1-9][0-9]+)(\.[0-9]{0,2})?)\s*$/', $value, $matches)) {
            $this->value = $matches[1] . substr('.00', isset($matches[2]) ? strlen($matches[2]) : 0);
        } else {
            if (defined('XH_ADM') && XH_ADM) {
                trigger_error('unexpected decimal format', E_USER_WARNING);
                $this->value = '0.00';
            } else {
                throw new RangeException('unexpected decimal format');
            }
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

    public function toString()
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
