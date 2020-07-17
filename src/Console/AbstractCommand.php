<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console;

use Lsv\TimeHarvestCli\Configuration;
use Lsv\TimeHarvestCli\TimeHarvestClient;
use RedAnt\Console\Helper\SelectHelper;
use RuntimeException;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractCommand extends Command
{
    protected SymfonyStyle $io;

    protected TimeHarvestClient $client;

    protected Configuration $configuration;

    public function __construct(?HttpClientInterface $httpClient = null, ?Configuration $configuration = null)
    {
        parent::__construct();
        $this->configuration = $configuration ?? new Configuration();
        $this->client = new TimeHarvestClient($this->configuration, $httpClient);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        if (!$this->configuration->isAlreadyInstalled()) {
            throw new RuntimeException('You have not installed thje CLI yet, please run app:install');
        }

        if (!$helper = $this->getHelperSet()) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Could not get helper');
            // @codeCoverageIgnoreEnd
        }
        $helper->set(new SelectHelper(), 'select');
    }

    protected function selectProject(InputInterface $input): string
    {
        if ($input->hasOption('project') && $projectForDirectory = $this->configuration->getProjectForDirectory()) {
            $input->setOption('project', $projectForDirectory);
        }

        $projects = $this->findProjects();
        $projectTitles = [];

        $filtered = array_filter($projects, static function (stdClass $project) use ($input) {
            $filter = $input->getOption('project');
            if (!$filter || !is_string($filter)) {
                return true;
            }

            $strings = [
                (string) $project->id,
                (string) $project->project->id,
                $project->project->code,
                $project->project->name,
                $project->client->name,
                (string) $project->client->id,
            ];

            foreach ($strings as $string) {
                if (false !== stripos($string, $filter)) {
                    return true;
                }
            }

            return false;
        });

        array_map(
            static function (stdClass $project) use (&$projectTitles) {
                $projectTitles[$project->project->id] = "[{$project->project->code}] {$project->client->name} - {$project->project->name}";
            },
            $filtered
        );

        if ($input->hasOption('no-select') && !$input->getOption('no-select')) {
            // @codeCoverageIgnoreStart
            /** @var SelectHelper $helper */
            $helper = $this->getHelper('select');

            return (string) $helper->select(
                $input,
                'Select project',
                $projectTitles
            );
            // @codeCoverageIgnoreEnd
        }

        return (string) array_key_first($projectTitles);
    }

    protected function selectTask(InputInterface $input, string $projectId): string
    {
        if ($input->hasOption('task') && $taskForDirectory = $this->configuration->getTaskForDirectory()) {
            $input->setOption('task', $taskForDirectory);
        }

        $project = $this->findProject($projectId);
        $tasks = $project->task_assignments;
        $taskTitles = [];
        array_map(
            static function (stdClass $task) use (&$taskTitles, $input) {
                if (
                    $input->hasOption('task')
                    && ($filter = $input->getOption('task'))
                    && is_string($filter)
                    && false === strpos($task->task->name, $filter)
                    && false === strpos((string) $task->task->id, $filter)
                    && false === strpos((string) $task->id, $filter)
                ) {
                    return;
                }

                $taskTitles[$task->task->id] = (string) ($task->task->name);
            },
            $tasks
        );

        if ($input->hasOption('no-select') && !$input->getOption('no-select')) {
            // @codeCoverageIgnoreStart
            /** @var SelectHelper $helper */
            $helper = $this->getHelper('select');

            return (string) $helper->select(
                $input,
                "Select task for [{$project->project->code}] {$project->client->name} - {$project->project->name}",
                $taskTitles
            );
            // @codeCoverageIgnoreEnd
        }

        return (string) array_key_first($taskTitles);
    }

    /**
     * @return stdClass[]
     */
    private function findProjects(): array
    {
        $json = $this->client->listActiveProjects();

        $json = json_decode($json, false, 512, JSON_THROW_ON_ERROR);

        return $json->project_assignments;
    }

    private function findProject(string $projectId): stdClass
    {
        $projects = $this->findProjects();
        /** @var stdClass[] $filtered */
        $filtered = array_values(
            array_filter(
                $projects,
                static function (stdClass $assignment) use ($projectId) {
                    return $assignment->project->id === (int) $projectId;
                }
            )
        );

        if (1 === count($filtered)) {
            return $filtered[0];
        }

        throw new RuntimeException("Could not find tasks for project ID '{$projectId}'");
    }
}
