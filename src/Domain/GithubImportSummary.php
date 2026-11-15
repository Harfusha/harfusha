<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Report only activity that survived the public, external-only filter.
 * Raw Search API totals also contain contributions to owned repositories.
 */
final class GithubImportSummary
{
    /** Never instantiate this pure aggregation helper. */
    private function __construct()
    {
    }

    /**
     * @param list<\App\Domain\GithubRecord> $records
     * @return array{imported:int,merged:int,issues:int,repositories:int}
     */
    public static function fromRecords(array $records, int $repositories): array
    {
        if ($repositories < 0) {
            throw new InvalidArgumentException('Invalid repository count');
        }

        $merged = 0;
        $issues = 0;
        foreach ($records as $record) {
            if ($record->kind === 'pr' && $record->state === 'merged') {
                ++$merged;
            }
            if ($record->kind === 'issue') {
                ++$issues;
            }
        }

        return [
            'imported' => count($records),
            'merged' => $merged,
            'issues' => $issues,
            'repositories' => $repositories,
        ];
    }
}
