<?php
declare(strict_types=1);

// Check the database target BEFORE loading CakePHP or running any fixtures.
// Never trust a filename suffix as proof that the database is disposable.
require_once dirname(__DIR__) . '/src/Security/TestDatabaseGuard.php';
\App\Security\TestDatabaseGuard::assertSafe(
    (string)(getenv('APP_ENV') ?: ''),
    (string)(getenv('DB_NAME') ?: ''),
    (string)(getenv('DB_HOST') ?: ''),
    (string)(getenv('DB_USER') ?: ''),
    (string)(getenv('LAB_TEST_DB_CONFIRM') ?: ''),
);
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/bootstrap.php';
