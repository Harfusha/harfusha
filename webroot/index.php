<?php
declare(strict_types=1);

// The development PHP server ignores Apache .htaccess rules. Serve only
// expected public static asset formats; route hidden/source files to CakePHP.
if (PHP_SAPI === 'cli-server') {
    $path = parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if (is_string($path) && $path !== '/' && !str_contains($path, '..')) {
        $segments = explode('/', ltrim($path, '/'));
        $hidden = false;
        foreach ($segments as $segment) {
            if ($segment === '' || str_starts_with($segment, '.')) {
                $hidden = true;
                break;
            }
        }
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $publicTypes = [
            'css', 'js', 'map', 'svg', 'png', 'jpg', 'jpeg', 'webp',
            'gif', 'ico', 'woff', 'woff2', 'ttf', 'txt', 'xml',
            'webmanifest', 'json',
        ];
        if (!$hidden && in_array($extension, $publicTypes, true) && is_file(__DIR__ . $path)) {
            return false;
        }
    }
}
require dirname(__DIR__) . '/vendor/autoload.php';
$server = new \Cake\Http\Server(new \App\Application(dirname(__DIR__) . '/config'));
$server->emit($server->run());
