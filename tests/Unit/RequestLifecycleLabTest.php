<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\RequestLifecycleLab;
use PHPUnit\Framework\TestCase;

final class RequestLifecycleLabTest extends TestCase
{
    public function testScenariosContractAndCompleteness(): void
    {
        $scenarios = RequestLifecycleLab::scenarios();
        self::assertCount(4, $scenarios);

        $expectedIds = [
            'public-project-get',
            'invalid-lang-slug-404',
            'admin-csrf-violation-403',
            'database-fallback-resilience',
        ];
        $actualIds = array_column($scenarios, 'id');
        self::assertSame($expectedIds, $actualIds);

        foreach ($scenarios as $scenario) {
            self::assertNotEmpty($scenario['method']);
            self::assertNotEmpty($scenario['url']);
            self::assertGreaterThanOrEqual(200, $scenario['status']);
            self::assertLessThan(600, $scenario['status']);
            self::assertNotEmpty($scenario['code_ref']);
            self::assertNotEmpty($scenario['pipeline']);

            foreach ($scenario['pipeline'] as $step) {
                self::assertNotEmpty($step['stage']);
                self::assertNotEmpty($step['component']);
                self::assertNotEmpty($step['outcome']);
            }
        }
    }
}
