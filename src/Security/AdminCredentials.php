<?php
declare(strict_types=1);

namespace App\Security;

/** Passwords are opaque bytes: never trim, lowercase or normalize the input. */
final class AdminCredentials
{
    private function __construct()
    {
    }

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
}
