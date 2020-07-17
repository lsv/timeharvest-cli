<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\Config;

use Lsv\TimeHarvestCli\Console\Config\RemoveDefaultProjectCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;

class RemoveDefaultProjectCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function can_remove_project_from_defaults(): void
    {
        $this->getConfiguration()->setProjectForDirectory('project');

        self::assertArrayHasKey('defaultprojects', $this->getConfiguration()->getConfiguration());
        self::assertArrayHasKey($this->getTestWorkingDirectory(), $this->getConfiguration()->getConfiguration()['defaultprojects']);

        $command = new RemoveDefaultProjectCommand($this->getHttpClient(), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
        self::assertSame(0, $tester->getStatusCode());
        self::assertArrayHasKey('defaultprojects', $this->getConfiguration()->getConfiguration());
        self::assertArrayNotHasKey($this->getTestWorkingDirectory(), $this->getConfiguration()->getConfiguration()['defaultprojects']);
    }
}
