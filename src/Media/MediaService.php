<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Media;

use Shopware\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailCollection;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\MediaType\ImageType;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class MediaService
{
    private EntityRepositoryInterface $mediaRepository;

    public function __construct(
        EntityRepositoryInterface $mediaRepository
    ) {
        $this->mediaRepository = $mediaRepository;
    }

    private function getMedia(string $id): ?MediaEntity
    {
        $criteria = new Criteria([$id]);
        $criteria->addAssociations(['thumbnails']);

        return $this->mediaRepository->search($criteria, Context::createDefaultContext())->first();
    }

    private function filterMediaThumbnail(
        MediaThumbnailCollection $thumbnails,
        int $width,
        int $height
    ): ?MediaThumbnailEntity {
        return $thumbnails
            ->filterByProperty('width', $width)
            ->filterByProperty('height', $height)
            ->first();
    }

    public function getMediaThumbnailUrl(
        string $mediaId,
        int $width = 400,
        int $height = 400,
        bool $originalUrl = true
    ): ?string {
        $media = $this->getMedia($mediaId);
        if (null === $media) {
            return null;
        }

        $type = $media->getMediaType();
        if (!$type instanceof ImageType) {
            return null;
        }

        $thumbnails = $media->getThumbnails();
        if (null === $thumbnails || 0 === $thumbnails->count()) {
            return null;
        }

        $thumbnail = $this->filterMediaThumbnail($thumbnails, $width, $height);
        if (null !== $thumbnail) {
            return $thumbnail->getUrl();
        }

        if ($originalUrl) {
            return $media->getUrl();
        }

        return null;
    }
}
