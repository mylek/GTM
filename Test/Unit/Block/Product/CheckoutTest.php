<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Block\Product;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Block\Checkout;
use \Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote\Item;

class CheckoutTest extends TestCase
{
    private Checkout $object;

    private MockObject $context;

    private MockObject $session;

    protected function setUp(): void
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $this->getMockBuilder(Checkout::class)
            ->setConstructorArgs(
                [
                    'context' => $this->context,
                    'checkoutSession' => $this->session,
                ]
            )
            ->onlyMethods(['getProducts'])
            ->getMock();
    }

    /**
     * @param array $expected
     * @param array $params
     * @return void
     * @dataProvider getCheckoutProvider
     */
    public function testGetCheckout(array $expected, array $params): void
    {
        $item = $this->getItem($params['name'], $params['sku'], $params['price'], $params['qty']);
        $products = [$item];
        $this->object->method('getProducts')->willReturn($products);
        $this->assertEquals($expected, $this->object->getCheckout());
    }

    private function getCheckoutProvider(): array
    {
        return [
            [
                [
                    'event' => 'checkout',
                    'ecommerce' => [
                        'checkout' => [
                            'actionField' => ['step' => 1],
                            'products' => [['name' => 'Product 5', 'id' => 'sku5', 'price' => 55.11, 'quantity' => 5]],
                        ],
                    ]
                ],
                ['name' => 'Product 5', 'sku' => 'sku5', 'price' => 55.11, 'qty' => 5]
            ]
        ];
    }

    /**
     * @param string $name
     * @param string $sku
     * @param float $price
     * @return Item
     */
    private function getItem(string $name, string $sku, float $price, int $qty): Item
    {
        $item = $this->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->getMock();
        $item->method('getName')->willReturn($name);
        $item->method('getSku')->willReturn($sku);
        $item->method('getPrice')->willReturn($price);
        $item->method('getQty')->willReturn($qty);

        return $item;
    }
}