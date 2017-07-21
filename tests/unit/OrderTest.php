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

    public function testGetCartSum()
    {
        $order = $this->createOrderWithOnePearAndTwoBlueSplitters();
        $this->assertEquals(24.93, $order->getCartSum(), '', 0.005);
    }

    public function testGetVat()
    {
        $order = $this->createOrderWithOnePearAndTwoBlueSplitters();
        $this->assertEquals(4.08, $order->getVat(), '', 0.005);
    }

    public function testGetVatReduced()
    {
        $order = $this->createOrderWithOnePearAndTwoBlueSplitters();
        $this->assertEquals(0.37, $order->getVatReduced(), '', 0.005);
    }

    public function testGetVatFull()
    {
        $order = $this->createOrderWithOnePearAndTwoBlueSplitters();
        $this->assertEquals(3.71, $order->getVatFull(), '', 0.005);
    }

    public function testGetShipping()
    {
        $order = $this->createOrderWithOnePearAndTwoBlueSplitters();
        $this->assertEquals(5.50, $order->getShipping(), '', 0.005);
    }

    public function testGetTotal()
    {
        $order = $this->createOrderWithOnePearAndTwoBlueSplitters();
        $this->assertEquals(28.93, $order->getTotal(), '', 0.005);
    }

    private function createOrderWithOnePearAndTwoBlueSplitters()
    {
        $order = new Order(19, 7);
        $order->addItem($this->createSplitterProduct(), 2, 'blue');
        $order->addItem($this->createPearsProduct(), 1);
        $order->setShipping(5.50);
        $order->setFee(-1.50);
        return $order;
    }

    private function createPearsProduct()
    {
        $product = $this->createMock(Product::class);
        $product->method('getUid')->willReturn('pears');
        $product->method('getGross')->willReturn(4.95);
        $product->method('getNet')->willReturn(4.63);
        $product->method('getVat')->willReturn('reduced');
        return $product;
    }

    private function createSplitterProduct()
    {
        $product = $this->createMock(Product::class);
        $product->method('getUid')->willReturn('splitter');
        $product->method('getGross')->willReturn(9.99);
        $product->method('getNet')->willReturn(8.39);
        $product->method('getVat')->willReturn('full');
        return $product;
    }
}
