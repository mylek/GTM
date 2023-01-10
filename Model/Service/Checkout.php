<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\Service;

use MylSoft\GTM\Model\GTM\Item;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Quote\Model\Quote\Item as ItemQuote;

class Checkout extends GTM
{
    protected Configuration $configuration;

    public function getData(): array {
        return [
            'event' => 'checkout',
            'ecommerce' => [
                'checkout' => [
                    'actionField' => ['step' => 1],
                    'products' => $this->getProducts()
                ]
            ]
        ];
    }

    public function setConfiguration(Configuration $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * @return array
     */
    protected function getProducts(bool $position = true): array
    {
        $items = [];
        foreach ($this->products as $product) {
            $itemGTM = new Item($product);
            $variant = $this->getVariant($product);
            $itemGTM->setVariant($variant);
            $items[] = $itemGTM->getProduct();
        }

        return $items;
    }

    protected function getVariant(ItemQuote $product): string {
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
}