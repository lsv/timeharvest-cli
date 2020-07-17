<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli;

use Lsv\TimeHarvestCli\Console\App\InstallCommand;
use Lsv\TimeHarvestCli\Console\App\UninstallCommand;
use Lsv\TimeHarvestCli\Console\App\ValidateCommand;
use Lsv\TimeHarvestCli\Console\Config\RemoveDefaultProjectCommand;
use Lsv\TimeHarvestCli\Console\Config\RemoveDefaultTaskCommand;
use Lsv\TimeHarvestCli\Console\Config\SetDefaultProjectCommand;
use Lsv\TimeHarvestCli\Console\Config\SetDefaultTaskCommand;
use Lsv\TimeHarvestCli\Console\Lists\TimeEntryDayCommand;
use Lsv\TimeHarvestCli\Console\Lists\TimeEntryWeekCommand;
use Lsv\TimeHarvestCli\Console\Time\CreateTimeEntryCommand;
use Lsv\TimeHarvestCli\Console\Time\EndTimeEntryCommand;
use Lsv\TimeHarvestCli\Console\Time\StartTimeEntryCommand;
use Symfony\Component\Console\Application;

class App
{
    public static function get(): Application
    {
        $app = new Application('TimeHarvest CLI', 'dev-master');
        $app->add(new InstallCommand());
        $app->add(new ValidateCommand());
        $app->add(new UninstallCommand());
        $app->add(new CreateTimeEntryCommand());
        $app->add(new TimeEntryDayCommand());
        $app->add(new TimeEntryWeekCommand());
        $app->add(new StartTimeEntryCommand());
        $app->add(new EndTimeEntryCommand());
        $app->add(new SetDefaultProjectCommand());
        $app->add(new RemoveDefaultProjectCommand());
        $app->add(new SetDefaultTaskCommand());
        $app->add(new RemoveDefaultTaskCommand());

        return $app;
    }

    public static function run(): int
    {
        return self::get()->run();
    }
}
