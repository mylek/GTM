<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Block\Category;

use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Block\Category\Listing;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Category;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\Store;

class ListingTest extends TestCase
{
    private Listing $object;

    private MockObject $context;

    private MockObject $registry;

    private MockObject $storeManager;

    protected function setUp(): void
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeManager = $this->getMockForAbstractClass(
            StoreManagerInterface::class,
            [],
            '',
            false,
            false,
            true,
            ['getStore']
        );
        $this->object = $this->getMockBuilder(Listing::class)
            ->setConstructorArgs(
                [
                    'context' => $this->context,
                    'registry' => $this->registry,
                    'storeManager' => $this->storeManager,
                ]
            )
            ->onlyMethods(['getProducts'])
            ->getMock();
    }

    /**
     * @param array $expected
     * @param array $params
     * @return void
     * @dataProvider getProductsDataProvider
     */
    public function testGetProductsData(array $expected, array $params): void
    {
        $products = [];
        foreach ($params['products'] as $product) {
            $products[] = $this->getProduct($product['name'], $product['sku'], $product['price']);
        }
        $this->object->method('getProducts')->willReturn($products);

        $category = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->getMock();
        $category->method('getName')->willReturn($params['categoryName']);
        $this->registry->method('registry')->willReturn($category);

        $store = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency = $this->getMockBuilder(Currency::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency->method('getCode')->willReturn($params['currency']);
        $store->method('getCurrentCurrency')->willReturn($currency);
        $this->storeManager->method('getStore')->willReturn($store);

        $data = $this->object->getProductsData();
        $this->assertEquals($expected, $data);
    }

    private function getProductsDataProvider(): array
    {
        return [
            [
                [
                    'ecommerce' => 'PLN',
                    'impressions' => [
                        [
                            'name' => 'Product 1',
                            'id' => 'sku1',
                            'price' => 11.11,
                            'category' => 'Category name',
                            'list' => 'Category name',
                            'position' => 1,
                        ],
                        [
                            'name' => 'Product 2',
                            'id' => 'sku2',
                            'price' => 22,
                            'category' => 'Category name',
                            'list' => 'Category name',
                            'position' => 2,
                        ],
                    ],
                ],
                [
                    'products' => [
                        ['name' => 'Product 1', 'sku' => 'sku1', 'price' => 11.11],
                        ['name' => 'Product 2', 'sku' => 'sku2', 'price' => 22],
                    ],
                    'categoryName' => 'Category name',
                    'currency' => 'PLN',
                ],
            ],
            [
                [
                    'ecommerce' => 'PLN',
                    'impressions' => [],
                ],
                [
                    'products' => [],
                    'categoryName' => 'Category name',
                    'currency' => 'PLN',
                ],
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