<?php
declare(strict_types=1);

require __DIR__ . DIRECTORY_SEPARATOR . 'paths.php';
require CORE_PATH . 'config' . DS . 'bootstrap.php';
require CAKE . 'functions.php';

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorTrap;
use Cake\Error\ExceptionTrap;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Utility\Security;

// Only private server-side code reads .env; no dotenv files under webroot.
if (is_file(ROOT . '/.env')) {
    \Dotenv\Dotenv::createUnsafeImmutable(ROOT)->load();
}
Configure::config('default', new PhpConfig());
Configure::load('app', 'default', false);
if (is_file(CONFIG . 'app_local.php')) {
    Configure::load('app_local', 'default');
}
date_default_timezone_set((string)Configure::read('App.defaultTimezone'));
mb_internal_encoding((string)Configure::read('App.encoding'));
(new ErrorTrap(Configure::read('Error')))->register();
(new ExceptionTrap(Configure::read('Error')))->register();
Cache::setConfig(Configure::consume('Cache'));
ConnectionManager::setConfig(Configure::consume('Datasources'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt((string)Configure::consume('Security.salt'));
Router::fullBaseUrl((string)Configure::read('App.fullBaseUrl'));

\App\Security\DeploymentPolicy::assertEnvironment(
    (string)(\App\Support\Environment::get('APP_ENV') ?: ''),
    (bool)Configure::read('debug'),
    (string)Configure::read('App.fullBaseUrl'),
    (string)(\App\Support\Environment::get('SECURITY_SALT') ?: ''),
);
