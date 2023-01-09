<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Block\Checkout\Onepage;

use Magento\Quote\Model\Quote\Item;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Block\Checkout\Onepage\Success;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order\Item as ItemOrder;
use MylSoft\GTM\Model\Service\OnepageSuccess;
use Magento\Sales\Model\Order;

class SuccessTest extends TestCase
{
    private Success $object;

    private MockObject $context;

    private MockObject $session;

    private MockObject $onepageSuccess;

    private MockObject $order;

    protected function setUp(): void
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->onepageSuccess = $this->getMockBuilder(OnepageSuccess::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new Success($this->context, $this->session, $this->onepageSuccess, $this->order);
    }

    /**
     * @param $expected
     * @param $params
     * @return void
     * @dataProvider getSuccessProvider
     */
    public function testGetSuccess($expected, $params): void
    {
        $items = [];
        foreach ($params['products'] as $product) {
            $items[] = $this->getItem(
                $product['name'],
                $product['sku'],
                $product['price'],
                $product['qty']
            );
        }

        $order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $order->method('getIncrementId')->willReturn($params['orderId']);
        $order->method('getBaseSubtotal')->willReturn($params['baseSubtotal']);
        $order->method('getTaxAmount')->willReturn($params['taxAmount']);
        $order->method('getBaseShippingAmount')->willReturn($params['baseShippingAmount']);
        $order->method('getCouponCode')->willReturn($params['couponCode']);
        $order->method('getAllVisibleItems')->willReturn($items);
        $this->order->method('loadByIncrementId')->willReturn($order);
        $this->session->method('getLastRealOrder')->willReturn($order);

        $this->assertEquals($expected, $this->object->getSuccess());
    }

    private function getSuccessProvider(): array
    {
        return [
            [
                [
                    'ecommerce' => [
                        'purchase' => [
                            'actionField' => [
                                'id' => 'T12345',
                                'affiliation' => 'Online Store',
                                'revenue' => '35.43',
                                'tax' => '4.90',
                                'shipping' => '5.99',
                                'coupon' => 'SUMMER_SALE',
                            ],
                            'products' => [
                                [
                                    'name' => 'Triblend Android T-Shirt',
                                    'id' => '12345',
                                    'price' => 15.25,
                                    'quantity' => 1,
                                ],
                                [
                                    'name' => 'Donut Friday Scented T-Shirt',
                                    'id' => '67890',
                                    'price' => '33.75',
                                    'quantity' => 1,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'orderId' => 'T12345',
                    'baseSubtotal' => 35.43,
                    'taxAmount' => 4.9,
                    'baseShippingAmount' => 5.99,
                    'couponCode' => 'SUMMER_SALE',
                    'products' => [
                        ['name' => 'Triblend Android T-Shirt', 'sku' => '12345', 'price' => 15.25, 'qty' => 1],
                        ['name' => 'Donut Friday Scented T-Shirt', 'sku' => '67890', 'price' => 33.75, 'qty' => 1],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $name
     * @param string $sku
     * @param float $price
     * @return ItemOrder
     */
    private function getItem(string $name, string $sku, float $price, int $qty): ItemOrder
    {
        $item = $this->getMockBuilder(ItemOrder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $item->method('getName')->willReturn($name);
        $item->method('getSku')->willReturn($sku);
        $item->method('getPrice')->willReturn($price);
        $item->method('getQtyOrdered')->willReturn($qty);

        return $item;
    }
}