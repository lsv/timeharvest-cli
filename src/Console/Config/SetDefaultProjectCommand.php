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
                'The ID of the project to associate with the time entry, or a string of the project name to filter them'
            )
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        parent::interact($input, $output);

        $input->setOption('project', $this->selectProject($input));
        if (!is_string($input->getOption('project'))) {
            throw new RuntimeException('Could not parse option "project"');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!is_string($input->getOption('project'))) {
            throw new RuntimeException('Could not parse option "project"');
        }

        $this->configuration->setProjectForDirectory($input->getOption('project'));

        return 0;
    }
}
