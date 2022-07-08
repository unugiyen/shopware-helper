<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config\Example;

use Wdt\ShopwareHelper\WdtShopwareHelper;

trait ConfigTrait
{
    protected function getPluginClass(): string
    {
        return WdtShopwareHelper::class;
    }
}
