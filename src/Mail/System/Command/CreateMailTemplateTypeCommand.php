<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\System\Command;

use Exception;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wdt\ShopwareHelper\Command\Traits\SymfonyStyleTrait;
use Wdt\ShopwareHelper\Mail\System\Command\Traits\MailTemplateTypeTrait;

class CreateMailTemplateTypeCommand extends Command
{
    use SymfonyStyleTrait;
    use MailTemplateTypeTrait;

    protected static $defaultName = 'wdt:create-mail-template-type';

    private const ARG_TECHNICAL_NAME = 'technicalName';
    private const ARG_NAME = 'name';

    private EntityRepositoryInterface $mailTemplateTypeRepository;

    public function __construct(
        EntityRepositoryInterface $mailTemplateTypeRepository
    ) {
        parent::__construct();
        $this->mailTemplateTypeRepository = $mailTemplateTypeRepository;
    }

    protected function configure(): void
    {
        $this->addArgument(self::ARG_TECHNICAL_NAME, InputOption::VALUE_REQUIRED);
        $this->addArgument(self::ARG_NAME, InputOption::VALUE_REQUIRED);
    }

    private function upsertMailTemplateType(string $technicalName, string $name): void
    {
        $mailTemplateType = $this->getMailTemplateType($technicalName, $this->mailTemplateTypeRepository);

        if (null !== $mailTemplateType) {
            $this->io->info(
                sprintf(
                    'Trying to upsert mail template type with technical name: "%1$s", but already exists. Skipping.',
                    $technicalName
                )
            );

            return;
        }

        $data = [
            'id' => Uuid::randomHex(),
            'name' => $name,
            'technicalName' => $technicalName,
        ];

        $this->mailTemplateTypeRepository->upsert([$data], Context::createDefaultContext());
    }

    private function create(string $technicalName, string $name): void
    {
        if (preg_match('/\s/', $technicalName) || preg_match('~\d+~', $technicalName)) {
            throw new Exception(sprintf('Invalid technical name: "%1$s". Cannot contain spaces and numbers.', $technicalName));
        }

        $this->upsertMailTemplateType($technicalName, $name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->create(
            (string) $input->getArgument(self::ARG_TECHNICAL_NAME), // @phpstan-ignore-line
            (string) $input->getArgument(self::ARG_NAME) // @phpstan-ignore-line
        );

        $this->io->success('Created mail template type');

        return self::SUCCESS;
    }
}
