<?php

declare(strict_types=1);

namespace MylSoft\GTM\Block\Checkout\Onepage;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use MylSoft\GTM\Model\Service\OnepageSuccess;
use Magento\Sales\Model\Order;

class Success extends Template
{
    public function __construct(
        Template\Context $context,
        private Session $checkoutSession,
        private OnepageSuccess $onepageSuccess,
        private Order $order,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    public function getSuccess(): array
    {
        $this->onepageSuccess->setProducts($this->getProducts());
        $this->onepageSuccess->setOrder($this->getOrder());

        return $this->onepageSuccess->getData();
    }

    /**
     * @return Order
     */
    protected function getOrder(): Order {
        $incrementId  = $this->checkoutSession->getLastRealOrder()->getIncrementId();
        return $this->order->loadByIncrementId($incrementId);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getProducts(): array {
        $checkoutSession = $this->checkoutSession->getQuote();
        return $checkoutSession->getItems();
    }
}