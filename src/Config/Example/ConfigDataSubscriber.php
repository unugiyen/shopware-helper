<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config\Example;

use Shopware\Storefront\Event\StorefrontRenderEvent;
use Wdt\ShopwareHelper\Config\AbstractConfigDataSubscriber;
use Wdt\ShopwareHelper\Config\ConfigDataInterface;

class ConfigDataSubscriber extends AbstractConfigDataSubscriber
{
    private ConfigDataService $configDataService;

    public function __construct(ConfigDataService $configDataService)
    {
        $this->configDataService = $configDataService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StorefrontRenderEvent::class => 'setConfigData',
        ];
    }

    public function setConfigData(StorefrontRenderEvent $event): void
    {
        $context = $event->getSalesChannelContext();
        $salesChannelId = $context->getSalesChannelId();
        $event->setParameter($this->getConfigNameSpace(), $this->getConfigData($salesChannelId));
    }

    protected function getConfigNameSpace(): string
    {
        return 'wdtConfigData';
    }

    protected function getConfigData(string $salesChannelId): ConfigDataInterface
    {
        return $this->configDataService->getConfigData($salesChannelId);
    }
}
