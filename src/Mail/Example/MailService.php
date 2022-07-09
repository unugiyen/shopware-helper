<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\Example;

use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Wdt\ShopwareHelper\Mail\AbstractMailService;
use Wdt\ShopwareHelper\Mail\Data\MailData;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class MailService extends AbstractMailService
{
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

    public function sendSpecificEmail(
        MailInputData $mailInputData,
        SalesChannelContext $context
    ): void {
        $templates = $this->validateAndGetTemplates(['specific_template'], $context);
        $someTemplate = $templates['some_template'];

        $senderEmail = 'example@mail.com';
        $senderName = 'Example';

        $mailData = new MailData(
            $someTemplate,
            $senderName,
            $senderEmail,
            [
                $senderEmail => $senderName,
            ]
        );

        $this->send($mailData, $context);
    }

    private function sendSomeOtherMail(
        MailTemplateEntity $template,
        MailInputData $mailInputData,
        SalesChannelContext $context
    ): void {
        $senderEmail = 'example@mail.com';
        $senderName = 'Example';

        $mailData = new MailData(
            $template,
            $senderName,
            $senderEmail,
            [
                $senderEmail => $senderName,
            ]
        );

        $this->send($mailData, $context);
    }

    private function sendSomeMail(
        MailTemplateEntity $template,
        MailInputData $mailInputData,
        SalesChannelContext $context
    ): void {
        $senderEmail = 'example@mail.com';
        $senderName = 'Example';

        $mailData = new MailData(
            $template,
            $senderName,
            $senderEmail,
            [
                $senderEmail => $senderName,
            ]
        );

        $mailData->setTemplateData([
            'foo' => true,
            'baz' => 'string',
        ]);

        $this->send($mailData, $context);
    }
}
