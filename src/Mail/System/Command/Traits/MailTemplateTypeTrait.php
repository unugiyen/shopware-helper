<?php

namespace Wdt\ShopwareHelper\Mail\System\Command\Traits;

use Shopware\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;

trait MailTemplateTypeTrait
{
    private function getMailTemplateType(
        string $technicalName,
        EntityRepositoryInterface $mailTemplateTypeRepository
    ): ?MailTemplateTypeEntity {
        $criteria = new Criteria();
        $criteria->setLimit(1);
        $criteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_AND,
                [
                    new EqualsFilter('technicalName', $technicalName),
                ]
            )
        );

        return ($mailTemplateTypeRepository->search($criteria, Context::createDefaultContext()))->first();
    }
}
