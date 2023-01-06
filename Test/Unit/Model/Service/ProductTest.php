<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Model\Service;

use Magento\Catalog\Model\Category;
use PHPUnit\Framework\TestCase;
use MylSoft\GTM\Model\Service\Product as ProductGTM;
use Magento\Catalog\Model\Product;

class ProductTest extends TestCase
{
    private ProductGTM $object;

    protected function setUp(): void
    {
        $this->object = new ProductGTM();
    }

    /**
     * @param $expected
     * @param $params
     * @return void
     * @dataProvider getDataProvider
     */
    public function testGetData($expected, $params): void
    {
        $category = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->getMock();
        $category->method('getName')->willReturn($params['categoryName']);
        $this->object->setCategory($category);

        $product = $this->getProduct($params['product']['name'], $params['product']['sku'], $params['product']['price']);
        $this->object->setProducts([$product]);

        $this->assertEquals($expected, $this->object->getData());
    }

    private function getDataProvider(): array
    {
        return [
            [
                [
                    'ecommerce' => [
                        'detail' => [
                            'actionField' => [
                                'list' => 'Category 1'
                            ],
                            'products' => [
                                ['name' => 'Product 1', 'id' => 'sku1', 'price' => 11.11, 'category' => 'Category 1'],
                            ],
                        ],
                    ],
                ],
                [
                    'categoryName' => 'Category 1',
                    'product' => [
                        'name' => 'Product 1', 'sku' => 'sku1', 'price' => 11.11,
                    ]
                ]
            ],
        ];
    }

    /**
     * @param string $name
     * @param string $sku
     * @param float $price
     * @return Product
     */
    private function getProduct(string $name, string $sku, float $price): Product
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $product->method('getName')->willReturn($name);
        $product->method('getSku')->willReturn($sku);
        $product->method('getFinalPrice')->willReturn($price);

        return $product;
    }
}