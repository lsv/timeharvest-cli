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

    public function getConfigurationDirectory(): string
    {
        return $this->getHomeDirectory().'/.config';
    }

    public function getConfigurationFile(): string
    {
        return $this->getConfigurationDirectory().'/.timeharvest_cli.json';
    }

    public function saveAccountToken(string $account, string $token): void
    {
        $data = json_encode(
            [
                'account' => $account,
                'token' => $token,
            ],
            JSON_THROW_ON_ERROR
        );

        file_put_contents($this->getConfigurationFile(), $data);
    }

    public function getAccountId(): string
    {
        return json_decode(
            (string) file_get_contents($this->getConfigurationFile()),
            true,
            512,
            JSON_THROW_ON_ERROR
        )['account'];
    }

    public function getToken(): string
    {
        return json_decode(
            (string) file_get_contents($this->getConfigurationFile()),
            true,
            512,
            JSON_THROW_ON_ERROR
        )['token'];
    }

    public function isAlreadyInstalled(): bool
    {
        return file_exists($this->getConfigurationFile());
    }
}
