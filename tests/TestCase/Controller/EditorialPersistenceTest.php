<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use RuntimeException;

/**
 * Real HTTP -> ORM -> MySQL persistence verification.
 * Runs only against the explicitly confirmed disposable CI database.
 */
final class EditorialPersistenceTest extends TestCase
{
    use IntegrationTestTrait;

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

            $this->get('/cs/journal');
            $this->assertResponseOk();
            $this->assertResponseContains('&lt;script&gt;alert(1)&lt;/script&gt;');

            $this->post('/admin/journal/update', ['id' => (string)$id, 'operation' => 'unpublish']);
            $this->assertRedirect('/admin?saved=1');
            $row = self::row($db, 'SELECT is_public FROM journal_entries WHERE id = :id', ['id' => $id]);
            self::assertSame(0, self::number($row['is_public'] ?? null));
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
