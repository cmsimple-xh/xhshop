<?php

namespace Xhshop;

use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testHasItems()
    {
        $order = new Order(19, 7);
        $this->assertFalse($order->hasItems());
        $product = $this->createSplitterProduct();
        $order->addItem($product, 1, 'blue');
        $this->assertTrue($order->hasItems());
        $order->removeItem($product, 'blue');
        $this->assertFalse($order->hasItems());
    }

    /**
     * @dataProvider provideDataForTestGetCartSum
     */
    public function testGetCartSum($factoryMethod, $expected)
    {
        $order = $factoryMethod();
        $this->assertSame($expected, $order->getCartSum());
    }

    public function provideDataForTestGetCartSum()
    {
        return array(
            [[$this, 'createOrderWithHundredPears'], '495.00'],
            [[$this, 'createOrderWithElevenGreenSplitters'], '109.89'],
            [[$this, 'createOrderWithOnePearAndTwoBlueSplitters'], '24.93']
        );
    }

    /**
     * @dataProvider provideDataForTestGetVat
     */
    public function testGetVat($factoryMethod, $expected)
    {
        $order = $factoryMethod();
        $this->assertSame($expected, $order->getVat());
    }

    public function provideDataForTestGetVat()
    {
        return array(
            [[$this, 'createOrderWithHundredPears'], '33.19'],
            [[$this, 'createOrderWithElevenGreenSplitters'], '18.03'],
            [[$this, 'createOrderWithOnePearAndTwoBlueSplitters'], '4.07']
        );
    }

    /**
     * @dataProvider provideDataForTestGetVatReduced
     */
    public function testGetVatReduced($factoryMethod, $expected)
    {
        $order = $factoryMethod();
        $this->assertSame($expected, $order->getVatReduced());
    }

    public function provideDataForTestGetVatReduced()
    {
        return array(
            [[$this, 'createOrderWithHundredPears'], '33.19'],
            [[$this, 'createOrderWithElevenGreenSplitters'], '0.00'],
            [[$this, 'createOrderWithOnePearAndTwoBlueSplitters'], '0.37']
        );
    }

    /**
     * @dataProvider provideDataForTestGetVatFull
     */
    public function testGetVatFull($factoryMethod, $expected)
    {
        $order = $factoryMethod();
        $this->assertSame($expected, $order->getVatFull());
    }

    public function provideDataForTestGetVatFull()
    {
        return array(
            [[$this, 'createOrderWithHundredPears'], '0.00'],
            [[$this, 'createOrderWithElevenGreenSplitters'], '18.03'],
            [[$this, 'createOrderWithOnePearAndTwoBlueSplitters'], '3.70']
        );
    }

    /**
     * @dataProvider provideDataForTestGetShipping
     */
    public function testGetShipping($factoryMethod, $expected)
    {
        $order = $factoryMethod();
        $this->assertSame($expected, $order->getShipping());
    }

    public function provideDataForTestGetShipping()
    {
        return array(
            [[$this, 'createOrderWithHundredPears'], '9.89'],
            [[$this, 'createOrderWithElevenGreenSplitters'], '3.33'],
            [[$this, 'createOrderWithOnePearAndTwoBlueSplitters'], '5.50']
        );
    }

    /**
     * @dataProvider provideDataForTestGetTotal
     */
    public function testGetTotal($factoryMethod, $expected)
    {
        $order = $factoryMethod();
        $this->assertSame($expected, $order->getTotal());
    }

    public function provideDataForTestGetTotal()
    {
        return array(
            [[$this, 'createOrderWithHundredPears'], '507.39'],
            [[$this, 'createOrderWithElevenGreenSplitters'], '112.89'],
            [[$this, 'createOrderWithOnePearAndTwoBlueSplitters'], '28.93']
        );
    }

    private function createOrderWithHundredPears()
    {
        $order = new Order(19, 7);
        $order->addItem($this->createPearsProduct(), 100);
        $order->setShipping('9.89');
        $order->setFee('2.50');
        return $order;
    }

    private function createOrderWithElevenGreenSplitters()
    {
        $order = new Order(19, 7);
        $order->addItem($this->createSplitterProduct(), 11, 'green');
        $order->setShipping('3.33');
        $order->setFee('-0.33');
        return $order;
    }

    private function createOrderWithOnePearAndTwoBlueSplitters()
    {
        $order = new Order(19, 7);
        $order->addItem($this->createSplitterProduct(), 2, 'blue');
        $order->addItem($this->createPearsProduct(), 1);
        $order->setShipping('5.50');
        $order->setFee('-1.50');
        return $order;
    }

    private function createPearsProduct()
    {
        $product = $this->createMock(Product::class);
        $product->method('getUid')->willReturn('pears');
        $product->method('getGross')->willReturn('4.95');
        $product->method('getVat')->willReturn('reduced');
        return $product;
    }

    private function createSplitterProduct()
    {
        $product = $this->createMock(Product::class);
        $product->method('getUid')->willReturn('splitter');
        $product->method('getGross')->willReturn('9.99');
        $product->method('getVat')->willReturn('full');
        return $product;
    }
}
