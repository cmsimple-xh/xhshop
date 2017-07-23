<?php

namespace Xhshop;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /**
     * @dataProvider provideDataForTestFormatFloat
     */
    public function testFormatFloat($value, $dsep, $tsep, $expected)
    {
        global $plugin_tx;

        $plugin_tx['xhshop'] = array(
            'config_decimal_separator' => $dsep,
            'config_thousands_separator' => $tsep
        );
        $view = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $this->assertSame($expected, $view->formatFloat($value));
    }

    public function provideDataForTestFormatFloat()
    {
        return array(
            [           1234567.89, ',', '.',             '1.234.567,89'],
            [               '1.23', ',', '.',                     '1,23'],
            [              '12.34', ',', '.',                    '12,34'],
            [             '123.45', ',', '.',                   '123,45'],
            [            '1234.56', ',', '.',                 '1.234,56'],
            [           '12345.67', ',', '.',                '12.345,67'],
            [          '123456.78', ',', '.',               '123.456,78'],
            [         '1234567.89', ',', '.',             '1.234.567,89'],
            ['1234567890123456.78', ',', '.', '1.234.567.890.123.456,78']
        );
    }
}
