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
        $this->assertResponseContains('<strong>' . count($allProjects) . '</strong><span>selected projects');
        $this->assertResponseContains('<strong>' . $mergedPrCount . '</strong><span>merged PRs in this feed');
        $this->assertResponseContains('<strong>' . $recentRepoCount . '</strong><span>recently updated repositories');
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
        $this->assertResponseContains('Historie příspěvků');
        $this->assertResponseContains('data-now-activity');
        $this->assertResponseContains('data-now-notes');
        $this->assertResponseContains('6.1.0: metadata vydání odpovídají tagu');
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
        $this->assertResponseContains('02 / REÁLNÁ ALTERNATIVA');
        $this->assertResponseContains('Craftcloud');
        $this->assertResponseContains('Spotify Jam');
        $this->assertResponseContains('Neprokazuje, že Jam tuto potřebu nepokrývá');
    }

    public function testProjectMapIsAnIndexInsteadOfGuessingRelationships(): void
    {
        $this->get('/cs/connections');
        $this->assertResponseOk();
        $this->assertResponseContains('Devět projektů. Tři technické okruhy.');
        $this->assertResponseContains('data-map-category');
        $this->assertResponseContains('data-map-state');
        $this->assertResponseContains('data-map-project');
        $this->assertResponseContains('Tiskov');
        $this->assertResponseContains('Discord bot');
        $this->assertResponseNotContains('spolupracují');
    }

    public function testRetiredPlaygroundLinkRedirectsToTheCuratedNetworkModel(): void
    {
        $this->get('/cs/playground');
        $this->assertResponseCode(301);
        $this->assertRedirect('/cs/homelab');
    }

    public function testProjectsDoNotFakeUnavailableOrUnimplementedInteractions(): void
    {
        $this->get('/cs/projects/local-ai-hub');
        $this->assertResponseOk();
        $this->assertResponseContains('Veřejný zdroj');
        $this->assertResponseNotContains('/playground');
        $this->assertResponseNotContains('data-hub-run');

        $this->get('/cs/projects/scan2cad');
        $this->assertResponseOk();
        $this->assertResponseContains('Beta aplikace provází rekonstrukci parametrického CAD modelu');
        $this->assertResponseContains('Co zatím není ověřeno');
        $this->assertResponseNotContains('/playground');
        $this->assertResponseNotContains('data-scan-scale');

        $this->get('/cs/projects/tiskov');
        $this->assertResponseOk();
        $this->assertResponseContains('Raspberry Pi');
        $this->assertResponseNotContains('data-print-next');

        $this->get('/cs/projects/discord-bot');
        $this->assertResponseOk();
        $this->assertResponseContains('Ověřený provozní rozsah');
        $this->assertResponseContains('Spravovat službu přes Ansible a systemd');
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
        $this->assertResponseContains('Nine projects. Three engineering themes.');
        $this->assertResponseContains('data-map-project');
        $this->assertResponseNotContains('maker-lab');
    }

    public function testMakerOverviewSeparatesTheProjectsAndOmitsUnverifiedEntries(): void
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
        $this->assertResponseContains('Dva samostatné projekty');
        $this->assertResponseContains('Scan2CAD Studio');
        $this->assertResponseContains('Tiskov');
        $this->assertResponseContains('Beta');
        $this->assertResponseContains('Návrh');
        $this->assertResponseNotContains('Solární sušička filamentu');
        $this->get('/en/maker');
        $this->assertResponseOk();
        $this->assertResponseContains('Two distinct projects');
        $this->assertResponseContains('Proposal');
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
