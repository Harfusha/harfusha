<?php
declare(strict_types=1);

use App\Security\TestDatabaseGuard;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\ConnectionHelper;
use Migrations\TestSuite\Migrator;

// Check the database target BEFORE loading CakePHP or running any fixtures.
// Never trust a filename suffix as proof that the database is disposable.
require_once dirname(__DIR__) . '/src/Security/TestDatabaseGuard.php';
TestDatabaseGuard::assertSafe(
    (string)(getenv('APP_ENV') ?: ''),
    (string)(getenv('DB_NAME') ?: ''),
    (string)(getenv('DB_HOST') ?: ''),
    (string)(getenv('DB_USER') ?: ''),
    (string)(getenv('LAB_TEST_DB_CONFIRM') ?: ''),
    (string)(getenv('LAB_TEST_DB_REMOTE_CONFIRM') ?: ''),
);
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/bootstrap.php';

// The guard above confirmed the explicit target and non-privileged DB account.
// CakePHP Migrations 5 requires an explicit datasource named "test".
$testConfig = ConnectionManager::getConfig('default');
if (!is_array($testConfig)) {
    throw new RuntimeException('Test database configuration is missing');
}
ConnectionManager::setConfig('test', $testConfig);
ConnectionHelper::addTestAliases();
(new Migrator())->run();
