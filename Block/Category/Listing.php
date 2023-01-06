<?php

declare(strict_types=1);

namespace MylSoft\GTM\Block\Category;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use MylSoft\GTM\Model\Service\Catalog;
use Magento\Store\Model\StoreManagerInterface;

class Listing extends Template
{
    public function __construct(
        Template\Context $context,
        protected Registry $registry,
        protected StoreManagerInterface $storeManager,
        array $data = [])
    {
        parent::__construct($context, $data);
    }

    protected function getProducts(): array {
        return $this->getLayout()->getBlock('category.products.list')->getLoadedProductCollection()->getItems();
    }

    public function getProductsData(): array {
        $products = $this->getProducts();
        $category = $this->registry->registry('current_category');
        $currency = $this->storeManager->getStore()->getCurrentCurrency();

        $catalog = new Catalog($category, $currency);
        $catalog->setProducts($products);
        return $catalog->getData();
    }
}