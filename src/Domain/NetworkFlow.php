<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * A deliberately abstract, hand-reviewed request path through a public graph.
 * No IP addresses, port numbers, hostnames or remote execution capabilities.
 */
final readonly class NetworkFlow
{
    /**
     * @param list<string> $path
     * @param list<string> $stepsCs
     * @param list<string> $stepsEn
     */
    private function __construct(
        public string $id,
        public string $titleCs,
        public string $titleEn,
        public string $descriptionCs,
        public string $descriptionEn,
        public array $path,
        public array $stepsCs,
        public array $stepsEn,
    ) {
    }

    /**
     * @param array<string,mixed> $row
     * @param list<array<string,mixed>> $nodes
     */
    public static function fromArray(array $row, array $nodes): self
    {
        $id = $row['id'] ?? null;
        if (!is_string($id) || preg_match('/^[a-z][a-z0-9-]{0,31}$/D', $id) !== 1) {
            throw new InvalidArgumentException('Invalid public flow identifier');
        }
        $titleCs = self::requiredText($row, 'title_cs');
        $titleEn = self::requiredText($row, 'title_en');
        $descriptionCs = self::requiredText($row, 'description_cs');
        $descriptionEn = self::requiredText($row, 'description_en');
        $path = self::listOfStrings($row['path'] ?? null, 2, 16, 32);
        $cs = self::listOfStrings($row['steps_cs'] ?? null, count($path), count($path), 200);
        $en = self::listOfStrings($row['steps_en'] ?? null, count($path), count($path), 200);

        $neighbors = [];
        foreach ($nodes as $node) {
            $nodeId = $node['id'] ?? null;
            $dependencies = $node['depends'] ?? null;
            if (!is_string($nodeId) || !is_array($dependencies) || !array_is_list($dependencies) || isset($neighbors[$nodeId])) {
                throw new InvalidArgumentException('Invalid public flow topology');
            }
            $neighbors[$nodeId] = [];
            foreach ($dependencies as $dependency) {
                if (!is_string($dependency)) {
                    throw new InvalidArgumentException('Invalid public flow neighbor');
                }
                $neighbors[$nodeId][] = $dependency;
            }
        }
        foreach ($path as $index => $nodeId) {
            if (!isset($neighbors[$nodeId])) {
                throw new InvalidArgumentException('Public flow refers to unknown node');
            }
            if ($index === 0) {
                continue;
            }
            $previous = $path[$index - 1] ?? null;
            $previousNeighbors = $previous !== null ? ($neighbors[$previous] ?? null) : null;
            $currentNeighbors = $neighbors[$nodeId];
            if ($previous === null || $previousNeighbors === null ||
                $previous === $nodeId ||
                (!in_array($previous, $currentNeighbors, true) && !in_array($nodeId, $previousNeighbors, true))) {
                throw new InvalidArgumentException('Public flow includes a disconnected hop');
            }
        }
        return new self($id, $titleCs, $titleEn, $descriptionCs, $descriptionEn, $path, $cs, $en);
    }

    /** @return array{id:string,title_cs:string,title_en:string,description_cs:string,description_en:string,path:list<string>,steps_cs:list<string>,steps_en:list<string>} */
    public function toArray(): array
    {
        return [
            'id' => $this->id, 'title_cs' => $this->titleCs, 'title_en' => $this->titleEn,
            'description_cs' => $this->descriptionCs, 'description_en' => $this->descriptionEn,
            'path' => $this->path, 'steps_cs' => $this->stepsCs, 'steps_en' => $this->stepsEn,
        ];
    }

    /** @param array<string,mixed> $row */
    private static function requiredText(array $row, string $key): string
    {
        $value = $row[$key] ?? null;
        if (!is_string($value) || trim($value) === '' || mb_strlen($value, 'UTF-8') > 600) {
            throw new InvalidArgumentException('Invalid public flow text: ' . $key);
        }
        return $value;
    }

    /** @return list<string> */
    private static function listOfStrings(mixed $value, int $min, int $max, int $maxChars): array
    {
        if (!is_array($value) || !array_is_list($value) || count($value) < $min || count($value) > $max) {
            throw new InvalidArgumentException('Invalid public flow step list');
        }
        $result = [];
        foreach ($value as $item) {
            if (!is_string($item) || trim($item) === '' || mb_strlen($item, 'UTF-8') > $maxChars) {
                throw new InvalidArgumentException('Invalid public flow step');
            }
            $result[] = $item;
        }
        return $result;
    }
}
