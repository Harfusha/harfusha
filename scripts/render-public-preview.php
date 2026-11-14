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
    $out = $root . '/tmp/public-preview';
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
    $featured = array_values(array_filter($projects, static fn (Project $p): bool => $p->featured));
    $fixtures = [
        'home' => ['projects' => array_slice($featured, 0, 4), 'journal' => $catalog->journal(), 'recentRepos' => [
            ['repository'=>'Harfusha/MutaUI','description'=>'Static snapshot','repo_url'=>'https://github.com/Harfusha/MutaUI','pushed_at'=>'2026-11-12 18:53:55'],
        ]],
        'projects' => ['projects' => $projects],
        'connections' => ['projects' => $projects, 'edges' => $catalog->connections()],
        'decisions' => ['decisions' => $catalog->decisions()],
        'homelab' => ['nodes' => $catalog->topology(), 'flows' => $catalog->flows()],
        'maker' => ['items' => $catalog->maker()],
        'playground' => [],
        'open-source' => ['activity' => $catalog->contributions()],
        'journal' => ['journal' => $catalog->journal()],
        'now' => ['recentRepos' => [['repository'=>'Harfusha/MutaUI','description'=>'Reviewed snapshot','repo_url'=>'https://github.com/Harfusha/MutaUI','pushed_at'=>'2026-11-12 18:53:55']], 'journal' => $catalog->journal(), 'provenance'=>['source'=>'reviewed-snapshot','last_sync'=>null]],
        'about' => [],
    ];
    $count = 0;
    foreach (['cs', 'en'] as $code) {
        $language = new Language($code);
        foreach ($fixtures as $template => $data) {
            $route = $template === 'home' ? '/' : '/' . $template;
            $dir = $out . '/' . $code . '/' . ($template === 'home' ? '' : $template);
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \RuntimeException('Cannot create preview directory');
            }
            $html = (new OfflineView())->build($root . '/templates/Pages/' . $template . '.php', [
                'lang' => $language, 'route' => $route, 'title' => 'Harfusha Lab',
                'description' => 'Offline view smoke preview', ...$data,
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
                'title' => $project->name, 'description' => $project->summary($language),
                'project' => $project, 'related' => $relations,
                'decisions' => array_values(array_filter($catalog->decisions(),
                    static fn (\App\Domain\EngineeringDecision $entry): bool => $entry->project === $project->slug)),
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
    restore_error_handler();
    printf("Offline preview: %d public pages rendered to %s\n", $count, $out);
}
