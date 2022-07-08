<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config;

use Wdt\ShopwareHelper\WdtShopwareHelper;

class ExampleConfigService extends AbstractConfigService
{
    protected function getPluginClass(): string
    {
        return WdtShopwareHelper::class;
    }

    public function getStringConfig(?string $salesChannelId = null): string
    {
        return (string) $this->getConfig(ExampleConfigEnum::STRING, $salesChannelId); // @phpstan-ignore-line
    }
}
