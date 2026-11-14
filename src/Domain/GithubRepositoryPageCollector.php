<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/** Public repository list has no total_count: final short page is mandatory. */
final class GithubRepositoryPageCollector
{
    private int $nextPage = 1;
    private bool $finished = false;

    /** @var list<array<string,mixed>> */
    private array $records = [];

    /** @var array<string,true> */
    private array $seen = [];

    /**
     * @param array<array-key,mixed> $response
     * @return bool true when another page is mandatory
     */
    public function append(array $response, int $page, int $maxPages): bool
    {
        if ($this->finished || $page !== $this->nextPage || $page < 1 || $maxPages < 1 || $page > $maxPages ||
            !array_is_list($response) || count($response) > 100) {
            throw new InvalidArgumentException('Invalid GitHub repository pagination');
        }
        foreach ($response as $item) {
            if (!is_array($item) || array_is_list($item)) {
                throw new InvalidArgumentException('Malformed GitHub repository item');
            }
            $name = $item['full_name'] ?? null;
            if (!is_string($name) || preg_match('~^[A-Za-z0-9-]+/[A-Za-z0-9_.-]+$~D', $name) !== 1) {
                throw new InvalidArgumentException('Invalid GitHub repository name');
            }
            $key = strtolower($name);
            if (isset($this->seen[$key])) {
                throw new InvalidArgumentException('Duplicate GitHub repository across pages');
            }
            $this->seen[$key] = true;
            /** @var array<string,mixed> $item */
            $this->records[] = $item;
        }
        $this->nextPage++;
        if (count($response) < 100) {
            $this->finished = true;
            return false;
        }
        if ($page === $maxPages) {
            throw new InvalidArgumentException('GitHub repository listing exceeds pagination limit');
        }
        return true;
    }

    /** @return list<array<string,mixed>> */
    public function complete(): array
    {
        if (!$this->finished) {
            throw new InvalidArgumentException('Repository snapshot not complete');
        }
        return $this->records;
    }
}
