<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console\App;

use Lsv\TimeHarvestCli\Console\App\InstallCommand;
use Lsv\TimeHarvestCliTest\Console\AbstractCommandTest;
use Symfony\Component\HttpClient\Response\MockResponse;

class InstallCommandTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function can_install(): void
    {
        $command = new InstallCommand($this->getHttpClient(), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->setInputs(
            [
                'account',
                'token',
            ]
        );
        $tester->execute([]);
        $output = $tester->getDisplay();
        self::assertStringContainsString('[OK] The TimeHarvest CLI has been installed', $output);
        self::assertSame('token', $this->getConfiguration()->getToken());
        self::assertSame('account', $this->getConfiguration()->getAccountId());
    }

    /**
     * @test
     */
    public function can_not_install_with_invalid_account(): void
    {
        $responses = [
            new MockResponse('', ['http_code' => 400]),
        ];
        $command = new InstallCommand($this->getHttpClient($responses), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->setInputs(
            [
                'account',
                'token',
            ]
        );
        $tester->execute([]);
        $output = $tester->getDisplay();
        self::assertStringNotContainsString('[OK] The TimeHarvest CLI has been installed', $output);
    }

    /**
     * @test
     */
    public function can_not_install_if_already_installed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Already installed/');

        $command = new InstallCommand($this->getHttpClient(), $this->getConfiguration());
        $tester = $this->getCommandTester($command);
        $tester->setInputs(
            [
                'account',
                'token',
            ]
        );
        $tester->execute([]);

        $tester = $this->getCommandTester($command);
        $tester->setInputs([
            'account', 'token',
        ]);
        $tester->execute([]);
    }

    protected function setInstalledConfiguration(): void
    {
        $this->createConfigurationDirectory();
    }
}
