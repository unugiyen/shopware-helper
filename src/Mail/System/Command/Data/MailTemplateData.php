<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\System\Command\Data;

use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\Language\LanguageEntity;

class MailTemplateData extends Struct
{
    private array $localContent = [];
    private LanguageEntity $language;
    private string $localeCode;

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(string $localeCode): void
    {
        $this->localeCode = $localeCode;
    }

    public function getLocalContent(): array
    {
        return $this->localContent;
    }

    public function setLocalContent(array $localContent): void
    {
        $this->localContent = $localContent;
    }

    public function getLanguage(): LanguageEntity
    {
        return $this->language;
    }

    public function setLanguage(LanguageEntity $language): void
    {
        $this->language = $language;
    }
}
