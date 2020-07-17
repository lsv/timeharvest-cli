<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\Config;

use Lsv\TimeHarvestCli\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveDefaultProjectCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('config:remove:project')
            ->setDescription('Removes the default project for the current directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration->removeProjectForDirectory();

        return 0;
    }
}
