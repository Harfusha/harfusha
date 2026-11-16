<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Security\TestDatabaseGuard;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class TestDatabaseGuardTest extends TestCase
{
    public function testAcceptsExplicitlyConfirmedIsolatedDatabase(): void
    {
        TestDatabaseGuard::assertSafe('testing', 'harfusha_lab_ci', '127.0.0.1', 'lab_ci', 'harfusha_lab_ci');
        $this->expectNotToPerformAssertions();
    }

    public function testAcceptsExplicitlyConfirmedRemoteTestDatabase(): void
    {
        TestDatabaseGuard::assertSafe('testing', 'harfusha_lab', 'db.example.test', 'lab_ci', '', 'db.example.test/harfusha_lab');
        $this->expectNotToPerformAssertions();
    }

    #[DataProvider('unsafeTargets')]
    public function testRejectsUnsafeDatabaseTargets(string $env, string $db, string $host, string $user, string $confirm, string $remoteConfirm): void
    {
        $this->expectException(RuntimeException::class);
        TestDatabaseGuard::assertSafe($env, $db, $host, $user, $confirm, $remoteConfirm);
    }

    /** @return iterable<string,array{string,string,string,string,string,string}> */
    public static function unsafeTargets(): iterable
    {
        yield 'production environment' => ['production', 'harfusha_lab_ci', 'localhost', 'lab_ci', 'harfusha_lab_ci', ''];
        yield 'development environment' => ['development', 'harfusha_lab_ci', 'localhost', 'lab_ci', 'harfusha_lab_ci', ''];
        yield 'production schema' => ['testing', 'harfusha_lab', 'localhost', 'lab_ci', 'harfusha_lab', ''];
        yield 'other confirmation' => ['testing', 'harfusha_lab_ci', 'localhost', 'lab_ci', 'not_the_database', ''];
        yield 'no confirmation' => ['testing', 'harfusha_lab_ci', 'localhost', 'lab_ci', '', ''];
        yield 'unconfirmed external database' => ['testing', 'harfusha_lab_ci', 'db.production.tld', 'lab_ci', 'harfusha_lab_ci', ''];
        yield 'mismatched remote confirmation' => ['testing', 'harfusha_lab_ci', 'db.production.tld', 'lab_ci', '', 'db.production.tld/other_schema'];
        yield 'empty remote schema' => ['testing', '', 'db.production.tld', 'lab_ci', '', 'db.production.tld/'];
        yield 'empty remote host' => ['testing', 'harfusha_lab_ci', '', 'lab_ci', '', '/harfusha_lab_ci'];
        yield 'root database account' => ['testing', 'harfusha_lab_ci', 'localhost', 'root', 'harfusha_lab_ci', ''];
    }
}
