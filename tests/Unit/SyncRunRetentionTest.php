<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Service\SyncRunRetention;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use RuntimeException;

/** Exercises bounded operational receipts on the disposable CI MySQL. */
final class SyncRunRetentionTest extends TestCase
{
    /** Extract a strictly positive-or-zero SQL count from mixed driver output. */
    private static function numberField(mixed $row, string $field): int
    {
        if (!is_array($row)) {
            throw new RuntimeException('Missing SQL aggregate row');
        }
        $value = $row[$field] ?? null;
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && ctype_digit($value)) {
            return (int)$value;
        }

        throw new RuntimeException('Invalid SQL aggregate value');
    }

    public function testOldReceiptsArePrunedWithoutAffectingOtherSources(): void
    {
        $connection = ConnectionManager::get('default');
        if (!$connection instanceof Connection) {
            throw new RuntimeException('Expected disposable MySQL connection');
        }
        $connection->begin();
        try {
            for ($index = 0; $index < 70; $index++) {
                $connection->execute(
                    'INSERT INTO sync_runs (source,item_count,completed_at)
                        VALUES (:source,:count,UTC_TIMESTAMP())',
                    ['source' => 'retention-test', 'count' => $index],
                );
            }
            $connection->execute(
                'INSERT INTO sync_runs (source,item_count,completed_at)
                    VALUES (:source,:count,UTC_TIMESTAMP())',
                ['source' => 'another-test', 'count' => 777],
            );
            SyncRunRetention::prune($connection, 'retention-test');
            $row = $connection->execute(
                'SELECT COUNT(*) AS total, MIN(item_count) AS oldest, MAX(item_count) AS newest
                    FROM sync_runs WHERE source = :source',
                ['source' => 'retention-test'],
            )->fetch('assoc');
            self::assertIsArray($row);
            self::assertSame(64, self::numberField($row, 'total'));
            self::assertSame(6, self::numberField($row, 'oldest'));
            self::assertSame(69, self::numberField($row, 'newest'));

            $other = $connection->execute(
                'SELECT COUNT(*) AS total FROM sync_runs WHERE source = :source',
                ['source' => 'another-test'],
            )->fetch('assoc');
            self::assertIsArray($other);
            self::assertSame(1, self::numberField($other, 'total'));

            // Idempotent: repeated pruning never discards any of the 64 newest.
            SyncRunRetention::prune($connection, 'retention-test');
            $same = $connection->execute(
                'SELECT COUNT(*) AS total FROM sync_runs WHERE source = :source',
                ['source' => 'retention-test'],
            )->fetch('assoc');
            self::assertIsArray($same);
            self::assertSame(64, self::numberField($same, 'total'));
        } finally {
            $connection->rollback();
        }
    }

    public function testShortHistoryIsNotDeleted(): void
    {
        $connection = ConnectionManager::get('default');
        if (!$connection instanceof Connection) {
            throw new RuntimeException('Expected disposable MySQL connection');
        }
        $connection->begin();
        try {
            $connection->execute(
                'INSERT INTO sync_runs (source,item_count,completed_at)
                    VALUES (:source,0,UTC_TIMESTAMP())',
                ['source' => 'short-history-test'],
            );
            SyncRunRetention::prune($connection, 'short-history-test');
            $row = $connection->execute(
                'SELECT COUNT(*) AS total FROM sync_runs WHERE source = :source',
                ['source' => 'short-history-test'],
            )->fetch('assoc');
            self::assertIsArray($row);
            self::assertSame(1, self::numberField($row, 'total'));
        } finally {
            $connection->rollback();
        }
    }
}
