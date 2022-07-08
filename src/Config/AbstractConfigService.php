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

    protected function getShortName(ReflectionClass $class): string
    {
        return $class->getShortName();
    }

    protected function getReflectionClass(): ReflectionClass
    {
        return new ReflectionClass($this->getPluginClass());
    }

    protected function getConfigPrefix(): string
    {
        return $this->getShortName($this->getReflectionClass()) .'.config.';
    }

    /**
     * @return mixed
     */
    public function getConfig(string $key, ?string $salesChannelId = null)
    {
        return $this->systemConfigService->get($this->getConfigPrefix().$key, $salesChannelId);
    }
}
