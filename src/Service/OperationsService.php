<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\OperationsStatus;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;
use Throwable;

/** Authenticated/CLI-only diagnostics. Never expose this output via a public route. */
final class OperationsService
{
    /** @return array{database:string,storage:string,github:string,github_age_hours:?int,ready:bool} */
    public function report(): array
    {
        $configured = (new ContentRepository())->hasDatabase();
        $reachable = false;
        $schemaReady = false;
        $lastSync = null;
        if ($configured) {
            try {
                $db = ConnectionManager::get('default');
                if (!$db instanceof Connection) {
                    throw new RuntimeException('Unsupported database driver');
                }
                $db->execute('SELECT 1');
                $reachable = true;
                // Zero-row probe validates all three migrations and the columns
                // required by public rendering, import and editorial audit.
                // Unlike a mere SELECT 1, incomplete installations cannot be ready.
                $db->execute('SELECT p.problem_cs, p.solution_en, j.is_public, a.content_digest,
                    ga.repository, gr.pushed_at, sr.completed_at
                    FROM project_overrides p, journal_entries j, editorial_audit a,
                    github_activity ga, github_public_repos gr, sync_runs sr
                    LIMIT 0');
                $schemaReady = true;
                $row = $db->execute('SELECT MAX(completed_at) AS last_sync FROM sync_runs WHERE source = :source', [
                    'source' => 'github',
                ])->fetch('assoc');
                if (is_array($row) && is_string($row['last_sync'] ?? null)) {
                    $lastSync = new DateTimeImmutable($row['last_sync'], new DateTimeZone('UTC'));
                }
            } catch (Throwable) {
                // Never disclose table names or database driver errors publicly.
                // Distinguish a live connection with missing migrations from an
                // unreachable database; both must fail readiness.
                $schemaReady = false;
                $lastSync = null;
            }
        }

        return OperationsStatus::evaluate(
            $configured,
            $reachable,
            is_writable(TMP) && is_writable(CACHE) && is_writable(LOGS),
            $lastSync,
            new DateTimeImmutable('now', new DateTimeZone('UTC')),
            $schemaReady,
        );
    }
}
