<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\Service;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\Quote\Model\Quote\Item as ItemQuote;
use Magento\Sales\Model\Order\Item as ItemOrder;
use MylSoft\GTM\Model\GTM\Product as ProductGTM;
use Magento\Catalog\Helper\Product\Configuration;

abstract class GTM
{
    protected array $products;
    protected Category $category;
    protected Configuration $configuration;

    /**
     * @param array $products
     * @return void
     */
    public function setProducts(array $products): void {
        $this->products = $products;
    }

    /**
     * @param Category $category
     * @return void
     */
    public function setCategory(Category $category): void {
        $this->category = $category;
    }

    /**
     * @param Configuration $configuration
     * @return void
     */
    public function setConfiguration(Configuration $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * @param ItemOrder|ItemQuote $product
     * @return string
     */
    protected function getVariant(ItemOrder|ItemQuote $product): string {
        $productOptions = $this->configuration->getOptions($product);
        if (!$productOptions) {
            return '';
        }

        $variant = '';
        foreach ($productOptions as $option) {
            $variant .= $option['label'] . ': ' . $option['value'] . ', ';
        }


        return rtrim($variant, ', ');
    }

    /**
     * @return array
     */
    protected function getProducts(bool $position = true): array
    {
        $products = [];

        $i = 1;
        foreach ($this->products as $product) {
            $productGTM = new ProductGTM($product, $this->category->getName());
            $products[] = $productGTM->getProduct($position ? $i++ : null);
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

    /**
     * @param array $product
     * @param int $i
     * @return array
     */
    public function getItem(array $item, ?int $i = null): array {
        $data = [
            'name' => $item->getName(),
            'id' => $item->getSku(),
            'price' => $item->getFinalPrice(),
            //'category' => $this->category->getName(),
            'quantity' => ''
        ];
        return $data;
    }
}