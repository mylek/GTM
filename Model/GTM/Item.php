<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\GTM;

use Magento\Quote\Model\Quote\Item as ItemQuote;
use Magento\Sales\Model\Order\Item as ItemOrder;

class Item
{
    public function __construct(
        private ItemOrder|ItemQuote $product
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
            'price' => $this->product->getPrice()
        ];

        if ($this->product instanceof ItemOrder) {
            $data['quantity'] = $this->product->getQtyOrdered();
        } else {
            $data['quantity'] = $this->product->getQty();
        }

        if ($i !== null) {
            $data['position'] = $i;
        }

        return $data;
    }
}