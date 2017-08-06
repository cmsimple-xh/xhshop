<?php

namespace Xhshop;

use RangeException;

/**
 * Value objects supporting exact arithmetic on rational numbers
 *
 * Objects of this class supplement `Decimal` arithmetic with regard to
 * division. Note that all operations supports arbitrary magnitudes and
 * are *exact*, except for `::toDecimal()` which therefore has to be
 * called as late as possible.
 *
 * Also note, that the constructor is supposed to be called from `Decimal`
 * only (besides from tests). This makes it easy to grep the code base for
 * "toDecimal" to see find all potentially inexact calculations.
 */
class Rational
{
    /**
     * @return Rational
     */
    public static function one()
    {
        return new self('1');
    }

    /**
     * @return Rational
     */
    public static function hundred()
    {
        return new self('100');
    }

    /**
     * @var string
     */
    private $numerator;

    /**
     * @var string
     */
    private $denominator;

    /**
     * @param string $numerator
     * @param string $denominator
     * @throws RangeException if the $denominator is equal to zero.
     */
    public function __construct($numerator, $denominator = '1')
    {
        if (bccomp($denominator, '0', 0) === 0) {
            throw new RangeException('Denominator of Rational must not be equal to zero');
        }
        $this->numerator = bcadd($numerator, '0', 0);
        $this->denominator = bcadd($denominator, '0', 0);
        $this->normalize();
    }

    private function normalize()
    {
        if (bccomp($this->denominator, '0', 0) < 0) {
            $this->numerator = bcsub('0', $this->numerator, 0);
            $this->denominator = bcsub('0', $this->denominator, 0);
        }
        if (bccomp($this->numerator, '0', 0) >= 0) {
            $gcd = $this->gcd($this->numerator, $this->denominator);
        } else {
            $gcd = $this->gcd(bcsub('0', $this->numerator, 0), $this->denominator);
        }
        $this->numerator = bcdiv($this->numerator, $gcd, 0);
        $this->denominator = bcdiv($this->denominator, $gcd, 0);
    }

    private function gcd($a, $b)
    {
        if (bccomp($b, '0', 0) === 0) {
            return $a;
        }
        return $this->gcd($b, bcmod($a, $b));
    }

    /**
     * @return Rational
     */
    public function plus(Rational $other)
    {
        $numerator = bcadd(
            bcmul($this->numerator, $other->denominator, 0),
            bcmul($other->numerator, $this->denominator, 0)
        );
        $denominator = bcmul($this->denominator, $other->denominator, 0);
        return new Rational($numerator, $denominator);
    }

    /**
     * @return Rational
     */
    public function minus(Rational $other)
    {
        return $this->plus(new Rational(bcsub('0', $other->numerator, 0), $other->denominator));
    }

    /**
     * @return Rational
     */
    public function times(Rational $other)
    {
        $numerator = bcmul($this->numerator, $other->numerator, 0);
        $denominator = bcmul($this->denominator, $other->denominator, 0);
        return new Rational($numerator, $denominator);
    }

    /**
     * @return Rational
     */
    public function dividedBy(Rational $other)
    {
        return $this->times(new Rational($other->denominator, $other->numerator));
    }

    public function toString()
    {
        return "{$this->numerator}/{$this->denominator}";
    }

    /**
     * Rounds to nearest
     *
     * @return Decimal
     */
    public function toDecimal()
    {
        $result = bcdiv($this->numerator, $this->denominator, 2);
        $doublemod = bcmul(bcmod(bcmul($this->numerator, '100', 0), $this->denominator), '2', 0);
        if (bccomp($doublemod, $this->denominator, 0) >= 0) {
            $result = bcadd($result, '0.01', 2);
        }
        return new Decimal($result);
    }
}
