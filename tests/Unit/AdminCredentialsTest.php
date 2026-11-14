<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Security\AdminCredentials;
use PHPUnit\Framework\TestCase;

final class AdminCredentialsTest extends TestCase
{
    public function testPasswordWhitespaceIsNotTrimmed(): void
    {
        $hash = password_hash('  spaced password  ', PASSWORD_DEFAULT);
        self::assertTrue(AdminCredentials::verify('  spaced password  ', $hash));
        self::assertFalse(AdminCredentials::verify('spaced password', $hash));
    }

    public function testMalformedCredentialsAreRejected(): void
    {
        $hash = password_hash('correct', PASSWORD_DEFAULT);
        self::assertFalse(AdminCredentials::verify(['correct'], $hash));
        self::assertFalse(AdminCredentials::verify(str_repeat('x', 1025), $hash));
        self::assertFalse(AdminCredentials::verify('correct', null));
        self::assertFalse(AdminCredentials::verify('', $hash));
    }
}
