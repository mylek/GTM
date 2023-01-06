<?php

declare(strict_types=1);

namespace MylSoft\GTM\Test\Unit\Block\Product;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MylSoft\GTM\Block\Product\View;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

class ViewTest extends TestCase
{
    private View $object;

    private MockObject $context;

    private MockObject $registry;

    protected function setUp(): void
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $this->getMockBuilder(View::class)
            ->setConstructorArgs(
                [
                    'context' => $this->context,
                    'registry' => $this->registry,
                ]
            )
            ->onlyMethods(['getCurrentProduct'])
            ->getMock();
    }

    /**
     * @param array $excepted
     * @param array $params
     * @return void
     * @dataProvider getProductDataPrivate
     */
    public function testGetProductData(array $excepted, array $params): void
    {
        $product = $this->getProduct(
            $params['product']['name'],
            $params['product']['sku'],
            $params['product']['price']
        );
        $this->object->method('getCurrentProduct')->willReturn($product);

        $category = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->getMock();
        $category->method('getName')->willReturn($params['categoryName']);
        $this->registry->method('registry')->willReturn($category);
        $this->assertEquals($excepted, $this->object->getProductData());
    }

    private function getProductDataPrivate(): array
    {
        return [
            [
                [
                    'ecommerce' => [
                        'detail' => [
                            'actionField' => [
                                'list' => 'Category 1',
                            ],
                            'products' => [
                                ['name' => 'Product 1', 'id' => 'sku1', 'price' => 11.0, 'category' => 'Category 1'],
                            ],
                        ],
                    ],
                ],
                [
                    'product' => ['name' => 'Product 1', 'sku' => 'sku1', 'price' => 11],
                    'categoryName' => 'Category 1',
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