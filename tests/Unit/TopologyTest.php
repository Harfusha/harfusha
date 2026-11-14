<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\NetworkFlow;
use App\Domain\ProjectCatalog;
use App\Domain\TopologyGraph;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TopologyTest extends TestCase
{
    public function testCuratedFlowsFollowExistingLinks(): void
    {
        $catalog = new ProjectCatalog(ROOT . '/data');
        $flows = $catalog->flows();
        self::assertCount(4, $flows);
        foreach ($flows as $flow) {
            self::assertCount(count($flow->path), $flow->stepsCs);
            self::assertCount(count($flow->path), $flow->stepsEn);
        }
        $invalid = $flows[0]->toArray();
        $invalid['path'] = ['clients', 'nas'];
        $invalid['steps_cs'] = ['A', 'B'];
        $invalid['steps_en'] = ['A', 'B'];
        $this->expectException(InvalidArgumentException::class);
        NetworkFlow::fromArray($invalid, $catalog->topology());
    }

    public function testDependenciesAreInternalToPublicModel(): void
    {
        $nodes = (new ProjectCatalog(ROOT . '/data'))->topology();
        self::assertCount(7, $nodes);
        $graph = new TopologyGraph($nodes);
        self::assertCount(6, $graph->affectedBy('router'));
        $ids = [];
        foreach ($nodes as $node) {
            $id = $node['id'] ?? null;
            self::assertIsString($id);
            $ids[] = $id;
        }
        self::assertSame(count($ids), count(array_unique($ids)));
        foreach ($nodes as $node) {
            $deps = $node['depends'] ?? null;
            self::assertIsArray($deps);
            foreach ($deps as $dependence) {
                self::assertContains($dependence, $ids);
            }
        }
        $raw = file_get_contents(ROOT . '/data/topology.json');
        self::assertIsString($raw);
        self::assertStringNotContainsString('192.168.', $raw);
        self::assertStringNotContainsString('10.0.0.', $raw);
    }
}
