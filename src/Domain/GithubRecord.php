<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final readonly class GithubRecord
{
    /**
     * Initialize the immutable domain model with validated inputs.
     */
    public function __construct(
        public string $url,
        public string $title,
        public string $repository,
        public string $kind,
        public string $state,
        public string $createdAt,
        public ?string $mergedAt,
        public ?string $closedAt,
    ) {
        if (
            !in_array($kind, ['pr', 'issue'], true)
            || !in_array($state, ['open', 'closed', 'merged', 'completed'], true)
        ) {
            throw new InvalidArgumentException('Invalid GitHub event classification');
        }
    }

    /** @param array<string,mixed> $item */
    public static function fromGithub(array $item, string $kind, string $state): self
    {
        $stateReason = $item['state_reason'] ?? null;
        if ($kind === 'issue' && $state === 'closed' && ($stateReason === 'completed' || $stateReason === 'merged')) {
            $state = 'completed';
        }
        $url = $item['html_url'] ?? null;
        if (
            !is_string($url)
            || preg_match(
                '~^https://github\.com/([A-Za-z0-9-]+)/([A-Za-z0-9_.-]+)/(pull|issues)/[1-9][0-9]*$~D',
                $url,
                $parts,
            ) !== 1
        ) {
            throw new InvalidArgumentException('Unexpected event URL');
        }
        if (($kind === 'pr' && $parts[3] !== 'pull') || ($kind === 'issue' && $parts[3] !== 'issues')) {
            throw new InvalidArgumentException('Kind and path mismatch');
        }
        $date = $item['created_at'] ?? null;
        if (!is_string($date)) {
            throw new InvalidArgumentException('Missing creation time');
        }
        $created = GithubTimestamp::normalize($date);
        $pullRequest = $item['pull_request'] ?? null;
        if ($pullRequest !== null && (!is_array($pullRequest) || array_is_list($pullRequest))) {
            throw new InvalidArgumentException('Invalid GitHub pull request metadata');
        }
        $mergedAt = $kind === 'pr'
            ? self::optionalTimestamp(is_array($pullRequest) ? ($pullRequest['merged_at'] ?? null) : null)
            : null;
        $closedAt = self::optionalTimestamp($item['closed_at'] ?? null);
        $title = $item['title'] ?? null;
        if (!is_string($title) || trim($title) === '') {
            throw new InvalidArgumentException('Missing event title');
        }
        PublicText::assertRenderable($title);

        return new self(
            $url,
            mb_substr($title, 0, 250),
            $parts[1] . '/' . $parts[2],
            $kind,
            $state,
            $created,
            $mergedAt,
            $closedAt,
        );
    }

    /** Validate an optional GitHub event time and normalize it for storage. */
    private static function optionalTimestamp(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (!is_string($value)) {
            throw new InvalidArgumentException('Invalid GitHub event timestamp');
        }

        return GithubTimestamp::normalize($value);
    }
}
