<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config;

use Generator;
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
    protected function getConfig(string $key, ?string $salesChannelId = null)
    {
        return $this->systemConfigService->get($this->getConfigPrefix().$key, $salesChannelId);
    }

    protected function transformToGenerator(string $commaSeperated, bool $skipEmpty = true): Generator
    {
        $rawValue = strtolower($commaSeperated);
        $rawValue = (string) preg_replace('/\s+/', '', $rawValue);
        $values = explode(',', $rawValue);

        foreach ($values as $value) {
            if ($skipEmpty) {
                if ('0' !== $value && empty($value)) {
                    continue;
                }
            }
            yield $value;
        }
    }
}
