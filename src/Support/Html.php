<?php
declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;
use RuntimeException;

final class Html
{
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
        $encoded = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
                | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR,
        );

        return $encoded;
    }

    /**
     * Cache-bust first-party assets using a content digest, not a manual date.
     * A new deployment gets new URLs even when ZIP extraction keeps timestamps.
     */
    public static function versionedAsset(string $asset): string
    {
        $allowed = ['assets/css/app.css', 'assets/js/app.js', 'assets/js/lab-core.js'];
        if (!in_array($asset, $allowed, true)) {
            throw new InvalidArgumentException('Unsupported public asset');
        }
        $root = defined('WWW_ROOT') ? WWW_ROOT : dirname(__DIR__, 2) . '/webroot/';
        $digest = hash_file('sha256', $root . str_replace('/', DIRECTORY_SEPARATOR, $asset));
        if (!is_string($digest)) {
            throw new RuntimeException('Cannot fingerprint required public asset');
        }

        return '/' . $asset . '?v=' . substr($digest, 0, 12);
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
     * Check an outbound hyperlink against the allowed URL policy.
     */
    public static function safeUrl(string $url): string
    {
        if ($url === '' || preg_match('~^https://(github\.com|www\.github\.com|github\.io)/~i', $url) !== 1) {
            return '#';
        }

        return $url;
    }
}
