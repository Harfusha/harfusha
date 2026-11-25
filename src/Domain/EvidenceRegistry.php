<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Central registry of verified engineering claims and evidence items (N-EVD-01, PUB01).
 */
final class EvidenceRegistry
{
    /**
     * @var list<\App\Domain\EngineeringClaim>
     */
    private array $claims = [];

    /**
     * @var array<string, list<\App\Domain\EngineeringClaim>>
     */
    private array $byProject = [];

    /**
     * @param list<\App\Domain\EngineeringClaim> $claims
     */
    public function __construct(array $claims = [])
    {
        foreach ($claims as $claim) {
            $this->claims[] = $claim;
            $this->byProject[$claim->projectId][] = $claim;
        }
    }

    /**
     * Factory loading claims from an absolute or relative JSON file.
     */
    public static function fromFile(string $path): self
    {
        if (!is_file($path)) {
            return new self([]);
        }
        $raw = file_get_contents($path);
        if ($raw === false || $raw === '') {
            return new self([]);
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new InvalidArgumentException('Invalid evidence JSON');
        }

        $claims = [];
        foreach ($decoded as $item) {
            if (is_array($item)) {
                /** @var array<string, mixed> $item */
                $claims[] = EngineeringClaim::fromArray($item);
            }
        }

        return new self($claims);
    }

    /**
     * Return all claims across projects.
     *
     * @return list<\App\Domain\EngineeringClaim>
     */
    public function all(bool $publishedOnly = true): array
    {
        if (!$publishedOnly) {
            return $this->claims;
        }

        return array_values(array_filter($this->claims, static fn(EngineeringClaim $c): bool => $c->isPublished()));
    }

    /**
     * Return published claims for a specific project.
     *
     * @return list<\App\Domain\EngineeringClaim>
     */
    public function forProject(string $projectId, bool $publishedOnly = true): array
    {
        $list = $this->byProject[$projectId] ?? [];
        if (!$publishedOnly) {
            return $list;
        }

        return array_values(array_filter($list, static fn(EngineeringClaim $c): bool => $c->isPublished()));
    }

    /**
     * Find a single claim by stable claim ID.
     */
    public function find(string $id): ?EngineeringClaim
    {
        foreach ($this->claims as $claim) {
            if ($claim->id === $id) {
                return $claim;
            }
        }

        return null;
    }
}
