<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\GithubRepositoryPageCollector;
use App\Domain\GithubSearchPageCollector;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GithubPaginationTest extends TestCase
{
    /** @return array<string,mixed> */
    private static function searchItem(int $number): array
    {
        return [
            'html_url' => 'https://github.com/cakephp/cakephp/pull/' . $number,
            'created_at' => '2026-11-12T12:00:00Z',
        ];
    }

    /** @return array<string,mixed> */
    private static function repoItem(int $number): array
    {
        return ['full_name' => 'Harfusha/repo' . $number];
    }

    /** @param list<array<string,mixed>> $items
     * @return array<string,mixed>
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

    public function testSearchRejectsNewerResultFromLaterPageEvenWhenTotalsAgree(): void
    {
        $pages = new GithubSearchPageCollector();
        $first = array_map(self::searchItem(...), range(1, 100));
        self::assertTrue($pages->append(self::page($first, 101), 1, 3));
        $newer = self::searchItem(101);
        $newer['created_at'] = '2026-11-13T12:00:00Z';

        $this->expectException(InvalidArgumentException::class);
        $pages->append(self::page([$newer], 101), 2, 3);
    }

    public function testSearchAcceptsOldestResultOnLaterPage(): void
    {
        $pages = new GithubSearchPageCollector();
        $first = array_map(self::searchItem(...), range(1, 100));
        self::assertTrue($pages->append(self::page($first, 101), 1, 3));
        $older = self::searchItem(101);
        $older['created_at'] = '2026-11-11T12:00:00Z';
        self::assertFalse($pages->append(self::page([$older], 101), 2, 3));
        self::assertCount(101, $pages->complete());
    }

    public function testSearchRejectsMissingAndImpossibleCreatedAt(): void
    {
        foreach (['tomorrow', '2026-02-30T12:00:00Z', null] as $createdAt) {
            $pages = new GithubSearchPageCollector();
            $item = self::searchItem(1);
            $item['created_at'] = $createdAt;
            try {
                $pages->append(self::page([$item], 1), 1, 3);
                self::fail('Malformed GitHub Search creation date was accepted');
            } catch (InvalidArgumentException $error) {
                self::assertNotSame('', $error->getMessage());
            }
        }
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
