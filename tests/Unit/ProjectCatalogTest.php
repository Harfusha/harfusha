<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\Project;
use App\Domain\ProjectCatalog;
use PHPUnit\Framework\TestCase;

final class ProjectCatalogTest extends TestCase
{
    public function testCuratedProjectsAreSafeAndUnique(): void
    {
        $catalog = new ProjectCatalog(ROOT . '/data');
        $projects = $catalog->projects();
        self::assertCount(13, $projects);
        self::assertSame(count($projects), count(array_unique(array_map(static fn(Project $p): string => $p->slug, $projects))));
        foreach ($projects as $project) {
            self::assertNotSame('', $project->name);
            self::assertNotSame('', $project->summaryCs);
            self::assertNotSame('', $project->summaryEn);
            self::assertContains($project->status, ['active','prototype','maintenance','archived']);
            if ($project->private) {
                self::assertSame('', $project->repo);
            }
        }
    }

    public function testValidatedDatasetsAreReadOncePerCatalogInstance(): void
    {
        $directory = sys_get_temp_dir() . '/harfusha-catalog-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($directory, 0700));
        try {
            self::assertTrue(copy(ROOT . '/data/journal.json', $directory . '/journal.json'));
            $catalog = new ProjectCatalog($directory);
            $original = $catalog->journal();
            self::assertNotEmpty($original);

            // Simulate a new deployment while a request is already rendering.
            file_put_contents($directory . '/journal.json', '[]');
            self::assertSame($original, $catalog->journal());
            self::assertSame([], (new ProjectCatalog($directory))->journal());
        } finally {
            unlink($directory . '/journal.json');
            rmdir($directory);
        }
    }

    public function testUnknownProjectDoesNotExist(): void
    {
        self::assertNull((new ProjectCatalog(ROOT . '/data'))->find('not-a-project'));
    }
}
