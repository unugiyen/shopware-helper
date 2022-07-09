<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config\Example;

use Generator;
use Wdt\ShopwareHelper\Config\AbstractConfigService;

class ConfigService extends AbstractConfigService
{
    use ConfigTrait;

    public function getStringConfig(?string $salesChannelId = null): string
    {
        return (string) $this->getConfig(ConfigEnum::STRING, $salesChannelId); // @phpstan-ignore-line
    }

    public function getCommaStringConfig(?string $salesChannelId = null, bool $skipEmpty = true): Generator
    {
        return $this->transformToGenerator(
            (string) $this->getConfig(ConfigEnum::COMMA_STRING, $salesChannelId), $skipEmpty // @phpstan-ignore-line
        );
    }

    public function getCommaStringConfigAsArray(?string $salesChannelId = null, bool $skipEmpty = true): array
    {
        $generator = $this->transformToGenerator(
            (string) $this->getConfig(ConfigEnum::COMMA_STRING, $salesChannelId), $skipEmpty // @phpstan-ignore-line
        );

        return iterator_to_array($generator);
    }
}
