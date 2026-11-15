<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Database\Connection;
use UnexpectedValueException;

/**
 * Bound operational history without touching successful import data.
 * Must be invoked inside the same transaction as the new sync receipt.
 */
final class SyncRunRetention
{
    public const KEEP_LATEST = 64;

    /** Prevent instantiation of this stateless service. */
    private function __construct()
    {
    }

    public static function prune(Connection $connection, string $source): void
    {
        // The 64th newest receipt is the oldest one we retain. A fixed SQL
        // offset avoids interpolating untrusted numeric values into SQL.
        $row = $connection->execute(
            'SELECT id FROM sync_runs WHERE source = :source ORDER BY id DESC LIMIT 1 OFFSET 63',
            ['source' => $source],
        )->fetch('assoc');
        if ($row === false) {
            return;
        }
        if (!is_array($row)) {
            throw new UnexpectedValueException('Invalid sync history query result');
        }
        $id = $row['id'] ?? null;
        if (!is_int($id) && !(is_string($id) && ctype_digit($id))) {
            throw new UnexpectedValueException('Invalid sync retention boundary');
        }
        // Restrict by BOTH source and id: never delete another importer's history.
        $connection->execute(
            'DELETE FROM sync_runs WHERE source = :source AND id < :boundary',
            ['source' => $source, 'boundary' => $id],
        );
    }
}
