<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\Service;

use MylSoft\GTM\Model\GTM\Item;

class Checkout extends GTM
{

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
}