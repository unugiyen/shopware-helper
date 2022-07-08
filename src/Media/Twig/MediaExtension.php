<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Media\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Wdt\ShopwareHelper\Media\MediaService;

class MediaExtension extends AbstractExtension
{
    private MediaService $mediaService;

    public function __construct(
        MediaService $mediaService
    ) {
        $this->mediaService = $mediaService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('wdtGetMediaThumbnailUrl', [$this, 'getMediaThumbnailUrl']),
        ];
    }

    public function getMediaThumbnailUrl(string $mediaId, int $width = 400, int $height = 400, bool $originalUrl = true): ?string
    {
        return $this->mediaService->getMediaThumbnailUrl($mediaId, $width, $height, $originalUrl);
    }
}
