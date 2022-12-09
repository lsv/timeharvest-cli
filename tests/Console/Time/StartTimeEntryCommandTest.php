<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\Time;

use Lsv\TimeHarvestCli\Console\Time\StartTimeEntryCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;
use Symfony\Component\HttpClient\Response\MockResponse;

class StartTimeEntryCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function canStartTimer(): void
    {
        $responses = [
            new MockResponse(
                (string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/project_assignments.json')
            ),
            new MockResponse(
                (string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/project_assignments.json')
            ),
        ];
        $command = new StartTimeEntryCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute(
            [
                '--no-select' => true,
            ]
        );
        self::assertSame(0, $tester->getStatusCode());
    }
}
