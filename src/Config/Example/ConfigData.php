<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config\Example;

use Wdt\ShopwareHelper\Config\ConfigDataInterface;

class ConfigData implements ConfigDataInterface
{
    private string $stringConfig = '';
    private string $specificStringConfig = '';

    public function getStringConfig(): string
    {
        return $this->stringConfig;
    }

    public function setStringConfig(string $stringConfig): void
    {
        $this->stringConfig = $stringConfig;
    }

    public function getSpecificStringConfig(): string
    {
        return $this->specificStringConfig;
    }

    public function setSpecificStringConfig(string $specificStringConfig): void
    {
        $this->specificStringConfig = $specificStringConfig;
    }
}
