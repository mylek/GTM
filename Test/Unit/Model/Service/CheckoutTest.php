<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Model\Service;

use PHPUnit\Framework\TestCase;
use MylSoft\GTM\Model\Service\Checkout;
use Magento\Quote\Model\Quote\Item;
use MylSoft\GTM\Model\GTM\Item as ItemGTM;

class CheckoutTest extends TestCase
{
    private Checkout $object;

    protected function setUp(): void
    {
        $this->object = $this->getMockBuilder(Checkout::class)
            ->onlyMethods(['getProducts'])
            ->getMock();
    }

    /**
     * @param array $excepted
     * @param array $params
     * @return void
     * @dataProvider getDataProvider
     */
    public function testGetData(array $excepted, array $params): void
    {
        $item = $this->getItem(
            $params['product']['name'],
            $params['product']['sku'],
            $params['product']['price'],
            $params['product']['qty']
        );
        $this->object->method('getProducts')->willReturn([$item]);
        $this->assertEquals($excepted, $this->object->getData());
    }

    private function getDataProvider(): array
    {
        return [
            [
                [
                    'event' => 'checkout',
                    'ecommerce' => [
                        'checkout' => [
                            'actionField' => ['step' => 1],
                            'products' => [['name' => 'Product 8', 'id' => 'sku8', 'price' => 88.0, 'quantity' => 8]],
                        ],
                    ],
                ],
                [
                    'product' => ['name' => 'Product 8', 'sku' => 'sku8', 'price' => 88, 'qty' => 8],
                ],
            ],
        ];
    }

    /**
     * @param string $name
     * @param string $sku
     * @param float $price
     * @return array
     */
    private function getItem(string $name, string $sku, float $price, int $qty): array
    {
        $item = $this->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->getMock();
        $item->method('getName')->willReturn($name);
        $item->method('getSku')->willReturn($sku);
        $item->method('getPrice')->willReturn($price);
        $item->method('getQty')->willReturn($qty);

        $itemGTM = new ItemGTM($item);
        return $itemGTM->getProduct();
    }
}