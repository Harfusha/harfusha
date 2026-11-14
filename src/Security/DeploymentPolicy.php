<?php
declare(strict_types=1);

namespace App\Security;

use InvalidArgumentException;

/** Validate canonical production origin and non-default secrets before boot. */
final class DeploymentPolicy
{
    private function __construct()
    {
    }

    public static function assertEnvironment(string $environment, bool $debug, string $origin, string $salt): void
    {
        if (!in_array($environment, ['production', 'development', 'testing'], true)) {
            throw new InvalidArgumentException('APP_ENV must be production, development or testing');
        }
        if ($environment !== 'production') {
            return;
        }
        if ($debug) {
            throw new InvalidArgumentException('Debugging must be disabled in production');
        }
        if (PHP_VERSION_ID < 80512 || PHP_VERSION_ID >= 80600) {
            throw new InvalidArgumentException('Production requires patched PHP 8.5.12+');
        }
        if (
            preg_match('/^[a-fA-F0-9]{64,128}$/D', $salt) !== 1 ||
            preg_match('/^(.)\\1+$/D', $salt) === 1
        ) {
            throw new InvalidArgumentException('Production requires a cryptographically random hexadecimal salt');
        }
        $parts = parse_url($origin);
        $host = is_array($parts) ? ($parts['host'] ?? null) : null;
        if (
            !is_array($parts) || ($parts['scheme'] ?? null) !== 'https' ||
            !is_string($host) || $host === '' ||
            isset($parts['path']) || isset($parts['query']) || isset($parts['fragment']) ||
            isset($parts['user']) || isset($parts['pass']) ||
            filter_var($origin, FILTER_VALIDATE_URL) === false
        ) {
            throw new InvalidArgumentException('APP_URL must be a bare canonical HTTPS origin');
        }
    }
}
