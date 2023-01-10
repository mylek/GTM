<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Model\GTM;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Model\GTM\Item;
use Magento\Quote\Model\Quote\Item as ItemQuote;

class ItemTest extends TestCase
{
    private Item $object;

    private MockObject $product;

    protected function setUp(): void
    {
    }

    /**
     * @param array $excepted
     * @param array $params
     * @return void
     * @dataProvider getProductProvider
     */
    public function testGetProduct(array $excepted, array $params): void
    {
        $this->setProduct($params['product']);
        $this->object = new Item($this->product);
        $this->object->setVariant($params['product']['variants']);
        $this->assertEquals($excepted, $this->object->getProduct());
    }

    /**
     * @param array $product
     * @return void
     */
    private function setProduct(array $product): void
    {
        $this->product = $this->getMockBuilder(ItemQuote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->product->method('getName')->willReturn($product['name']);
        $this->product->method('getSku')->willReturn($product['sku']);
        $this->product->method('getPrice')->willReturn($product['price']);
        $this->product->method('getQty')->willReturn($product['qty']);
    }

    private function getProductProvider(): array
    {
        return [
            [
                [
                    'name' => 'Product 1',
                    'id' => 'sku1',
                    'price' => 11.11,
                    'quantity' => 3,
                    'variant' => 'color: black, size: M',
                ],
                [
                    'product' => [
                        'name' => "Product 1",
                        "sku" => 'sku1',
                        'price' => 11.11,
                        'qty' => 3,
                        'variants' => 'color: black, size: M',
                    ],
                ],
            ],
        ];
    }
}