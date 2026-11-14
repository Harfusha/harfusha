<?php
declare(strict_types=1);

namespace App\Security;

use RuntimeException;

/**
 * Destructive integration tests must never run against an accidental live DB.
 * The name suffix is not sufficient by itself: an explicit confirmation, a
 * testing environment and a local database host are mandatory together.
 */
final class TestDatabaseGuard
{
    /**
     * Initialize validated dependencies and immutable configuration.
     */
    private function __construct()
    {
    }

    /**
     * Ensure integration tests can only target a confirmed disposable database.
     */
    public static function assertSafe(
        string $environment,
        string $database,
        string $host,
        string $username,
        string $confirmation,
    ): void {
        if ($environment !== 'testing') {
            throw new RuntimeException('Database tests require APP_ENV=testing');
        }
        if (preg_match('/^[a-z][a-z0-9_]{2,62}_(?:test|testing|ci)$/D', $database) !== 1) {
            throw new RuntimeException('Database tests require a clearly named disposable schema');
        }
        if ($confirmation === '' || !hash_equals($database, $confirmation)) {
            throw new RuntimeException('Database tests require LAB_TEST_DB_CONFIRM matching DB_NAME');
        }
        if (!in_array(strtolower($host), ['localhost', '127.0.0.1', '::1', 'db'], true)) {
            throw new RuntimeException('Database tests require a local or isolated container DB host');
        }
        if ($username === '' || in_array(strtolower($username), ['root', 'admin', 'administrator'], true)) {
            throw new RuntimeException('Database tests require a dedicated non-privileged database account');
        }
    }
}
