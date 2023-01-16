<?php

declare(strict_types=1);

namespace MylSoft\GTM\Plugin\Controller\Cart;

use Magento\Checkout\Controller\Cart\Add as AddOrg;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\Quote\Item;

class Add
{
    private const GTM_PRODUC_KEY = 'gtm_product';

    public function __construct(
        private Cart $cart
    )
    {
    }

    /**
     * @param AddOrg $subject
     * @param ResponseInterface|ResultInterface $result
     * @return ResponseInterface|ResultInterface
     */
    public function afterExecute(AddOrg $subject, ResponseInterface|ResultInterface $result): ResponseInterface|ResultInterface {
        $quote = $this->cart->getQuote();
        if (!$quote) {
            throw new \Exception('Quote is not set');
        }

        $item = $quote->getLastAddedItem();
        $product = $this->prepareProductData($item);
        $context = $this->setBodyJson($result->getBody(), $product);

        $result->setBody($context);
        return $result;
    }

    /**
     * @param Item $item
     * @return array
     */
    public function prepareProductData(Item $item): array {
        return [];
    }

    /**
     * @param string $context
     * @param array $product
     * @return string
     */
    public function setBodyJson(string $context, array $product): string {
        $result = json_decode($context);
        $result[self::GTM_PRODUC_KEY] = $product;

        return json_encode($result);
    }
}