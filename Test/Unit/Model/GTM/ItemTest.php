<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Model\GTM;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Model\GTM\Item;
use Magento\Quote\Model\Quote\Item as ItemQuote;
use Magento\Sales\Model\Order\Item as ItemOrder;

class ItemTest extends TestCase
{
    private Item $object;

    private MockObject $product;

    protected function setUp(): void
    {
        $this->product = $this->getMockBuilder(ItemOrder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->object = new Item($this->product);
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
     * @param string $excepted
     * @param array $params
     * @return void
     * @dataProvider getVariantByArrayProvider
     */
    public function testGetVariantByArray(string $excepted, array $params): void
    {
        $this->assertEquals($excepted, $this->object->getVariantByArray($params));
    }

    /**
     * @param string $excepted
     * @param array $params
     * @return void
     * @dataProvider getVariantProvider
     */
    public function testGetVariant(string $excepted, array $params): void
    {
        $this->product->method('getProductOptions')->willReturn($params);
        $this->assertEquals($excepted, $this->object->getVariant());
    }

    /**
     * @param array $params
     * @return void
     * @throws Exception
     * @dataProvider getVariantExceptionProvider
     */
    public function testGetVariantException(array $params): void
    {
        $this->product = $this->getMockBuilder(ItemQuote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new Item($this->product);
        $this->expectException(Exception::class);
        $this->object->getVariant();
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

    /**
     * @return array[]
     */
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

    /**
     * @return array[]
     */
    private function getVariantProvider(): array
    {
        return [
            [
                '',
                [],
            ],
            [
                'color: black, size: M',
                [
                    'attributes_info' => [
                        [
                            'label' => 'color',
                            'value' => 'black',
                        ],
                        [
                            'label' => 'size',
                            'value' => 'M',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return \string[][][][][]
     */
    private function getVariantExceptionProvider(): array
    {
        return [
            [
                [
                    'attributes_info' => [
                        [
                            'label' => 'color',
                            'value' => 'black',
                        ],
                        [
                            'label' => 'size',
                            'value' => 'M',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array[]
     */
    private function getVariantByArrayProvider(): array
    {
        return [
            [
                'color: black, size: M',
                [
                    [
                        'label' => 'color',
                        'value' => 'black',
                    ],
                    [
                        'label' => 'size',
                        'value' => 'M',
                    ],
                ],
            ],
            [
                '',
                [],
            ],
        ];
    }
}