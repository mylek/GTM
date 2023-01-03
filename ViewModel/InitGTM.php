<?php

declare(strict_types=1);

namespace MylSoft\GTM\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use MylSoft\GTM\Helper\Config;


class InitGTM implements ArgumentInterface
{
    public function __construct(
        private Config $config
    )
    {
    }

    /**
     * @return bool
     */
    private function isEnabled(): bool {
        return $this->config->isEnabled();
    }

    /**
     * @return string
     */
    public function getCode(): string {
        return $this->config->getCode();
    }

    /**
     * @return bool
     */
    public function isShow(): bool {
        return $this->isEnabled() && !empty($this->getCode());
    }
}