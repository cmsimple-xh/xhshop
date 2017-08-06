<?php

namespace Xhshop;

use PHPUnit\Framework\TestCase;
use RangeException;

class RationalTest extends TestCase
{
    public function testOne()
    {
        $this->assertEquals('1/1', Rational::one()->toString());
    }

    public function testHundred()
    {
        $this->assertEquals('100/1', Rational::hundred()->toString());
    }

    public function testZeroDenominatorThrows()
    {
        $this->expectException(RangeException::class);
        new Rational('1', '0');
    }

    public function testZeroNumerator()
    {
        $this->assertEquals('0/1', (new Rational('0', '17'))->toString());
    }

    public function testAddition()
    {
        $a = new Rational('2', '3');
        $b = new Rational('3', '4');
        $this->assertEquals('17/12', $a->plus($b)->toString());
    }

    public function testSubtraction()
    {
        $a = new Rational('2', '3');
        $b = new Rational('3', '-4');
        $this->assertEquals('17/12', $a->minus($b)->toString());
    }

    public function testMultiplication()
    {
        $a = new Rational('2', '3');
        $b = new Rational('3', '4');
        $this->assertEquals('1/2', $a->times($b)->toString());
    }

    public function testDivision()
    {
        $a = new Rational('2', '3');
        $b = new Rational('3', '4');
        $this->assertEquals('8/9', $a->dividedBy($b)->toString());
    }

    /**
     * @param string $numerator
     * @param string $denominator
     * @param string $expected
     * @dataProvider provideDataForDecimalConversion
     */
    public function testDecimalConversion($numerator, $denominator, $expected)
    {
        $this->assertEquals(
            new Decimal($expected),
            (new Rational($numerator, $denominator))->toDecimal()
        );
    }

    /**
     * @return array
     */
    public function provideDataForDecimalConversion()
    {
        return array(
            ['1', '200', '0.01'],
            ['1', '201', '0.00']
        );
    }
}
