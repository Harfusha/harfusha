<?php
declare(strict_types=1);

use App\Domain\PublicRouteRegistry;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes): void {
    $routes->setRouteClass(DashedRoute::class);
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'redirectToHome'])->setMethods(['GET', 'HEAD']);
    $routes->connect('/healthz', ['controller' => 'System', 'action' => 'health'])->setMethods(['GET', 'HEAD']);
    $routes->connect('/api/v1/projects', ['controller' => 'Api', 'action' => 'projects'])->setMethods(['GET', 'HEAD']);
    $routes->connect('/api/v1/projects/{slug}', ['controller' => 'Api', 'action' => 'project'])
        ->setPatterns(['slug' => '[a-z0-9-]+'])->setMethods(['GET', 'HEAD']);
    $routes->connect('/api/v1/evidence', ['controller' => 'Api', 'action' => 'evidence'])->setMethods(['GET', 'HEAD']);
    $routes->connect('/sitemap.xml', ['controller' => 'System', 'action' => 'sitemap'])->setMethods(['GET', 'HEAD']);
    $routes->connect('/cron/github', ['controller' => 'System', 'action' => 'sync'])->setMethods(['POST']);
    $routes->connect('/admin', ['controller' => 'Admin', 'action' => 'index'])->setMethods(['GET', 'HEAD']);
    $routes->connect('/admin/login', ['controller' => 'Admin', 'action' => 'login'])->setMethods(['POST']);
    $routes->connect('/admin/logout', ['controller' => 'Admin', 'action' => 'logout'])->setMethods(['POST']);
    $routes->connect('/admin/project', ['controller' => 'Admin', 'action' => 'project'])->setMethods(['POST']);
    $routes->connect('/admin/journal/update', ['controller' => 'Admin', 'action' => 'journalUpdate'])->setMethods(['POST']);
    $routes->connect('/admin/journal', ['controller' => 'Admin', 'action' => 'journal'])->setMethods(['POST']);
    $routes->connect('/{lang}/feed.xml', ['controller' => 'System', 'action' => 'feed'])
        ->setPatterns(['lang' => 'cs|en'])->setMethods(['GET', 'HEAD']);
    $routes->connect('/{lang}/', ['controller' => 'Pages', 'action' => 'home'])->setPatterns(['lang' => 'cs|en'])->setMethods(['GET', 'HEAD']);

    $registry = new PublicRouteRegistry();
    foreach ($registry->pageActions() as $page => $action) {
        if ($page === '') {
            continue;
        }
        $routes->connect('/{lang}/' . $page, ['controller' => 'Pages', 'action' => $action])
            ->setPatterns(['lang' => 'cs|en'])->setMethods(['GET', 'HEAD']);
    }
    $routes->connect('/{lang}/projects/{slug}', ['controller' => 'Pages', 'action' => 'project'])
        ->setPatterns(['lang' => 'cs|en', 'slug' => '[a-z0-9-]+'])->setMethods(['GET', 'HEAD']);
    $routes->connect('/{lang}/journal/{entryId}', ['controller' => 'Pages', 'action' => 'journalEntry'])
        ->setPatterns(['lang' => 'cs|en', 'entryId' => 'entry-[a-z0-9-]+'])->setMethods(['GET', 'HEAD']);
    // No fallback routes, no auto-exposed controller actions.
};
