<?php

namespace Xhshop;

use PHPUnit\Framework\TestCase;

class DecimalTest extends TestCase
{
    public function testZero()
    {
        $this->assertEquals(new Decimal('0.00'), Decimal::zero());
    }

    /**
     * @dataProvider provideDataForTestConversion
     */
    public function testConversion($value, $expected)
    {
        $this->assertSame($expected, (string) new Decimal($value));
    }

    public function provideDataForTestConversion()
    {
        return array(
            [  '0.00',   '0.00'],
            ['123.45', '123.45'],
            ['-12.34', '-12.34'],
            [       0,   '0.00'],
            [     0.0,   '0.00'],
            [  123.45, '123.45'],
            [  -12.34, '-12.34']
        );
    }

    public function testPlus()
    {
        $x = new Decimal('9.20');
        $y = new Decimal('0.02');
        $z = new Decimal('9.22');
        $this->assertEquals($z, $x->plus($y));
    }

    public function testMinus()
    {
        $x = new Decimal('9.22');
        $y = new Decimal('0.02');
        $z = new Decimal('9.20');
        $this->assertEquals($z, $x->minus($y));
    }

    public function testTimes()
    {
        $x = new Decimal(' 0.33');
        $y = new Decimal('11.00');
        $z = new Decimal(' 3.63');
        $this->assertEquals($z, $x->times($y));
    }

    public function testDividedBy()
    {
        $x = new Decimal(' 3.63');
        $y = new Decimal(' 0.33');
        $z = new Decimal('11.00');
        $this->assertEquals($z, $x->dividedBy($y));
    }

    public function testIsEqualTo()
    {
        $x = new Decimal('1.23');
        $y = new Decimal('1.23');
        $this->assertTrue($x->isEqualTo($y));
    }

    public function testIsLessThan()
    {
        $x = new Decimal('1.23');
        $y = new Decimal('1.24');
        $this->assertTrue($x->isLessThan($y));
    }

    public function testIsGreaterThan()
    {
        $x = new Decimal('1.24');
        $y = new Decimal('1.23');
        $this->assertTrue($x->isGreaterThan($y));
    }
}
