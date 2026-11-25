<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Pure domain model for an engineering claim with attached evidence items (N-EVD-01).
 */
final class EngineeringClaim
{
    public const ALLOWED_PUBLICATION_STATES = [
        'draft',
        'reviewed',
        'published',
        'expired',
        'revoked',
    ];

    /**
     * @param array{cs: string, en: string} $claim
     * @param list<\App\Domain\EngineeringEvidence> $evidence
     * @param array{state: string, approved_content_hash: ?string} $publication
     */
    public function __construct(
        public readonly int $schemaVersion,
        public readonly string $id,
        public readonly string $projectId,
        public readonly array $claim,
        public readonly array $evidence,
        public readonly array $publication,
    ) {
        if ($this->schemaVersion !== 1) {
            throw new InvalidArgumentException('Unsupported evidence schema version: ' . $this->schemaVersion);
        }
        if (!in_array($this->publication['state'], self::ALLOWED_PUBLICATION_STATES, true)) {
            throw new InvalidArgumentException('Invalid publication state: ' . $this->publication['state']);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $version = $data['schema_version'] ?? null;
        $id = $data['id'] ?? null;
        $projectId = $data['project_id'] ?? null;
        $claim = $data['claim'] ?? null;
        $evidenceRaw = $data['evidence'] ?? null;
        $pubRaw = $data['publication'] ?? null;

        if (!is_int($version) || !is_string($id) || !is_string($projectId) || !is_array($claim) || !is_array($evidenceRaw) || !is_array($pubRaw)) {
            throw new InvalidArgumentException('Incomplete claim structure');
        }

        $cs = $claim['cs'] ?? null;
        $en = $claim['en'] ?? null;
        if (!is_string($cs) || $cs === '' || !is_string($en) || $en === '') {
            throw new InvalidArgumentException('Claim must provide non-empty cs and en texts');
        }

        $state = $pubRaw['state'] ?? 'draft';
        if (!is_string($state)) {
            throw new InvalidArgumentException('Publication state must be string');
        }
        $hash = isset($pubRaw['approved_content_hash']) && is_string($pubRaw['approved_content_hash']) && $pubRaw['approved_content_hash'] !== ''
            ? $pubRaw['approved_content_hash']
            : null;

        $evidenceList = [];
        foreach ($evidenceRaw as $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException('Evidence item must be array');
            }
            /** @var array<string, mixed> $item */
            $evidenceList[] = EngineeringEvidence::fromArray($item);
        }

        return new self(
            $version,
            $id,
            $projectId,
            ['cs' => $cs, 'en' => $en],
            $evidenceList,
            ['state' => $state, 'approved_content_hash' => $hash],
        );
    }

    /**
     * Check whether this engineering claim is approved for public display.
     *
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->publication['state'] === 'published';
    }

    /**
     * Resolve the primary or highest-confidence verified evidence record.
     *
     * @return \App\Domain\EngineeringEvidence|null
     */
    public function primaryEvidence(): ?EngineeringEvidence
    {
        foreach ($this->evidence as $item) {
            if ($item->verificationStatus === 'verified') {
                return $item;
            }
        }

        return $this->evidence[0] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'schema_version' => $this->schemaVersion,
            'id' => $this->id,
            'project_id' => $this->projectId,
            'claim' => $this->claim,
            'evidence' => array_map(static fn(EngineeringEvidence $e): array => $e->toArray(), $this->evidence),
            'publication' => $this->publication,
        ];
    }
}
