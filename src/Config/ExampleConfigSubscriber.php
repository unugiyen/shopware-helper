<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config;

use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class ExampleConfigSubscriber implements EventSubscriberInterface
{
    private ExampleConfigService $configService;
    private ExampleSpecificConfigService $specificConfigService;

    public function __construct(
        ExampleConfigService $configService,
        ExampleSpecificConfigService $specificConfigService
    ) {
        $this->configService = $configService;
        $this->specificConfigService = $specificConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StorefrontRenderEvent::class => 'onStorefrontRender',
        ];
    }

    public function onStorefrontRender(StorefrontRenderEvent $event): void
    {
        $context = $event->getSalesChannelContext();

        /**
         * Set $salesChannelId param to get specific sales channel config value.
         */

        $globalStringConfig = $this->configService->getStringConfig();
        $stringConfig = $this->configService->getStringConfig($context->getSalesChannelId());
        $specificStringConfig = $this->specificConfigService->getSpecificStringConfig($context->getSalesChannelId());
    }
}
