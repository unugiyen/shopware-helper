<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config;

use ReflectionClass;
use Shopware\Core\System\SystemConfig\SystemConfigService;

abstract class AbstractConfigService
{
    protected SystemConfigService $systemConfigService;

    public function __construct(
        SystemConfigService $systemConfigService
    ) {
        $this->systemConfigService = $systemConfigService;
    }

    abstract protected function getPluginClass(): string;

    protected function getShortName(ReflectionClass $class): string // @phpstan-ignore-line
    {
        return $class->getShortName();
    }

    protected function getReflectionClass(): ReflectionClass // @phpstan-ignore-line
    {
        return new ReflectionClass($this->getPluginClass()); // @phpstan-ignore-line
    }

    protected function getConfigPrefix(): string
    {
        return $this->getShortName($this->getReflectionClass()).'.config.';
    }

    /**
     * @return array|bool|float|int|string|null
     */
    public function getConfig(string $key, ?string $salesChannelId = null)
    {
        return $this->systemConfigService->get($this->getConfigPrefix().$key, $salesChannelId);
    }
}