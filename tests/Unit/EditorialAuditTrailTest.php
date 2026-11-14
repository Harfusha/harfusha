<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Service\EditorialAuditTrail;
use PHPUnit\Framework\TestCase;

final class EditorialAuditTrailTest extends TestCase
{
    public function testDigestIsDeterministicAcrossFieldOrdering(): void
    {
        $a = ['title_en' => 'Details', 'title_cs' => 'Podrobnosti'];
        $b = ['title_cs' => 'Podrobnosti', 'title_en' => 'Details'];
        self::assertSame(EditorialAuditTrail::digest($a), EditorialAuditTrail::digest($b));
        self::assertMatchesRegularExpression('/^[a-f0-9]{64}$/D', EditorialAuditTrail::digest($a));
        self::assertNotSame(EditorialAuditTrail::digest($a), EditorialAuditTrail::digest(['title_en' => 'Other']));
    }
}
