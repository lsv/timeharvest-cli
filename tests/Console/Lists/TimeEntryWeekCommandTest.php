<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\Lists;

use Lsv\TimeHarvestCli\Console\Lists\TimeEntryWeekCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;
use Symfony\Component\HttpClient\Response\MockResponse;

class TimeEntryWeekCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function willOutputDayEntries(): void
    {
        $responses = [
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/me.json')),
            new MockResponse(
                (string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/time_entries_week.json')
            ),
        ];
        $command = new TimeEntryWeekCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
        $output = $tester->getDisplay();
        self::assertStringContainsString('ABC Corp', $output);
        self::assertStringContainsString('Marketing Website', $output);
        self::assertStringContainsString('2.11', $output);
        self::assertStringContainsString('123 Industries', $output);
    }
}
