<?php
declare(strict_types=1);
if (PHP_SAPI === 'cli-server') {
    $path = parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if (is_string($path) && $path !== '/' && !str_contains($path, '..') && is_file(__DIR__ . $path)) {
        return false;
    }
}
require dirname(__DIR__) . '/vendor/autoload.php';
$server = new \Cake\Http\Server(new \App\Application(dirname(__DIR__) . '/config'));
$server->emit($server->run());
