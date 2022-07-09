<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\PageExtension\Example;

use Shopware\Storefront\Event\StorefrontRenderEvent;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PageExtensionSubscriber implements EventSubscriberInterface
{
    private PageDataService $pageDataService;

    public function __construct(PageDataService $pageDataService)
    {
        $this->pageDataService = $pageDataService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
            StorefrontRenderEvent::class => 'onStorefrontRender',
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $page = $event->getPage();
        $page->addExtension(PageExtensionEnum::PAGE_DATA, $this->pageDataService->getPageData());
    }

    public function onStorefrontRender(StorefrontRenderEvent $event): void
    {
        $event->setParameter(PageExtensionEnum::PAGE_DATA, $this->pageDataService->getPageData());
    }
}
