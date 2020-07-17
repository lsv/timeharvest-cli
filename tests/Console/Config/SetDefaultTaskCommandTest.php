<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\Config;

use Lsv\TimeHarvestCli\Console\Config\SetDefaultTaskCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;
use Symfony\Component\HttpClient\Response\MockResponse;

class SetDefaultTaskCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function can_set_task_for_directory(): void
    {
        $responses = [
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/active_projects.json')),
        ];

        $command = new SetDefaultTaskCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute(
            [
                '--task' => 'My task',
            ]
        );
        self::assertSame(0, $tester->getStatusCode());
        self::assertArrayHasKey('defaulttasks', $this->getConfiguration()->getConfiguration());
        self::assertArrayHasKey(
            $this->getTestWorkingDirectory(),
            $this->getConfiguration()->getConfiguration()['defaulttasks']
        );
        self::assertSame(
            'My task',
            $this->getConfiguration()->getConfiguration()['defaulttasks'][$this->getTestWorkingDirectory()]
        );
    }

    /**
     * @test
     */
    public function can_set_task_as_global(): void
    {
        $responses = [
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/active_projects.json')),
        ];

        $command = new SetDefaultTaskCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute(
            [
                '--task' => 'My task',
                '--global' => true,
            ]
        );
        self::assertSame(0, $tester->getStatusCode());
        self::assertArrayHasKey('defaulttasks', $this->getConfiguration()->getConfiguration());
        self::assertArrayHasKey(
            '_GLOBAL_',
            $this->getConfiguration()->getConfiguration()['defaulttasks']
        );
        self::assertSame(
            'My task',
            $this->getConfiguration()->getConfiguration()['defaulttasks']['_GLOBAL_']
        );
    }
}
