<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\Service;

use Magento\Sales\Model\Order;
use MylSoft\GTM\Model\GTM\Item;

class OnepageSuccess extends GTM
{
    protected Order $order;

    /**
     * @return array
     */
    public function getData(): array
    {
        var_dump($this->getProducts());
        return [
            'ecommerce' => [
                'purchase' => [
                    'actionField' => $this->getActionField(),
                    'products' => $this->getProducts(),
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
            $items[] = $itemGTM->getProduct();
        }

        return $items;
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