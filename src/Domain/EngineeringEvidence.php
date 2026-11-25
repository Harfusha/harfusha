<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Strongly typed representation of an individual verifiable engineering evidence item (N-EVD-01).
 */
final class EngineeringEvidence
{
    public const ALLOWED_KINDS = [
        'source',
        'unit_test',
        'integration_test',
        'benchmark',
        'release',
        'runtime',
        'external_merge',
        'simulation',
        'design',
        'unverified',
    ];

    public const ALLOWED_STATUSES = [
        'verified',
        'unverified',
        'expired',
        'revoked',
    ];

    public const ALLOWED_VISIBILITY = [
        'public',
        'redacted',
        'internal',
    ];

    /**
     * @param list<string> $limitations
     */
    public function __construct(
        public readonly string $id,
        public readonly string $kind,
        public readonly string $visibility,
        public readonly string $verificationStatus,
        public readonly ?string $revision,
        public readonly ?string $sourceUrl,
        public readonly ?string $artifactSha256,
        public readonly ?string $verifiedAt,
        public readonly string $method,
        public readonly array $limitations,
    ) {
        if (!in_array($this->kind, self::ALLOWED_KINDS, true)) {
            throw new InvalidArgumentException('Invalid evidence kind: ' . $this->kind);
        }
        if (!in_array($this->verificationStatus, self::ALLOWED_STATUSES, true)) {
            throw new InvalidArgumentException('Invalid evidence verification status: ' . $this->verificationStatus);
        }
        if (!in_array($this->visibility, self::ALLOWED_VISIBILITY, true)) {
            throw new InvalidArgumentException('Invalid evidence visibility: ' . $this->visibility);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;
        $kind = $data['kind'] ?? null;
        $visibility = $data['visibility'] ?? 'public';
        $status = $data['verification_status'] ?? 'unverified';
        $method = $data['method'] ?? '';
        $limitations = $data['limitations'] ?? [];

        if (!is_string($id) || $id === '' || !is_string($kind) || !is_string($visibility) || !is_string($status) || !is_string($method)) {
            throw new InvalidArgumentException('Incomplete evidence definition');
        }
        if (!is_array($limitations)) {
            throw new InvalidArgumentException('Evidence limitations must be a list');
        }
        $typedLimitations = [];
        foreach ($limitations as $item) {
            if (is_string($item) && $item !== '') {
                $typedLimitations[] = $item;
            }
        }

        $revision = isset($data['revision']) && is_string($data['revision']) && $data['revision'] !== ''
            ? $data['revision']
            : null;
        $sourceUrl = isset($data['source_url']) && is_string($data['source_url']) && $data['source_url'] !== ''
            ? $data['source_url']
            : null;
        $sha = isset($data['artifact_sha256']) && is_string($data['artifact_sha256']) && $data['artifact_sha256'] !== ''
            ? $data['artifact_sha256']
            : null;
        $verifiedAt = isset($data['verified_at']) && is_string($data['verified_at']) && $data['verified_at'] !== ''
            ? $data['verified_at']
            : null;

        return new self(
            $id,
            $kind,
            $visibility,
            $status,
            $revision,
            $sourceUrl,
            $sha,
            $verifiedAt,
            $method,
            $typedLimitations,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'kind' => $this->kind,
            'visibility' => $this->visibility,
            'verification_status' => $this->verificationStatus,
            'revision' => $this->revision,
            'source_url' => $this->sourceUrl,
            'artifact_sha256' => $this->artifactSha256,
            'verified_at' => $this->verifiedAt,
            'method' => $this->method,
            'limitations' => $this->limitations,
        ];
    }
}
