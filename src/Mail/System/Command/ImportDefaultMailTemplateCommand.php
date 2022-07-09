<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\System\Command;

use Exception;
use Shopware\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeCollection;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\System\Language\LanguageCollection;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Wdt\ShopwareHelper\Command\Traits\SymfonyStyleTrait;
use Wdt\ShopwareHelper\Mail\System\Command\Data\LogData;
use Wdt\ShopwareHelper\Mail\System\Command\Data\MailTemplateData;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImportDefaultMailTemplateCommand extends Command
{
    use SymfonyStyleTrait;

    protected static $defaultName = 'wdt:mail-template-import';

    private const BASE_DIR = 'Resources/views/email';

    private const HTML = 'html.twig';
    private const PLAIN = 'plain.twig';
    private const SUBJECT = 'subject.twig';

    private const OPT_LOCALE = 'locale';

    private array $cachedLocaleCodes = [];
    private LanguageCollection $cachedLanguages;
    private LogData $logData;

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

    private function generateLogs(string $type, array $logs): void
    {
        $cachedLanguages = $this->cachedLanguages;

        foreach ($logs as $languageId => $technicalNames) {
            $cachedLanguage = $cachedLanguages->get($languageId);
            if (null === $cachedLanguage) {
                continue;
            }

            $cachedLanguageLocale = $cachedLanguage->getLocale();
            if (null === $cachedLanguageLocale) {
                continue;
            }
            $cachedLanguageLocaleCode = $cachedLanguageLocale->getCode();

            $this->io->info(
                sprintf(
                    'Logging "%1$s" (Language ID: %2$s) (Locale: %3$s)',
                    $type, $languageId, $cachedLanguageLocaleCode
                )
            );

            foreach ($technicalNames as $technicalName) {
                $this->io->text($technicalName);
            }
        }
    }

    private function displayLogs(): void
    {
        $logData = $this->logData;
        $missingContentFiles = $logData->getMissingContentFiles();
        $emptyContentFiles = $logData->getEmptyContentFiles();
        $unknownLocalTemplates = $logData->getUnknownLocalTemplates();
        $missingLocalTemplates = $logData->getMissingLocalTemplates();

        $this->generateLogs(LogData::MISSING_LOCAL_TEMPLATES, $missingLocalTemplates);
        $this->generateLogs(LogData::UNKNOWN_LOCAL_TEMPLATES, $unknownLocalTemplates);
        $this->generateLogs(LogData::MISSING_CONTENT_FILES, $missingContentFiles);
        $this->generateLogs(LogData::EMPTY_CONTENT_FILES, $emptyContentFiles);

        $this->io->newLine();
    }

    private function init(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->cachedLanguages = new LanguageCollection();
        $this->logData = new LogData();
    }

    private function getSalesChannels(): EntityCollection
    {
        $criteria = new Criteria();
        $criteria->addAssociations(['languages.locale']);

        return ($this->salesChannelRepository->search($criteria, Context::createDefaultContext()))->getEntities();
    }

    private function getShopTechnicalNames(): array
    {
        $shopTechnicalNames = [];

        /** @var MailTemplateTypeCollection $templateTypes */
        $templateTypes = ($this->mailTemplateTypeRepository->search(new Criteria(), Context::createDefaultContext()))->getEntities();
        foreach ($templateTypes as $templateType) {
            $shopTechnicalNames[] = $templateType->getTechnicalName();
        }

        return $shopTechnicalNames;
    }

    private function getShopDefaultMailTemplate(string $technicalName): ?MailTemplateEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_AND,
                [
                    new EqualsFilter('systemDefault', true), //prevent newly created templates being overwritten
                    new EqualsFilter('mailTemplateType.technicalName', $technicalName),
                ]
            )
        );

        return ($this->mailTemplateRepository->search($criteria, Context::createDefaultContext()))->first();
    }

    private function validateLocalContent(array $localContentFile, string $localTechnicalName, string $languageId): bool
    {
        if (!isset(
            $localContentFile[self::HTML],
            $localContentFile[self::PLAIN],
            $localContentFile[self::SUBJECT])
        ) {
            $this->logData->setMissingContentFiles($languageId, $localTechnicalName);

            return false;
        }

        $html = $localContentFile[self::HTML];
        $plain = $localContentFile[self::PLAIN];
        $subject = $localContentFile[self::SUBJECT];

        if (empty($html) || empty($plain) || empty($subject)) {
            $this->logData->setEmptyContentFiles($languageId, $localTechnicalName);

            return false;
        }

        return true;
    }

    private function upsert(array $localContent, string $languageId): void
    {
        foreach ($localContent as $localTechnicalName => $localContentFile) {
            $shopDefaultTemplate = $this->getShopDefaultMailTemplate($localTechnicalName);

            if (null === $shopDefaultTemplate) {
                //local templates that could not be found in shop
                $this->logData->setUnknownLocalTemplates($languageId, $localTechnicalName);

                continue;
            }

            if (!$this->validateLocalContent($localContentFile, $localTechnicalName, $languageId)) {
                continue;
            }

            $html = $localContentFile[self::HTML];
            $plain = $localContentFile[self::PLAIN];
            $subject = $localContentFile[self::SUBJECT];

            if (Defaults::LANGUAGE_SYSTEM === $languageId) {
                $data = [
                    'id' => $shopDefaultTemplate->getId(),
                    'contentHtml' => $html,
                    'contentPlain' => $plain,
                    'subject' => $subject,
                ];
            } else {
                $data = [
                    'id' => $shopDefaultTemplate->getId(),
                    'translations' => [
                        [
                            'languageId' => $languageId,
                            'contentHtml' => $html,
                            'contentPlain' => $plain,
                            'subject' => $subject,
                        ],
                    ],
                ];
            }

            $this->mailTemplateRepository->upsert([$data], Context::createDefaultContext());

            $this->io->text(sprintf('Importing %1$s', $localTechnicalName));
        }
    }

    private function import(
        array $localMailTemplateDataCollection,
        array $shopTechnicalNames,
        string $inputLocaleCode = ''
    ): void {
        if (!empty($inputLocaleCode)) {
            if (!isset($localMailTemplateDataCollection[$inputLocaleCode])) {
                throw new Exception(sprintf('Local directory of locale "%1$s" not found', $inputLocaleCode));
            }

            $localMailTemplateDataCollection = array_filter(
                $localMailTemplateDataCollection,
                function (MailTemplateData $mailTemplateData) use ($inputLocaleCode) {
                    return $mailTemplateData->getLocaleCode() === $inputLocaleCode;
                }
            );
        }

        /** @var MailTemplateData $singleMailTemplateData */
        $singleMailTemplateData = reset($localMailTemplateDataCollection);

        if (empty($singleMailTemplateData->getLocalContent())) {
            $this->io->note('Nothing to be imported.');

            return;
        }

        /** @var MailTemplateData $mailTemplateData */
        foreach ($localMailTemplateDataCollection as $dataCollectionLocaleCode => $mailTemplateData) {
            $language = $mailTemplateData->getLanguage();
            $languageId = $language->getId();
            $localContent = $mailTemplateData->getLocalContent();

            if (empty($localContent)) {
                continue;
            }

            $this->io->title(
                sprintf(
                    'Importing templates for shop language "%1$s" (%3$s) (ID: %2$s)',
                    $language->getName(),
                    $languageId,
                    $dataCollectionLocaleCode,
                )
            );

            foreach ($shopTechnicalNames as $key => $shopTechnicalName) {
                if (isset($localContent[$shopTechnicalName])) {
                    unset($shopTechnicalNames[$key]);
                }
            }

            $this->logData->setMissingLocalTemplates($languageId, array_values($shopTechnicalNames));

            $this->upsert($localContent, $languageId);
        }

        $this->io->success('Import done');
    }

    private function getLocalContent(string $locale): array
    {
        $finder = new Finder();
        $finder->files()->in(
            sprintf('%1$s/../%2$s/%3$s', __DIR__, self::BASE_DIR, $locale)
        );

        $data = [];

        foreach ($finder as $file) {
            $data[$file->getRelativePath()][$file->getFilename()] = $file->getContents();
        }

        return $data;
    }

    private function getLocalMailTemplateDataCollection(EntityCollection $salesChannels): array
    {
        $localMailTemplateCollection = [];

        /** @var SalesChannelEntity $salesChannel */
        foreach ($salesChannels as $salesChannel) {
            $languages = $salesChannel->getLanguages();
            if (null === $languages) {
                continue;
            }

            foreach ($languages as $language) {
                $locale = $language->getLocale();
                if (null === $locale) {
                    continue;
                }

                $localeCode = $locale->getCode();

                //if sales channels have the same language, skip collecting because it's already done before
                if (in_array($localeCode, $this->cachedLocaleCodes, true)) {
                    continue;
                }

                $this->cachedLocaleCodes[] = $localeCode;
                $this->cachedLanguages->add($language);

                try {
                    $localContent = $this->getLocalContent($localeCode);
                } catch (Exception $e) {
                    $localContent = [];
                }

                $mailTemplateData = new MailTemplateData();
                $mailTemplateData->setLocalContent($localContent);
                $mailTemplateData->setLanguage($language);
                $mailTemplateData->setLocaleCode($localeCode);

                $localMailTemplateCollection[$localeCode] = $mailTemplateData;
            }
        }

        return $localMailTemplateCollection;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input, $output);

        $optionLocale = (string) $input->getOption(self::OPT_LOCALE); // @phpstan-ignore-line

        $shopTechnicalNames = $this->getShopTechnicalNames();
        $salesChannels = $this->getSalesChannels();

        //collect data based on shop sales channels
        $localMailTemplateDataCollection = $this->getLocalMailTemplateDataCollection($salesChannels);

        $this->import($localMailTemplateDataCollection, $shopTechnicalNames, $optionLocale);
        $this->displayLogs();

        return self::SUCCESS;
    }
}
