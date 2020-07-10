<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\Time;

use Lsv\TimeHarvestCli\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EndTimeEntryCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('time:stop')
            ->setAliases(['stop'])
            ->setDescription('Stop a running time for a project');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->client->stopTimer();

        return 0;
    }
}
