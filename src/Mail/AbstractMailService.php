<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail;

use Exception;
use Shopware\Core\Content\Mail\Service\AbstractMailService as ShopwareAbstractMailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Wdt\ShopwareHelper\Mail\Data\MailData;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractMailService
{
    private const SUBJECT = 'subject';
    private const SENDER_EMAIL = 'senderEmail';
    private const SENDER_NAME = 'senderName';
    private const RECIPIENTS = 'recipients';
    private const SALES_CHANNEL_ID = 'salesChannelId';
    private const CONTENT_HTML = 'contentHtml';
    private const CONTENT_PLAIN = 'contentPlain';
    private const RECIPIENTS_CC = 'recipientsCc';
    private const RECIPIENTS_BCC = 'recipientsBcc';
    private const BIN_ATTACHMENTS = 'binAttachments';

    protected ShopwareAbstractMailService $shopwareMailService;
    protected EntityRepositoryInterface $mailTemplateRepository;

    public function __construct(
        ShopwareAbstractMailService $shopwareMailService,
        EntityRepositoryInterface $mailTemplateRepository
    ) {
        $this->shopwareMailService = $shopwareMailService;
        $this->mailTemplateRepository = $mailTemplateRepository;
    }

    protected function validateAndGetTemplates(array $templateTechnicalNames, SalesChannelContext $context): array
    {
        $templates = [];

        foreach ($templateTechnicalNames as $technicalName) {
            $template = $this->getMailTemplate($technicalName, $context);
            if (null === $template) {
                throw new Exception(sprintf('Template with technical name "%1$s" not found', $technicalName));
            }
            $templates[$technicalName] = $template;
        }

        return $templates;
    }

    protected function setMailData(MailData $mailData, SalesChannelContext $context): array
    {
        $template = $mailData->getTemplate();

        $data = [];

        $data[self::SUBJECT] = $template->getSubject();
        $data[self::SENDER_EMAIL] = $mailData->getSenderEmail();
        $data[self::SENDER_NAME] = $template->getSenderName();
        $data[self::RECIPIENTS] = $mailData->getRecipients();
        $data[self::SALES_CHANNEL_ID] = $context->getSalesChannelId();
        $data[self::CONTENT_HTML] = $template->getContentHtml();
        $data[self::CONTENT_PLAIN] = $template->getContentPlain();

        $recipientsCc = $mailData->getRecipientsCc();
        if (null !== $recipientsCc) {
            $data[self::RECIPIENTS_CC] = $recipientsCc;
        }

        $recipientsBcc = $mailData->getRecipientsBcc();
        if (!empty($recipientsBcc)) {
            $data[self::RECIPIENTS_BCC] = $recipientsBcc;
        }

        $binAttachments = $mailData->getBinAttachments();
        if (!empty($binAttachments)) {
            $data[self::BIN_ATTACHMENTS] = $binAttachments;
        }

        return $data;
    }

    public function getMailTemplate(string $technicalName, SalesChannelContext $context): ?MailTemplateEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_AND, [new EqualsFilter('mailTemplateType.technicalName', $technicalName)]
            )
        );

        return ($this->mailTemplateRepository->search($criteria, $context->getContext()))->first();
    }

    public function send(MailData $mailData, SalesChannelContext $context): void
    {
        $shopwareMailData = $this->setMailData($mailData, $context);
        $this->shopwareMailService->send($shopwareMailData, $context->getContext(), $mailData->getTemplateData());
    }
}
