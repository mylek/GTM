<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Block;

use Magento\Quote\Model\Quote\Item as ItemQuote;
use MylSoft\GTM\Block\Checkout;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote;
use Magento\Catalog\Helper\Product\Configuration;

class CheckoutTest extends TestCase
{
    private Checkout $object;

    private MockObject $context;

    private MockObject $checkoutSession;

    private MockObject $configuration;

    protected function setUp(): void
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->checkoutSession = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configuration = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new Checkout($this->context, $this->checkoutSession, $this->configuration);
    }

    /**
     * @param array $expected
     * @param array $params
     * @return void
     * @dataProvider getCheckoutProvider
     */
    public function testGetCheckout(array $expected, array $params): void
    {
        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $items = [];
        $this->configuration->method('getOptions')->willReturnOnConsecutiveCalls($params['products'][0]['variants'], $params['products'][1]['variants']);
        foreach ($params['products'] as $key => $product) {
            $items[] = $this->getItem(
                $product['name'],
                $product['sku'],
                $product['price'],
                $product['qty'],
                $product['variants'],
            );
        }

        $quote->method('getItems')->willReturn($items);

        $this->checkoutSession->method('getQuote')->willReturn($quote);

        $this->assertEquals($expected, $this->object->getCheckout());
    }

    /**
     * @return \array[][]
     */
    private function getCheckoutProvider(): array
    {
        return [
            [
                [
                    'event' => 'checkout',
                    'ecommerce' => [
                        'checkout' => [
                            'actionField' => ['step' => 1],
                            'products' => [
                                [
                                    'name' => 'Triblend Android T-Shirt',
                                    'id' => '12345',
                                    'price' => 15.25,
                                    'variant' => 'color: black, size: M',
                                    'quantity' => 1,
                                ],
                                [
                                    'name' => 'Donut Friday Scented T-Shirt',
                                    'id' => '67890',
                                    'price' => '33.75',
                                    'variant' => 'color: red, size: XXL',
                                    'quantity' => 1,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'step' => 1,
                    'products' => [
                        [
                            'name' => 'Triblend Android T-Shirt',
                            'sku' => '12345',
                            'price' => 15.25,
                            'qty' => 1,
                            'variants' => [
                                [
                                    'label' => 'color',
                                    'value' => 'black'
                                ],
                                [
                                    'label' => 'size',
                                    'value' => 'M'
                                ]
                            ]
                        ],
                        [
                            'name' => 'Donut Friday Scented T-Shirt',
                            'sku' => '67890',
                            'price' => 33.75,
                            'qty' => 1,
                            'variants' => [
                                [
                                    'label' => 'color',
                                    'value' => 'red'
                                ],
                                [
                                    'label' => 'size',
                                    'value' => 'XXL'
                                ]
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $name
     * @param string $sku
     * @param float $price
     * @return ItemQuote
     */
    private function getItem(string $name, string $sku, float $price, int $qty, array $variants): ItemQuote
    {
        $item = $this->getMockBuilder(ItemQuote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $item->method('getName')->willReturn($name);
        $item->method('getSku')->willReturn($sku);
        $item->method('getPrice')->willReturn($price);
        $item->method('getQty')->willReturn($qty);

        return $item;
    }
}