<?php

declare(strict_types=1);

namespace MylSoft\GTM\Plugin\Controller\Cart;

use Magento\Checkout\Controller\Cart\Add as AddOrg;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\Quote\Item;
use MylSoft\GTM\Model\GTM\Item as ItemGTM;
use Magento\Catalog\Helper\Product\Configuration;

class Add
{
    private const GTM_PRODUC_KEY = 'gtm_product';

    public function __construct(
        private Cart $cart,
        private Configuration $configuration
    ) {
    }

    /**
     * @param AddOrg $subject
     * @param ResponseInterface|ResultInterface $result
     * @return ResponseInterface|ResultInterface
     */
    public function afterExecute(
        AddOrg $subject,
        ResponseInterface|ResultInterface $result
    ): ResponseInterface|ResultInterface {
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
    public function prepareProductData(Item $item): array
    {
        $product = new ItemGTM($item);
        $productOptions = $this->configuration->getOptions($item);
        $variants = $product->getVariantByArray($productOptions);
        $product->setVariant($variants);

        return $product->getProduct();
    }

    /**
     * @param string $body
     * @param array $product
     * @return string
     */
    public function setBodyJson(string $body, array $product): string
    {
        if (!isset($product) || empty($product)) {
            return $body;
        }

        $result = json_decode($body, true);
        $result[self::GTM_PRODUC_KEY] = $product;

        return json_encode($result);
    }
}