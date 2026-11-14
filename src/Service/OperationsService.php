<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\OperationsStatus;
use Cake\Datasource\ConnectionManager;
use DateTimeImmutable;
use DateTimeZone;
use Throwable;

/** Authenticated/CLI-only diagnostics. Never expose this output via a public route. */
final class OperationsService
{
    /** @return array{database:string,storage:string,github:string,github_age_hours:?int,ready:bool} */
    public function report(): array
    {
        $configured = (new ContentRepository())->hasDatabase();
        $reachable = false;
        $lastSync = null;
        if ($configured) {
            try {
                $db = ConnectionManager::get('default');
                $db->execute('SELECT 1');
                $reachable = true;
                $row = $db->execute('SELECT MAX(completed_at) AS last_sync FROM sync_runs WHERE source = :source', [
                    'source' => 'github',
                ])->fetch('assoc');
                if (is_array($row) && is_string($row['last_sync'] ?? null)) {
                    $lastSync = new DateTimeImmutable($row['last_sync'], new DateTimeZone('UTC'));
                }
            } catch (Throwable) {
                // A missing table/migration must not disclose DB internals to the admin UI.
                // If the connection works but schema does not, readiness still fails.
                $reachable = false;
                $lastSync = null;
            }
        }
        return OperationsStatus::evaluate(
            $configured,
            $reachable,
            is_writable(TMP) && is_writable(CACHE) && is_writable(LOGS),
            $lastSync,
            new DateTimeImmutable('now', new DateTimeZone('UTC')),
        );
    }
}
