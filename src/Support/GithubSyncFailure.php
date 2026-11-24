<?php
declare(strict_types=1);

namespace App\Support;

use Cake\Http\Client\Exception\NetworkException;
use InvalidArgumentException;
use PDOException;
use RuntimeException;
use Throwable;

final class GithubSyncFailure
{
    /** @return string */
    public static function describe(Throwable $error): string
    {
        $message = $error->getMessage();
        if (
            $error instanceof NetworkException
            && preg_match('/^cURL Error \(([0-9]{1,3})\) /D', $message, $matches) === 1
        ) {
            return 'network-curl-' . $matches[1];
        }
        if (
            $error instanceof RuntimeException
            && str_starts_with($message, 'GitHub rate limit exceeded')
        ) {
            return 'upstream-rate-limited';
        }
        if (
            $error instanceof RuntimeException
            && preg_match('/^GitHub request failed with HTTP ([1-5][0-9]{2})$/D', $message, $matches) === 1
        ) {
            return 'upstream-http-' . $matches[1];
        }
        if ($error instanceof RuntimeException && $message === 'Database is not configured') {
            return 'database-not-configured';
        }
        if ($error instanceof RuntimeException && $message === 'GitHub import already running or lock unavailable') {
            return 'lock-unavailable';
        }
        if ($error instanceof PDOException) {
            return 'database-error';
        }
        if ($error instanceof InvalidArgumentException) {
            return 'validation-rejected';
        }

        return 'unexpected-' . str_replace('\\', '-', get_class($error));
    }
}
