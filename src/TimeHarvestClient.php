<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli;

use DateTime;
use RuntimeException;
use stdClass;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TimeHarvestClient
{
    private HttpClientInterface $client;

    private Configuration $configuration;

    public function __construct(Configuration $configuration, ?HttpClientInterface $client = null)
    {
        $this->client = $client ?? HttpClient::create();
        $this->configuration = $configuration;
    }

    public function testToken(?string $account, ?string $token): bool
    {
        if (!$account) {
            $account = $this->configuration->getAccountId();
        }

        if (!$token) {
            $token = $this->configuration->getToken();
        }

        $response = $this->client->request(
            'GET',
            'https://api.harvestapp.com/api/v2/users/me.json',
            $this->getHeaders($account, $token),
        );

        return 200 === $response->getStatusCode();
    }

    public function getUserId(): int
    {
        $response = $this->client->request(
            'GET',
            'https://api.harvestapp.com/api/v2/users/me.json',
            $this->getHeaders(
                $this->configuration->getAccountId(),
                $this->configuration->getToken()
            ),
        );

        $json = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $json['id'];
    }

    public function listActiveProjects(): string
    {
        static $activeProjects;
        if (!$activeProjects) {
            $response = $this->client->request(
                'GET',
                'https://api.harvestapp.com/api/v2/users/me/project_assignments.json',
                $this->getHeaders($this->configuration->getAccountId(), $this->configuration->getToken()),
            );

            $activeProjects = $response->getContent();
        }

        return $activeProjects;
    }

    public function stopTimer(): void
    {
        $timeEntries = $this->getTimeStartedTimeEntry();
        if (0 === count($timeEntries)) {
            throw new RuntimeException('You do not have any running timers');
        }

        if (count($timeEntries) > 1) {
            throw new RuntimeException('You have multiple running timers, which are not currently supported');
        }

        $entry = $timeEntries[0];
        $response = $this->client->request(
            'PATCH',
            "https://api.harvestapp.com/api/v2/time_entries/{$entry->id}/stop",
            $this->getHeaders(
                $this->configuration->getAccountId(),
                $this->configuration->getToken()
            ),
        );
        $response->getContent();
    }

    public function startTimer(string $project, string $task): void
    {
        $response = $this->client->request(
            'POST',
            'https://api.harvestapp.com/api/v2/time_entries',
            $this->getHeaders(
                $this->configuration->getAccountId(),
                $this->configuration->getToken(),
                [
                    'project_id' => (int) $project,
                    'task_id' => (int) $task,
                    'spent_date' => (new DateTime())->format('Y-m-d'),
                ]
            ),
        );
        $response->getContent();
    }

    public function createTimeEntry(string $project, string $task, string $hours, string $notes): void
    {
        $response = $this->client->request(
            'POST',
            'https://api.harvestapp.com/api/v2/time_entries',
            $this->getHeaders(
                $this->configuration->getAccountId(),
                $this->configuration->getToken(),
                [
                    'project_id' => (int) $project,
                    'task_id' => (int) $task,
                    'hours' => $hours,
                    'notes' => $notes,
                    'spent_date' => (new DateTime())->format('Y-m-d'),
                ]
            ),
        );
        $response->getContent();
    }

    public function getTimeEntries(DateTime $from, DateTime $to): string
    {
        static $timeEntries;
        if (!$timeEntries) {
            $response = $this->client->request(
                'GET',
                'https://api.harvestapp.com/v2/time_entries.json',
                $this->getHeaders(
                    $this->configuration->getAccountId(),
                    $this->configuration->getToken(),
                    [],
                    [
                        'user_id' => $this->getUserId(),
                        'from' => $from->format('Y-m-d'),
                        'to' => $to->format('Y-m-d'),
                    ]
                ),
            );

            $timeEntries = $response->getContent();
        }

        return $timeEntries;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getHeaders(
        string $account,
        string $token,
        array $bodyParameters = [],
        array $queryParameters = []
    ): array {
        $headers = [
            'auth_bearer' => $token,
            'headers' => [
                'Content-Type' => 'application/json',
                'User-Agent' => 'TimeHarvest CLI - dev-master',
                'Harvest-Account-ID' => $account,
            ],
        ];

        if ($bodyParameters) {
            $headers['json'] = $bodyParameters;
        }

        if ($queryParameters) {
            $headers['query'] = $queryParameters;
        }

        return $headers;
    }

    /**
     * @return stdClass[]
     */
    private function getTimeStartedTimeEntry(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.harvestapp.com/v2/time_entries.json?is_running=true',
            $this->getHeaders(
                $this->configuration->getAccountId(),
                $this->configuration->getToken(),
                [
                    'user_id' => $this->getUserId(),
                ]
            ),
        );

        $timeEntries = $response->getContent();

        return json_decode($timeEntries, false, 512, JSON_THROW_ON_ERROR)->time_entries;
    }
}
