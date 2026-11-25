<?php
declare(strict_types=1);

namespace App\Domain;

/**
 * Atomic, request-consistent public snapshot of GitHub provenance,
 * external activity items, and repository updates from a single generation.
 */
final readonly class GithubPublicSnapshot
{
    /**
     * @param list<array<string,mixed>> $activity
     * @param list<array{repository:string,description:string,repo_url:string,pushed_at:string}> $recentRepos
     */
    public function __construct(
        public GithubProvenance $provenance,
        public array $activity,
        public array $recentRepos,
    ) {
    }
}
