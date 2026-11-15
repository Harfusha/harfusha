<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\GithubTimestamp;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GithubTimestampTest extends TestCase
{
    public function testAcceptsOnlyCanonicalUtcCalendarTimestamps(): void
    {
        self::assertSame('2026-02-28 23:59:59', GithubTimestamp::normalize('2026-02-28T23:59:59Z'));
        self::assertSame('2024-02-29 00:00:00', GithubTimestamp::normalize('2024-02-29T00:00:00Z'));
    }

    public function testRejectsImpossibleOrNoncanonicalTimestamps(): void
    {
        $invalid = [
            '2026-02-29T12:00:00Z',
            '2026-02-30T12:00:00Z',
            '2026-13-01T12:00:00Z',
            '2026-12-01T24:00:00Z',
            '2026-12-01T01:60:00Z',
            '2026-12-01T01:01:60Z',
            '2026-12-01',
            'tomorrow',
            '2026-12-01T12:00:00+00:00',
            '2026-12-01T12:00:00.123Z',
            '',
            123,
        ];
        foreach ($invalid as $value) {
            try {
                GithubTimestamp::normalize($value);
                self::fail('Unexpectedly accepted malformed upstream timestamp');
            } catch (InvalidArgumentException) {
                // Invalid remote data must not silently become a MySQL date.
            }
        }
        self::assertCount(12, $invalid);
    }
}
