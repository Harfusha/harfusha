<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Runs ONLY against a disposable migrated MySQL test database.
 * Verifies that the real controller, Cake ORM, transactions and audit receipts
 * agree. CI must never point DB_NAME at a production schema.
 */
final class EditorialPersistenceTest extends TestCase
{
    use IntegrationTestTrait;

    public function testProjectOverridePersistsAndProducesAuditReceipt(): void
    {
        $this->session(['lab.admin' => true]);
        $this->enableCsrfToken();
        $values = [
            'slug' => 'local-ai-hub', 'status' => 'maintenance',
            'summary_cs' => 'Ověřený text pro test', 'summary_en' => 'Verified integration text',
            'problem_cs' => '', 'problem_en' => '', 'solution_cs' => '', 'solution_en' => '',
        ];
        try {
            $this->post('/admin/project', $values);
            $this->assertRedirect('/admin?saved=1');
            $db = ConnectionManager::get('default');
            $record = $db->execute('SELECT summary_en,status FROM project_overrides WHERE slug = :slug', [
                'slug' => 'local-ai-hub',
            ])->fetch('assoc');
            self::assertIsArray($record);
            self::assertSame('Verified integration text', $record['summary_en']);
            self::assertSame('maintenance', $record['status']);
            $audit = $db->execute('SELECT COUNT(*) AS total FROM editorial_audit WHERE entity_type = :type AND entity_key = :key', [
                'type' => 'project', 'key' => 'local-ai-hub',
            ])->fetch('assoc');
            self::assertIsArray($audit);
            self::assertGreaterThan(0, (int)$audit['total']);
        } finally {
            $db = ConnectionManager::get('default');
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
        $values = [
            'category' => 'Integration', 'title_cs' => 'Jednorázový test', 'title_en' => 'One-time test',
            'body_cs' => '<script>alert(1)</script> běžný text', 'body_en' => '<script>alert(1)</script> plain text',
            'visibility' => 'draft',
        ];
        try {
            $this->post('/admin/journal', $values);
            $this->assertRedirect('/admin?saved=1');
            $db = ConnectionManager::get('default');
            $row = $db->execute('SELECT id,is_public FROM journal_entries WHERE title_en = :title ORDER BY id DESC LIMIT 1', [
                'title' => 'One-time test',
            ])->fetch('assoc');
            self::assertIsArray($row);
            $id = (int)$row['id'];
            self::assertSame(0, (int)$row['is_public']);

            $this->post('/admin/journal/update', ['id' => (string)$id, 'operation' => 'publish']);
            $this->assertRedirect('/admin?saved=1');
            $row = $db->execute('SELECT is_public,published_at FROM journal_entries WHERE id = :id', ['id' => $id])->fetch('assoc');
            self::assertIsArray($row);
            self::assertSame(1, (int)$row['is_public']);
            self::assertNotEmpty($row['published_at']);

            $this->get('/cs/journal');
            $this->assertResponseOk();
            $this->assertResponseContains('&lt;script&gt;alert(1)&lt;/script&gt;');

            $this->post('/admin/journal/update', ['id' => (string)$id, 'operation' => 'unpublish']);
            $this->assertRedirect('/admin?saved=1');
            $row = $db->execute('SELECT is_public FROM journal_entries WHERE id = :id', ['id' => $id])->fetch('assoc');
            self::assertIsArray($row);
            self::assertSame(0, (int)$row['is_public']);
        } finally {
            if ($id !== 0) {
                $db = ConnectionManager::get('default');
                $db->execute('DELETE FROM editorial_audit WHERE entity_type = :type AND entity_key = :key', [
                    'type' => 'journal', 'key' => (string)$id,
                ]);
                $db->execute('DELETE FROM journal_entries WHERE id = :id', ['id' => $id]);
            }
        }
    }
}
