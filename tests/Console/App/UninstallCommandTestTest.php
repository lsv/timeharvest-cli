<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\App;

use Lsv\TimeHarvestCli\Console\App\UninstallCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;

class UninstallCommandTestTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function can_uninstall(): void
    {
        $command = new UninstallCommand($this->getHttpClient(), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
        $output = $tester->getDisplay();
        self::assertStringContainsString('Configuration has been removed', $output);
    }
}
