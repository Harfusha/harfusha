<?php
declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

/**
 * Pure operational policy. Reports do not contain infrastructure topology,
 * credentials, network addresses or driver exception messages.
 */
final class OperationsStatus
{
    /**
     * Initialize the immutable domain model with validated inputs.
     */
    private function __construct()
    {
    }

    /**
     * @return array{database:string,storage:string,github:string,github_age_hours:?int,ready:bool}
     */
    public static function evaluate(
        bool $databaseConfigured,
        bool $databaseReachable,
        bool $storageWritable,
        ?DateTimeImmutable $lastGithubSync,
        DateTimeImmutable $now,
        bool $schemaReady = true,
    ): array {
        $database = !$databaseConfigured ? 'not-configured' : (
            !$databaseReachable ? 'unreachable' : ($schemaReady ? 'ok' : 'schema-incomplete')
        );
        $storage = $storageWritable ? 'ok' : 'unwritable';
        $github = 'not-synced';
        $age = null;
        if ($databaseReachable && $lastGithubSync !== null) {
            // Reject timestamps from the future rather than showing a false fresh status.
            $ageSeconds = $now->getTimestamp() - $lastGithubSync->getTimestamp();
            if ($ageSeconds < -300) {
                $github = 'invalid-timestamp';
            } else {
                $age = max(0, intdiv($ageSeconds, 3600));
                $github = $ageSeconds > 48 * 3600 ? 'stale' : 'ok';
            }
        } elseif (!$databaseReachable) {
            $github = 'unknown';
        }

        return [
            'database' => $database,
            'storage' => $storage,
            'github' => $github,
            'github_age_hours' => $age,
            'ready' => $database === 'ok' && $storage === 'ok' && $github === 'ok',
        ];
    }
}
