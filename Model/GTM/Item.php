<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\GTM;

use Exception;
use Magento\Quote\Model\Quote\Item as ItemQuote;
use Magento\Sales\Model\Order\Item as ItemOrder;

class Item
{
    private string $variant;

    public function __construct(
        private ItemOrder|ItemQuote $product
    ) {
    }

    /**
     * @param string $variant
     * @return void
     */
    public function setVariant(string $variant): void
    {
        $this->variant = $variant;
    }

    public function getVariantByArray(array $variants): string {
        $variant = '';
        foreach ($variants as $option) {
            $variant .= $option['label'] . ': ' . $option['value'] . ', ';
        }

        return rtrim($variant, ', ');
    }

    public function getVariant(): string
    {
        if (!($this->product instanceof ItemOrder)) {
            throw new Exception('You must set ItemOrder');
        }

        $options = $this->product->getProductOptions();
        if (!$options) {
            return '';
        }

        return $this->getVariantByArray($options['attributes_info']);
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
        ];

        if ($this->product instanceof ItemOrder) {
            $data['quantity'] = $this->product->getQtyOrdered();
        } else {
            $data['quantity'] = $this->product->getQty();
        }

        if (!empty($this->variant)) {
            $data['variant'] = $this->variant;
        }

        if ($i !== null) {
            $data['position'] = $i;
        }

        return $data;
    }
}