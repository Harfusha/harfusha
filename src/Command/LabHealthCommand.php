<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\OperationsService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;

/** CLI readiness gate for deployment smoke, not a public diagnostic endpoint. */
final class LabHealthCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $report = (new OperationsService())->report();
        foreach (['database', 'storage', 'github'] as $field) {
            $io->out($field . ': ' . $report[$field]);
        }
        if ($report['github_age_hours'] !== null) {
            $io->out('github age: ' . $report['github_age_hours'] . ' hours');
        }

        return $report['ready'] ? static::CODE_SUCCESS : static::CODE_ERROR;
    }
}
