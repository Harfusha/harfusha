<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\GithubSearchPageCollector;
use App\Domain\GithubRepositoryPageCollector;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GithubPaginationTest extends TestCase
{
    /** @return array<string,mixed> */
    private static function searchItem(int $number): array
    {
        return ['html_url' => 'https://github.com/cakephp/cakephp/pull/' . $number];
    }

    /** @return array<string,mixed> */
    private static function repoItem(int $number): array
    {
        return ['full_name' => 'Harfusha/repo' . $number];
    }

    /** @param list<array<string,mixed>> $items
     *  @return array<string,mixed>
     */
    private static function page(array $items, int $count): array
    {
        return ['items' => $items, 'total_count' => $count, 'incomplete_results' => false];
    }

    public function testSearchCollectsExactlyTwoPages(): void
    {
        $pages = new GithubSearchPageCollector();
        $first = array_map(self::searchItem(...), range(1, 100));
        self::assertTrue($pages->append(self::page($first, 101), 1, 3));
        self::assertFalse($pages->append(self::page([self::searchItem(101)], 101), 2, 3));
        self::assertCount(101, $pages->complete());
    }

    public function testSearchRejectsRepeatedItemAcrossPages(): void
    {
        $pages = new GithubSearchPageCollector();
        $first = array_map(self::searchItem(...), range(1, 100));
        $pages->append(self::page($first, 101), 1, 3);
        $this->expectException(InvalidArgumentException::class);
        $pages->append(self::page([self::searchItem(100)], 101), 2, 3);
    }

    public function testSearchRejectsTruncatedAndIncompleteResponse(): void
    {
        $pages = new GithubSearchPageCollector();
        $this->expectException(InvalidArgumentException::class);
        $pages->append(self::page([self::searchItem(1)], 2), 1, 3);
    }

    public function testSearchRejectsTooManyResultsForBudget(): void
    {
        $pages = new GithubSearchPageCollector();
        $first = array_map(self::searchItem(...), range(1, 100));
        $this->expectException(InvalidArgumentException::class);
        $pages->append(self::page($first, 101), 1, 1);
    }

    public function testSearchHandlesZeroMatches(): void
    {
        $pages = new GithubSearchPageCollector();
        self::assertFalse($pages->append(self::page([], 0), 1, 3));
        self::assertSame([], $pages->complete());
    }

    public function testSearchRejectsOneThousandOneTotal(): void
    {
        $pages = new GithubSearchPageCollector();
        $this->expectException(InvalidArgumentException::class);
        $pages->append(self::page([], 1001), 1, 20);
    }

    public function testRepositoriesCollectFinalShortPage(): void
    {
        $pages = new GithubRepositoryPageCollector();
        $first = array_map(self::repoItem(...), range(1, 100));
        self::assertTrue($pages->append($first, 1, 5));
        self::assertFalse($pages->append([self::repoItem(101)], 2, 5));
        self::assertCount(101, $pages->complete());
    }

    public function testRepositoriesRejectDuplicates(): void
    {
        $pages = new GithubRepositoryPageCollector();
        $first = array_map(self::repoItem(...), range(1, 100));
        $pages->append($first, 1, 5);
        $this->expectException(InvalidArgumentException::class);
        $pages->append([self::repoItem(50)], 2, 5);
    }

    public function testRepositoriesRejectUnfinishedListing(): void
    {
        $pages = new GithubRepositoryPageCollector();
        $this->expectException(InvalidArgumentException::class);
        $pages->complete();
    }
}
