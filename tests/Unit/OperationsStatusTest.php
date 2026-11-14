<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\OperationsStatus;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class OperationsStatusTest extends TestCase
{
    private const NOW = '2026-11-13 12:00:00';

    private function time(string $value): DateTimeImmutable
    {
        return new DateTimeImmutable($value, new DateTimeZone('UTC'));
    }

    public function testFreshOperationalSnapshotIsReady(): void
    {
        $report = OperationsStatus::evaluate(true, true, true, $this->time('2026-11-13 11:00:00'), $this->time(self::NOW));
        self::assertSame('ok', $report['github']);
        self::assertSame(1, $report['github_age_hours']);
        self::assertTrue($report['ready']);
    }

    public function testUnsyncedOrStaleSnapshotsCannotBeReady(): void
    {
        $missing = OperationsStatus::evaluate(true, true, true, null, $this->time(self::NOW));
        $old = OperationsStatus::evaluate(true, true, true, $this->time('2026-11-10 11:00:00'), $this->time(self::NOW));
        self::assertSame('not-synced', $missing['github']);
        self::assertSame('stale', $old['github']);
        self::assertFalse($missing['ready']);
        self::assertFalse($old['ready']);
    }

    public function testFailureDoesNotExposeDatabaseInternalsOrBecomeSuccess(): void
    {
        $report = OperationsStatus::evaluate(true, false, false, null, $this->time(self::NOW));
        self::assertSame('unreachable', $report['database']);
        self::assertSame('unwritable', $report['storage']);
        self::assertSame('unknown', $report['github']);
        self::assertFalse($report['ready']);
    }

    public function testFutureTimestampIsRejected(): void
    {
        $report = OperationsStatus::evaluate(true, true, true, $this->time('2026-11-13 12:10:00'), $this->time(self::NOW));
        self::assertSame('invalid-timestamp', $report['github']);
        self::assertFalse($report['ready']);
    }
}
