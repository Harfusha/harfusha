<?php
declare(strict_types=1);

namespace App\Support;

final class Html
{
    public static function escape(string|int|float|null $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /** @param mixed $data */
    public static function json(mixed $data): string
    {
        $encoded = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);

        return $encoded;
    }

    public static function path(string $lang, string $route = '/'): string
    {
        $route = '/' . ltrim($route, '/');

        return '/' . ($lang === 'en' ? 'en' : 'cs') . ($route === '/' ? '/' : $route);
    }

    public static function safeUrl(string $url): string
    {
        if ($url === '' || preg_match('~^https://(github\.com|www\.github\.com|github\.io)/~i', $url) !== 1) {
            return '#';
        }

        return $url;
    }
}
