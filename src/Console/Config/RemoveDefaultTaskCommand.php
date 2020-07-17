<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\Config;

use Lsv\TimeHarvestCli\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveDefaultTaskCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('config:remove:task')
            ->setDescription('Remove a default task for the current directory or global')
            ->addOption('global', null, InputOption::VALUE_NONE, 'Remove the the task from global');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration->removeTaskForDirectory((bool) $input->getOption('global'));

        return 0;
    }
}
