<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Validates a complete GitHub Search API pagination snapshot before any DB write.
 * Detects unstable totals, duplicated issues across pages, shortened pages, and
 * GitHub's 1,000-result search limit. Does not trust the remote response shape.
 */
final class GithubSearchPageCollector
{
    private ?int $total = null;
    private int $nextPage = 1;
    private bool $finished = false;

    /**
     * @var list<array<string,mixed>>
     */
    private array $records = [];

    /**
     * @var array<string,true>
     */
    private array $seen = [];

    /**
     * @param array<array-key,mixed> $response
     * @return bool true when another page is mandatory
     */
    public function append(array $response, int $page, int $maxPages): bool
    {
        if ($this->finished || $page !== $this->nextPage || $page < 1 || $maxPages < 1 || $page > $maxPages) {
            throw new InvalidArgumentException('Unexpected GitHub Search page sequence');
        }
        $items = $response['items'] ?? null;
        $count = $response['total_count'] ?? null;
        if (
            !is_array($items) || !array_is_list($items) || count($items) > 100 ||
            ($response['incomplete_results'] ?? null) !== false || !is_int($count) || $count < 0 || $count > 1000
        ) {
            throw new InvalidArgumentException('Malformed or incomplete GitHub Search page');
        }
        if ($this->total !== null && $count !== $this->total) {
            throw new InvalidArgumentException('GitHub Search result count changed during pagination');
        }
        $this->total = $count;
        foreach ($items as $item) {
            if (!is_array($item) || array_is_list($item)) {
                throw new InvalidArgumentException('Malformed GitHub Search item');
            }
            $url = $item['html_url'] ?? null;
            if (
                !is_string($url)
                || preg_match(
                    '~^https://github\.com/[A-Za-z0-9-]+/[A-Za-z0-9_.-]+/(?:pull|issues)/[1-9][0-9]*$~D',
                    $url,
                ) !== 1
            ) {
                throw new InvalidArgumentException('Invalid GitHub Search result URL');
            }
            if (isset($this->seen[$url])) {
                throw new InvalidArgumentException('Duplicate GitHub result in paginated snapshot');
            }
            $this->seen[$url] = true;
            /** @var array<string,mixed> $item */
            $this->records[] = $item;
        }
        $size = count($this->records);
        if ($size > $count || (count($items) < 100 && $size !== $count)) {
            throw new InvalidArgumentException('Truncated or contradictory GitHub Search result');
        }
        $this->nextPage++;
        if ($size === $count) {
            $this->finished = true;

            return false;
        }
        if ($page === $maxPages) {
            throw new InvalidArgumentException('GitHub Search results exceed the page budget');
        }

        return true;
    }

    /** @return list<array<string,mixed>> */
    public function complete(): array
    {
        if (!$this->finished || $this->total === null || count($this->records) !== $this->total) {
            throw new InvalidArgumentException('GitHub Search snapshot is incomplete');
        }

        return $this->records;
    }
}
