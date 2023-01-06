<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\Service;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use MylSoft\GTM\Model\GTM\Product as ProductGTM;

abstract class GTM
{
    protected array $products;
    protected Category $category;

    public function setProducts(array $products): void {
        $this->products = $products;
    }

    public function setCategory(Category $category): void {
        $this->category = $category;
    }

    /**
     * @return array
     */
    protected function getProducts(): array
    {
        $products = [];

        $i = 1;
        foreach ($this->products as $product) {
            $productGTM = new ProductGTM($product, $this->category->getName());
            $products[] = $productGTM->getProduct($i++);
        }

        return $products;
    }

    /**
     * @param Product $product
     * @param int $i
     * @return array
     */
    public function getProduct(Product $product, ?int $i = null): array
    {
        $data = [
            'name' => $product->getName(),
            'id' => $product->getSku(),
            'price' => $product->getFinalPrice(),
            'category' => $this->category->getName(),
        ];

        if ($i !== null) {
            $data['list'] = $this->category->getName();
            $data['position'] = $i;
        }

        return $data;
    }
}