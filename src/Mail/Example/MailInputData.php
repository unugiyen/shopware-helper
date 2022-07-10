<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\Example;

use Shopware\Core\Framework\Struct\Struct;
use Wdt\ShopwareHelper\Mail\MailInputDataInterface;

class MailInputData extends Struct implements MailInputDataInterface
{
    private string $dir;

    public function getDir(): string
    {
        return $this->dir;
    }

    public function setDir(string $dir): void
    {
        $this->dir = $dir;
    }
}
