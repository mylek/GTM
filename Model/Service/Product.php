<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\Service;

use Magento\Catalog\Model\Product as ProducModel;

class Product extends GTM
{

    /**
     * @return array
     */
    public function getData(ProducModel $product): array
    {
        return [
            'ecommerce' => [
                'detail' => [
                    'actionField' => [
                        'list' => $this->category->getName()
                    ],
                    'products' => [$this->getProduct($product)]
                ]
            ]
        ];
    }
}