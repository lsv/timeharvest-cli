<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\Lists;

use Lsv\TimeHarvestCli\Console\Lists\TimeEntryDayCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;
use Symfony\Component\HttpClient\Response\MockResponse;

class TimeEntryDayCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function will_output_day_entries(): void
    {
        $responses = [
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/me.json')),
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/time_entries_day.json')),
        ];
        $command = new TimeEntryDayCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
        $output = $tester->getDisplay();
        self::assertStringContainsString('ABC Corp', $output);
        self::assertStringContainsString('Marketing Website', $output);
        self::assertStringContainsString('2.11', $output);
        self::assertStringContainsString('123 Industries', $output);
    }
}
