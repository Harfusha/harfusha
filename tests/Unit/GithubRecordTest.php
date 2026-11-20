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
            'closed_at' => '2025-10-11T12:00:00Z',
            'pull_request' => ['merged_at' => '2025-10-11T11:30:00Z'],
        ], 'pr', 'merged');
        self::assertSame('cakephp/cakephp', $event->repository);
        self::assertSame('merged', $event->state);
        self::assertSame('2025-10-09 12:45:00', $event->createdAt);
        self::assertSame('2025-10-11 11:30:00', $event->mergedAt);
        self::assertSame('2025-10-11 12:00:00', $event->closedAt);
    }

    public function testRejectsInvalidGitHubCreationDates(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GithubRecord::fromGithub([
            'html_url' => 'https://github.com/cakephp/cakephp/pull/18957',
            'created_at' => '2026-02-30T12:45:00Z',
            'title' => 'Bad upstream timestamp',
        ], 'pr', 'merged');
    }

    public function testRejectsMalformedMergeAndClosureTimes(): void
    {
        foreach (
            [
                ['closed_at' => '2026-02-30T12:45:00Z'],
                ['pull_request' => ['merged_at' => 42]],
                ['pull_request' => 'unexpected'],
            ] as $extra
        ) {
            try {
                GithubRecord::fromGithub($extra + [
                    'html_url' => 'https://github.com/cakephp/cakephp/pull/18957',
                    'created_at' => '2025-10-09T12:45:00Z', 'title' => 'Timestamp validation',
                ], 'pr', 'merged');
                self::fail('Invalid upstream timestamp was accepted');
            } catch (InvalidArgumentException $error) {
                self::assertNotSame('', $error->getMessage());
            }
        }
    }

    public function testRejectsZeroNumberAndControlCharacters(): void
    {
        foreach (
            [
                ['html_url' => 'https://github.com/cakephp/cakephp/pull/0',
                    'created_at' => '2026-11-12T12:00:00Z', 'title' => 'Invalid number'],
                ['html_url' => 'https://github.com/cakephp/cakephp/pull/20',
                    'created_at' => '2026-11-12T12:00:00Z', 'title' => "Bad \x01 title"],
                ['html_url' => 'https://github.com/cakephp/cakephp/issues/20',
                    'created_at' => '2026-11-12T12:00:00Z', 'title' => '  '],
            ] as $case
        ) {
            try {
                GithubRecord::fromGithub($case, str_contains($case['html_url'], '/pull/') ? 'pr' : 'issue', 'open');
                self::fail('Invalid upstream issue was accepted');
            } catch (InvalidArgumentException $error) {
                self::assertNotSame('', $error->getMessage());
            }
        }
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

    public function testIssueClosureReasonDistinguishesCompletedAndClosed(): void
    {
        $completed = GithubRecord::fromGithub([
            'html_url' => 'https://github.com/cakephp/cakephp/issues/18954',
            'created_at' => '2025-10-07T10:00:00Z',
            'title' => 'Specific table aware TableLocator',
            'state_reason' => 'completed',
        ], 'issue', 'closed');
        self::assertSame('completed', $completed->state);

        $mergedReason = GithubRecord::fromGithub([
            'html_url' => 'https://github.com/cakephp/cakephp/issues/18954',
            'created_at' => '2025-10-07T10:00:00Z',
            'title' => 'Specific table aware TableLocator',
            'state_reason' => 'merged',
        ], 'issue', 'closed');
        self::assertSame('completed', $mergedReason->state);

        $notPlanned = GithubRecord::fromGithub([
            'html_url' => 'https://github.com/cakephp/cakephp/issues/18954',
            'created_at' => '2025-10-07T10:00:00Z',
            'title' => 'Specific table aware TableLocator',
            'state_reason' => 'not_planned',
        ], 'issue', 'closed');
        self::assertSame('closed', $notPlanned->state);

        $unspecified = GithubRecord::fromGithub([
            'html_url' => 'https://github.com/cakephp/cakephp/issues/18954',
            'created_at' => '2025-10-07T10:00:00Z',
            'title' => 'Specific table aware TableLocator',
        ], 'issue', 'closed');
        self::assertSame('closed', $unspecified->state);
    }
}
