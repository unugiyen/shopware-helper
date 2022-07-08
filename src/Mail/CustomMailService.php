<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail;

use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\InputBag;
use Wdt\ShopwareHelper\Mail\Data\MailData;

class CustomMailService extends AbstractMailService
{
    public function sendMails(
        InputBag $inputBag,
        SalesChannelContext $context
    ): void {
        $templates = $this->validateAndGetTemplates(['contact_form'], $context);
        $template = $templates['contact_form'];

        $this->sendMail($template, $inputBag, $context);
    }

    private function sendMail(
        MailTemplateEntity $template,
        InputBag $inputBag,
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
            'fieldEmail' => $inputBag->get('fieldEmail'),
            'fieldTextArea' => $inputBag->get('fieldTextArea'),
            'fieldText' => $inputBag->get('fieldText'),
            'fieldSelect' => $inputBag->get('fieldSelect'),
        ]);

        $this->send($mailData, $context);
    }
}
