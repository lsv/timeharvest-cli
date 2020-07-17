<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest\Console;

use Lsv\TimeHarvestCli\Configuration;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractCommandTest extends TestCase
{
    protected function getCommandTester(Command $command): CommandTester
    {
        $app = new Application();
        if (!$appCommand = $app->add($command)) {
            throw new RuntimeException('Command not found');
        }

        return new CommandTester($appCommand);
    }

    protected function setUp(): void
    {
        $this->removeInstalledConfiguration();
        $this->setInstalledConfiguration();
    }

    protected function tearDown(): void
    {
//        $this->removeInstalledConfiguration();
    }

    /**
     * @param MockResponse[] $responses
     */
    protected function getHttpClient(array $responses = []): HttpClientInterface
    {
        if (!$responses) {
            $responses[] = new MockResponse();
        }

        return new MockHttpClient($responses);
    }

    protected function getConfiguration(): Configuration
    {
        return
            new class($this->getTestConfigurationDirectory(), $this->getTestWorkingDirectory()) extends Configuration {
                private string $configurationDirectory;
                private string $workingDirectory;

                public function __construct(string $configurationDirectory, string $workingDirectory)
                {
                    $this->configurationDirectory = $configurationDirectory;
                    $this->workingDirectory = $workingDirectory;
                }

                public function getHomeDirectory(): string
                {
                    return $this->configurationDirectory;
                }

                public function getCurrentWorkingDirectory(): string
                {
                    return $this->workingDirectory;
                }
            };
    }

    private function getTestConfigurationDirectory(): string
    {
        return __DIR__.'/../.testdir';
    }

    protected function getTestWorkingDirectory(): string
    {
        return __DIR__.'/../.testworking';
    }

    protected function removeInstalledConfiguration(): void
    {
        if (is_dir($this->getTestConfigurationDirectory())) {
            $this->deleteDirectory($this->getTestConfigurationDirectory());
        }
    }

    protected function createConfigurationDirectory(): void
    {
        if (!is_dir($this->getTestConfigurationDirectory())) {
            mkdir($this->getTestConfigurationDirectory(), 0777, true);
        }
    }

    protected function setInstalledConfiguration(): void
    {
        $this->createConfigurationDirectory();
        $this->getConfiguration()->saveAccountToken('account', 'token');
    }

    private function deleteDirectory(string $directory): void
    {
        if (is_dir($directory)) {
            $objects = (array) scandir($directory);
            foreach ($objects as $object) {
                if ('.' !== $object && '..' !== $object) {
                    if (is_dir($directory.DIRECTORY_SEPARATOR.$object) && !is_link($directory.'/'.$object)) {
                        $this->deleteDirectory($directory.DIRECTORY_SEPARATOR.$object);
                    } else {
                        unlink($directory.DIRECTORY_SEPARATOR.$object);
                    }
                }
            }

            rmdir($directory);
        }
    }
}
