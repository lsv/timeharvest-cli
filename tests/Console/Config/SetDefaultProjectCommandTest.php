<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\Config;

use Lsv\TimeHarvestCli\Console\Config\SetDefaultProjectCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;
use Symfony\Component\HttpClient\Response\MockResponse;

class SetDefaultProjectCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function canAddDefaultProjectToWorkingDirectory(): void
    {
        $responses = [
            new MockResponse((string) file_get_contents(__DIR__.'/../../Fixtures/ClientResponses/active_projects.json')),
        ];

        $command = new SetDefaultProjectCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([
            '--no-select' => true,
        ]);
        self::assertSame(0, $tester->getStatusCode());
        self::assertArrayHasKey('defaultprojects', $this->getConfiguration()->getConfiguration());
        self::assertArrayHasKey(
            $this->getTestWorkingDirectory(),
            $this->getConfiguration()->getConfiguration()['defaultprojects']
        );
        self::assertSame(
            '14308069',
            $this->getConfiguration()->getConfiguration()['defaultprojects'][$this->getTestWorkingDirectory()]
        );
    }
}
