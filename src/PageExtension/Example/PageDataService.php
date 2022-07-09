<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\PageExtension\Example;

class PageDataService
{
    public function getPageData(): PageData
    {
        $pageData = new PageData();
        $pageData->setFoo('string');
        $pageData->setBaz([1, 2, 3, 4, 5]);

        return $pageData;
    }
}
