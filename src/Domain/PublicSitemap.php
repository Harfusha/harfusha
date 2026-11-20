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
    /**
     * Initialize validated domain state and required dependencies.
     */
    private function __construct()
    {
    }

    /**
     * @param list<string> $routes
     * @param array<string,string> $lastmodMap Map of route => YYYY-MM-DD
     * @param array<string,list<array{loc:string,title?:string}>> $imageMap Map of route => images
     */
    public static function render(
        string $origin,
        array $routes,
        array $lastmodMap = [],
        array $imageMap = [],
    ): string {
        if (preg_match('~^https?://[a-z0-9.-]+(?::[0-9]{1,5})?$~iD', $origin) !== 1) {
            throw new InvalidArgumentException('Sitemap origin must be a bare HTTP(S) host');
        }
        if (count($routes) !== count(array_unique($routes))) {
            throw new InvalidArgumentException('Duplicate sitemap route');
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
            . ' xmlns:xhtml="http://www.w3.org/1999/xhtml"'
            . ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        foreach ($routes as $route) {
            if ($route !== '' && preg_match('~^[a-z0-9]+(?:[a-z0-9/-]*[a-z0-9])?$~D', $route) !== 1) {
                throw new InvalidArgumentException('Invalid sitemap route');
            }
            $lastmod = $lastmodMap[$route] ?? null;
            if ($lastmod !== null && preg_match('~^\d{4}-\d{2}-\d{2}$~D', $lastmod) !== 1) {
                throw new InvalidArgumentException('Invalid sitemap lastmod format');
            }
            $images = $imageMap[$route] ?? [];
            foreach ($images as $img) {
                if (!str_starts_with($img['loc'], '/') || str_contains($img['loc'], '..')) {
                    throw new InvalidArgumentException('Invalid sitemap image location');
                }
            }
            foreach (['cs', 'en'] as $language) {
                $xml .= '<url><loc>' . self::escape($origin . Html::path($language, $route)) . '</loc>';
                if ($lastmod !== null) {
                    $xml .= '<lastmod>' . self::escape($lastmod) . '</lastmod>';
                }
                foreach ($images as $img) {
                    $xml .= '<image:image><image:loc>' . self::escape($origin . $img['loc']) . '</image:loc>';
                    if (isset($img['title']) && $img['title'] !== '') {
                        $xml .= '<image:title>' . self::escape($img['title']) . '</image:title>';
                    }
                    $xml .= '</image:image>';
                }
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

    /**
     * Encode untrusted text for safe inclusion in this output format.
     */
    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
