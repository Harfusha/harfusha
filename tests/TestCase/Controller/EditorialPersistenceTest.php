<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Support\HttpIntegrationTestCase;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use RuntimeException;

/**
 * Real HTTP -> ORM -> MySQL persistence verification.
 * Runs only against the explicitly confirmed disposable CI database.
 */
final class EditorialPersistenceTest extends HttpIntegrationTestCase
{
    private static function db(): Connection
    {
        $connection = ConnectionManager::get('default');
        if (!$connection instanceof Connection) {
            throw new RuntimeException('Test requires a real CakePHP database connection');
        }

        return $connection;
    }

    /**
     * @param array<string, string|int> $params
     * @return array<string, mixed>
     */
    private static function row(Connection $db, string $sql, array $params = []): array
    {
        $row = $db->execute($sql, $params)->fetch('assoc');
        if (!is_array($row)) {
            throw new RuntimeException('Expected persisted database row');
        }
        /** @var array<string, mixed> $row */
        return $row;
    }

    private static function text(mixed $value): string
    {
        if (!is_string($value)) {
            throw new RuntimeException('Expected text from the database');
        }

        return $value;
    }

    private static function number(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && ctype_digit($value)) {
            return (int)$value;
        }
        throw new RuntimeException('Expected integer from the database');
    }

    /**
     * The audit insert can fail even when the schema's required columns exist.
     * No partially updated project must survive that failed transaction.
     */
    public function testProjectChangeRollsBackWhenAuditInsertFails(): void
    {
        $db = self::db();
        $slug = 'local-ai-hub';
        $db->execute('DELETE FROM project_overrides WHERE slug = :slug', ['slug' => $slug]);
        $db->execute('CREATE TEMPORARY TABLE editorial_audit (
            id INT AUTO_INCREMENT PRIMARY KEY, entity_type VARCHAR(32),
            entity_key VARCHAR(100), action VARCHAR(32),
            content_digest CHAR(1) NOT NULL, occurred_at DATETIME
        )');
        try {
            $this->session(['lab.admin' => true]);
            $this->enableCsrfToken();
            $this->post('/admin/project', [
                'slug' => $slug,
                'status' => 'maintenance',
                'summary_cs' => 'Test rollback',
                'summary_en' => 'Audit failure must roll back',
                'problem_cs' => '',
                'problem_en' => '',
                'solution_cs' => '',
                'solution_en' => '',
            ]);
            $this->assertResponseCode(503);
            $row = self::row($db, 'SELECT COUNT(*) AS total
                FROM project_overrides WHERE slug = :slug', ['slug' => $slug]);
            self::assertSame(0, self::number($row['total'] ?? null));
        } finally {
            $db->execute('DROP TEMPORARY TABLE IF EXISTS editorial_audit');
            $db->execute('DELETE FROM project_overrides WHERE slug = :slug', ['slug' => $slug]);
        }
    }

    /** An audit failure must not leave a partially published journal record. */
    public function testJournalCreationRollsBackWhenAuditInsertFails(): void
    {
        $db = self::db();
        $title = 'Audit receipt failure regression';
        $db->execute('CREATE TEMPORARY TABLE editorial_audit (
            id INT AUTO_INCREMENT PRIMARY KEY, entity_type VARCHAR(32),
            entity_key VARCHAR(100), action VARCHAR(32),
            content_digest CHAR(1) NOT NULL, occurred_at DATETIME
        )');
        try {
            $this->session(['lab.admin' => true]);
            $this->enableCsrfToken();
            $this->post('/admin/journal', [
                'category' => 'Integration',
                'title_cs' => 'Test vrácení změny',
                'title_en' => $title,
                'body_cs' => 'Původní zápis',
                'body_en' => 'No partial publication',
                'visibility' => 'public',
            ]);
            $this->assertResponseCode(503);
            $row = self::row($db, 'SELECT COUNT(*) AS total
                FROM journal_entries WHERE title_en = :title', ['title' => $title]);
            self::assertSame(0, self::number($row['total'] ?? null));
        } finally {
            $db->execute('DROP TEMPORARY TABLE IF EXISTS editorial_audit');
            $db->execute('DELETE FROM journal_entries WHERE title_en = :title', ['title' => $title]);
        }
    }

    public function testProjectOverridePersistsAndProducesAuditReceipt(): void
    {
        $this->session(['lab.admin' => true]);
        $this->enableCsrfToken();
        $values = [
            'slug' => 'local-ai-hub', 'status' => 'maintenance',
            'summary_cs' => 'Ověřený text pro test', 'summary_en' => 'Verified integration text',
            'problem_cs' => '', 'problem_en' => '', 'solution_cs' => '', 'solution_en' => '',
        ];
        $db = self::db();
        try {
            $this->post('/admin/project', $values);
            $this->assertRedirect('/admin?saved=1');
            $record = self::row($db, 'SELECT summary_en,status FROM project_overrides WHERE slug = :slug', [
                'slug' => 'local-ai-hub',
            ]);
            self::assertSame('Verified integration text', self::text($record['summary_en'] ?? null));
            self::assertSame('maintenance', self::text($record['status'] ?? null));
            $audit = self::row($db, 'SELECT COUNT(*) AS total FROM editorial_audit WHERE entity_type = :type AND entity_key = :key', [
                'type' => 'project', 'key' => 'local-ai-hub',
            ]);
            self::assertGreaterThan(0, self::number($audit['total'] ?? null));
        } finally {
            $db->execute('DELETE FROM editorial_audit WHERE entity_type = :type AND entity_key = :key', [
                'type' => 'project', 'key' => 'local-ai-hub',
            ]);
            $db->execute('DELETE FROM project_overrides WHERE slug = :slug', ['slug' => 'local-ai-hub']);
        }
    }

    public function testDraftCanBePublishedAndUnpublishedWithoutLeakingHtml(): void
    {
        $this->session(['lab.admin' => true]);
        $this->enableCsrfToken();
        $id = 0;
        $db = self::db();
        $values = [
            'category' => 'Integration', 'title_cs' => 'Jednorázový test', 'title_en' => 'One-time test',
            'body_cs' => '<script>alert(1)</script> běžný text',
            'body_en' => '<script>alert(1)</script> plain text', 'visibility' => 'draft',
        ];
        try {
            $this->post('/admin/journal', $values);
            $this->assertRedirect('/admin?saved=1');
            // The POST sets a server-side receipt. The CakePHP integration
            // harness does not propagate mutated sessions between requests,
            // so explicitly seed the next request as a redirected browser would.
            $this->assertSession(true, 'lab.saveConfirmed');
            $this->session(['lab.saveConfirmed' => true]);
            $this->get('/admin?saved=1');
            $this->assertResponseOk();
            $this->assertResponseContains('Changes saved successfully.');
            $this->assertSessionNotHasKey('lab.saveConfirmed');
            $row = self::row($db, 'SELECT id,is_public FROM journal_entries WHERE title_en = :title ORDER BY id DESC LIMIT 1', [
                'title' => 'One-time test',
            ]);
            $id = self::number($row['id'] ?? null);
            self::assertSame(0, self::number($row['is_public'] ?? null));

            $this->post('/admin/journal/update', ['id' => (string)$id, 'operation' => 'publish']);
            $this->assertRedirect('/admin?saved=1');
            $row = self::row($db, 'SELECT is_public,published_at FROM journal_entries WHERE id = :id', ['id' => $id]);
            self::assertSame(1, self::number($row['is_public'] ?? null));
            self::assertNotEmpty($row['published_at'] ?? null);

            // An already public note must keep its original publication date
            // even when an administrator submits the publish operation again.
            $connectionDate = '2024-01-02 03:04:05';
            $db->execute('UPDATE journal_entries SET published_at = :published WHERE id = :id', [
                'published' => $connectionDate, 'id' => $id,
            ]);
            $this->post('/admin/journal/update', ['id' => (string)$id, 'operation' => 'publish']);
            $this->assertRedirect('/admin?saved=1');
            $row = self::row($db, 'SELECT published_at FROM journal_entries WHERE id = :id', ['id' => $id]);
            self::assertSame($connectionDate, self::text($row['published_at'] ?? null));

            $this->get('/cs/journal');
            $this->assertResponseOk();
            $this->assertResponseContains('&lt;script&gt;alert(1)&lt;/script&gt;');
            $this->assertResponseContains('id="entry-db-' . $id . '"');

            $this->get('/cs/feed.xml');
            $this->assertResponseOk();
            $this->assertResponseContains('/cs/journal#entry-db-' . $id);

            $this->post('/admin/journal/update', ['id' => (string)$id, 'operation' => 'unpublish']);
            $this->assertRedirect('/admin?saved=1');
            $row = self::row($db, 'SELECT is_public FROM journal_entries WHERE id = :id', ['id' => $id]);
            self::assertSame(0, self::number($row['is_public'] ?? null));

            // Unpublication must disappear immediately from both languages
            // and their RSS feeds, not just from the database flag.
            foreach (['cs', 'en'] as $locale) {
                $this->get('/' . $locale . '/journal');
                $this->assertResponseOk();
                $this->assertResponseNotContains('id="entry-db-' . $id . '"');
                $this->get('/' . $locale . '/feed.xml');
                $this->assertResponseOk();
                $this->assertResponseNotContains('/' . $locale . '/journal#entry-db-' . $id);
            }
        } finally {
            if ($id !== 0) {
                $db->execute('DELETE FROM editorial_audit WHERE entity_type = :type AND entity_key = :key', [
                    'type' => 'journal', 'key' => (string)$id,
                ]);
                $db->execute('DELETE FROM journal_entries WHERE id = :id', ['id' => $id]);
            }
        }
    }
}
