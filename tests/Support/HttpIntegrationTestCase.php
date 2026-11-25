<?php
declare(strict_types=1);

namespace App\Test\Support;

use App\Security\AdminCredentials;
use App\Support\Environment;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Execute HTTP integration tests with the same canonical origin as the CI
 * application's APP_URL (http://localhost:8765). CakePHP's test client uses
 * a default port unless explicitly configured; production HostGuard must not
 * be relaxed to make tests pass.
 */
abstract class HttpIntegrationTestCase extends TestCase
{
    use IntegrationTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configRequest([
            'environment' => [
                'HTTP_HOST' => 'localhost:8765',
                'SERVER_NAME' => 'localhost',
                'SERVER_PORT' => '8765',
            ],
        ]);
    }

    /**
     * Seed a valid authenticated admin session matching N-SEC-04 session contract.
     *
     * @param array<string, mixed> $extra
     */
    protected function actingAsAdmin(array $extra = []): void
    {
        $now = time();
        $session = [
            'lab.admin' => true,
            'lab.admin_login_at' => $now,
            'lab.admin_last_seen_at' => $now,
        ];
        $hash = Environment::get('ADMIN_PASSWORD_HASH');
        if (is_string($hash) && $hash !== '') {
            $session['lab.admin_cred_digest'] = AdminCredentials::sessionDigest($hash);
        }
        $this->session($session + $extra);
    }
}
