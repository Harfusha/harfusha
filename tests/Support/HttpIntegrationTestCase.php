<?php
declare(strict_types=1);

namespace App\Test\Support;

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
}
