<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Model\GTM;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Model\GTM\Product;
use Magento\Catalog\Model\Product as ProducModel;

class ProductTest extends TestCase
{
    private Product $object;

    private MockObject $product;

    private string $categoryName;

    protected function setUp(): void
    {
        $this->product = $this->getMockBuilder(ProducModel::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param array $expected
     * @param array $params
     * @return void
     * @dataProvider getProductProvider
     */
    public function testGetProduct(array $expected, array $params): void
    {
        $this->setProduct($params['product']['name'], $params['product']['sku'], $params['product']['price']);
        $this->object = new Product($this->product, $params['categoryName']);

        $this->assertEquals($expected, $this->object->getProduct());
    }

    private function getProductProvider(): array
    {
        return [
            [
                [
                    'name' => 'Product 1',
                    'id' => 'sku1',
                    'price' => 44.3,
                    'category' => 'Test'
                ],
                [
                    'product' => [
                        'name' => 'Product 1',
                        'sku' => 'sku1',
                        'price' => 44.3,
                    ],
                    'categoryName' => 'Test'
                ],
            ],
        ];
    }

    /**
     * @param string $name
     * @param string $sku
     * @param float $price
     */
    private function setProduct(string $name, string $sku, float $price): void
    {
        $this->product = $this->getMockBuilder(ProducModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->product->method('getName')->willReturn($name);
        $this->product->method('getSku')->willReturn($sku);
        $this->product->method('getFinalPrice')->willReturn($price);
    }
}