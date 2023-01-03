<?php

declare(strict_types=1);

namespace MylSoft\GTM\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const CONFIG_PATH = 'gtm/';
    public const CONFIG_ENABLE = 'main/enabled';
    public const CONFIG_CODE = 'main/code';

    public function __construct(
        private ScopeConfigInterface $scope
    ) {
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->scope->getValue(
            self::CONFIG_PATH . self::CONFIG_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return (string)$this->scope->getValue(
            self::CONFIG_PATH . self::CONFIG_CODE,
            ScopeInterface::SCOPE_STORE
        );
    }
}