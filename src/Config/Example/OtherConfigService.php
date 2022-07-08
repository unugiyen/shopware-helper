<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config\Example;

use Wdt\ShopwareHelper\Config\AbstractConfigService;

class OtherConfigService extends AbstractConfigService
{
    use ConfigTrait;

    public function getOtherStringConfig(?string $salesChannelId = null): string
    {
        return (string) $this->getConfig(OtherConfigEnum::OTHER_STRING, $salesChannelId); // @phpstan-ignore-line
    }
}
