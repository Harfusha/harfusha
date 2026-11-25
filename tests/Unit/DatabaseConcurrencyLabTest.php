<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\DatabaseConcurrencyLab;
use PHPUnit\Framework\TestCase;

final class DatabaseConcurrencyLabTest extends TestCase
{
    public function testLostUpdateSimulationProducesDataLoss(): void
    {
        $result = DatabaseConcurrencyLab::simulateLostUpdate(100, 10, 10);

        self::assertTrue($result['lost_update_occurred']);
        self::assertSame(100, $result['initial_value']);
        self::assertSame(110, $result['final_value']);
        self::assertSame(120, $result['expected_value']);
    }

    public function testOptimisticLockingRecoversFromConflict(): void
    {
        $result = DatabaseConcurrencyLab::simulateOptimisticLocking(100, 10, 10);

        self::assertTrue($result['success']);
        self::assertSame(100, $result['initial_value']);
        self::assertSame(120, $result['final_value']);
        self::assertSame(3, $result['final_version']);
        self::assertSame(1, $result['conflicts_handled']);
        self::assertSame(1, $result['retries']);
    }

    public function testAtomicIncrementIsConsistentWithoutApplicationRetry(): void
    {
        $result = DatabaseConcurrencyLab::simulateAtomicIncrement(100, 10, 10);

        self::assertTrue($result['success']);
        self::assertSame(120, $result['final_value']);
    }

    public function testIdempotencySuppressesDuplicateMutation(): void
    {
        $keys = ['req_101', 'req_101', 'req_102', 'req_101'];
        $result = DatabaseConcurrencyLab::simulateIdempotency(100, $keys, 10);

        self::assertSame(4, $result['total_requests']);
        self::assertSame(2, $result['mutations_executed']);
        self::assertSame(2, $result['replayed_cached_responses']);
        self::assertSame(120, $result['final_value']);
    }

    public function testScenariosContractAndCompleteness(): void
    {
        $scenarios = DatabaseConcurrencyLab::scenarios();
        self::assertCount(4, $scenarios);

        $expectedIds = ['lost-update', 'optimistic-locking', 'atomic-increment', 'idempotency-token'];
        $actualIds = array_column($scenarios, 'id');
        self::assertSame($expectedIds, $actualIds);

        foreach ($scenarios as $scenario) {
            self::assertNotEmpty($scenario['id']);
            self::assertNotEmpty($scenario['badge']);
            self::assertNotEmpty($scenario['steps']);

            foreach ($scenario['steps'] as $step) {
                self::assertGreaterThan(0, $step['step_number']);
                self::assertNotEmpty($step['actor']);
                self::assertNotEmpty($step['sql']);
                self::assertNotEmpty($step['db_state']);
            }
        }
    }
}
