<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\Service;

use Magento\Sales\Model\Order;
use MylSoft\GTM\Model\GTM\Item;
use Magento\Quote\Model\Quote\Item as ItemQuote;
use Magento\Sales\Model\Order\Item as ItemOrder;

class OnepageSuccess extends GTM
{
    protected Order $order;

    /**
     * @return array
     */
    public function getData(): array
    {
        return [
            'ecommerce' => [
                'purchase' => [
                    'actionField' => $this->getActionField(),
                    'products' => $this->getProducts()
                ],
            ],
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

    /**
     * @param ItemOrder|ItemQuote $product
     * @return string
     */
    protected function getVariant(ItemOrder|ItemQuote $product): string {
        $options = $product->getProductOptions();
        if (!$options) {
            return '';
        }

        $variant = '';
        foreach ($options['attributes_info'] as $option) {
            $variant .= $option['label'] . ': ' . $option['value'] . ', ';
        }


        return rtrim($variant, ', ');
    }

    public function setOrder(Order $order): void {
        $this->order = $order;
    }

    private function getActionField(): array
    {
        return [
            'id' => $this->order->getIncrementId(),
            'affiliation' => 'Online Store',
            'revenue' => $this->order->getBaseSubtotal(),
            'tax' => $this->order->getTaxAmount(),
            'shipping' => $this->order->getBaseShippingAmount(),
            'coupon' => $this->order->getCouponCode(),
        ];
    }
}