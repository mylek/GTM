<?php

declare(strict_types=1);

namespace MylSoft\GTM\Block;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use MylSoft\GTM\Model\Service\Checkout as CheckoutService;

class Checkout extends Template
{
    public function __construct(
        Template\Context $context,
        private Session $checkoutSession,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    public function getCheckout(): array
    {
        $checkout = new CheckoutService();
        $checkout->setProducts($this->getProducts());

        return $checkout->getData();
    }

    protected function getProducts(): array {
        $checkoutSession = $this->checkoutSession->getQuote();
        return $checkoutSession->getItems();
    }
}