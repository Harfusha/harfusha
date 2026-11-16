<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\ProjectCatalog;
use App\Domain\ProjectConnections;
use App\Support\Language;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ProjectConnectionsTest extends TestCase
{
    public function testEveryLinkResolvesAndIsUnique(): void
    {
        $catalog = new ProjectCatalog(ROOT . '/data');
        $edges = $catalog->connections();
        self::assertNotEmpty($edges);
        $keys = [];
        foreach ($edges as $edge) {
            self::assertNotNull($catalog->find($edge['from']));
            self::assertNotNull($catalog->find($edge['to']));
            $keys[] = $edge['from'] . '/' . $edge['to'];
        }
        self::assertCount(count(array_unique($keys)), $keys);
    }

    public function testRelationshipLabelsAreLocalized(): void
    {
        self::assertSame('podpora vývoje', ProjectConnections::label('assists-development', new Language('cs')));
        self::assertSame('development tooling', ProjectConnections::label('assists-development', new Language('en')));
    }

    public function testUnknownDestinationFailsClosed(): void
    {
        $catalog = new ProjectCatalog(ROOT . '/data');
        $this->expectException(InvalidArgumentException::class);
        new ProjectConnections([['from' => 'local-ai-hub', 'to' => 'not-a-project', 'type' => 'hosts']], $catalog->projects());
    }

    public function testDuplicateRelationshipFailsClosed(): void
    {
        $catalog = new ProjectCatalog(ROOT . '/data');
        $edge = ['from' => 'local-ai-hub', 'to' => 'mutaui', 'type' => 'assists-development'];
        $this->expectException(InvalidArgumentException::class);
        new ProjectConnections([$edge, $edge], $catalog->projects());
    }
}
