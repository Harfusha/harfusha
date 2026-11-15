<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\ProjectCatalog;
use App\Service\ContentRepository;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use RuntimeException;

/**
 * Test that a broken public journal row cannot leak a partially read DB batch.
 */
final class JournalDatabaseReadTest extends TestCase
{
    public function testInvalidPersistedJournalRowDropsTheEntireDatabaseBatch(): void
    {
        $connection = ConnectionManager::get('default');
        if (!$connection instanceof Connection) {
            throw new RuntimeException('Test requires the disposable MySQL database');
        }
        // A temporary table isolates this corruption scenario from the
        // real, already migrated journal and every editorial HTTP test.
        $connection->execute('CREATE TEMPORARY TABLE journal_entries (
            id INT NOT NULL,
            title_cs VARCHAR(240) NOT NULL, title_en VARCHAR(240) NOT NULL,
            body_cs TEXT NOT NULL, body_en TEXT NOT NULL,
            category VARCHAR(40) NOT NULL, source_url VARCHAR(255) NOT NULL,
            is_public TINYINT(1) NOT NULL, published_at DATETIME NOT NULL
        )');
        try {
            $connection->execute("INSERT INTO journal_entries
                (id,title_cs,title_en,body_cs,body_en,category,source_url,is_public,published_at)
                VALUES
                (42,'First','Partial good row','Text','Text','Testing','',1,'2026-11-15 16:00:00'),
                (0,'Second','Invalid database row','Text','Text','Testing','',1,'2026-11-15 15:00:00')");

            $notes = (new ContentRepository())->journal();
            $curated = (new ProjectCatalog(ROOT . '/data'))->journal();
            self::assertCount(count($curated), $notes);
            self::assertNotContains('Partial good row', array_column($notes, 'title_en'));
            self::assertNotContains('Invalid database row', array_column($notes, 'title_en'));
        } finally {
            $connection->execute('DROP TEMPORARY TABLE IF EXISTS journal_entries');
        }
    }
}
