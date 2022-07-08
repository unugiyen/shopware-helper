<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Subscriber;

use Exception;
use ReflectionClass;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Wdt\ShopwareHelper\Config\ExampleConfigService;
use Wdt\ShopwareHelper\Config\ExampleSpecificConfigService;
use Wdt\ShopwareHelper\WdtShopwareHelper;

class ProductSubscriber implements EventSubscriberInterface
{
    public function __construct(

    ) {

    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $page = $event->getPage();
        $product = $page->getProduct();
        $context = $event->getSalesChannelContext();

        $page->addExtension('productData', new ArrayStruct([

        ]));
    }
}
