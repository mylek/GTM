<?php

declare(strict_types=1);

namespace MylSoft\GTM\Model\Service;

use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use MylSoft\GTM\Model\GTM\Item;

class OnepageSuccess extends GTM
{
    public function __construct(
        private Session $checkoutSession,
        private Order $orderItemsDetails
    )
    {
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $incrementId  = $this->checkoutSession->getLastRealOrder()->getIncrementId();
        $order = $this->orderItemsDetails->loadByIncrementId($incrementId);

        return [
            'ecommerce' => [
                'purchase' => [
                    'actionField' => $this->getActionField($incrementId, $order),
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


    private function getActionField(string $incrementId, Order $order): array
    {
        return [
            'id' => $incrementId,
            'affiliation' => 'Online Store',
            'revenue' => $order->getBaseSubtotal(),
            'tax' => $order->getTaxAmount(),
            'shipping' => $order->getBaseShippingAmount(),
            'coupon' => $order->getCouponCode(),
        ];
    }
}