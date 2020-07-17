<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli;

use RuntimeException;

class Configuration
{
    public function getHomeDirectory(): string
    {
        // Cannot use $_SERVER superglobal since that's empty during UnitUnishTestCase
        // getenv('HOME') isn't set on Windows and generates a Notice.
        $home = getenv('HOME');
        if (!empty($home)) {
            // home should never end with a trailing slash.
            return rtrim($home, '/');
        }

        if (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH'])) {
            // home on windows
            $home = $_SERVER['HOMEDRIVE'].$_SERVER['HOMEPATH'];
            // If HOMEPATH is a root directory the path can end with a slash. Make sure
            // that doesn't happen.
            return rtrim($home, '\\/');
        }

        throw new RuntimeException('Could not determine your home directory');
    }

    public function getCurrentWorkingDirectory(): string
    {
        return (string) getcwd();
    }

    public function getConfigurationDirectory(): string
    {
        if (
            !is_dir($this->getHomeDirectory().'/.config')
            && !mkdir($concurrentDirectory = $this->getHomeDirectory().'/.config', 0777, true)
            && !is_dir($concurrentDirectory)
        ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        return $this->getHomeDirectory().'/.config';
    }

    public function getConfigurationFile(): string
    {
        return $this->getConfigurationDirectory().'/.timeharvest_cli.json';
    }

    public function saveAccountToken(string $account, string $token): void
    {
        $data = $this->getConfiguration();
        $data['account'] = $account;
        $data['token'] = $token;
        $this->writeConfiguration($data);
    }

    public function getAccountId(): string
    {
        return $this->getConfiguration()['account'];
    }

    public function getToken(): string
    {
        return $this->getConfiguration()['token'];
    }

    public function isAlreadyInstalled(): bool
    {
        return file_exists($this->getConfigurationFile());
    }

    public function setProjectForDirectory(string $project): void
    {
        $data = $this->getConfiguration();

        $data['defaultprojects'][$this->getCurrentWorkingDirectory()] = $project;

        $this->writeConfiguration($data);
    }

    public function getProjectForDirectory(): ?string
    {
        $data = $this->getConfiguration();

        return $data['defaultprojects'][$this->getCurrentWorkingDirectory()] ?? null;
    }

    public function removeProjectForDirectory(): void
    {
        $data = $this->getConfiguration();

        if (isset($data['defaultprojects'][$this->getCurrentWorkingDirectory()])) {
            unset($data['defaultprojects'][$this->getCurrentWorkingDirectory()]);
        }

        $this->writeConfiguration($data);
    }

    public function getConfiguration(): array
    {
        if (!file_exists($this->getConfigurationFile())) {
            $this->writeConfiguration([]);
        }

        return json_decode(
            (string) file_get_contents($this->getConfigurationFile()),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    private function writeConfiguration(array $configuration): void
    {
        file_put_contents($this->getConfigurationFile(), json_encode($configuration, JSON_THROW_ON_ERROR));
    }
}
