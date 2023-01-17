<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Plugin\Controller\Cart;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Plugin\Controller\Cart\Add;
use Magento\Checkout\Model\Cart;
use Magento\Sales\Model\Order\Item as ItemOrder;
use Magento\Quote\Model\Quote\Item as ItemQuote;
use Magento\Catalog\Helper\Product\Configuration;

class AddTest extends TestCase
{
    private Add $object;

    private MockObject $cart;

    private MockObject $configuration;

    protected function setUp(): void
    {
        $this->cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configuration = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new Add($this->cart, $this->configuration);
    }

    /**
     * @param array $expected
     * @param array $params
     * @return void
     * @dataProvider prepareProductDataProvider
     */
    public function testPrepareProductData(array $expected, array $params): void
    {
        $item = $this->getMockBuilder(ItemQuote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $item->method('getName')->willReturn($params['product']['name']);
        $item->method('getSku')->willReturn($params['product']['sku']);
        $item->method('getPrice')->willReturn($params['product']['price']);
        $item->method('getQty')->willReturn($params['product']['qty']);
        $this->configuration->method('getOptions')->willReturn($params['attributesInfo']);
        $this->assertEquals($expected, $this->object->prepareProductData($item));
    }

    /**
     * @param array $expected
     * @param array $params
     * @return void
     * @dataProvider preparesetBodyJsonProvider
     */
    public function testSetBodyJson(array $expected, array $params): void
    {
        $this->assertJsonStringEqualsJsonString(
            json_encode($expected),
            $this->object->setBodyJson($params['body'], $params['product'])
        );
    }

    /**
     * @return array[]
     */
    private function prepareProductDataProvider(): array
    {
        return [
            [
                [
                    'name' => 'Product 1',
                    'id' => 'sku1',
                    'price' => 11.11,
                    'variant' => 'color: black, size: XL',
                    'quantity' => 3,
                ],
                [
                    'attributesInfo' => [
                        [
                            'label' => 'color',
                            'value' => 'black',
                        ],
                        [
                            'label' => 'size',
                            'value' => 'XL',
                        ],
                    ],
                    'product' => [
                        'name' => 'Product 1',
                        'sku' => 'sku1',
                        'price' => 11.11,
                        'qty' => 3,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return \array[][]
     */
    private function preparesetBodyJsonProvider(): array
    {
        return [
            [
                [
                    'issetData' => 1234,
                    'gtm_product' => [
                        'name' => 'Product 1',
                        'id' => 'sku1',
                        'price' => 11.11,
                        'category' => 'Category 1',
                        'variant' => 'color: black, size: XL',
                        'quantity' => 3,
                    ],
                ],
                [
                    'body' => '{"issetData": 1234}',
                    'product' => [
                        'name' => 'Product 1',
                        'id' => 'sku1',
                        'price' => 11.11,
                        'category' => 'Category 1',
                        'variant' => 'color: black, size: XL',
                        'quantity' => 3,
                    ],
                ],
            ],
            [
                [
                    'issetData' => 1234,
                ],
                [
                    'body' => '{"issetData": 1234}',
                    'product' => [],
                ],
            ],
            [
                [
                    'gtm_product' => [
                        'name' => 'Product 1',
                        'id' => 'sku1',
                        'price' => 11.11,
                        'category' => 'Category 1',
                        'variant' => 'color: black, size: XL',
                        'quantity' => 3,
                    ],
                ],
                [
                    'body' => '',
                    'product' => [
                        'name' => 'Product 1',
                        'id' => 'sku1',
                        'price' => 11.11,
                        'category' => 'Category 1',
                        'variant' => 'color: black, size: XL',
                        'quantity' => 3,
                    ],
                ],
            ],
        ];
    }
}