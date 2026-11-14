<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\GithubRecord;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GithubRecordTest extends TestCase
{
    public function testRealisticPayloadIsNormalized(): void
    {
        $event = GithubRecord::fromGithub([
            'html_url' => 'https://github.com/cakephp/cakephp/pull/18957',
            'created_at' => '2025-10-09T12:45:00Z', 'title' => 'FQCN TableLocator',
        ], 'pr', 'merged');
        self::assertSame('cakephp/cakephp', $event->repository);
        self::assertSame('merged', $event->state);
        self::assertSame('2025-10-09 12:45:00', $event->createdAt);
    }

    public function testRejectsUntrustedEventUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GithubRecord::fromGithub(['html_url' => 'https://not-github.com/owner/repo/pull/2','created_at' => '2025-01-01'], 'pr', 'open');
    }

    public function testRejectsKindMismatch(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GithubRecord::fromGithub(['html_url' => 'https://github.com/a/b/issues/2','created_at' => '2025-01-01'], 'pr', 'open');
    }
}
