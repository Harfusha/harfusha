<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Service\ContentRepository;
use App\Support\Html;
use App\Test\Support\HttpIntegrationTestCase;

/** These integration tests require installed CakePHP vendor dependencies. */
final class PagesControllerTest extends HttpIntegrationTestCase
{
    public function testPublicAndErrorPagesUseContentFingerprintAssets(): void
    {
        $css = Html::versionedAsset('assets/css/app.css');
        $this->get('/en/');
        $this->assertResponseOk();
        $this->assertResponseContains($css);
        $this->assertResponseContains(Html::versionedAsset('assets/js/app.js'));

        $this->get('/en/projects/not-a-real-project');
        $this->assertResponseCode(404);
        $this->assertResponseContains($css);
    }

    public function testHomeStatisticsReflectActualVisibleSources(): void
    {
        $content = new ContentRepository();
        $allProjects = $content->projects();
        $mergedPrCount = count(array_filter(
            $content->activity(),
            static fn(array $entry): bool => ($entry['kind'] ?? null) === 'pr'
                && ($entry['state'] ?? null) === 'merged',
        ));
        $recentRepoCount = count($content->publicRepoUpdates());

        $this->get('/en/');
        $this->assertResponseOk();
        $this->assertResponseContains('<strong>' . count($allProjects) . '</strong><span>curated project studies');
        $this->assertResponseContains('<strong>' . $mergedPrCount . '</strong><span>merged external PRs');
        $this->assertResponseContains('<strong>' . $recentRepoCount . '</strong><span>recent repository updates');
        $this->assertResponseNotContains('<strong>26</strong><span>repositories inventoried');
        $this->assertResponseNotContains('<strong>62</strong><span>merged external PRs');
    }

    public function testLandingPageWorksWithoutDatabase(): void
    {
        $this->get('/cs/');
        $this->assertResponseOk();
        $this->assertResponseContains('HARFUSHA');
    }

    public function testHomeLabContainsSafeSerializedModel(): void
    {
        $this->get('/en/homelab');
        $this->assertResponseOk();
        $this->assertResponseContains('topology-data');
        $this->assertResponseNotContains('192.168.');
    }

    public function testConnectionsRenderCuratedGraph(): void
    {
        $this->get('/cs/connections');
        $this->assertResponseOk();
        $this->assertResponseContains('connections-data');
        $this->assertResponseNotContains('192.168.');
    }

    public function testUnknownProjectReturns404(): void
    {
        $this->get('/cs/projects/nonexistent');
        $this->assertResponseCode(404);
    }

    public function testCronRequiresHeader(): void
    {
        $this->post('/cron/github');
        $this->assertResponseCode(403);
    }
}
