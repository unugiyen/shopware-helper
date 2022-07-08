<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\Data;

use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\Locale\LocaleEntity;

class MailTemplateData
{
    /** @var array<string, array<string, string>> */
    private array $localContent = [];
    private LanguageEntity $language;
    private LocaleEntity $locale;

    public function getLocale(): LocaleEntity
    {
        return $this->locale;
    }

    public function setLocale(LocaleEntity $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getLocalContent(): array
    {
        return $this->localContent;
    }

    /**
     * @param array<string, array<string, string>> $localContent
     */
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
