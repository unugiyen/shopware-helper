<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Config\Example;

use Generator;
use Shopware\Core\Framework\Struct\Struct;
use Wdt\ShopwareHelper\Config\ConfigDataInterface;

class ConfigData extends Struct implements ConfigDataInterface
{
    private string $stringConfig = '';
    private string $otherStringConfig = '';
    private Generator $commaStringConfig;
    private array $commaStringConfigAsArray = [];

    public function getStringConfig(): string
    {
        return $this->stringConfig;
    }

    public function setStringConfig(string $stringConfig): ConfigData
    {
        $this->stringConfig = $stringConfig;

        return $this;
    }

    public function getOtherStringConfig(): string
    {
        return $this->otherStringConfig;
    }

    public function setOtherStringConfig(string $otherStringConfig): ConfigData
    {
        $this->otherStringConfig = $otherStringConfig;

        return $this;
    }

    public function getCommaStringConfig(): Generator
    {
        return $this->commaStringConfig;
    }

    public function setCommaStringConfig(Generator $commaStringConfig): ConfigData
    {
        $this->commaStringConfig = $commaStringConfig;

        return $this;
    }

    public function getCommaStringConfigAsArray(): array
    {
        return $this->commaStringConfigAsArray;
    }

    public function setCommaStringConfigAsArray(array $commaStringConfigAsArray): ConfigData
    {
        $this->commaStringConfigAsArray = $commaStringConfigAsArray;

        return $this;
    }
}
