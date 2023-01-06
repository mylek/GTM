<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\Service;

use Magento\Catalog\Model\Category;
use Magento\Directory\Model\Currency;
use MylSoft\GTM\Model\GTM\Product;

class Catalog extends GTM
{
    public function __construct(
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
            'impressions' => $this->getProducts(),
        ];
    }
}