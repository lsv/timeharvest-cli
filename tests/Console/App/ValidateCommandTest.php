<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\App;

use Lsv\TimeHarvestCli\Console\App\ValidateCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;
use Symfony\Component\HttpClient\Response\MockResponse;

class ValidateCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function can_validate(): void
    {
        $command = new ValidateCommand($this->getHttpClient(), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
        $output = $tester->getDisplay();
        self::assertStringContainsString('Your account and token is valid', $output);
        self::assertSame(0, $tester->getStatusCode());
    }

    /**
     * @test
     */
    public function can_not_validate(): void
    {
        $responses = [
            new MockResponse('', ['http_code' => 400]),
        ];
        $command = new ValidateCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->execute([]);
        $output = $tester->getDisplay();
        self::assertStringContainsString('Your account or token is not valid', $output);
        self::assertSame(1, $tester->getStatusCode());
    }
}
