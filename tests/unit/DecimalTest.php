<?php

namespace Xhshop;

use PHPUnit\Framework\TestCase;

class DecimalTest extends TestCase
{
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
}
