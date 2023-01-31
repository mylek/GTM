<?php

declare(strict_types=1);

namespace MylSoft\GTM\Controller\Cart;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Model\Quote\Item;
use MylSoft\GTM\Model\GTM\Item as ItemGTM;
use Magento\Catalog\Helper\Product\Configuration;

class Add implements HttpPostActionInterface
{
    public function __construct(
        private JsonFactory $jsonResultFactory,
        private Cart $cart,
        private Configuration $configuration,
    )
    {
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $item = $this->getLastAddItem();

        $resultJson = $this->jsonResultFactory->create();
        $resultJson->setData(["message" => ("Invalid Data"), "suceess" => true]);
        return $resultJson;
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
     * @return Item|null
     * @throws \Exception
     */
    public function getLastAddItem(): ?Item {
        $quote = $this->cart->getQuote();
        if (!$quote) {
            throw new \Exception('Quote is not set');
        }

        $items = $quote->getItems();

        if (!is_array($items)) {
            return null;
        }

        $item = end($items);
        return $item;
    }
}