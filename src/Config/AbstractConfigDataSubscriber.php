<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config;

use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractConfigDataSubscriber implements EventSubscriberInterface
{
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

    abstract protected function getConfigNameSpace(): string;

    abstract protected function getConfigData(string $salesChannelId): ConfigDataInterface;
}
