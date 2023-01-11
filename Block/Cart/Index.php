<?php

declare(strict_types=1);

namespace MylSoft\GTM\Block\Cart;

use Magento\Framework\View\Element\Template;
use \Magento\Checkout\Model\Session as CheckoutSession;

class Index extends Template
{
    public function __construct(
        Template\Context $context,
        private CheckoutSession $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getCart(): array
    {
        $quote = $this->checkoutSession->getQuote();
        $lastItem = $quote->getLastAddedItem();
        return [];
    }
}