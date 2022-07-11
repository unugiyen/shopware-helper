<?php

declare(strict_types=1);

namespace Wdt\ShopwareHelper\Mail\System\Command;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wdt\ShopwareHelper\Command\Traits\SymfonyStyleTrait;
use Wdt\ShopwareHelper\Mail\System\Command\Traits\MailTemplateTypeTrait;

class DeleteMailTemplateTypeCommand extends Command
{
    use SymfonyStyleTrait;
    use MailTemplateTypeTrait;

    protected static $defaultName = 'wdt:delete-mail-template-type';

    private const ARG_TECHNICAL_NAME = 'technicalName';

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
    }

    private function delete(string $technicalName): void
    {
        $mailTemplateType = $this->getMailTemplateType($technicalName, $this->mailTemplateTypeRepository);
        if (null === $mailTemplateType) {
            $this->io->info(
                sprintf(
                    'Trying to delete mail template type with technical name: "%1$s", but does not exist. Skipping.',
                    $technicalName
                )
            );

            return;
        }

        $data = [
            'id' => $mailTemplateType->getId(),
        ];
        $this->mailTemplateTypeRepository->delete([$data], Context::createDefaultContext());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->delete((string)$input->getArgument(self::ARG_TECHNICAL_NAME)); // @phpstan-ignore-line

        $this->io->success('Deleted mail template type');

        return self::SUCCESS;
    }
}
