<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config;

class ExampleSpecificConfigService extends ExampleConfigService
{
    public function getSpecificStringConfig(?string $salesChannelId): string
    {
        return (string) $this->getConfig(ExampleConfigEnum::SPECIFIC_STRING, $salesChannelId);
    }
}
