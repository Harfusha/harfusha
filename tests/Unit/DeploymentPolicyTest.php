<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Security\DeploymentPolicy;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** Keep the production preflight and runtime boot on one security policy. */
final class DeploymentPolicyTest extends TestCase
{
    private const GOOD_SALT = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';

    protected function setUp(): void
    {
        parent::setUp();
        if (PHP_VERSION_ID < 80512 || PHP_VERSION_ID >= 80600) {
            self::markTestSkipped('DeploymentPolicy tests require patched PHP 8.5.12+ (run in Docker staging)');
        }
    }

    public function testValidProductionOriginAndSaltAreAccepted(): void
    {
        $this->expectNotToPerformAssertions();
        DeploymentPolicy::assertEnvironment('production', false, 'https://example.com', self::GOOD_SALT);
    }

    public function testUnsafeProductionOriginsAreRejected(): void
    {
        foreach (
            [
                'http://example.com',
                'https://example.com/',
                'https://example.com/path',
                'https://example.com?q=1',
                'https://example.com#fragment',
                'https://user:pass@example.com',
                'https://example.com:99999',
                'https://example.com/hidden?x=1',
                '',
            ] as $origin
        ) {
            try {
                DeploymentPolicy::assertEnvironment('production', false, $origin, self::GOOD_SALT);
                self::fail('Unsafe origin was accepted: ' . $origin);
            } catch (InvalidArgumentException $error) {
                self::assertStringContainsString('APP_URL', $error->getMessage());
            }
        }
    }

    public function testRepeatedOrMalformedProductionSaltsAreRejected(): void
    {
        foreach ([str_repeat('0', 64), str_repeat('f', 128), 'invalid', ''] as $salt) {
            try {
                DeploymentPolicy::assertEnvironment('production', false, 'https://example.com', $salt);
                self::fail('Unsafe production salt was accepted');
            } catch (InvalidArgumentException $error) {
                self::assertStringContainsString('salt', $error->getMessage());
                self::assertStringNotContainsString($salt === '' ? self::GOOD_SALT : $salt, $error->getMessage());
            }
        }
    }

    public function testDebugMustBeOffInProduction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DeploymentPolicy::assertEnvironment('production', true, 'https://example.com', self::GOOD_SALT);
    }

    public function testInvalidEnvironmentIsRejectedBeforeProductionChecks(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DeploymentPolicy::assertEnvironment('staging', false, 'https://example.com', self::GOOD_SALT);
    }

    public function testLocalDevelopmentDoesNotRequireProductionSecrets(): void
    {
        $this->expectNotToPerformAssertions();
        DeploymentPolicy::assertEnvironment('development', true, 'http://localhost:8765', '');
        DeploymentPolicy::assertEnvironment('testing', false, 'http://localhost:8765', '');
    }
}
