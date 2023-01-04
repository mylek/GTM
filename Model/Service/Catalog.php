<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\Service;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Directory\Model\Currency;

class Catalog
{
    public function __construct(
        protected array $collection,
        protected Category $category,
        protected Currency $currency
    ) {
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return [
              'ecommerce' => $this->currency->getCode(),
              'impressions' => $this->getProdcuts()
          ];
    }

    /**
     * @return array
     */
    private function getProdcuts(): array
    {
        $products = [];
        $i = 1;
        foreach ($this->collection as $product) {
            $products[] = $this->getProduct($product, $i++);
        }

        return $products;
    }

    /**
     * @param Product $product
     * @param int $i
     * @return array
     */
    private function getProduct(Product $product, int $i): array
    {
        return [
            'name' => $product->getName(),
            'id' => $product->getSku(),
            'price' => $product->getFinalPrice(),
            //'brand' => 'Google',
            'category' => $this->category->getName(),
            //'variant' => 'Gray',
            'list' => $this->category->getName(),
            'position' => $i,
        ];
    }
}