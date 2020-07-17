<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCli\Console\Lists;

use DateTime;
use Lsv\TimeHarvestCli\Console\AbstractCommand;
use RuntimeException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TimeEntryDayCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('list:day')
            ->setAliases(['day'])
            ->setDescription('See your entries for a day')
            ->addArgument(
                'day',
                InputArgument::OPTIONAL,
                'The date you want to see, format Y-m-d',
                (new DateTime())->format('Y-m-d')
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // @codeCoverageIgnoreStart
        if (!is_string($input->getArgument('day'))) {
            throw new RuntimeException('Day parameter is not a string');
        }
        // @codeCoverageIgnoreEnd

        $date = new DateTime((string) $input->getArgument('day'));
        $entries = $this->client->getTimeEntries($date, $date);
        $json = json_decode($entries, false, 512, JSON_THROW_ON_ERROR);

        $table = new Table($output);
        $table->setHeaders(
            [
                'Client',
                'Project',
                'Task',
                'Hours',
                'Note',
            ]
        );
        $total = 0;
        foreach ($json->time_entries as $row) {
            $total += $row->hours;
            $table->addRow(
                [
                    $row->client->name,
                    $row->project->name,
                    $row->task->name,
                    $row->hours,
                    $row->notes,
                ]
            );
        }

        $table->addRow(new TableSeparator());
        $table->addRow([
            $date->format('D (Y-m-d)'),
            '',
            '',
            $total,
            '',
        ]);
        $table->render();

        return 0;
    }
}
