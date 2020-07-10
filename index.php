<?php

declare(strict_types=1);

use Lsv\TimeHarvestCli\Console\App\InstallCommand;
use Lsv\TimeHarvestCli\Console\App\UninstallCommand;
use Lsv\TimeHarvestCli\Console\App\ValidateCommand;
use Lsv\TimeHarvestCli\Console\Lists\TimeEntryDayCommand;
use Lsv\TimeHarvestCli\Console\Lists\TimeEntryWeekCommand;
use Lsv\TimeHarvestCli\Console\Time\CreateTimeEntryCommand;
use Lsv\TimeHarvestCli\Console\Time\EndTimeEntryCommand;
use Lsv\TimeHarvestCli\Console\Time\StartTimeEntryCommand;
use Symfony\Component\Console\Application;

require __DIR__.'/vendor/autoload.php';

$app = new Application('TimeHarvest CLI', 'dev-master');
$app->add(new InstallCommand());
$app->add(new ValidateCommand());
$app->add(new UninstallCommand());
$app->add(new CreateTimeEntryCommand());
$app->add(new TimeEntryDayCommand());
$app->add(new TimeEntryWeekCommand());
$app->add(new StartTimeEntryCommand());
$app->add(new EndTimeEntryCommand());

$app->run();
