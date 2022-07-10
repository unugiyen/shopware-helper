<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\Data;

use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\Struct\Struct;

class MailData extends Struct
{
    protected string $senderName;
    protected string $senderEmail;
    protected array $recipients = [];
    protected MailTemplateEntity $template;
    protected array $templateData = [];
    protected ?string $recipientsCc = null;
    protected ?string $recipientsBcc = null;
    protected array $binAttachments = [];
    protected array $mediaIds = [];

    public function __construct(
        MailTemplateEntity $template,
        string $senderName,
        string $senderEmail,
        array $recipients
    ) {
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
        $this->recipients = $recipients;
        $this->template = $template;
    }

    public function getRecipientsCc(): ?string
    {
        return $this->recipientsCc;
    }

    public function setRecipientsCc(?string $recipientsCc): MailData
    {
        $this->recipientsCc = $recipientsCc;

        return $this;
    }

    public function getRecipientsBcc(): ?string
    {
        return $this->recipientsBcc;
    }

    public function setRecipientsBcc(?string $recipientsBcc): MailData
    {
        $this->recipientsBcc = $recipientsBcc;

        return $this;
    }

    public function getTemplate(): MailTemplateEntity
    {
        return $this->template;
    }

    public function setTemplate(MailTemplateEntity $template): MailData
    {
        $this->template = $template;

        return $this;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function setRecipients(array $recipients): MailData
    {
        $this->recipients = $recipients;

        return $this;
    }

    public function getTemplateData(): array
    {
        return $this->templateData;
    }

    public function setTemplateData(array $templateData): MailData
    {
        $this->templateData = $templateData;

        return $this;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function setSenderName(string $senderName): MailData
    {
        $this->senderName = $senderName;

        return $this;
    }

    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    public function setSenderEmail(string $senderEmail): MailData
    {
        $this->senderEmail = $senderEmail;

        return $this;
    }

    public function getBinAttachments(): array
    {
        return $this->binAttachments;
    }

    public function setBinAttachments(array $binAttachments): MailData
    {
        $this->binAttachments = $binAttachments;

        return $this;
    }

    public function getMediaIds(): array
    {
        return $this->mediaIds;
    }

    public function setMediaIds(array $mediaIds): MailData
    {
        $this->mediaIds = $mediaIds;

        return $this;
    }
}
