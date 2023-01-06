<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\GTM;

use Magento\Catalog\Model\Product as ProducModel;

class Product
{
    public function __construct(
        protected ProducModel $product,
        protected string $categoryName
    )
    {
    }

    /**
     * @param int|null $i
     * @return array
     */
    public function getProduct(?int $i = null): array
    {
        $data = [
            'name' => $this->product->getName(),
            'id' => $this->product->getSku(),
            'price' => $this->product->getFinalPrice(),
            'category' => $this->categoryName,
            'list' => $this->categoryName
        ];

        if ($i !== null) {
            $data['position'] = $i;
        }

        return $data;
    }
}