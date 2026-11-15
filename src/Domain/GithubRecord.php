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
    ) {
        if (!in_array($kind, ['pr', 'issue'], true) || !in_array($state, ['open','closed','merged'], true)) {
            throw new InvalidArgumentException('Invalid GitHub event classification');
        }
    }

    /** @param array<string,mixed> $item */
    public static function fromGithub(array $item, string $kind, string $state): self
    {
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
        );
    }
}
