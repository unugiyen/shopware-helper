<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config\Example;

use Wdt\ShopwareHelper\Config\ConfigDataInterface;

class ConfigDataService
{
    private ConfigService $configService;
    private OtherConfigService $otherConfigService;

    public function __construct(ConfigService $configService, OtherConfigService $otherConfigService)
    {
        $this->configService = $configService;
        $this->otherConfigService = $otherConfigService;
    }

    public function getConfigData(string $salesChannelId): ConfigDataInterface
    {
        $data = (new ConfigData())
            ->setStringConfig($this->configService->getStringConfig($salesChannelId))
            ->setOtherStringConfig($this->otherConfigService->getOtherStringConfig($salesChannelId))
            ->setCommaStringConfig($this->configService->getCommaStringConfig($salesChannelId))
            ->setCommaStringConfigAsArray($this->configService->getCommaStringConfigAsArray($salesChannelId, false))
        ;

        return $data;
    }
}
