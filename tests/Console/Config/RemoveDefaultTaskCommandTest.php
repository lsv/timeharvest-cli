<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\Config;

use Lsv\TimeHarvestCli\Console\Config\RemoveDefaultTaskCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;

class RemoveDefaultTaskCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function canRemoveTaskForDirectory(): void
    {
        $this->getConfiguration()->setTaskForDirectory('task', false);
        self::assertArrayHasKey('defaulttasks', $this->getConfiguration()->getConfiguration());
        self::assertArrayHasKey(
            $this->getTestWorkingDirectory(),
            $this->getConfiguration()->getConfiguration()['defaulttasks']
        );
        self::assertSame(
            'task',
            $this->getConfiguration()->getConfiguration()['defaulttasks'][$this->getTestWorkingDirectory()]
        );

        $command = new RemoveDefaultTaskCommand($this->getHttpClient(), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
        self::assertSame(0, $tester->getStatusCode());
        self::assertArrayHasKey('defaulttasks', $this->getConfiguration()->getConfiguration());
        self::assertArrayNotHasKey(
            $this->getTestWorkingDirectory(),
            $this->getConfiguration()->getConfiguration()['defaulttasks']
        );
    }

    /**
     * @test
     */
    public function canRemoveTaskAsGlobal(): void
    {
        $this->getConfiguration()->setTaskForDirectory('task', true);
        self::assertArrayHasKey('defaulttasks', $this->getConfiguration()->getConfiguration());
        self::assertArrayHasKey('_GLOBAL_', $this->getConfiguration()->getConfiguration()['defaulttasks']);
        self::assertSame('task', $this->getConfiguration()->getConfiguration()['defaulttasks']['_GLOBAL_']);

        $command = new RemoveDefaultTaskCommand($this->getHttpClient(), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute(
            [
                '--global' => true,
            ]
        );
        self::assertSame(0, $tester->getStatusCode());
        self::assertArrayHasKey('defaulttasks', $this->getConfiguration()->getConfiguration());
        self::assertArrayNotHasKey(
            '_GLOBAL_',
            $this->getConfiguration()->getConfiguration()['defaulttasks']
        );
    }
}
