<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\Config;

use Lsv\TimeHarvestCli\Console\AbstractCommand;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetDefaultProjectCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('config:set:project')
            ->setDescription('Sets a default project for the current directory')
            ->addOption(
                'project',
                'p',
                InputOption::VALUE_OPTIONAL,
                'The ID of the project to associate with this directory, or a string of the project name to filter them'
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // @codeCoverageIgnoreStart
        if (!is_string($input->getOption('project'))) {
            throw new RuntimeException('Could not parse option "project"');
        }
        // @codeCoverageIgnoreEnd

        $this->configuration->setProjectForDirectory($input->getOption('project'));

        return 0;
    }
}
