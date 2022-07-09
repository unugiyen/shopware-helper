<?php

namespace Wdt\ShopwareHelper\Command\Traits;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;

trait SymfonyStyleTrait
{
    private SymfonyStyle $io;

    private static string $progressStart = 'start';
    private static string $progressFinish = 'stop';

    private function setProgressBar(string $name, int $count, string $message): ProgressBar
    {
        $progressBar = $this->io->createProgressBar($count);
        $progressBar::setFormatDefinition($name, '%current%/%max% [%bar%] %percent:3s%% - %message%');
        $progressBar->setFormat($name);
        $progressBar->setOverwrite(true);
        $progressBar->setMessage($message);

        return $progressBar;
    }

    protected function setProgressBarStartFinish(string $startFinish, ProgressBar $progressBar): void
    {
        switch ($startFinish) {
            case self::$progressStart:
                $this->io->newLine();
                $progressBar->start();
                break;
            case self::$progressFinish:
                $progressBar->finish();
                $this->io->newLine();
                $this->io->newLine();
                break;
        }
    }
}
