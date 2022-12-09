<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\App;

use Lsv\TimeHarvestCli\Console\App\UninstallCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;

class UninstallCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function canUninstall(): void
    {
        $command = new UninstallCommand($this->getHttpClient(), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
        $output = $tester->getDisplay();
        self::assertStringContainsString('Configuration has been removed', $output);
    }

    /**
     * @test
     */
    public function canNotUninstallIfNotInstalled(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You have not installed thje CLI yet, please run app:install');

        $this->removeInstalledConfiguration();
        $command = new UninstallCommand($this->getHttpClient(), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
    }
}
