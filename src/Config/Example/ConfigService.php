<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config\Example;

use Wdt\ShopwareHelper\Config\AbstractConfigService;

class ConfigService extends AbstractConfigService
{
    use ConfigTrait;

    public function getStringConfig(?string $salesChannelId = null): string
    {
        return (string) $this->getConfig(ConfigEnum::STRING, $salesChannelId); // @phpstan-ignore-line
    }
}
