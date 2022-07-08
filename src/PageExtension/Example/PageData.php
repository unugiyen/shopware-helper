<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\PageExtension\Example;

use Shopware\Core\Framework\Struct\Struct;

class PageData extends Struct
{
    private ?string $foo = null;
    private array $baz = [];
    private bool $bar = false;

    public function getFoo(): ?string
    {
        return $this->foo;
    }

    public function setFoo(?string $foo): void
    {
        $this->foo = $foo;
    }

    public function getBaz(): array
    {
        return $this->baz;
    }

    public function setBaz(array $baz): void
    {
        $this->baz = $baz;
    }

    public function isBar(): bool
    {
        return $this->bar;
    }

    public function setBar(bool $bar): void
    {
        $this->bar = $bar;
    }
}
