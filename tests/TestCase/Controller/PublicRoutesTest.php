<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Domain\JournalFeed;
use App\Domain\ProjectCatalog;
use App\Test\Support\HttpIntegrationTestCase;

/** Exercises the actual CakePHP middleware, routing and template pipeline. */
final class PublicRoutesTest extends HttpIntegrationTestCase
{
    public function testPublicPagesAndProjectDetails(): void
    {
        foreach (
            ['/cs/', '/en/', '/cs/projects', '/cs/homelab', '/en/maker',
            '/cs/connections', '/en/decisions', '/en/open-source', '/cs/journal'] as $path
        ) {
            $this->get($path);
            $this->assertResponseOk();
            $this->assertResponseContains('HARFUSHA');
        }
        $this->get('/cs/projects/local-ai-hub');
        $this->assertResponseOk();
        $this->assertResponseContains('Local AI Hub');
    }

    public function testUnknownSlugIs404(): void
    {
        $this->get('/cs/projects/not-a-real-project');
        $this->assertResponseCode(404);
    }

    public function testClientErrorTemplateNeverClaimsAnIncorrectStatusCode(): void
    {
        $this->get('/fr/projects');
        $this->assertResponseCode(404);
        $this->assertResponseContains('4XX / CLIENT ERROR');

        $this->get('/api/v1/projects?lang=fr');
        $this->assertResponseCode(400);
        $this->assertResponseContains('4XX / CLIENT ERROR');
        $this->assertResponseNotContains('404 / CLIENT ERROR');
    }

    public function testUnknownLanguageIs404(): void
    {
        $this->get('/fr/projects');
        $this->assertResponseCode(404);
    }

    public function testMutablePublicPagesAndRssAreNotStoredByCaches(): void
    {
        foreach (
            [
                '/en/', '/en/projects', '/en/projects/local-ai-hub',
                '/cs/journal', '/en/now', '/en/open-source',
                '/cs/feed.xml', '/en/feed.xml',
            ] as $path
        ) {
            $this->get($path);
            $this->assertResponseOk();
            $this->assertHeaderContains('Cache-Control', 'no-store');
        }
    }

    public function testSitemapAndFeeds(): void
    {
        $this->get('/sitemap.xml');
        $this->assertResponseOk();
        $this->assertResponseContains('<urlset');
        $this->assertResponseContains('/cs/projects/local-ai-hub');
        $this->assertResponseNotContains('/assets/img/scale-studio.webp');
        $this->assertResponseNotContains('/assets/img/tiskov-bambu-lab-a1.webp');

        $this->get('/cs/feed.xml');
        $this->assertResponseOk();
        $this->assertResponseContains('<rss version="2.0"');
    }

    public function testRssPermalinksResolveToPublishedJournalArticles(): void
    {
        $entries = (new ProjectCatalog(ROOT . '/data'))->journal();
        self::assertNotEmpty($entries);
        $id = JournalFeed::entryId($entries[0]);

        foreach (['cs', 'en'] as $locale) {
            $this->get('/' . $locale . '/feed.xml');
            $this->assertResponseOk();
            $this->assertResponseContains('/' . $locale . '/journal#' . $id);

            $this->get('/' . $locale . '/journal');
            $this->assertResponseOk();
            $this->assertResponseContains('id="' . $id . '"');
        }
    }

    public function testHealthDoesNotLeakDependencies(): void
    {
        $this->get('/healthz');
        $this->assertResponseOk();
        $this->assertResponseContains('ok');
    }
}
