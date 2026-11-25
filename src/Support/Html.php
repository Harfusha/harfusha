<?php
declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;
use RuntimeException;

final class Html
{
    /**
     * Request-scoped asset fingerprint cache to avoid repeated hash_file() I/O.
     *
     * @var array<string, string>
     */
    private static array $assetVersionCache = [];

    /**
     * Encode untrusted text for safe inclusion in this output format.
     */
    public static function escape(string|int|float|null $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /** @param mixed $data */
    public static function json(mixed $data): string
    {
        return json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
                | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR,
        );
    }

    /**
     * Cache-bust first-party assets using a content digest, not a manual date.
     * A new deployment gets new URLs even when ZIP extraction keeps timestamps.
     */
    public static function versionedAsset(string $asset): string
    {
        $allowed = [
            'assets/css/app.css',
            'assets/css/portfolio.css',
            'assets/js/app.js',
            'assets/js/lab-core.js',
            'assets/js/theme-init.js',
        ];
        if (!in_array($asset, $allowed, true)) {
            throw new InvalidArgumentException('Unsupported public asset');
        }
        $root = defined('WWW_ROOT') ? WWW_ROOT : dirname(__DIR__, 2) . '/webroot/';
        $fullPath = $root . str_replace('/', DIRECTORY_SEPARATOR, $asset);
        $mtime = is_file($fullPath) ? filemtime($fullPath) : false;
        $size = is_file($fullPath) ? filesize($fullPath) : false;
        $cacheKey = $fullPath . '|' . ($mtime !== false ? (string)$mtime : '0') . '|' . ($size !== false ? (string)$size : '0');
        if (isset(self::$assetVersionCache[$cacheKey])) {
            return self::$assetVersionCache[$cacheKey];
        }
        $digest = hash_file('sha256', $fullPath);
        if (!is_string($digest)) {
            throw new RuntimeException('Cannot fingerprint required public asset');
        }
        $versioned = '/' . $asset . '?v=' . substr($digest, 0, 12);
        self::$assetVersionCache[$cacheKey] = $versioned;

        return $versioned;
    }

    /**
     * Build a safe URL path for the selected locale.
     */
    public static function path(string $lang, string $route = '/'): string
    {
        $route = '/' . ltrim($route, '/');

        return '/' . ($lang === 'en' ? 'en' : 'cs') . ($route === '/' ? '/' : $route);
    }

    /**
     * Check an outbound or internal hyperlink against the allowed URL policy.
     */
    public static function safeUrl(string $url): string
    {
        if ($url === '' || preg_match('/[\x00-\x1F\x7F]/', $url) === 1) {
            return '#';
        }

        if (str_starts_with($url, '/') && !str_starts_with($url, '//') && !str_contains($url, '\\')) {
            return $url;
        }

        if (str_contains($url, '\\')) {
            return '#';
        }

        $parsed = parse_url($url);
        if (
            !is_array($parsed) ||
            strtolower($parsed['scheme'] ?? '') !== 'https' ||
            isset($parsed['user']) ||
            isset($parsed['pass'])
        ) {
            return '#';
        }

        $host = strtolower($parsed['host'] ?? '');
        $allowedHosts = [
            'github.com',
            'www.github.com',
            'github.io',
            'adam-halfar.cz',
            'book.cakephp.org',
            'www.php.net',
            'phpstan.org',
        ];
        if (!in_array($host, $allowedHosts, true)) {
            return '#';
        }

        return $url;
    }
}
