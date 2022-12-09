<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\Lists;

use Lsv\TimeHarvestCli\Console\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TimeEntryWeekCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('list:week')
            ->setAliases(['week'])
            ->setDescription('See your entries for a week')
            ->addArgument(
                'week',
                InputArgument::OPTIONAL,
                'The week number you want to see',
                (new \DateTime())->format('W')
            )
            ->addArgument(
                'year',
                InputArgument::OPTIONAL,
                'The year you want to see, format Y-m-d',
                (new \DateTime())->format('Y')
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // @codeCoverageIgnoreStart
        if (!is_string($input->getArgument('week'))) {
            throw new \RuntimeException('Week parameter is not a string');
        }

        if (!is_string($input->getArgument('year'))) {
            throw new \RuntimeException('Year parameter is not a string');
        }
        // @codeCoverageIgnoreEnd

        [$from, $to] = $this->getWeekStartAndEndDate(
            (int) $input->getArgument('week'),
            (int) $input->getArgument('year')
        );

        $entries = $this->client->getTimeEntries($from, $to);
        $json = json_decode($entries, false, 512, JSON_THROW_ON_ERROR);

        $table = new Table($output);
        $table->setHeaders(
            [
                'Date',
                'Client',
                'Project',
                'Task',
                'Hours',
                'Note',
            ]
        );
        $total = 0;
        foreach (array_reverse($json->time_entries) as $row) {
            $total += $row->hours;
            $table->addRow(
                [
                    (new \DateTime($row->spent_date))->format('D (Y-m-d)'),
                    $row->client->name,
                    $row->project->name,
                    $row->task->name,
                    $row->hours,
                    $row->notes,
                ]
            );
        }

        $table->addRow(new TableSeparator());
        $table->addRow(
            [
                "{$from->format('D (Y-m-d)')} - {$to->format('D (Y-m-d)')}",
                '',
                '',
                '',
                $total,
                '',
            ]
        );
        $table->render();

        return 0;
    }

    /**
     * @return \DateTime[]
     */
    private function getWeekStartAndEndDate(int $week, int $year): array
    {
        return [
            (new \DateTime())->setISODate($year, $week),
            (new \DateTime())->setISODate($year, $week, 7),
        ];
    }
}
