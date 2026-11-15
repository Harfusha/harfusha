<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

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

    public function testUnknownLanguageIs404(): void
    {
        $this->get('/fr/projects');
        $this->assertResponseCode(404);
    }

    public function testSitemapAndFeeds(): void
    {
        $this->get('/sitemap.xml');
        $this->assertResponseOk();
        $this->assertResponseContains('<urlset');
        $this->assertResponseContains('/cs/projects/local-ai-hub');

        $this->get('/cs/feed.xml');
        $this->assertResponseOk();
        $this->assertResponseContains('<rss version="2.0"');
    }

    public function testHealthDoesNotLeakDependencies(): void
    {
        $this->get('/healthz');
        $this->assertResponseOk();
        $this->assertResponseContains('ok');
    }
}
