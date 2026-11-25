<?php
declare(strict_types=1);

/**
 * Offline view smoke renderer. Exercises real project templates without a CakePHP
 * runtime. It intentionally does not test controllers/ORM/HTTP or replace PHPUnit.
 * Run: php scripts/render-public-preview.php
 */
namespace Cake\Core {
    final class Configure
    {
        public static function read(string $key, mixed $fallback = null): mixed
        {
            return $key === 'App.fullBaseUrl' ? 'https://portfolio.invalid' : $fallback;
        }
    }
}
namespace {
    use App\Domain\Project;
    use App\Domain\JournalFeed;
    use App\Domain\ProjectCatalog;
    use App\Support\Language;

    // Browser preview can run without ext-mbstring; production CakePHP cannot.
    if (!function_exists('mb_strlen')) {
        function mb_strlen(string $value, ?string $encoding = null): int { return strlen($value); }
    }
    $root = dirname(__DIR__);
    $previewDir = getenv('HAFRUSHA_PREVIEW_DIR');
    $out = $previewDir === false || $previewDir === ''
        ? $root . '/tmp/public-preview'
        : (str_starts_with($previewDir, DIRECTORY_SEPARATOR) ? $previewDir : $root . '/' . $previewDir);
    spl_autoload_register(static function (string $class) use ($root): void {
        if (!str_starts_with($class, 'App\\')) {
            return;
        }
        $file = $root . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file)) {
            require $file;
        }
    });

    final class OfflineView
    {
        private string $contents = '';

        public function fetch(string $block): string
        {
            return $block === 'content' ? $this->contents : '';
        }

        /** @param array<string,mixed> $variables */
        public function build(string $file, array $variables): string
        {
            extract($variables, EXTR_SKIP);
            ob_start();
            try {
                require $file;
                $this->contents = (string)ob_get_clean();
                ob_start();
                require dirname(__DIR__) . '/templates/layout/default.php';
                return (string)ob_get_clean();
            } catch (\Throwable $error) {
                ob_end_clean();
                throw $error;
            }
        }
    }

    set_error_handler(static function (int $level, string $message): never {
        throw new \ErrorException($message, 0, $level);
    });
    $catalog = new ProjectCatalog($root . '/data');
    $projects = $catalog->projects();
    $validProjectSlugs = array_fill_keys(array_map(static fn (Project $project): string => $project->slug, $projects), true);
    foreach (['cs', 'en'] as $code) {
        $projectDir = $out . '/' . $code . '/projects';
        foreach (glob($projectDir . '/*/index.html') ?: [] as $page) {
            if (!isset($validProjectSlugs[basename(dirname($page))])) {
                unlink($page);
            }
        }
    }
    $featured = array_values(array_filter($projects, static fn (Project $p): bool => $p->featured));
    $mergedPrCount = count(array_filter(
        $catalog->contributions(),
        static fn (array $entry): bool => ($entry['kind'] ?? null) === 'pr'
            && ($entry['state'] ?? null) === 'merged',
    ));
    $snapshotProvenance = [
        'source' => 'reviewed-snapshot',
        'last_sync' => null,
        'age_hours' => null,
        'is_stale' => false,
        'snapshot_date' => '2026-11-12',
        'is_expired' => false,
    ];
    $fixtures = [
        'home' => [
            'projects' => array_slice($featured, 0, 4),
            'projectCount' => count($projects),
            'mergedPrCount' => $mergedPrCount,
            'provenance' => $snapshotProvenance,
            'evidenceRegistry' => $catalog->evidence(),
            'journal' => $catalog->journal(),
            'recentRepos' => [
                [
                    'repository' => 'Harfusha/MutaUI',
                    'description' => 'Reviewed static snapshot (2026-11-12)',
                    'repo_url' => 'https://github.com/Harfusha/MutaUI',
                    'pushed_at' => '2026-11-12 18:53:55',
                ],
            ],
        ],
        'projects' => ['projects' => $projects, 'evidenceRegistry' => $catalog->evidence()],
        'connections' => ['projects' => $projects, 'edges' => $catalog->connections()],
        'decisions' => ['decisions' => $catalog->decisions()],
        'homelab' => ['nodes' => $catalog->topology(), 'flows' => $catalog->flows()],
        'maker' => [],
        'open-source' => [
            'activity' => $catalog->contributions(),
            'provenance' => $snapshotProvenance,
        ],
        'journal' => ['journal' => $catalog->journal()],
        'now' => [
            'recentRepos' => [
                [
                    'repository' => 'Harfusha/MutaUI',
                    'description' => 'Reviewed static snapshot (2026-11-12)',
                    'repo_url' => 'https://github.com/Harfusha/MutaUI',
                    'pushed_at' => '2026-11-12 18:53:55',
                ],
            ],
            'journalEntries' => $catalog->journal(),
            'activity' => $catalog->contributions(),
            'provenance' => $snapshotProvenance,
        ],
        'about' => [],
    ];
    $count = 0;
    $metadata = [
        'home' => [['Adam Halfar | PHP a vývojářské nástroje', 'Adam Halfar | PHP and developer tools'], ['Portfolio Adama Halfara: projekty v PHP a CakePHP, příspěvky do open-source knihoven a nástroje pro vývoj i provoz.', 'Adam Halfar’s portfolio: PHP and CakePHP projects, open-source contributions, and tools for development and operations.']],
        'projects' => [['Projekty | Harfusha', 'Projects | Harfusha'], ['Případové studie vývojářských nástrojů, infrastruktury a technických prototypů.', 'Case studies in developer tools, infrastructure and engineering prototypes.']],
        'connections' => [['Skupiny projektů | Harfusha', 'Project groups | Harfusha'], ['Tematický přehled projektů a jejich současné fáze.', 'A thematic index of projects and their current stages.']],
        'homelab' => [['Mapa závislostí domácí sítě | Harfusha', 'Home network dependency map | Harfusha'], ['Prozkoumej dokumentovaná zařízení, služby a dopady výpadků.', 'Explore documented devices, services and outage impacts.']],
        'decisions' => [['Technická rozhodnutí | Harfusha Lab', 'Engineering decisions | Harfusha Lab'], ['Důvody, kompromisy a ověřitelné limity návrhu skutečných projektů.', 'Rationale, trade-offs and stated limitations behind real projects.']],
        'maker' => [['3D skeny a příprava tisku | Harfusha', '3D Scanning and Print Preparation | Harfusha'], ['Dva samostatné projekty: rekonstrukce CAD modelu ze skenu a návrh služby pro poptávky 3D tisku.', 'Two separate projects: CAD reconstruction from a scan and a proposed service for 3D-print requests.']],
        'open-source' => [['Open-source příspěvky | Harfusha Lab', 'Open Source | Harfusha Lab'], ['Vybrané pull requesty a hlášení chyb do open-source projektů.', 'Selected pull requests and bug reports in open-source projects.']],
        'journal' => [['Vývojový deník | Harfusha Lab', 'Development Journal | Harfusha Lab'], ['Poznámky k opravám, novým funkcím a technickým rozhodnutím.', 'Notes on fixes, new features and engineering decisions.']],
        'now' => [['Co se změnilo | Harfusha', 'What’s Changed | Harfusha'], ['Přehled posledních zaznamenaných změn veřejných repozitářů.', 'A view of the latest recorded changes in public repositories.']],
        'about' => [['O mně | Harfusha Lab', 'About | Harfusha Lab'], ['Adam Halfar, backendový vývojář se zaměřením na PHP a CakePHP.', 'Adam Halfar, a backend developer focused on PHP and CakePHP.']],
    ];
    foreach (['cs', 'en'] as $code) {
        $language = new Language($code);
        foreach ($fixtures as $template => $data) {
            $route = $template === 'home' ? '/' : '/' . $template;
            $dir = $out . '/' . $code . '/' . ($template === 'home' ? '' : $template);
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \RuntimeException('Cannot create preview directory');
            }
            $html = (new OfflineView())->build($root . '/templates/Pages/' . ($template === 'open-source' ? 'open_source' : $template) . '.php', [
                'lang' => $language, 'route' => $route,
                'title' => $language->value(...$metadata[$template][0]),
                'description' => $language->value(...$metadata[$template][1]), ...$data,
            ]);
            file_put_contents($dir . '/index.html', $html);
            $count++;
        }
        file_put_contents($out . '/' . $code . '/feed.xml', JournalFeed::render('https://portfolio.invalid', $language, $catalog->journal()));
        foreach ($projects as $project) {
            $dir = $out . '/' . $code . '/projects/' . $project->slug;
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \RuntimeException('Cannot create project preview directory');
            }
            $relations = [];
            foreach ($catalog->connections() as $edge) {
                if ($edge['from'] !== $project->slug && $edge['to'] !== $project->slug) {
                    continue;
                }
                $other = $edge['from'] === $project->slug ? $edge['to'] : $edge['from'];
                $related = $catalog->find($other);
                if ($related !== null) {
                    $relations[] = ['slug' => $other,'name' => $related->name,'type' => $edge['type']];
                }
            }
            $html = (new OfflineView())->build($root . '/templates/Pages/project.php', [
                'lang' => $language, 'route' => '/projects/' . $project->slug,
                'title' => $project->name . ' | Harfusha Lab', 'description' => $project->summary($language),
                'project' => $project, 'related' => $relations,
                'decisions' => array_values(array_filter($catalog->decisions(),
                    static fn (\App\Domain\EngineeringDecision $entry): bool => $entry->project === $project->slug)),
                'evidence' => $catalog->evidence()->forProject($project->slug),
            ]);
            file_put_contents($dir . '/index.html', $html);
            $count++;
        }
    }
    // Static assets are copied to make the preview independent of a running Cake app.
    $static = $root . '/webroot/assets';
    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($static));
    foreach ($iterator as $entry) {
        if (!$entry->isFile()) {
            continue;
        }
        $relative = substr($entry->getPathname(), strlen($static) + 1);
        $target = $out . '/assets/' . $relative;
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0775, true);
        }
        copy($entry->getPathname(), $target);
    }
    foreach (['manifest.webmanifest', 'robots.txt'] as $topFile) {
        if (is_file($root . '/webroot/' . $topFile)) {
            copy($root . '/webroot/' . $topFile, $out . '/' . $topFile);
        }
    }
    restore_error_handler();
    printf("Offline preview: %d public pages rendered to %s\n", $count, $out);
}
