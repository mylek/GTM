<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Plugin\Controller\Cart;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Plugin\Controller\Cart\Add;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as ItemQuote;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Checkout\Controller\Cart\Add as AddOrg;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http;

class AddTest extends TestCase
{
    private Add $object;

    private MockObject $cart;

    private MockObject $configuration;

    private MockObject $addOrg;

    private MockObject $result;

    private MockObject $quote;

    private MockObject $item;

    protected function setUp(): void
    {
        $this->cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configuration = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->addOrg = $this->getMockBuilder(AddOrg::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->result = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->addMethods(['getLastAddedItem'])
            ->getMock();
        $this->item = $this->getMockBuilder(ItemQuote::class)
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
        $this->cart->method('getQuote')->willReturn($this->quote);
        $this->item->method('getName')->willReturn($params['product']['name']);
        $this->item->method('getSku')->willReturn($params['product']['sku']);
        $this->item->method('getPrice')->willReturn($params['product']['price']);
        $this->item->method('getQty')->willReturn($params['product']['qty']);
        $this->configuration->method('getOptions')->willReturn($params['attributesInfo']);

        $this->assertEquals($expected, $this->object->prepareProductData($this->item));
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
     * @param array $params
     * @return void
     * @throws Exception
     * @dataProvider afterExecuteProvider
     */
    public function testAfterExecute(array $params): void
    {
        $this->configuration->method('getOptions')->willReturn($params['variants']);
        $this->quote->method('getLastAddedItem')->willReturn($this->item);
        $this->cart->method('getQuote')->willReturn($this->quote);

        $this->result->method('getBody')->willReturn($params['body']);

        $result = $this->object->afterExecute($this->addOrg, $this->result);
        $body = json_decode($result->getBody());

        $this->assertArrayHasKey('gtm_product', $body);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testAfterExecuteException(): void
    {
        $quote = null;
        $this->expectException(Exception::class);
        $this->cart->method('getQuote')->willReturn($quote);
        $this->object->afterExecute($this->addOrg, $this->result);
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

    /**
     * @return \string[][][][]
     */
    private function afterExecuteProvider(): array
    {
        return [
            [
                [
                    'body' => '{}',
                    'variants' => [
                        [
                            'label' => 'color',
                            'value' => 'red',
                        ],
                        [
                            'label' => 'size',
                            'value' => 'XS',
                        ],
                    ],
                ],
            ],
        ];
    }
}