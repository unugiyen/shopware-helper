<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\Data;

use Shopware\Core\Content\MailTemplate\MailTemplateEntity;

class MailData
{
    protected ?string $senderName = null;
    protected ?string $senderEmail = null;

    /** @var array<string, string> */
    protected array $recipients = [];
    protected MailTemplateEntity $template;

    /** @var array<string, mixed> */
    protected array $templateData = [];
    protected ?string $recipientsCc = null;
    protected ?string $recipientsBcc = null;

    /** @var array<string, string> */
    protected ?array $binAttachments = [];

    /**
     * @param array<string, string> $recipients
     */
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

    public function setRecipientsCc(?string $recipientsCc): void
    {
        $this->recipientsCc = $recipientsCc;
    }

    public function getRecipientsBcc(): ?string
    {
        return $this->recipientsBcc;
    }

    public function setRecipientsBcc(?string $recipientsBcc): void
    {
        $this->recipientsBcc = $recipientsBcc;
    }

    public function getTemplate(): MailTemplateEntity
    {
        return $this->template;
    }

    public function setTemplate(MailTemplateEntity $template): void
    {
        $this->template = $template;
    }

    /**
     * @return array<string, string>
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @param array<string, string> $recipients
     */
    public function setRecipients(array $recipients): void
    {
        $this->recipients = $recipients;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTemplateData(): array
    {
        return $this->templateData;
    }

    /**
     * @param array<string, mixed> $templateData
     */
    public function setTemplateData(array $templateData): void
    {
        $this->templateData = $templateData;
    }

    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    public function setSenderName(?string $senderName): void
    {
        $this->senderName = $senderName;
    }

    public function getSenderEmail(): ?string
    {
        return $this->senderEmail;
    }

    public function setSenderEmail(?string $senderEmail): void
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * @return array<string, string>
     */
    public function getBinAttachments(): ?array
    {
        return $this->binAttachments;
    }

    /**
     * @param array<string, string> $binAttachments
     */
    public function setBinAttachments(?array $binAttachments): void
    {
        $this->binAttachments = $binAttachments;
    }
}
