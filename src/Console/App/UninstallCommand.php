<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\App;

use Lsv\TimeHarvestCli\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UninstallCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('app:uninstall')
            ->setDescription('Uninstall the configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->io->confirm('Are you sure you want to uninstall this?')) {
            unlink($this->configuration->getConfigurationFile());
            $this->io->success('Configuration has been removed');
        }

        return 0;
    }
}
