<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Service\OperationsService;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use RuntimeException;

/**
 * Integration with disposable CI MySQL: temporary tables shadow real ones
 * without altering or dropping any persistent schema.
 */
final class OperationsServiceTest extends TestCase
{
    public function testMissingEditorialMigrationIsNotReportedAsReady(): void
    {
        $connection = ConnectionManager::get('default');
        if (!$connection instanceof Connection) {
            throw new RuntimeException('Expected disposable CakePHP MySQL test database');
        }
        self::assertSame('ok', (new OperationsService())->report()['database']);
        $connection->execute('CREATE TEMPORARY TABLE editorial_audit (id BIGINT NOT NULL)');
        try {
            $report = (new OperationsService())->report();
            self::assertSame('schema-incomplete', $report['database']);
            self::assertFalse($report['ready']);
        } finally {
            $connection->execute('DROP TEMPORARY TABLE IF EXISTS editorial_audit');
        }
        self::assertSame('ok', (new OperationsService())->report()['database']);
    }
}
