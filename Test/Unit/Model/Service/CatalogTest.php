<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Model\Service;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Model\Service\Catalog;
use Magento\Catalog\Model\Category;
use Magento\Directory\Model\Currency;
use Magento\Catalog\Model\Product;

class CatalogTest extends TestCase
{
    private Catalog $object;

    private array $collection;

    private MockObject $category;

    private MockObject $currency;

    protected function setUp(): void
    {
        $this->collection = [];

        $this->category = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->currency = $this->getMockBuilder(Currency::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new Catalog(
            $this->collection,
            $this->category,
            $this->currency
        );
    }

    /**
     * @param array $expected
     * @param string $currency
     * @param string $categoryName
     * @param array $product
     * @return void
     * @dataProvider getDataProvider
     */
    public function testGetData(array $expected, string $currency, string $categoryName, array $products): void
    {
        foreach ($products as $product) {
            $this->collection[] = $this->getProduct($product['name'], $product['sku'], $product['price']);
        }

        $this->currency->method('getCode')->willReturn($currency);
        $this->category->method('getName')->willReturn($categoryName);

        $this->object = new Catalog(
            $this->collection,
            $this->category,
            $this->currency
        );

        $data = $this->object->getData();
        $this->assertEquals($expected['ecommerce'], $data['ecommerce']);
        $this->assertEquals($expected, $data);
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

    private function getDataProvider(): array
    {
        return [
            [
                [
                    'ecommerce' => 'USD',
                    'impressions' => [
                        [
                            'name' => 'Product 3',
                            'id' => 'sku3',
                            'price' => 33.11,
                            'position' => 1,
                            'category' => 'Computer',
                            'list' => 'Computer',
                        ],
                        [
                            'name' => 'Product 4',
                            'id' => 'sku4',
                            'price' => 44.11,
                            'position' => 2,
                            'category' => 'Computer',
                            'list' => 'Computer',
                        ],
                    ],
                ],
                'currency' => 'USD',
                'categoryName' => 'Computer',
                'product' => [
                    ['name' => 'Product 3', 'sku' => 'sku3', 'price' => 33.11],
                    ['name' => 'Product 4', 'sku' => 'sku4', 'price' => 44.11],
                ],
            ],

            [
                [
                    'ecommerce' => 'PLN',
                    'impressions' => [
                        [
                            'name' => 'Product 1',
                            'id' => 'sku1',
                            'price' => 11.11,
                            'position' => 1,
                            'category' => 'Computer',
                            'list' => 'Computer',
                        ],
                    ],
                ],
                'currency' => 'PLN',
                'categoryName' => 'Computer',
                'product' => [['name' => 'Product 1', 'sku' => 'sku1', 'price' => 11.11]],
            ],
        ];
    }
}