<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\Install;

use Lsv\TimeHarvestCli\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateUserCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('app:validatetoken')
            ->setAliases(['validate'])
            ->setDescription('Validates your account and token');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->client->testToken(null, null)) {
            $this->io->error('Your account or token is not valid');

            return 1;
        }

        $this->io->success('Your account and token is valid');

        return 0;
    }
}
