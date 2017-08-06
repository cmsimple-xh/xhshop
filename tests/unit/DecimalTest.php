<?php

namespace Xhshop;

use PHPUnit\Framework\TestCase;
use RangeException;

class DecimalTest extends TestCase
{
    public function testZero()
    {
        $this->assertEquals(new Decimal('0.00'), Decimal::zero());
    }

    /**
     * @dataProvider provideDataForValidationTest
     * @return void
     */
    public function testValidation($value, $expected)
    {
        $this->assertSame($expected, Decimal::isValid($value));
    }

    /**
     * @return array
     */
    public function provideDataForValidationTest()
    {
        return array(
            ['.123', false],
            ['1.23', true],
            ['12.3', true],
            ['123.', true],
            ['1234', true]
        );
    }

    /**
     * @dataProvider provideDataForTestConversion
     */
    public function testConversion($value, $expected)
    {
        $this->assertSame($expected, (new Decimal($value))->toString());
    }

    public function provideDataForTestConversion()
    {
        return array(
            ['  1   ',   '1.00'],
            ['  1.  ',   '1.00'],
            ['  1.0 ',   '1.00'],
            [  '1.00',   '1.00'],
            ['123.45', '123.45'],
            ['-12.34', '-12.34']
        );
    }

    public function testInvalidFormatThrows()
    {
        $this->expectException(RangeException::class);
        new Decimal('9,99');
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

    public function testRationalConversion()
    {
        $this->assertEquals('1/50', (new Decimal('0.02'))->toRational()->toString());
    }
}
