<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Support\Html;
use App\Test\Support\HttpIntegrationTestCase;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use RuntimeException;

final class AdminControllerTest extends HttpIntegrationTestCase
{
    public function testAdminPagesAlwaysUseFingerprintBasedAssets(): void
    {
        $css = Html::versionedAsset('assets/css/app.css');
        $this->get('/admin');
        $this->assertResponseOk();
        $this->assertResponseContains($css);

        $this->session(['lab.admin' => true]);
        $this->get('/admin');
        $this->assertResponseOk();
        $this->assertResponseContains($css);
        $this->assertResponseContains(Html::versionedAsset('assets/js/app.js'));
        $this->assertResponseContains(Html::versionedAsset('assets/js/lab-core.js'));
    }

    public function testLoginPageSetsNoStoreAndNoIndex(): void
    {
        $this->get('/admin');
        $this->assertResponseOk();
        $this->assertHeaderContains('Cache-Control', 'no-store');
        $this->assertHeaderContains('X-Robots-Tag', 'noindex');
    }

    public function testSaveConfirmationCannotBeForgedByQueryString(): void
    {
        $this->session(['lab.admin' => true]);
        $this->get('/admin?saved=1');
        $this->assertResponseOk();
        $this->assertResponseNotContains('Changes saved successfully.');
    }

    public function testSuccessfulSaveReceiptIsOnlyDisplayedOnce(): void
    {
        $this->session(['lab.admin' => true, 'lab.saveConfirmed' => true]);
        $this->get('/admin');
        $this->assertResponseOk();
        $this->assertResponseContains('role="status"');
        $this->assertResponseContains('Changes saved successfully.');

        // IntegrationTestTrait seeds its _session state afresh for every
        // request; inspect the actual response session to test consumption.
        $this->assertSessionNotHasKey('lab.saveConfirmed');
    }

    public function testIncompleteDatabaseSchemaDisablesAdminEditorButtons(): void
    {
        $connection = ConnectionManager::get('default');
        if (!$connection instanceof Connection) {
            throw new RuntimeException('Expected disposable MySQL test database');
        }
        // Include an existing draft: previously only new-entry buttons were
        // disabled, leaving management actions active on an incomplete schema.
        $title = 'Readiness gate editorial fixture';
        $connection->execute(
            'INSERT INTO journal_entries
                (title_cs,title_en,body_cs,body_en,category,source_url,is_public,published_at)
                VALUES (:title_cs,:title_en,:body_cs,:body_en,:category,:source_url,:is_public,UTC_TIMESTAMP())',
            [
                'title_cs' => 'Test připravenosti', 'title_en' => $title,
                'body_cs' => 'Test', 'body_en' => 'Test', 'category' => 'Testing',
                'source_url' => '', 'is_public' => 0,
            ],
        );
        try {
            $connection->execute('CREATE TEMPORARY TABLE editorial_audit (id BIGINT NOT NULL)');
            $this->session(['lab.admin' => true]);
            $this->get('/admin');
            $this->assertResponseOk();
            $this->assertResponseContains('Editorial changes are unavailable');
            $this->assertResponseContains('type="submit" disabled>Save project</button>');
            $this->assertResponseContains('type="submit" disabled>Save journal note</button>');
            $this->assertResponseContains('type="submit" disabled>Save changes</button>');
            $this->assertResponseContains('type="submit" disabled>Publish</button>');
        } finally {
            $connection->execute('DROP TEMPORARY TABLE IF EXISTS editorial_audit');
            $connection->execute('DELETE FROM journal_entries WHERE title_en = :title', ['title' => $title]);
        }
    }

    public function testIncompleteDatabaseSchemaRejectsEveryEditorialWrite(): void
    {
        $connection = ConnectionManager::get('default');
        if (!$connection instanceof Connection) {
            throw new RuntimeException('Expected disposable MySQL test database');
        }
        $connection->execute('CREATE TEMPORARY TABLE editorial_audit (id BIGINT NOT NULL)');
        try {
            $this->session(['lab.admin' => true]);
            $this->enableCsrfToken();
            $this->post('/admin/project', [
                'slug' => 'local-ai-hub', 'status' => 'active',
                'summary_cs' => 'Text', 'summary_en' => 'Text',
                'problem_cs' => '', 'problem_en' => '', 'solution_cs' => '', 'solution_en' => '',
            ]);
            $this->assertResponseCode(503);
            $this->post('/admin/journal', [
                'category' => 'Testing', 'title_cs' => 'Zápis', 'title_en' => 'Note',
                'body_cs' => 'Obsah', 'body_en' => 'Body', 'visibility' => 'draft',
            ]);
            $this->assertResponseCode(503);
            $this->post('/admin/journal/update', ['id' => '1', 'operation' => 'publish']);
            $this->assertResponseCode(503);
        } finally {
            $connection->execute('DROP TEMPORARY TABLE IF EXISTS editorial_audit');
        }
    }

    public function testProjectEditFailsWithoutSession(): void
    {
        $this->enableCsrfToken();
        $this->post('/admin/project', ['slug' => 'local-ai-hub','summary_cs' => 'x','summary_en' => 'y','status' => 'active']);
        $this->assertResponseCode(403);
    }

    public function testJournalUpdateFailsWithoutSession(): void
    {
        $this->enableCsrfToken();
        $this->post('/admin/journal/update', ['id' => '1','operation' => 'publish']);
        $this->assertResponseCode(403);
    }

    public function testUnknownAdminActionNotRoutable(): void
    {
        $this->get('/admin/invoke-any-method');
        $this->assertResponseCode(404);
    }
}
