<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\ProjectCatalog;
use App\Domain\PublicProjectView;
use App\Support\Language;
use PHPUnit\Framework\TestCase;

final class PublicProjectViewTest extends TestCase
{
    public function testApiPayloadIsBilingualAndDoesNotDisclosePrivateSource(): void
    {
        $catalog = new ProjectCatalog(dirname(__DIR__, 2) . '/data');
        $project = $catalog->find('woodbound');
        self::assertNotNull($project);
        $view = new PublicProjectView($project);
        $cs = $view->toArray(new Language('cs'));
        $en = $view->toArray(new Language('en'));
        self::assertSame($project->name, $cs['name']);
        self::assertSame($project->summaryCs, $cs['summary']);
        self::assertSame($project->summaryEn, $en['summary']);
        self::assertNull($cs['repository']);
        self::assertSame($cs['slug'], $en['slug']);
        self::assertArrayNotHasKey('private', $cs);
    }

    public function testDemoLinksAreLocalAndUseTheRequestedLanguage(): void
    {
        $catalog = new ProjectCatalog(dirname(__DIR__, 2) . '/data');
        $ai = $catalog->find('local-ai-hub');
        $private = $catalog->find('woodbound');
        $homelab = $catalog->find('home-infrastructure');
        $withoutDemo = $catalog->find('tiskov');
        self::assertNotNull($ai);
        self::assertNotNull($private);
        self::assertNotNull($homelab);
        self::assertNotNull($withoutDemo);

        self::assertSame('/en/playground#ai', (new PublicProjectView($ai))->toArray(new Language('en'))['demo']);
        self::assertSame('/cs/playground#world', (new PublicProjectView($private))->toArray(new Language('cs'))['demo']);
        self::assertSame('/en/homelab', (new PublicProjectView($homelab))->toArray(new Language('en'))['demo']);
        self::assertNull((new PublicProjectView($withoutDemo))->toArray(new Language('en'))['demo']);
    }

    public function testApiAllowsOnlyReviewedPublicRepositoryLinks(): void
    {
        $catalog = new ProjectCatalog(dirname(__DIR__, 2) . '/data');
        $project = $catalog->find('local-ai-hub');
        self::assertNotNull($project);
        $view = (new PublicProjectView($project))->toArray(new Language('en'));
        self::assertSame('https://github.com/Harfusha/local-ai-hub', $view['repository']);
    }
}
