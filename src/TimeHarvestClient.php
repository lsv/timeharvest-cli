<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli;

use DateTime;
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

    public function createTimeEntry(string $project, string $task, string $hours, string $notes): void
    {
        try {
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
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getHeaders(string $account, string $token, array $bodyParameters = [], array $queryParameters = []): array
    {
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
}
