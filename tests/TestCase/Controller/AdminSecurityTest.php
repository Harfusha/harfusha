<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Support\HttpIntegrationTestCase;

/** Refusal tests must pass even when MySQL is unavailable or uninitialized. */
final class AdminSecurityTest extends HttpIntegrationTestCase
{
    public function testAnonymousEditorPostIsForbidden(): void
    {
        $this->enableCsrfToken();
        $this->post('/admin/project', [
            'slug' => 'local-ai-hub', 'status' => 'active',
            'summary_cs' => 'A', 'summary_en' => 'B',
            'problem_cs' => '', 'problem_en' => '', 'solution_cs' => '', 'solution_en' => '',
        ]);
        $this->assertResponseCode(403);
    }

    public function testAnonymousJournalUpdateIsForbidden(): void
    {
        $this->enableCsrfToken();
        $this->post('/admin/journal/update', ['id' => '1', 'operation' => 'publish']);
        $this->assertResponseCode(403);
    }

    public function testAuthenticatedInvalidPayloadIsRejectedBeforeDatabase(): void
    {
        $this->session(['lab.admin' => true]);
        $this->enableCsrfToken();
        $this->post('/admin/journal', [
            'category' => '<script>', 'title_cs' => 'A', 'title_en' => 'B',
            'body_cs' => 'C', 'body_en' => 'D',
        ]);
        $this->assertResponseCode(422);
    }

    public function testAuthenticatedEditorRejectsAmbiguousJournalVisibility(): void
    {
        $this->session(['lab.admin' => true]);
        $this->enableCsrfToken();
        $this->post('/admin/journal', [
            'category' => 'Development', 'title_cs' => 'Zápis', 'title_en' => 'Entry',
            'body_cs' => 'Text', 'body_en' => 'Text',
            'visibility' => 'published',
        ]);
        $this->assertResponseCode(422);
    }

    public function testCronRequiresSecretHeader(): void
    {
        $this->post('/cron/github', []);
        $this->assertResponseCode(403);
    }
}
