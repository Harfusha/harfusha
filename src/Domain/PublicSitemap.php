<?php
declare(strict_types=1);

namespace App\Domain;

use App\Support\Html;
use InvalidArgumentException;

/**
 * A deterministic, localized and search-engine-friendly public sitemap.
 * URL paths come only from reviewed route names and project slugs.
 */
final class PublicSitemap
{
    private function __construct()
    {
    }

    /** @param list<string> $routes */
    public static function render(string $origin, array $routes): string
    {
        if (preg_match('~^https?://[a-z0-9.-]+(?::[0-9]{1,5})?$~iD', $origin) !== 1) {
            throw new InvalidArgumentException('Sitemap origin must be a bare HTTP(S) host');
        }
        if (count($routes) !== count(array_unique($routes))) {
            throw new InvalidArgumentException('Duplicate sitemap route');
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
        foreach ($routes as $route) {
            if ($route !== '' && preg_match('~^[a-z0-9]+(?:[a-z0-9/-]*[a-z0-9])?$~D', $route) !== 1) {
                throw new InvalidArgumentException('Invalid sitemap route');
            }
            foreach (['cs', 'en'] as $language) {
                $xml .= '<url><loc>' . self::escape($origin . Html::path($language, $route)) . '</loc>';
                foreach (['cs', 'en', 'x-default'] as $alternate) {
                    $target = $alternate === 'x-default' ? 'cs' : $alternate;
                    $xml .= '<xhtml:link rel="alternate" hreflang="' . $alternate
                        . '" href="' . self::escape($origin . Html::path($target, $route)) . '"/>';
                }
                $xml .= '</url>' . "\n";
            }
        }

        return $xml . '</urlset>';
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
