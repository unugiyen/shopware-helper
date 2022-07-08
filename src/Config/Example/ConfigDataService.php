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
        $configData = new ConfigData();
        $stringConfig = $this->configService->getStringConfig($salesChannelId);
        $specificStringConfig = $this->otherConfigService->getOtherStringConfig($salesChannelId);
        $configData->setSpecificStringConfig($specificStringConfig);
        $configData->setStringConfig($stringConfig);

        return $configData;
    }
}
