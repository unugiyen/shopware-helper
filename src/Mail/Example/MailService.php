<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\Example;

use League\Flysystem\FilesystemInterface;
use Shopware\Core\Content\Mail\Service\AbstractMailService as ShopwareAbstractMailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Wdt\ShopwareHelper\Mail\AbstractMailService;
use Wdt\ShopwareHelper\Mail\Data\MailData;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class MailService extends AbstractMailService
{
    public function __construct(
        ShopwareAbstractMailService $shopwareMailService,
        EntityRepositoryInterface $mailTemplateRepository,
        FilesystemInterface $filesystem
    ) {
        parent::__construct($shopwareMailService, $mailTemplateRepository, $filesystem);
    }

    public function sendSpecificEmail(
        MailInputData $mailInputData,
        SalesChannelContext $context
    ): void {
        $templates = $this->validateAndGetTemplates(['specific_template'], $context);
        $someTemplate = $templates['specific_template'];

        $senderEmail = 'sender@example.com';
        $senderName = 'Example';

        $binAttachments = $this->generateBinAttachments(['bundles/wdtshopwarehelper/example/shopware.png']);

        $mailData = (new MailData(
            $someTemplate,
            $senderName,
            $senderEmail,
            [
                'recipient1@example.com' => 'Recipient 1',
                'recipient2@example.com' => 'Recipient 2',
            ]
        ))
        ->setRecipientsCc('me1@live.com')
        ->setBinAttachments($binAttachments)
        ;

        $this->send($mailData, $context);
    }

    public function sendMails(
        MailInputData $mailInputData,
        SalesChannelContext $context
    ): void {
        $templates = $this->validateAndGetTemplates(['some_template', 'some_other_template'], $context);
        $someTemplate = $templates['some_template'];
        $someOtherTemplate = $templates['some_other_template'];

        $this->sendSomeMail($someTemplate, $mailInputData, $context);
        $this->sendSomeOtherMail($someOtherTemplate, $mailInputData, $context);
    }

    private function sendSomeOtherMail(
        MailTemplateEntity $template,
        MailInputData $mailInputData,
        SalesChannelContext $context
    ): void {
    }

    private function sendSomeMail(
        MailTemplateEntity $template,
        MailInputData $mailInputData,
        SalesChannelContext $context
    ): void {
    }
}
