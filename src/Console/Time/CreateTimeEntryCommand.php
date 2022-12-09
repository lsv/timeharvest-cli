<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\Time;

use Lsv\TimeHarvestCli\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateTimeEntryCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('time:create')
            ->setAliases(['time'])
            ->setDescription('Adds time entry for a project')
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
            ->addArgument('hours', InputArgument::REQUIRED, 'The current amount of time tracked')
            ->addArgument('note', InputArgument::REQUIRED, 'Note for the time entry')
            ->addOption('no-select', null, InputOption::VALUE_NONE, 'Do not use project and task select');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        parent::interact($input, $output);

        // @codeCoverageIgnoreStart
        if (!$input->getArgument('hours')) {
            return;
        }

        if (!$input->getArgument('note')) {
            return;
        }
        // @codeCoverageIgnoreEnd

        $input->setOption('project', $this->selectProject($input));

        // @codeCoverageIgnoreStart
        if (!is_string($input->getOption('project'))) {
            throw new \RuntimeException('Could not parse option "project"');
        }
        // @codeCoverageIgnoreEnd

        $input->setOption('task', $this->selectTask($input, $input->getOption('project')));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // @codeCoverageIgnoreStart
        if (!is_string($input->getOption('project'))) {
            throw new \RuntimeException('Could not parse option "project"');
        }

        if (!is_string($input->getOption('task'))) {
            throw new \RuntimeException('Could not parse option "task"');
        }

        if (!is_string($input->getArgument('hours'))) {
            throw new \RuntimeException('Could not parse option "hours"');
        }

        if (!is_string($input->getArgument('note'))) {
            throw new \RuntimeException('Could not parse option "note"');
        }
        // @codeCoverageIgnoreEnd

        $this->client->createTimeEntry(
            $input->getOption('project'),
            $input->getOption('task'),
            $input->getArgument('hours'),
            $input->getArgument('note')
        );

        return 0;
    }
}
