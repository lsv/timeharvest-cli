<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\Time;

use Lsv\TimeHarvestCli\Console\AbstractCommand;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StartTimeEntryCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('time:start')
            ->setAliases(['start'])
            ->setDescription('Start a running time for a project')
            ->addOption(
                'project',
                'p',
                InputOption::VALUE_OPTIONAL,
                'The ID of the project to associate with the time entry, or a string of the project name to filter them'
            )
            ->addOption(
                'task',
                't',
                InputOption::VALUE_OPTIONAL,
                'The ID of the task to associate with the time entry, or a string of the task to filter them'
            )
            ->addOption('no-select', null, InputOption::VALUE_NONE, 'Do not use project and task select')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        parent::interact($input, $output);

        $input->setOption('project', $this->selectProject($input));

        // @codeCoverageIgnoreStart
        if (!is_string($input->getOption('project'))) {
            throw new RuntimeException('Could not parse option "project"');
        }
        // @codeCoverageIgnoreEnd

        $input->setOption('task', $this->selectTask($input, $input->getOption('project')));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // @codeCoverageIgnoreStart
        if (!is_string($input->getOption('project'))) {
            throw new RuntimeException('Could not parse option "project"');
        }

        if (!is_string($input->getOption('task'))) {
            throw new RuntimeException('Could not parse option "task"');
        }
        // @codeCoverageIgnoreEnd

        $this->client->startTimer(
            $input->getOption('project'),
            $input->getOption('task')
        );

        return 0;
    }
}
