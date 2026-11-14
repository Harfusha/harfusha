<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$issues = [];
// Load the same private environment settings as the application itself.
// The extension and PHP version checks still report deficiencies gracefully.
if (is_file($root . '/vendor/autoload.php')) {
    require_once $root . '/vendor/autoload.php';
    if (class_exists(\Dotenv\Dotenv::class) && is_file($root . '/.env')) {
        \Dotenv\Dotenv::createUnsafeImmutable($root)->load();
    }
}

if (PHP_VERSION_ID < 80512 || PHP_VERSION_ID >= 80600) { $issues[] = 'PHP 8.5.12+ (8.5 branch) is required for security and compatibility'; }
foreach (['intl','mbstring','pdo','pdo_mysql','simplexml','json','openssl'] as $extension) {
    if (!extension_loaded($extension)) { $issues[] = 'Missing extension: ' . $extension; }
}
$environment = getenv('APP_ENV') ?: '';
if (!in_array($environment, ['development','testing','production'], true)) {
    $issues[] = 'APP_ENV must explicitly select development, testing or production';
}
if ($environment === 'production') {
    $origin = getenv('APP_URL') ?: '';
    $parts = parse_url($origin);
    if (!is_array($parts) || ($parts['scheme'] ?? null) !== 'https' ||
        !is_string($parts['host'] ?? null) || isset($parts['path']) ||
        isset($parts['query']) || isset($parts['user'])) {
        $issues[] = 'APP_URL must be an HTTPS origin without a path';
    }
    if (!preg_match('/^[a-fA-F0-9]{64,128}$/D', getenv('SECURITY_SALT') ?: '')) {
        $issues[] = 'SECURITY_SALT must be random hexadecimal';
    }
    $adminHash = getenv('ADMIN_PASSWORD_HASH');
    if (!is_string($adminHash) || $adminHash === '' || password_get_info($adminHash)['algoName'] === 'unknown') {
        $issues[] = 'ADMIN_PASSWORD_HASH must be a supported password_hash() value';
    }
    $cron = getenv('CRON_SECRET');
    if ($cron !== false && $cron !== '' && strlen($cron) < 32) {
        $issues[] = 'Configured CRON_SECRET must be at least 32 characters';
    }
    if (filter_var(getenv('DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN)) {
        $issues[] = 'DEBUG must be disabled in production';
    }
}
if (!is_file($root . '/vendor/autoload.php')) { $issues[] = 'Composer dependencies missing: run composer install'; }
if (!is_file($root . '/composer.lock')) { $issues[] = 'composer.lock missing: resolve dependencies on PHP 8.5 and commit the lock'; }
if (!is_file($root . '/.env')) {
    $issues[] = '.env is missing: configure private secrets';
} elseif ($environment === 'production') {
    $permissions = fileperms($root . '/.env');
    if ($permissions !== false && ($permissions & 0o007) !== 0) {
        $issues[] = '.env is accessible by other users: restrict filesystem permissions';
    }
}
foreach (['tmp','tmp/cache','logs'] as $dir) {
    if (!is_writable($root . '/' . $dir)) { $issues[] = 'Not writable: ' . $dir; }
}
foreach ($issues as $issue) { fwrite(STDERR, 'FAIL ' . $issue . "\n"); }
if ($issues === []) { echo "OK: basic PHP extensions, vendor and filesystem prerequisites detected.\n"; }
exit($issues === [] ? 0 : 1);
