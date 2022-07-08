<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config;

use Exception;
use ReflectionClass;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Wdt\ShopwareHelper\Config\ExampleConfigService;
use Wdt\ShopwareHelper\Config\ExampleSpecificConfigService;
use Wdt\ShopwareHelper\WdtShopwareHelper;

class ExampleConfigSubscriber implements EventSubscriberInterface
{
    private ExampleConfigService $configService;
    private ExampleSpecificConfigService $specificConfigService;

    public function __construct(
        ExampleConfigService         $configService,
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

    public function onStorefrontRender(StorefrontRenderEvent $event)
    {
        $context = $event->getSalesChannelContext();

        /**
         * Set $salesChannelId param to get specific sales channel value.
         */

        $globalStringConfig = $this->configService->getStringConfig();
        $stringConfig = $this->configService->getStringConfig($context->getSalesChannelId());
        $specificStringConfig = $this->specificConfigService->getSpecificStringConfig($context->getSalesChannelId());
    }
}
