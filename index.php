<?php

declare(strict_types=1);

use Lsv\TimeHarvestCli\Console\Install\InstallCommand;
use Lsv\TimeHarvestCli\Console\Install\UninstallCommand;
use Lsv\TimeHarvestCli\Console\Install\ValidateUserCommand;
use Lsv\TimeHarvestCli\Console\Time\CreateTimeEntryCommand;
use Symfony\Component\Console\Application;

require __DIR__.'/vendor/autoload.php';

$app = new Application('TimeHarvest CLI', 'dev-master');
$app->add(new InstallCommand());
$app->add(new ValidateUserCommand());
$app->add(new UninstallCommand());
$app->add(new CreateTimeEntryCommand());

$app->run();
