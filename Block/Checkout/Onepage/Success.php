<?php

declare(strict_types=1);

namespace MylSoft\GTM\Block\Checkout\Onepage;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use MylSoft\GTM\Model\Service\OnepageSuccess;
use Magento\Sales\Model\Order;
use Magento\Catalog\Helper\Product\Configuration;

class Success extends Template
{
    public function __construct(
        Template\Context $context,
        private Session $checkoutSession,
        private OnepageSuccess $onepageSuccess,
        private Order $order,
        private Configuration $configuration,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    public function getSuccess(): array
    {
        $this->onepageSuccess = new OnepageSuccess();
        $order = $this->getOrder();
        $this->onepageSuccess->setConfiguration($this->configuration);
        $this->onepageSuccess->setProducts($this->getProducts($order));
        $this->onepageSuccess->setOrder($order);

        return $this->onepageSuccess->getData();
    }

    /**
     * @return Order
     */
    protected function getOrder(): Order {
        $incrementId = $this->checkoutSession->getLastRealOrder()->getIncrementId();
        return $this->order->loadByIncrementId($incrementId);
    }

    /**
     * @param Order $order
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getProducts(Order $order): array {
        return $order->getAllVisibleItems();
    }
}