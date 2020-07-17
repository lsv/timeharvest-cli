<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\App;

use Lsv\TimeHarvestCli\Console\AbstractCommand;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('app:install')
            ->setDescription('Install the client to be used')
            ->addOption('account', null, InputOption::VALUE_OPTIONAL, 'The account id')
            ->addOption('token', null, InputOption::VALUE_OPTIONAL, 'The access token');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        if (
            $this->configuration->isAlreadyInstalled()
            && !$this->io->confirm('You have already this, do you want to reset your configuration?')
        ) {
            throw new RuntimeException('Already installed');
        }

        if (!$input->getOption('token') || !$input->getOption('account')) {
            $this->io->block('Go to https://id.getharvest.com/developers and create a new personal access token');
        }

        if (!$input->getOption('account')) {
            $input->setOption('account', $this->io->ask('Please type Account ID?'));
        }

        if (!$input->getOption('token')) {
            $input->setOption('token', $this->io->ask('Please type Your Token?'));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // @codeCoverageIgnoreStart
        if (!is_string($input->getOption('account'))) {
            throw new RuntimeException('Account is not a string');
        }

        if (!is_string($input->getOption('token'))) {
            throw new RuntimeException('Token is not a string');
        }
        // @codeCoverageIgnoreEnd

        // Test token
        if (!$this->client->testToken((string) $input->getOption('account'), (string) $input->getOption('token'))) {
            $this->io->error('Your account or token is not valid');

            return 1;
        }

        $this->configuration->saveAccountToken((string) $input->getOption('account'), (string) $input->getOption('token'));
        $this->io->success('The TimeHarvest CLI has been installed');

        return 0;
    }
}
