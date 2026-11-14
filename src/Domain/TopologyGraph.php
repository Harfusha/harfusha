<?php
declare(strict_types=1);
namespace App\Domain;

use InvalidArgumentException;

/**
 * Public, coarse-grained dependencies. Not a real hardware monitoring model.
 */
final readonly class TopologyGraph
{
    /** @var array<string,list<string>> */
    private array $dependencies;

    /** @param list<array<string,mixed>> $nodes */
    public function __construct(array $nodes)
    {
        $deps = [];
        foreach ($nodes as $node) {
            $id = $node['id'] ?? null;
            $depends = $node['depends'] ?? null;
            if (!is_string($id) || preg_match('/^[a-z0-9-]{1,32}$/D', $id) !== 1 || isset($deps[$id]) || !is_array($depends) || !array_is_list($depends)) {
                throw new InvalidArgumentException('Invalid or duplicate public topology node');
            }
            $values = [];
            foreach ($depends as $related) {
                if (!is_string($related)) {
                    throw new InvalidArgumentException('Invalid dependency identifier');
                }
                $values[] = $related;
            }
            $deps[$id] = $values;
        }
        foreach ($deps as $id => $depends) {
            foreach ($depends as $related) {
                if (!array_key_exists($related, $deps) || $related === $id) {
                    throw new InvalidArgumentException('Dangling/self dependency');
                }
            }
        }
        foreach (array_keys($deps) as $id) {
            $this->assertAcyclic($id, $deps, []);
        }
        $this->dependencies = $deps;
    }

    /** @param array<string,list<string>> $deps @param list<string> $path */
    private function assertAcyclic(string $id, array $deps, array $path): void
    {
        if (in_array($id, $path, true)) {
            throw new InvalidArgumentException('Cyclic public topology');
        }
        $path[] = $id;
        foreach ($deps[$id] as $related) {
            $this->assertAcyclic($related, $deps, $path);
        }
    }

    /**
     * A deterministic simulated impact from losing a component.
     * Real networks may have redundancy and alternative paths not reflected here.
     * @return list<string>
     */
    public function affectedBy(string $failed): array
    {
        if (!array_key_exists($failed, $this->dependencies)) {
            throw new InvalidArgumentException('Unknown node');
        }
        $down = [$failed];
        do {
            $before = count($down);
            foreach ($this->dependencies as $id => $depends) {
                if (count(array_intersect($depends, $down)) > 0 && !in_array($id, $down, true)) {
                    $down[] = $id;
                }
            }
        } while (count($down) > $before);
        return $down;
    }
}
