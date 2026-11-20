<?php
declare(strict_types=1);

if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}
if (!defined('TMP')) {
    define('TMP', ROOT . '/tmp/');
}
if (!defined('LOGS')) {
    define('LOGS', ROOT . '/logs/');
}
if (!defined('CACHE')) {
    define('CACHE', TMP . 'cache/');
}
require_once ROOT . '/vendor/autoload.php';
