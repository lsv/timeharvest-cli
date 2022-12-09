<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\Config;

use Lsv\TimeHarvestCli\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetDefaultTaskCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('config:set:task')
            ->setDescription('Sets a default task for the current directory or global')
            ->addOption(
                'task',
                't',
                InputOption::VALUE_REQUIRED,
                'The name of the task to associate with this directory or global'
            )
            ->addOption('global', null, InputOption::VALUE_NONE, 'Set the task as global')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // @codeCoverageIgnoreStart
        if (!is_string($input->getOption('task'))) {
            throw new \RuntimeException('Could not parse option "task"');
        }
        // @codeCoverageIgnoreEnd

        $this->configuration->setTaskForDirectory($input->getOption('task'), (bool) $input->getOption('global'));

        return 0;
    }
}
