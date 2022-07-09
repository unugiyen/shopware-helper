<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\System\Command\Data;

use Shopware\Core\Framework\Struct\Struct;

class LogData extends Struct
{
    private array $missingContentFiles = [];
    private array $emptyContentFiles = [];
    private array $unknownLocalTemplates = [];
    private array $missingLocalTemplates = [];

    public const MISSING_CONTENT_FILES = 'missingContentFiles';
    public const EMPTY_CONTENT_FILES = 'emptyContentFiles';
    public const UNKNOWN_LOCAL_TEMPLATES = 'unknownLocalTemplates';
    public const MISSING_LOCAL_TEMPLATES = 'missingLocalTemplates';

    public function setMissingLocalTemplates(string $languageId, array $missingLocalTemplates): void
    {
        $this->missingLocalTemplates[$languageId] = $missingLocalTemplates;
    }

    public function getMissingLocalTemplates(): array
    {
        return $this->missingLocalTemplates;
    }

    public function setUnknownLocalTemplates(string $languageId, string $localTechnicalName): void
    {
        $this->unknownLocalTemplates[$languageId][] = $localTechnicalName;
    }

    public function getUnknownLocalTemplates(): array
    {
        return $this->unknownLocalTemplates;
    }

    public function setEmptyContentFiles(string $languageId, string $localTechnicalName): void
    {
        $this->emptyContentFiles[$languageId][] = $localTechnicalName;
    }

    public function getEmptyContentFiles(): array
    {
        return $this->emptyContentFiles;
    }

    public function setMissingContentFiles(string $languageId, string $localTechnicalName): void
    {
        $this->missingContentFiles[$languageId][] = $localTechnicalName;
    }

    public function getMissingContentFiles(): array
    {
        return $this->missingContentFiles;
    }
}
