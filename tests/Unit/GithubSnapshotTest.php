<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\GithubSnapshot;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GithubSnapshotTest extends TestCase
{
    /** @return array<string,mixed> */
    private static function record(string $url, string $state = 'closed', string $login = 'Harfusha'): array
    {
        return ['html_url' => $url, 'title' => 'A valid patch', 'state' => $state,
            'created_at' => '2025-10-10T10:00:00Z', 'user' => ['login' => $login]];
    }

    public function testAcceptedPRAndIssuesExcludeOwnRepositories(): void
    {
        $merged = self::record('https://github.com/cakephp/cakephp/pull/18957');
        $own = self::record('https://github.com/Harfusha/local-ai-hub/pull/4');
        $issue = self::record('https://github.com/phpstan/phpstan/issues/13291', 'closed');
        $result = GithubSnapshot::externalActivity([$merged, $own], [$merged], [$issue], 'Harfusha');
        self::assertCount(2, $result);
        self::assertSame('merged', $result[0]->state);
        self::assertSame('issue', $result[1]->kind);
    }

    public function testConflictingMergedSearchFailsClosed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GithubSnapshot::externalActivity([], [self::record('https://github.com/cakephp/cakephp/pull/1')], [], 'Harfusha');
    }

    public function testUnexpectedAuthorFailsClosed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GithubSnapshot::externalActivity([self::record('https://github.com/a/b/pull/1', 'open', 'someone-else')], [], [], 'Harfusha');
    }

    public function testMalformedResultDoesNotBecomePartialSnapshot(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GithubSnapshot::externalActivity([['title' => 'Without URL', 'user' => ['login' => 'Harfusha']]], [], [], 'Harfusha');
    }
}
