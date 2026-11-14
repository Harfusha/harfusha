<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Pure GitHub Search snapshot builder.
 * Performs complete, fail-closed normalization before the importer may clear DB rows.
 */
final class GithubSnapshot
{
    /**
     * Initialize validated domain state and required dependencies.
     */
    private function __construct()
    {
    }

    /**
     * @param list<array<string,mixed>> $pullRequests
     * @param list<array<string,mixed>> $mergedPullRequests
     * @param list<array<string,mixed>> $issues
     * @return list<\App\Domain\GithubRecord>
     */
    public static function externalActivity(
        array $pullRequests,
        array $mergedPullRequests,
        array $issues,
        string $author,
    ): array {
        if (preg_match('/^[A-Za-z0-9-]{1,39}$/D', $author) !== 1) {
            throw new InvalidArgumentException('Invalid GitHub account');
        }
        $mergedUrls = [];
        foreach ($mergedPullRequests as $item) {
            self::assertAuthor($item, $author);
            $record = GithubRecord::fromGithub($item, 'pr', 'merged');
            $mergedUrls[$record->url] = true;
        }
        $records = [];
        $seenMerged = [];
        foreach ($pullRequests as $item) {
            self::assertAuthor($item, $author);
            $url = $item['html_url'] ?? null;
            if (!is_string($url)) {
                throw new InvalidArgumentException('Missing PR URL');
            }
            $state = $item['state'] ?? null;
            if (!is_string($state) || !in_array($state, ['open', 'closed'], true)) {
                throw new InvalidArgumentException('Unexpected pull request status');
            }
            if (isset($mergedUrls[$url])) {
                if ($state !== 'closed') {
                    throw new InvalidArgumentException('Inconsistent GitHub search snapshot');
                }
                $state = 'merged';
                $seenMerged[$url] = true;
            }
            $record = GithubRecord::fromGithub($item, 'pr', $state);
            self::includeExternal($records, $record, $author);
        }
        // Merged PRs are a strict subset of authored PRs. If the two search
        // endpoints disagree, retain the last known good database snapshot.
        if (count($seenMerged) !== count($mergedUrls)) {
            throw new InvalidArgumentException('GitHub search snapshots disagree');
        }
        foreach ($issues as $item) {
            self::assertAuthor($item, $author);
            $state = $item['state'] ?? null;
            if (!is_string($state) || !in_array($state, ['open', 'closed'], true)) {
                throw new InvalidArgumentException('Unexpected issue status');
            }
            $record = GithubRecord::fromGithub($item, 'issue', $state);
            self::includeExternal($records, $record, $author);
        }

        return array_values($records);
    }

    /** @param array<string,mixed> $item */
    private static function assertAuthor(array $item, string $author): void
    {
        $user = $item['user'] ?? null;
        $login = is_array($user) ? ($user['login'] ?? null) : null;
        if (!is_string($login) || strcasecmp($author, $login) !== 0) {
            throw new InvalidArgumentException('Unexpected author in GitHub public search result');
        }
    }

    /** @param array<string,\App\Domain\GithubRecord> $records */
    private static function includeExternal(array &$records, GithubRecord $record, string $author): void
    {
        $repositoryOwner = explode('/', $record->repository, 2)[0];
        if (strcasecmp($repositoryOwner, $author) !== 0) {
            if (isset($records[$record->url])) {
                throw new InvalidArgumentException('Duplicated GitHub search record');
            }
            $records[$record->url] = $record;
        }
    }
}
