<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\Command;

use Exception;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\SalesChannel\SalesChannelCollection;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Wdt\ShopwareHelper\Mail\Data\MailTemplateData;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MailTemplateCommand extends Command
{
    protected static $defaultName = 'wdt:mail-template-import';

    private const BASE_DIR = 'Resources/views/email';

    private const HTML = 'html.twig';
    private const PLAIN = 'plain.twig';
    private const SUBJECT = 'subject.twig';

    private const OPT_LOCALE = 'locale';

    private SymfonyStyle $io;

    /** @var array<int, string> */
    private array $localeCodes = [];

    private EntityRepositoryInterface $salesChannelRepository;
    private EntityRepositoryInterface $mailTemplateTypeRepository;
    private EntityRepositoryInterface $mailTemplateRepository;

    public function __construct(
        EntityRepositoryInterface $salesChannelRepository,
        EntityRepositoryInterface $mailTemplateTypeRepository,
        EntityRepositoryInterface $mailTemplateRepository
    ) {
        parent::__construct();
        $this->salesChannelRepository = $salesChannelRepository;
        $this->mailTemplateTypeRepository = $mailTemplateTypeRepository;
        $this->mailTemplateRepository = $mailTemplateRepository;
    }

    protected function configure(): void
    {
        $this->addOption(self::OPT_LOCALE, null, InputOption::VALUE_OPTIONAL);
    }

    /**
     * @return array<int, string>
     */
    private function getTechnicalNames(): array
    {
        $technicalNames = [];

        $templateTypes = ($this->mailTemplateTypeRepository->search(new Criteria(), Context::createDefaultContext()))->getEntities();
        foreach ($templateTypes as $templateType) {
            $technicalNames[] = $templateType->getTechnicalName();
        }

        return $technicalNames;
    }

    private function getMailTemplate(string $technicalName): ?MailTemplateEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_AND,
                [
                    new EqualsFilter('mailTemplateType.technicalName', $technicalName),
                ]
            )
        );

        return ($this->mailTemplateRepository->search($criteria, Context::createDefaultContext()))->first();
    }

    /**
     * @param array<string, string> $localContentFile
     */
    private function validateLocalContent(array $localContentFile, string $technicalName): bool
    {
        if (!isset(
            $localContentFile[self::HTML],
            $localContentFile[self::PLAIN],
            $localContentFile[self::SUBJECT])
        ) {
            $this->io->note(
                sprintf(
                    'Skip upsert "%1$s" - local content files not complete: html, plain and twig',
                    $technicalName
                )
            );

            return false;
        }

        $html = $localContentFile[self::HTML];
        $plain = $localContentFile[self::PLAIN];
        $subject = $localContentFile[self::SUBJECT];

        if (empty($html) || empty($plain) || empty($subject)) {
            $this->io->note(
                sprintf(
                    'Skip upsert "%1$s" - local content file(s) empty',
                    $technicalName
                )
            );

            return false;
        }

        return true;
    }

    /**
     * @param array<int, string>                   $technicalNames
     * @param array<string, array<string, string>> $localContent
     */
    private function logNotFoundLocalTemplates(
        array $technicalNames,
        array $localContent
    ): void {
        foreach ($technicalNames as $key => $technicalName) {
            if (isset($localContent[$technicalName])) {
                unset($technicalNames[$key]);
            }
        }

        $newTemplates = array_values($technicalNames);

        $this->io->note('Skip upsert templates that were not found locally:');
        $this->io->listing($newTemplates);
    }

    /**
     * @param array<string, array<string, string>> $localContent
     */
    private function upsert(array $localContent, LanguageEntity $language): void
    {
        foreach ($localContent as $technicalName => $localContentFile) {
            $mailTemplate = $this->getMailTemplate($technicalName);

            if (null === $mailTemplate) {
                $this->io->note(
                    sprintf(
                        'Skip upsert "%1$s" - mail template not found',
                        $technicalName,
                    )
                );
                continue;
            }

            if (!$this->validateLocalContent($localContentFile, $technicalName)) {
                continue;
            }

            $html = $localContentFile[self::HTML];
            $plain = $localContentFile[self::PLAIN];
            $subject = $localContentFile[self::SUBJECT];

            if (Defaults::LANGUAGE_SYSTEM === $language->getId()) {
                $data = [
                    'id' => $mailTemplate->getId(),
                    'contentHtml' => $html,
                    'contentPlain' => $plain,
                    'subject' => $subject,
                ];
            } else {
                $data = [
                    'id' => $mailTemplate->getId(),
                    'translations' => [
                        [
                            'languageId' => $language->getId(),
                            'contentHtml' => $html,
                            'contentPlain' => $plain,
                            'subject' => $subject,
                        ],
                    ],
                ];
            }

            $this->mailTemplateRepository->upsert([$data], Context::createDefaultContext());

            $this->io->text(
                sprintf(
                    'Upsert "%1$s"',
                    $technicalName,
                )
            );
        }
    }

    /**
     * @param array<string, MailTemplateData> $mailTemplateDataCollection
     * @param array<int, string>              $technicalNames
     */
    private function import(
        array $mailTemplateDataCollection,
        array $technicalNames,
        string $localeCode = ''
    ): void {
        if (!empty($localeCode)) {
            $single = [];

            if (!isset($mailTemplateDataCollection[$localeCode])) {
                throw new Exception(sprintf('Locale code "%1$s" not set for sales channels', $localeCode));
            }
            $single[$localeCode] = $mailTemplateDataCollection[$localeCode];
            $mailTemplateDataCollection = $single;
        }

        /** @var MailTemplateData $mailTemplateData */
        foreach ($mailTemplateDataCollection as $mailTemplateData) {
            $locale = $mailTemplateData->getLocale();
            $language = $mailTemplateData->getLanguage();
            $localContent = $mailTemplateData->getLocalContent();

            if (empty($localContent)) {
                continue;
            }

            $this->io->success(
                sprintf(
                    'Upsert templates for language "%1$s" with ID "%2$s" and locale "%3$s"',
                    $language->getName(),
                    $language->getId(),
                    $locale->getCode(),
                )
            );

            $this->upsert($localContent, $language);
            $this->logNotFoundLocalTemplates($technicalNames, $localContent);
        }
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function getLocalContent(string $locale): array
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../../'.self::BASE_DIR.'/'.$locale);

        $localeContent = [];

        foreach ($finder as $file) {
            $relativePath = $file->getRelativePath();

            $localeContent[$relativePath][$file->getFilename()] = $file->getContents();
        }

        return $localeContent;
    }

    /**
     * @return EntityCollection<SalesChannelEntity>
     */
    private function getSalesChannels(): EntityCollection
    {
        $criteria = new Criteria();
        $criteria->addAssociations(['languages.locale']);

        return ($this->salesChannelRepository->search($criteria, Context::createDefaultContext()))->getEntities();
    }

    /**
     * @return array<string, MailTemplateData>
     */
    private function getMailTemplateDataCollection(): array
    {
        /** @var SalesChannelCollection $salesChannels */
        $salesChannels = $this->getSalesChannels();

        $data = [];

        foreach ($salesChannels as $salesChannel) {
            $languages = $salesChannel->getLanguages();
            if (null === $languages) {
                continue;
            }

            foreach ($languages as $language) {
                $locale = $language->getLocale();
                if (null === $locale) {
                    throw new Exception(sprintf('Locale %1$s not found', $locale));
                }

                $localeCode = $locale->getCode();

                if (in_array($localeCode, $this->localeCodes, true)) {
                    continue;
                }

                $this->localeCodes[] = $localeCode;

                $localContent = $this->getLocalContent($localeCode);

                $mailTemplateData = new MailTemplateData();
                $mailTemplateData->setLocalContent($localContent);
                $mailTemplateData->setLanguage($language);
                $mailTemplateData->setLocale($locale);

                $data[$locale->getCode()] = $mailTemplateData;
            }
        }

        return $data;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $optionLocale = (string) $input->getOption(self::OPT_LOCALE);
        $technicalNames = $this->getTechnicalNames();
        $mailTemplateDataCollection = $this->getMailTemplateDataCollection();

        $this->import($mailTemplateDataCollection, $technicalNames, $optionLocale);

        return self::SUCCESS;
    }
}
