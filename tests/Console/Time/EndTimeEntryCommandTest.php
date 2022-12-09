<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\Time;

use Lsv\TimeHarvestCli\Console\Time\EndTimeEntryCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;
use Symfony\Component\HttpClient\Response\MockResponse;

class EndTimeEntryCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function canStopTimer(): void
    {
        $responses = [
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/me.json')),
            new MockResponse(
                (string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/time_entry_running.json')
            ),
            new MockResponse(),
        ];
        $command = new EndTimeEntryCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
        self::assertSame(0, $tester->getStatusCode());
    }

    /**
     * @test
     */
    public function canNotStopTimerIfNotAlreadyStarted(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You do not have any running timers');

        $responses = [
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/me.json')),
            new MockResponse(
                (string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/time_entry_none_running.json')
            ),
        ];
        $command = new EndTimeEntryCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
    }

    /**
     * @test
     */
    public function canNotStopTimerIfEmptyArray(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You do not have any running timers');

        $responses = [
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/me.json')),
            new MockResponse(
                (string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/time_entry_empty_running.json')
            ),
        ];
        $command = new EndTimeEntryCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
    }

    /**
     * @test
     */
    public function canNotStopTimerIfHaveMoreThanOneStarted(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You have multiple running timers, which are not currently supported');
        $responses = [
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/me.json')),
            new MockResponse(
                (string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/time_entry_multiple_running.json')
            ),
        ];
        $command = new EndTimeEntryCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
    }
}
