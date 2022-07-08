<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\PageExtension\Example;

use Shopware\Storefront\Event\StorefrontRenderEvent;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PageDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
            StorefrontRenderEvent::class => 'onStorefrontRender',
        ];
    }

    private function getPageData(): PageData
    {
        $pageData = new PageData();
        $pageData->setFoo('string');
        $pageData->setBaz([1, 2, 3, 4, 5]);

        return $pageData;
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        // Data will be available in Twig templates in page extensions of product detail.

        $page = $event->getPage();
        $page->addExtension(PageExtensionEnum::PAGE_DATA, $this->getPageData());
    }

    public function onStorefrontRender(StorefrontRenderEvent $event): void
    {
        // Data will be available in Twig templates of every Shopware frontend core routes

        $event->setParameter(PageExtensionEnum::PAGE_DATA, $this->getPageData());
    }
}
