<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Model\Service;

use Magento\Quote\Model\Quote\Item;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Model\Service\OnepageSuccess;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;

class OnepageSuccessTest extends TestCase
{
    private OnepageSuccess $object;
    private MockObject $checkoutSession;
    private MockObject $orderItemsDetails;

    protected function setUp(): void
    {
        $this->checkoutSession = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderItemsDetails = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->object = new OnepageSuccess($this->checkoutSession, $this->orderItemsDetails);
    }

    /**
     * @param array $expected
     * @param array $params
     * @return void
     * @dataProvider getDataProvider
     */
    public function testGetData(array $expected, array $params): void {
        $order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $order->method('getIncrementId')->willReturn($params['incrementId']);
        $order->method('getBaseSubtotal')->willReturn($params['baseSubtotal']);
        $order->method('getTaxAmount')->willReturn($params['taxAmount']);
        $order->method('getBaseShippingAmount')->willReturn(5.99);
        $order->method('getCouponCode')->willReturn($params['couponCode']);
        $this->orderItemsDetails->method('loadByIncrementId')->willReturn($order);

        $this->checkoutSession->method('getLastRealOrder')->willReturn($order);

        $products = [];
        foreach ($params['products'] as $product) {
            $products[] = $this->getItem($product['name'], $product['sku'], $product['price'], $product['qty']);
        }

        $this->object->setProducts($products);
        $this->assertEquals($expected, $this->object->getData());
    }

    private function getDataProvider(): array
    {
        return [
            [
                [
                    'ecommerce' => [
                        'purchase' => [
                            'actionField' => [
                                'id' => '00001',
                                'affiliation' => 'Online Store',
                                'revenue' => 35.43,
                                'tax' => 4.90,
                                'shipping' => 5.99,
                                'coupon' => 'SUMMER_SALE',
                            ],
                            'products' => [
                                [
                                    'name' => 'Triblend Android T-Shirt',
                                    'id' => '12345',
                                    'price' => 35.43,
                                    'quantity' => 1
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'incrementId' => '00001',
                    'baseSubtotal' => 35.43,
                    'taxAmount' => 4.9,
                    'baseShippingAmount' => 5.99,
                    'couponCode' => 'SUMMER_SALE',
                    'products' => [
                        ['name' => 'Triblend Android T-Shirt', 'sku' => '12345', 'price' => 35.43, 'qty' => 1]
                    ]
                ],
            ],
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