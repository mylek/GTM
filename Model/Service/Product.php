<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\Service;

class Product extends GTM
{

    /**
     * @return array
     */
    public function getData(): array
    {
        return [
            'ecommerce' => [
                'detail' => [
                    'actionField' => [
                        'list' => $this->category !== null ? $this->category->getName() : ''
                    ],
                    'products' => $this->getProducts(false)
                ]
            ]
        ];
    }
}