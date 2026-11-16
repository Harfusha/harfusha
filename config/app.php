<?php
declare(strict_types=1);

use Cake\Cache\Engine\FileEngine;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Log\Engine\FileLog;

$baseUrl = \App\Support\Environment::get('APP_URL') ?: 'http://localhost:8765';
$debug = filter_var(\App\Support\Environment::get('DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN);
return [
    'debug' => $debug,
    'App' => [
        'namespace' => 'App', 'encoding' => 'UTF-8', 'defaultLocale' => 'cs_CZ',
        'defaultTimezone' => 'UTC', 'base' => false, 'dir' => 'src',
        'webroot' => 'webroot', 'wwwRoot' => WWW_ROOT, 'fullBaseUrl' => $baseUrl,
        'imageBaseUrl' => 'assets/img/', 'cssBaseUrl' => 'assets/css/', 'jsBaseUrl' => 'assets/js/',
        'paths' => ['plugins' => [ROOT . DS . 'plugins' . DS], 'templates' => [ROOT . DS . 'templates' . DS], 'locales' => [RESOURCES . 'locales' . DS]],
    ],
    'Security' => ['salt' => \App\Support\Environment::get('SECURITY_SALT') ?: str_repeat('0', 64)],
    'Asset' => ['cacheTime' => '+1 year'],
    'Cache' => [
        'default' => ['className' => FileEngine::class, 'path' => CACHE],
        '_cake_model_' => ['className' => FileEngine::class, 'path' => CACHE . 'models' . DS, 'prefix' => 'lab_model_'],
        '_cake_translations_' => ['className' => FileEngine::class, 'path' => CACHE . 'persistent' . DS, 'prefix' => 'lab_translation_'],
    ],
    'Error' => ['errorLevel' => E_ALL, 'skipLog' => [], 'log' => true, 'trace' => $debug],
    'Datasources' => [
        'default' => [
            'className' => Connection::class, 'driver' => Mysql::class,
            'host' => \App\Support\Environment::get('DB_HOST') ?: '127.0.0.1',
            'port' => (int)(\App\Support\Environment::get('DB_PORT') ?: 3306),
            'database' => \App\Support\Environment::get('DB_NAME') ?: 'harfusha_lab',
            'username' => \App\Support\Environment::get('DB_USER') ?: 'harfusha_lab',
            'password' => \App\Support\Environment::get('DB_PASS') ?: '',
            'persistent' => false, 'timezone' => 'UTC', 'encoding' => 'utf8mb4',
            'cacheMetadata' => true, 'log' => false, 'quoteIdentifiers' => false,
        ],
    ],
    'Log' => [
        'debug' => ['className' => FileLog::class, 'path' => LOGS, 'file' => 'debug', 'levels' => ['notice','info','debug']],
        'error' => ['className' => FileLog::class, 'path' => LOGS, 'file' => 'error', 'levels' => ['warning','error','critical','alert','emergency']],
    ],
    'Session' => ['defaults' => 'php', 'timeout' => 60, 'cookie' => 'harfusha_lab', 'ini' => [
        'session.use_strict_mode' => 1, 'session.cookie_httponly' => 1,
        'session.cookie_secure' => str_starts_with($baseUrl, 'https://') ? 1 : 0,
        'session.cookie_samesite' => 'Strict',
    ]],
    'Lab' => ['databaseEnabled' => (\App\Support\Environment::get('DB_NAME') ?: '') !== ''],
];
