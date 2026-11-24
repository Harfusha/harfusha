<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Support\GithubSyncFailure;
use Cake\Http\Client\Exception\NetworkException;
use Cake\Http\Client\Request;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class GithubSyncFailureTest extends TestCase
{
    public function testSafeFailureCodes(): void
    {
        self::assertSame('upstream-rate-limited', GithubSyncFailure::describe(new RuntimeException('GitHub rate limit exceeded until 1732450000')));
        self::assertSame('upstream-http-403', GithubSyncFailure::describe(new RuntimeException('GitHub request failed with HTTP 403')));
        self::assertSame('network-curl-60', GithubSyncFailure::describe(new NetworkException('cURL Error (60) sensitive details', new Request('https://example.com'))));
        self::assertSame('database-not-configured', GithubSyncFailure::describe(new RuntimeException('Database is not configured')));
        self::assertSame('lock-unavailable', GithubSyncFailure::describe(new RuntimeException('GitHub import already running or lock unavailable')));
        self::assertSame('validation-rejected', GithubSyncFailure::describe(new InvalidArgumentException('secret should not appear')));
        self::assertSame('unexpected-RuntimeException', GithubSyncFailure::describe(new RuntimeException('secret should not appear')));
    }
}
