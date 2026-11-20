<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Domain\JournalFeed;
use App\Domain\ProjectCatalog;
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

    public function testJournalPreviewIsDistinctFromCurrentStatusPage(): void
    {
        $entries = (new ProjectCatalog(ROOT . '/data'))->journal();
        self::assertNotEmpty($entries);
        $id = JournalFeed::entryId($entries[0]);
        $this->get('/en/');
        $this->assertResponseOk();
        $this->assertResponseContains('/en/journal#' . $id);
        $this->get('/en/now');
        $this->assertResponseOk();
        $this->assertResponseNotContains('/en/journal#' . $id);
        $this->assertResponseNotContains('Traceable development notes');
        $this->get('/en/journal');
        $this->assertResponseOk();
        $this->assertResponseContains('id="' . $id . '"');
        $this->get('/cs/now');
        $this->assertResponseOk();
        $this->assertResponseNotContains('/cs/journal#' . $id);
        $this->get('/cs/journal');
        $this->assertResponseOk();
        $this->assertResponseContains('id="' . $id . '"');
    }

    public function testOpenSourcePageDoesNotClaimTheFirstImportIsPending(): void
    {
        foreach (['/cs/open-source', '/en/open-source'] as $path) {
            $this->get($path);
            $this->assertResponseOk();
            $this->assertResponseNotContains('před prvním importem');
            $this->assertResponseNotContains('until the first import');
        }
    }

    public function testClosedIssuesArePresentedAsCompletedWork(): void
    {
        $this->get('/cs/open-source');
        $this->assertResponseOk();
        $this->assertResponseContains('Dokončeno');
        $this->assertResponseContains('class="status completed"');
        $this->assertResponseNotContains('Uzavřeno');

        $this->get('/en/open-source');
        $this->assertResponseOk();
        $this->assertResponseContains('Completed');
        $this->assertResponseNotContains('Closed');
    }

    public function testNowPageIncludesAHistoryOfPublicWork(): void
    {
        $this->get('/cs/now');
        $this->assertResponseOk();
        $this->assertResponseContains('Historie veřejné práce');
        $this->assertResponseContains('data-now-activity');
        $this->assertResponseContains('data-now-notes');
        $this->assertResponseContains('6.1.0: release metadata se musí potkat se zdrojem');
    }

    public function testJournalHasAnExpandedSourceLinkedTimeline(): void
    {
        $entries = (new ProjectCatalog(ROOT . '/data'))->journal();
        self::assertGreaterThanOrEqual(14, count($entries));
        $this->get('/en/journal');
        $this->assertResponseOk();
        $this->assertResponseContains('6.1.0: release metadata has to match its source');
        $this->assertResponseContains('Scan2CAD: scale before projection');
        $this->assertResponseContains('CHANGELOG.md');
    }

    public function testDecisionCardsCompareAlternativesWithoutDeclaringThemInadequate(): void
    {
        $this->get('/cs/decisions');
        $this->assertResponseOk();
        $this->assertResponseContains('CO UŽ UMÍ JINÉ ŘEŠENÍ');
        $this->assertResponseContains('Craftcloud');
        $this->assertResponseContains('Spotify Jam');
        $this->assertResponseContains('Neprokazuje, že Jam tuto potřebu nepokrývá');
    }

    public function testProjectMapIsAnIndexInsteadOfGuessingRelationships(): void
    {
        $this->get('/cs/connections');
        $this->assertResponseOk();
        $this->assertResponseContains('Co stavím, proč a jak to funguje.');
        $this->assertResponseContains('Devět projektů, tři tematické okruhy. Okruh pomáhá najít podobný typ práce; neznamená technickou závislost mezi repozitáři.');
        $this->assertResponseContains('data-map-category');
        $this->assertResponseContains('data-map-state');
        $this->assertResponseContains('data-map-project');
        $this->assertResponseContains('Tiskov');
        $this->assertResponseContains('Discord bot');
        $this->assertResponseNotContains('spolupracují');
    }

    public function testPlaygroundContainsDistinctWorkingStations(): void
    {
        $this->get('/cs/playground');
        $this->assertResponseOk();
        foreach (['preprocessing', 'sync-preview', 'scale-check', 'route-finder', 'queue-conflict'] as $station) {
            $this->assertResponseContains('data-playground-station="' . $station . '"');
        }
        $this->assertResponseContains('data-preprocess-form');
        $this->assertResponseContains('data-preprocess-content');
        $this->assertResponseContains('data-preprocess-kind');
        $this->assertResponseContains('data-scale-mm');
        $this->assertResponseContains('data-route-grid');
        $this->assertResponseContains('data-queue-add');
        $this->assertResponseContains('playground-project-index');
        $this->assertResponseContains('Discord bot');
    }

    public function testProjectsDoNotFakeUnavailableOrUnimplementedInteractions(): void
    {
        $this->get('/cs/projects/local-ai-hub');
        $this->assertResponseOk();
        $this->assertResponseContains('/cs/playground#station-preprocessing');
        $this->assertResponseNotContains('data-hub-run');

        $this->get('/cs/projects/scan2cad');
        $this->assertResponseOk();
        $this->assertResponseContains('/cs/playground#station-scale-check');
        $this->assertResponseNotContains('data-scan-scale');

        $this->get('/cs/projects/tiskov');
        $this->assertResponseOk();
        $this->assertResponseContains('Raspberry Pi worker');
        $this->assertResponseNotContains('data-print-next');

        $this->get('/cs/projects/discord-bot');
        $this->assertResponseOk();
        $this->assertResponseContains('Zdrojový repozitář nemám');
        $this->assertResponseNotContains('data-discord-role');
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
        $this->assertResponseContains('Unbound');
        $this->assertResponseContains('Restic');
        $this->assertResponseContains('CUPS');
        $this->assertResponseContains('DoT/DoH');
        $this->assertResponseNotContains('192.168.');
    }

    public function testConnectionsRenderIndependentProjectIndex(): void
    {
        $this->get('/cs/connections');
        $this->assertResponseOk();
        $this->assertResponseContains('data-map-project');
        $this->assertResponseNotContains('project-map-route');
        $this->assertResponseNotContains('maker-lab');
        $this->assertResponseNotContains('192.168.');
        $this->get('/en/connections');
        $this->assertResponseOk();
        $this->assertResponseContains('Nine projects, three thematic groups. A group helps find similar work; it does not imply technical dependencies between repositories.');
        $this->assertResponseContains('data-map-project');
        $this->assertResponseNotContains('maker-lab');
    }

    public function testMakerPreparationPageDoesNotPublishUnverifiedProjects(): void
    {
        self::assertSame([], (new ProjectCatalog(ROOT . '/data'))->maker());
        foreach (['/cs/projects', '/en/projects'] as $path) {
            $this->get($path);
            $this->assertResponseOk();
            $this->assertResponseNotContains('maker-lab');
        }
        foreach (['/cs/projects/maker-lab', '/en/projects/maker-lab'] as $path) {
            $this->get($path);
            $this->assertResponseCode(404);
        }
        $this->get('/cs/maker');
        $this->assertResponseOk();
        $this->assertResponseContains('Dílna se připravuje.');
        $this->assertResponseNotContains('Solární sušička filamentu');
    }

    public function testUnknownProjectReturns404(): void
    {
        $this->get('/cs/projects/nonexistent');
        $this->assertResponseCode(404);
        $this->get('/cs/projects/maker-lab');
        $this->assertResponseCode(404);
    }

    public function testCronRequiresHeader(): void
    {
        $this->post('/cron/github');
        $this->assertResponseCode(403);
    }
}
