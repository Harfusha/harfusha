<?php
declare(strict_types=1);

namespace App\Support;

final class Environment
{
    /**
     * Resolve an application setting without requiring process-level putenv().
     */
    public static function get(string $key): ?string
    {
        $value = $_SERVER[$key] ?? $_ENV[$key] ?? getenv($key);

        return is_string($value) ? $value : null;
    }
}
