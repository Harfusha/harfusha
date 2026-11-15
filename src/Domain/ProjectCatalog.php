<?php
declare(strict_types=1);

namespace App\Domain;

use JsonException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Public content is an explicitly reviewed dataset, never a direct reflection of
 * private repositories. The catalogue can be rendered without MySQL connectivity.
 */
final class ProjectCatalog
{
    /**
     * @var list<\App\Domain\Project>|null
     */
    private ?array $cache = null;

    /**
     * Immutable-on-read datasets, cached only for this catalog instance.
     * A new HTTP request constructs a new catalog and sees new deployments.
     *
     * @var array<string,list<array<string,mixed>>>
     */
    private array $datasetCache = [];

    /**
     * Initialize the immutable domain model with validated inputs.
     */
    public function __construct(private readonly string $directory)
    {
    }

    /** @return list<\App\Domain\Project> */
    public function projects(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }
        $slugs = [];
        $result = [];
        foreach ($this->read('projects.json') as $raw) {
            $project = Project::fromArray($raw);
            if ($project->name === '' || isset($slugs[$project->slug])) {
                throw new UnexpectedValueException('Invalid or duplicate project definition');
            }
            if ($project->private && $project->repo !== '') {
                throw new UnexpectedValueException('Private repository URL must not be published');
            }
            $slugs[$project->slug] = true;
            $result[] = $project;
        }

        return $this->cache = $result;
    }

    /** @return list<array{from:string,to:string,type:string}> */
    public function connections(): array
    {
        return (new ProjectConnections($this->read('connections.json'), $this->projects()))->edges;
    }

    /**
     * Find a public project by its canonical slug.
     */
    public function find(string $slug): ?Project
    {
        foreach ($this->projects() as $project) {
            if ($project->slug === $slug) {
                return $project;
            }
        }

        return null;
    }

    /** @return list<array<string,mixed>> */
    public function topology(): array
    {
        $nodes = $this->read('topology.json');
        new TopologyGraph($nodes);

        return $nodes;
    }

    /** @return list<\App\Domain\NetworkFlow> */
    public function flows(): array
    {
        $topology = $this->topology();

        return array_map(
            static fn(array $row): NetworkFlow => NetworkFlow::fromArray($row, $topology),
            $this->read('flows.json'),
        );
    }

    /** @return list<array<string,mixed>> */
    public function maker(): array
    {
        $items = $this->read('maker.json');
        foreach ($items as $item) {
            if (!is_string($item['related'] ?? null) || $this->find($item['related']) === null) {
                throw new UnexpectedValueException('Unknown related project in maker catalogue');
            }
        }

        return $items;
    }

    /** @return list<\App\Domain\EngineeringDecision> */
    public function decisions(): array
    {
        $decisions = [];
        foreach ($this->read('decisions.json') as $row) {
            $entry = EngineeringDecision::fromArray($row);
            if ($this->find($entry->project) === null) {
                throw new UnexpectedValueException('Engineering decision references an unknown project');
            }
            $decisions[] = $entry;
        }

        return $decisions;
    }

    /** @return list<array<string,mixed>> */
    public function contributions(): array
    {
        return $this->read('contributions.json');
    }

    /** @return list<array<string,mixed>> */
    public function journal(): array
    {
        return $this->read('journal.json');
    }

    /** @return list<array<string,mixed>> */
    private function read(string $file): array
    {
        if (isset($this->datasetCache[$file])) {
            return $this->datasetCache[$file];
        }
        $path = $this->directory . '/' . $file;
        $contents = file_get_contents($path);
        if (!is_string($contents)) {
            throw new RuntimeException('Cannot read curated dataset: ' . $file);
        }
        try {
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new UnexpectedValueException('Invalid curated JSON', 0, $e);
        }
        if (!is_array($decoded) || !array_is_list($decoded)) {
            throw new UnexpectedValueException('Expected a list of records: ' . $file);
        }
        $records = [];
        foreach ($decoded as $item) {
            if (!is_array($item)) {
                throw new UnexpectedValueException('Expected record object: ' . $file);
            }
            /** @var array<string,mixed> $item */
            $records[] = $item;
        }
        PublicContentValidator::validate($file, $records);

        return $this->datasetCache[$file] = $records;
    }
}
