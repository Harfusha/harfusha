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

    public function testApiAllowsOnlyReviewedPublicRepositoryLinks(): void
    {
        $catalog = new ProjectCatalog(dirname(__DIR__, 2) . '/data');
        $project = $catalog->find('local-ai-hub');
        self::assertNotNull($project);
        $view = (new PublicProjectView($project))->toArray(new Language('en'));
        self::assertSame('https://github.com/Harfusha/local-ai-hub', $view['repository']);
    }
}
