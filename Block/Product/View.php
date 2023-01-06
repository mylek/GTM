<?php

declare(strict_types=1);

namespace MylSoft\GTM\Block\Product;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use MylSoft\GTM\Model\Service\Product;

class View extends Template
{
    public function __construct(
        Template\Context $context,
        protected Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getProductData(): array
    {
        $product = $this->getCurrentProduct();
        if (!$product) {
            return [];
        }

        $gtmProduct = new Product();
        $category = $this->registry->registry('current_category');
        $gtmProduct->setCategory($category);
        $gtmProduct->setProducts($product);

        return $gtmProduct->getData();
    }

    protected function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }
}