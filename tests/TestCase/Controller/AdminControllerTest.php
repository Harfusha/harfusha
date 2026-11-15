<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Support\HttpIntegrationTestCase;

final class AdminControllerTest extends HttpIntegrationTestCase
{
    public function testLoginPageSetsNoStoreAndNoIndex(): void
    {
        $this->get('/admin');
        $this->assertResponseOk();
        $this->assertHeaderContains('Cache-Control', 'no-store');
        $this->assertHeaderContains('X-Robots-Tag', 'noindex');
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
