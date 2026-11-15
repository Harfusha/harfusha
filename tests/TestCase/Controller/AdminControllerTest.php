<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Support\Html;
use App\Test\Support\HttpIntegrationTestCase;

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

    public function testSaveConfirmationOnlyAppearsAfterSuccessfulRedirect(): void
    {
        $this->session(['lab.admin' => true]);
        $this->get('/admin');
        $this->assertResponseOk();
        $this->assertResponseNotContains('Changes saved successfully.');

        $this->get('/admin?saved=1');
        $this->assertResponseOk();
        $this->assertResponseContains('role="status"');
        $this->assertResponseContains('Changes saved successfully.');
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
