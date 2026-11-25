<?php
declare(strict_types=1);

namespace App\Security;

/** Passwords are opaque bytes: never trim, lowercase or normalize the input. */
final class AdminCredentials
{
    /**
     * Initialize validated dependencies and immutable configuration.
     */
    private function __construct()
    {
    }

    /**
     * Verify the supplied password without modifying significant whitespace.
     */
    public static function verify(mixed $submitted, mixed $storedHash): bool
    {
        if (
            !is_string($submitted) || $submitted === '' || strlen($submitted) > 1024 ||
            !is_string($storedHash) || $storedHash === ''
        ) {
            return false;
        }

        return password_verify($submitted, $storedHash);
    }

    /**
     * Compute a session-bound credential digest so rotating ADMIN_PASSWORD_HASH
     * immediately invalidates any previously authenticated admin session.
     */
    public static function sessionDigest(mixed $storedHash): string
    {
        if (!is_string($storedHash) || $storedHash === '') {
            return '';
        }

        return hash('sha256', $storedHash);
    }
}
