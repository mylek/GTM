<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\GTM;

use Magento\Quote\Model\Quote\Item as ItemQuote;

class Item
{
    public function __construct(
        private ItemQuote $product
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
            'price' => $this->product->getPrice(),
            'quantity' => $this->product->getQty()
        ];

        if ($i !== null) {
            $data['position'] = $i;
        }

        return $data;
    }
}