<?php
declare(strict_types=1);

namespace App\Domain;

/**
 * Single authoritative registry of public localized routes, distinguishing
 * indexable canonical pages from non-indexed aliases (such as /playground).
 */
final class PublicRouteRegistry
{
    /**
     * Indexable static routes mapped to their CakePHP controller action.
     *
     * @var array<string, string>
     */
    private const array INDEXABLE_PAGES = [
        '' => 'home',
        'projects' => 'projects',
        'connections' => 'connections',
        'decisions' => 'decisions',
        'homelab' => 'homelab',
        'maker' => 'maker',
        'open-source' => 'openSource',
        'now' => 'now',
        'journal' => 'journal',
        'about' => 'about',
    ];

    /**
     * Non-indexed legacy alias routes mapped to their CakePHP controller action.
     *
     * @var array<string, string>
     */
    private const array ALIAS_PAGES = [
        'playground' => 'playground',
    ];

    /**
     * Return all localized PagesController route => action bindings.
     *
     * @return array<string, string>
     */
    public function pageActions(): array
    {
        return self::INDEXABLE_PAGES + self::ALIAS_PAGES;
    }

    /**
     * Return canonical, indexable routes for sitemap.xml generation.
     *
     * @param list<\App\Domain\Project> $projects
     * @return list<string>
     */
    public function sitemapRoutes(array $projects): array
    {
        $routes = array_keys(self::INDEXABLE_PAGES);
        foreach ($projects as $project) {
            $routes[] = 'projects/' . $project->slug;
        }

        return $routes;
    }
}
