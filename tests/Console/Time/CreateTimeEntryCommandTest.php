<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\Time;

use Lsv\TimeHarvestCli\Console\Time\CreateTimeEntryCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;
use Symfony\Component\HttpClient\Response\MockResponse;

class CreateTimeEntryCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function canCreateTimeEntry(): void
    {
        $responses = [
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/project_assignments.json')),
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/create_time_entry.json')),
        ];
        $command = new CreateTimeEntryCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([
            '--project' => 'Store',
            '--task' => 'Design',
            '--no-select' => true,
            'hours' => '3',
            'note' => 'Hello World',
        ]);
        self::assertSame(0, $tester->getStatusCode());
    }
}
