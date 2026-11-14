<?php
declare(strict_types=1);

// The isolated evaluator lacks mbstring; runtime CakePHP requires it.
if (!function_exists('mb_strlen')) {
    // Isolated PHP 8.4 runner only; CakePHP production requires ext-mbstring.
    function mb_strlen(string $value, ?string $encoding = null): int { return strlen($value); }
}
if (!function_exists('mb_substr')) {
    function mb_substr(string $value, int $start, ?int $length = null): string {
        return substr($value, $start, $length);
    }
}
$root = dirname(__DIR__);
spl_autoload_register(static function (string $class) use ($root): void {
    if (!str_starts_with($class, 'App\\')) {
        return;
    }
    $path = $root . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

use App\Domain\ProjectCatalog;
use App\Domain\PublicProjectView;
use App\Domain\PublicContentValidator;
use App\Security\LoginLimiter;
use App\Security\DeploymentPolicy;
use App\Security\AdminCredentials;
use App\Security\TestDatabaseGuard;
use App\Domain\GithubRecord;
use App\Domain\TopologyGraph;
use App\Domain\ProjectConnections;
use App\Domain\EditorialInput;
use App\Domain\GithubSnapshot;
use App\Domain\GithubSearchPageCollector;
use App\Domain\GithubRepositoryPageCollector;
use App\Domain\PublicRepository;
use App\Service\EditorialAuditTrail;
use App\Domain\JournalFeed;
use App\Domain\PublicSitemap;
use App\Domain\OperationsStatus;
use App\Support\Language;
use App\Support\Html;

final class StandaloneRenderer
{
    /** @param array<string,mixed> $variables */
    public function render(string $file, array $variables): string
    {
        extract($variables, EXTR_SKIP);
        ob_start();
        try {
            require $file;
            return (string)ob_get_clean();
        } catch (Throwable $error) {
            ob_end_clean();
            throw $error;
        }
    }
}
$passed = 0;
$failed = 0;
function verify(bool $condition, string $message): void
{
    global $passed, $failed;
    if (!$condition) {
        $failed++;
        fwrite(STDERR, "FAIL: {$message}\n");
    } else {
        $passed++;
    }
}
set_error_handler(static function (int $severity, string $message): void {
    // Respect explicitly suppressed, handled filesystem races (e.g. mkdir EEXIST).
    if ((error_reporting() & $severity) === 0) {
        return;
    }
    throw new ErrorException($message, 0, $severity);
});
foreach (['projects','topology','connections','contributions','journal','maker','decisions','flows'] as $dataset) {
    $raw = file_get_contents($root . '/data/' . $dataset . '.json');
    $items = json_decode((string)$raw, true, 512, JSON_THROW_ON_ERROR);
    PublicContentValidator::validate($dataset . '.json', $items);
    verify(true, 'Full public schema: ' . $dataset);
}
try {
    PublicContentValidator::validate('contributions.json', [[
      'source_url'=>'https://github.com.evil.test/a/b/pull/1', 'repository'=>'a/b',
      'title'=>'Bad', 'kind'=>'pr', 'state'=>'merged','published_at'=>'2026-11-12',
    ]]);
    verify(false, 'Contribution URL origin validation');
} catch (InvalidArgumentException) {
    verify(true, 'Contribution URL origin validation');
}
try {
    PublicContentValidator::validate('topology.json', [[
      'id'=>'a','name'=>'A','kind'=>'compute','description_cs'=>'test',
      'description_en'=>'test','tech'=>[],'services'=>[],'depends'=>[],
    ]]);
    verify(false, 'Missing diagram coordinate validation');
} catch (InvalidArgumentException) {
    verify(true, 'Missing diagram coordinate validation');
}
$sampleHash = password_hash('  test password  ', PASSWORD_DEFAULT);
verify(AdminCredentials::verify('  test password  ', $sampleHash), 'Admin password retains significant whitespace');
verify(!AdminCredentials::verify('test password', $sampleHash), 'Trimmed admin password is not accepted');
verify(!AdminCredentials::verify(['password'], $sampleHash), 'Non-string admin password is rejected');
verify(!AdminCredentials::verify(str_repeat('x', 1025), $sampleHash), 'Overlong admin password is rejected');

TestDatabaseGuard::assertSafe('testing', 'harfusha_lab_ci', 'localhost', 'lab_ci', 'harfusha_lab_ci');
verify(true, 'Explicitly confirmed disposable MySQL test target accepted');
foreach ([
    ['production', 'harfusha_lab_ci', 'localhost', 'lab_ci', 'harfusha_lab_ci'],
    ['testing', 'harfusha_lab', 'localhost', 'lab_ci', 'harfusha_lab'],
    ['testing', 'harfusha_lab_ci', 'remote.example', 'lab_ci', 'harfusha_lab_ci'],
    ['testing', 'harfusha_lab_ci', 'localhost', 'root', 'harfusha_lab_ci'],
    ['testing', 'harfusha_lab_ci', 'localhost', 'lab_ci', 'wrong'],
] as $unsafe) {
    try {
        TestDatabaseGuard::assertSafe(...$unsafe);
        verify(false, 'Dangerous database test target rejected');
    } catch (RuntimeException) {
        verify(true, 'Dangerous database test target rejected');
    }
}

$nowOps = new DateTimeImmutable('2026-11-13T12:00:00Z');
$freshOps = OperationsStatus::evaluate(true, true, true, new DateTimeImmutable('2026-11-13T11:00:00Z'), $nowOps);
verify($freshOps['ready'] && $freshOps['github_age_hours'] === 1, 'Private operations readiness: fresh data');
$staleOps = OperationsStatus::evaluate(true, true, true, new DateTimeImmutable('2026-11-11T11:00:00Z'), $nowOps);
verify(!$staleOps['ready'] && $staleOps['github'] === 'stale', 'Private operations readiness: stale GitHub cache');
$downOps = OperationsStatus::evaluate(true, false, false, null, $nowOps);
verify(!$downOps['ready'] && $downOps['database'] === 'unreachable' && $downOps['github'] === 'unknown',
    'Private operations readiness: database failure fails closed');
$futureOps = OperationsStatus::evaluate(true, true, true, new DateTimeImmutable('2026-11-13T12:30:00Z'), $nowOps);
verify(!$futureOps['ready'] && $futureOps['github'] === 'invalid-timestamp', 'Future snapshot rejected');
$unconfiguredOps = OperationsStatus::evaluate(false, false, true, null, $nowOps);
verify(!$unconfiguredOps['ready'] && $unconfiguredOps['database'] === 'not-configured', 'Missing DB is not ready');

$limiterDir = sys_get_temp_dir() . '/harfusha-limit-' . bin2hex(random_bytes(5));
$nowRate = 1700000000;
$clockRate = static function () use (&$nowRate): int { return $nowRate; };
$limiter = new LoginLimiter($limiterDir, $clockRate);
for ($attempt=0; $attempt<5; $attempt++) {
    verify($limiter->consume('203.0.113.15'), 'Rate limiter reserves an attempt atomically');
}
verify(!$limiter->consume('203.0.113.15') && $limiter->consume('203.0.113.16'),
    'Rate limiter isolation and lockout');
$nowRate += 900;
verify($limiter->consume('203.0.113.15'), 'Rate limiter expires the sliding window');
verify($limiter->clear('203.0.113.15') && $limiter->consume('203.0.113.15'),
    'Successful authentication clears the identity counter');
$nowRate--;
verify(!$limiter->consume('203.0.113.15'), 'Clock rollback must not drop previously recorded failures');
$nowRate++;
verify(!$limiter->consume('') && !$limiter->consume(str_repeat('A', 129)),
    'Rate limiter rejects invalid and oversized client identifiers');
$limiterFiles = glob($limiterDir . '/login-*.json') ?: [];
verify(count($limiterFiles) <= 32, 'Rate limiter writes at most 32 shard filenames');
$corruptFile = $limiterFiles[0] ?? null;
if (is_string($corruptFile)) {
    file_put_contents($corruptFile, '{bad json');
    $corruptAddress = null;
    foreach (['203.0.113.15', '203.0.113.16'] as $address) {
        $digest = hash('sha256', $address);
        $shard = hexdec(substr($digest, 0, 2)) % 32;
        if (str_ends_with($corruptFile, '/login-' . $shard . '.json')) {
            $corruptAddress = $address;
            break;
        }
    }
    verify($corruptAddress !== null && !$limiter->consume($corruptAddress),
        'Corrupt limiter state fails closed');
}
foreach (glob($limiterDir . '/*') ?: [] as $entry) { unlink($entry); }
if (is_dir($limiterDir)) { rmdir($limiterDir); }

// All fresh identities in one shard compete for a fixed bounded slot count.
$capacityDir = sys_get_temp_dir() . '/harfusha-capacity-' . bin2hex(random_bytes(5));
$capacityGroups = [];
$capacityIdentities = [];
for ($index = 0; $index < 400 && count($capacityIdentities) < 3; $index++) {
    $address = 'identity-' . $index;
    $shard = hexdec(substr(hash('sha256', $address), 0, 2)) % 32;
    $capacityGroups[$shard] ??= [];
    $capacityGroups[$shard][] = $address;
    if (count($capacityGroups[$shard]) === 3) {
        $capacityIdentities = $capacityGroups[$shard];
    }
}
verify(count($capacityIdentities) === 3, 'Deterministic same-shard capacity test cases');
$capacityNow = 1700000000;
$capacityClock = static function () use (&$capacityNow): int { return $capacityNow; };
$capacityLimiter = new LoginLimiter($capacityDir, $capacityClock, 2);
verify($capacityLimiter->consume($capacityIdentities[0]) && $capacityLimiter->consume($capacityIdentities[1]) &&
    !$capacityLimiter->consume($capacityIdentities[2]), 'Full shard rejects newcomers without evicting lockouts');
$capacityNow += 900;
verify($capacityLimiter->consume($capacityIdentities[2]), 'Expired shard identity frees capacity');
foreach (glob($capacityDir . '/*') ?: [] as $entry) { unlink($entry); }
if (is_dir($capacityDir)) { rmdir($capacityDir); }

// A real process-level regression for the old check-then-record race.
// This runs when pcntl is installed; it does not require CakePHP or MySQL.
if (function_exists('pcntl_fork') && function_exists('pcntl_waitpid')) {
    $parallelDir = sys_get_temp_dir() . '/harfusha-parallel-' . bin2hex(random_bytes(5));
    $children = [];
    for ($i = 0; $i < 16; $i++) {
        $pid = pcntl_fork();
        if ($pid === -1) { throw new RuntimeException('Could not create rate-limit test child'); }
        if ($pid === 0) {
            $wasAllowed = (new LoginLimiter($parallelDir))->consume('192.0.2.100');
            exit($wasAllowed ? 0 : 1);
        }
        $children[] = $pid;
    }
    $allowed = 0;
    $denied = 0;
    foreach ($children as $pid) {
        $status = 0;
        pcntl_waitpid($pid, $status);
        if (pcntl_wifexited($status) && pcntl_wexitstatus($status) === 0) {
            ++$allowed;
        } elseif (pcntl_wifexited($status) && pcntl_wexitstatus($status) === 1) {
            ++$denied;
        }
    }
    verify($allowed === 5 && $denied === 11,
        'Parallel authentication attempts are counted atomically across processes');
    foreach (glob($parallelDir . '/*') ?: [] as $entry) { unlink($entry); }
    if (is_dir($parallelDir)) { rmdir($parallelDir); }
}

$validProjectForm = [
    'slug' => 'local-ai-hub', 'status' => 'active',
    'summary_cs' => ' Český zápis ', 'summary_en' => 'English',
    'problem_cs' => '', 'problem_en' => '', 'solution_cs' => '', 'solution_en' => '',
];
verify(EditorialInput::project($validProjectForm)['summary_cs'] === 'Český zápis', 'Typed editorial normalization');
verify(EditorialInput::positiveId('73') === 73, 'Strict editorial positive ID');
foreach ([
    [...$validProjectForm, 'slug' => '../sneaky'],
    [...$validProjectForm, 'summary_en' => ['nested']],
    [...$validProjectForm, 'summary_en' => str_repeat('x', 8001)],
] as $invalid) {
    try {
        EditorialInput::project($invalid);
        verify(false, 'Invalid editorial payload blocked');
    } catch (InvalidArgumentException) {
        verify(true, 'Invalid editorial payload blocked');
    }
}
try {
    EditorialInput::journal(['category'=>'<script>','title_cs'=>'A','title_en'=>'B','body_cs'=>'C','body_en'=>'D']);
    verify(false, 'Invalid journal category blocked');
} catch (InvalidArgumentException) {
    verify(true, 'Invalid journal category blocked');
}

$catalog = new ProjectCatalog($root . '/data');
$projects = $catalog->projects();
$nodes = $catalog->topology();
verify(count($projects) >= 13, 'Project catalogue populated');
verify(count($nodes) >= 7, 'Safe topology populated');
$flows = $catalog->flows();
verify(count($flows) >= 4, 'Curated data paths are present');
foreach ($flows as $flow) {
    verify(count($flow->path) === count($flow->stepsCs) && count($flow->path) === count($flow->stepsEn),
        'Every hop has bilingual explanation');
    verify(App\Domain\NetworkFlow::fromArray($flow->toArray(), $nodes)->id === $flow->id,
        'Validated data path is serializable without data loss');
}
$badFlow = $flows[0]->toArray();
$badFlow['path'] = ['clients', 'nas'];
$badFlow['steps_cs'] = ['A', 'B'];
$badFlow['steps_en'] = ['A', 'B'];
try {
    App\Domain\NetworkFlow::fromArray($badFlow, $nodes);
    verify(false, 'Disconnected path fails closed');
} catch (InvalidArgumentException) {
    verify(true, 'Disconnected path fails closed');
}
$badFlow = $flows[0]->toArray();
$badFlow['steps_en'] = ['missing'];
try {
    PublicContentValidator::validate('flows.json', [$badFlow]);
    verify(false, 'Untranslated flow fails closed');
} catch (InvalidArgumentException) {
    verify(true, 'Untranslated flow fails closed');
}

$privateProject = $catalog->find('woodbound');
$publicProject = $catalog->find('local-ai-hub');
verify($privateProject !== null && (new PublicProjectView($privateProject))->toArray(new Language('en'))['repository'] === null,
    'Read-only API redacts private repository sources');
verify($publicProject !== null && (new PublicProjectView($publicProject))->toArray(new Language('en'))['repository'] === 'https://github.com/Harfusha/local-ai-hub',
    'Read-only API exposes approved public repository source');
verify($publicProject !== null && (new PublicProjectView($publicProject))->toArray(new Language('cs'))['summary'] === $publicProject->summaryCs,
    'Read-only API produces requested language');

$links = $catalog->connections();
verify(count($links) >= 10, 'Curated inter-project relationships');
verify(count(array_unique(array_map(static fn (array $edge): string => $edge['from'].'/'.$edge['to'], $links))) === count($links), 'No duplicate connection edges');
foreach ($links as $link) {
    verify($catalog->find($link['from']) !== null && $catalog->find($link['to']) !== null, 'Relationship resolves to real projects');
}
try {
    new ProjectConnections([['from'=>'not-exists','to'=>'mutaui','type'=>'assists-development']],$projects);
    verify(false,'Unknown project connection rejected');
} catch (InvalidArgumentException) {
    verify(true, 'Unknown project connection rejected');
}
try {
    new ProjectConnections([['from'=>'mutaui','to'=>'mutaui','type'=>'self']],$projects);
    verify(false,'Self relation rejected');
} catch (InvalidArgumentException) {
    verify(true, 'Self relation rejected');
}
try {
    new TopologyGraph([['id'=>'a','depends'=>['b']],['id'=>'b','depends'=>['a']]]);
    verify(false, 'Cycle detected');
} catch (InvalidArgumentException) {
    verify(true, 'Cycle detected');
}
$graph = new TopologyGraph($nodes);
verify(count($graph->affectedBy('router')) === 6, 'Router failure impacts all dependent nodes');
verify($graph->affectedBy('nas') === ['nas'], 'NAS failure isolated in model');
verify(count($catalog->contributions()) >= 10, 'Upstream contribution snapshot');
verify((new Language('en'))->value('ano', 'yes') === 'yes', 'Language selection');
verify(Html::safeUrl('javascript:alert(1)') === '#', 'Unsafe URL blocked');
verify(Html::safeUrl('https://github.com.evil.example/pull/1') === '#', 'Lookalike GitHub host rejected');
verify(Html::escape('<script>alert(1)</script>') === '&lt;script&gt;alert(1)&lt;/script&gt;', 'HTML escaping');
verify(Html::path('cs','/connections') === '/cs/connections', 'Connections route locale');
$sitemap = PublicSitemap::render('https://portfolio.invalid', ['', 'projects', 'projects/local-ai-hub']);
verify(substr_count($sitemap, '<url>') === 6, 'Localized sitemap contains both languages');
verify(str_contains($sitemap, 'hreflang="x-default"') && str_contains($sitemap, 'hreflang="en"'),
    'Localized sitemap contains alternate language discovery');
try {
    PublicSitemap::render('https://portfolio.invalid', ['../secrets']);
    verify(false, 'Sitemap rejects traversal');
} catch (InvalidArgumentException) {
    verify(true, 'Sitemap rejects traversal');
}

verify(str_contains(Html::json(['danger'=>'</script>']), '\\u003C'), 'Inline JSON escaped');
foreach ($projects as $project) {
    verify($project->summaryCs !== '' && $project->summaryEn !== '', 'Bilingual content for ' . $project->slug);
    verify(!$project->private || $project->repo === '', 'No private repository URL in ' . $project->slug);
}
$ids = array_map(static fn (array $n): mixed => $n['id'] ?? null, $nodes);
verify(count($ids) === count(array_unique($ids)), 'Unique topology ids');
foreach ($nodes as $node) {
    verify(is_array($node['depends'] ?? null) && array_diff($node['depends'], $ids) === [], 'Topology references are valid');
}
$event = GithubRecord::fromGithub(['html_url'=>'https://github.com/cakephp/cakephp/pull/18957','title'=>'FQCN','created_at'=>'2025-10-10T00:00:00Z'], 'pr', 'merged');
verify($event->repository === 'cakephp/cakephp' && $event->state === 'merged', 'GitHub typed normalization');
try {
    GithubRecord::fromGithub(['html_url'=>'https://evil.test/x/y/pull/1','created_at'=>'2025-10-10'], 'pr','open');
    verify(false, 'GitHub URL validation');
} catch (InvalidArgumentException) {
    verify(true, 'GitHub URL validation');
}
$prFixture = ['html_url'=>'https://github.com/cakephp/cakephp/pull/18957','title'=>'TableLocator','state'=>'closed','created_at'=>'2025-10-10T00:00:00Z','user'=>['login'=>'Harfusha']];
$issueFixture = ['html_url'=>'https://github.com/phpstan/phpstan/issues/13291','title'=>'Regression','state'=>'closed','created_at'=>'2025-07-22T00:00:00Z','user'=>['login'=>'Harfusha']];
$records = GithubSnapshot::externalActivity([$prFixture], [$prFixture], [$issueFixture], 'Harfusha');
verify(count($records) === 2 && $records[0]->state === 'merged' && $records[1]->kind === 'issue', 'GitHub snapshot classification');
foreach ([
    [[array_replace($prFixture, ['user'=>['login'=>'Other']])], [], []],
    [[], [$prFixture], []],
    [[array_replace($prFixture, ['html_url'=>'https://github.com.evil/a/b/pull/1'])], [], []],
] as $badFixture) {
    try {
        GithubSnapshot::externalActivity($badFixture[0], $badFixture[1], $badFixture[2], 'Harfusha');
        verify(false, 'GitHub snapshot rejects inconsistent data');
    } catch (InvalidArgumentException) {
        verify(true, 'GitHub snapshot rejects inconsistent data');
    }
}

DeploymentPolicy::assertEnvironment('development', true, 'http://localhost:8765', '');
verify(true,'Explicit development mode permits local HTTP');
foreach ([
    ['staging',false,'https://example.org',str_repeat('a',64)],
    ['production',true,'https://example.org',str_repeat('a',64)],
    ['production',false,'http://example.org',str_repeat('a',64)],
    ['production',false,'https://example.org/private',str_repeat('a',64)],
] as [$mode,$debug,$origin,$salt]) {
    try {
        DeploymentPolicy::assertEnvironment($mode,$debug,$origin,$salt);
        verify(false,'Unsafe deployment environment rejected');
    } catch (InvalidArgumentException) {
        verify(true,'Unsafe deployment environment rejected');
    }
}

$repoFixture = ['full_name'=>'Harfusha/local-ai-hub','html_url'=>'https://github.com/Harfusha/local-ai-hub',
    'fork'=>false,'private'=>false,'pushed_at'=>'2026-11-12T11:45:05Z','description'=>'Typed developer tools'];
$normalized = PublicRepository::fromGithub([$repoFixture], 'Harfusha');
verify(count($normalized) === 1 && $normalized[0]->name === 'Harfusha/local-ai-hub', 'Public GitHub repository normalization');
foreach ([
    [array_replace($repoFixture,['private'=>true])],
    [array_replace($repoFixture,['full_name'=>'Attacker/local-ai-hub','html_url'=>'https://github.com/Attacker/local-ai-hub'])],
    [$repoFixture,$repoFixture],
    [],
] as $invalidRepos) {
    try {
        PublicRepository::fromGithub($invalidRepos, 'Harfusha');
        verify(false,'Reject unsafe/incomplete repository snapshot');
    } catch (InvalidArgumentException) {
        verify(true,'Reject unsafe/incomplete repository snapshot');
    }
}
verify(EditorialAuditTrail::digest(['a'=>1,'b'=>2]) === EditorialAuditTrail::digest(['b'=>2,'a'=>1]), 'Deterministic audit digests');
verify(count($catalog->decisions()) >= 7, 'Curated engineering tradeoffs present');
foreach ($catalog->decisions() as $decision) {
    verify($catalog->find($decision->project) !== null, 'Design decision references real project');
}
try {
    PublicContentValidator::validate('decisions.json', [['id'=>'bad','project'=>'unknown','status'=>'documented']]);
    verify(false,'Incomplete decision fails closed');
} catch (InvalidArgumentException) {
    verify(true,'Incomplete decision fails closed');
}

$feed = JournalFeed::render('https://example.org', new Language('cs'), $catalog->journal());
verify(str_contains($feed, '<rss version="2.0">') && str_contains($feed, '<pubDate>') && str_contains($feed, '</channel></rss>'), 'Valid public RSS structure');
try {
    JournalFeed::render('javascript:alert(1)', new Language('en'), $catalog->journal());
    verify(false, 'Unsafe RSS origin blocked');
} catch (InvalidArgumentException) {
    verify(true, 'Unsafe RSS origin blocked');
}
$renderer = new StandaloneRenderer();
$fixtures = [
    'home' => ['projects' => array_slice($projects,0,4), 'recentRepos' => [['repository'=>'Harfusha/MutaUI','description'=>'snapshot','repo_url'=>'https://github.com/Harfusha/MutaUI','pushed_at'=>'2026-11-12 18:53:55']], 'journal' => $catalog->journal()],
    'projects' => ['projects' => $projects],
    'connections' => ['projects' => $projects, 'edges' => $links],
    'decisions' => ['decisions' => $catalog->decisions()],
    'project' => ['project' => $projects[0], 'related' => [], 'decisions' => array_values(array_filter($catalog->decisions(), static fn ($item): bool => $item->project === $projects[0]->slug))],
    'homelab' => ['nodes' => $nodes, 'flows' => $catalog->flows()],
    'maker' => ['items' => $catalog->maker()],
    'playground' => [],
    'open-source' => ['activity' => $catalog->contributions()],
    'journal' => ['journal' => $catalog->journal()],
    'now' => ['recentRepos'=>[['repository'=>'Harfusha/MutaUI','description'=>'Snapshot','repo_url'=>'https://github.com/Harfusha/MutaUI','pushed_at'=>'2026-11-12 18:53:55']], 'journal'=>$catalog->journal(), 'provenance'=>['source'=>'reviewed-snapshot','last_sync'=>null]],
    'about' => [],
];
foreach (['cs','en'] as $code) {
    $lang = new Language($code);
    foreach ($fixtures as $name => $variables) {
        $html = $renderer->render($root . '/templates/Pages/' . $name . '.php', ['lang'=>$lang, 'route'=>'/' . $name, ...$variables]);
        verify(strlen($html) > 100 && str_contains($html, 'class='), "SSR template {$code}/{$name}");
    }
}
$labRaw = file_get_contents($root . '/data/topology.json');
verify(is_string($labRaw) && !str_contains($labRaw, '192.168.') && !str_contains($labRaw, '10.0.0.'), 'No internal IPs in published model');
$homelabText = $renderer->render($root . '/templates/Pages/homelab.php', [
    'lang' => new Language('en'), 'nodes' => $nodes, 'flows' => $flows,
]);
verify(str_contains($homelabText, '<noscript>') && str_contains($homelabText, 'Video playback'),
    'Network paths have no-JavaScript text fallback');

$connectionsRaw = file_get_contents($root.'/data/connections.json');
verify(is_string($connectionsRaw) && !str_contains($connectionsRaw,'192.168.'), 'No internal IPs in connection model');
$jsSource = file_get_contents($root.'/webroot/assets/js/app.js');
verify(is_string($jsSource) && str_contains($jsSource, 'initConnections()'), 'Project graph frontend initialized');
$middlewareSource = file_get_contents($root.'/src/Middleware/SecurityHeadersMiddleware.php');
verify(is_string($middlewareSource) && str_contains($middlewareSource, "frame-ancestors 'none'"), 'Frame protection configured');
$projectGraph = $renderer->render($root.'/templates/Pages/connections.php', ['lang'=>new Language('cs'),'projects'=>$projects,'edges'=>$links]);
verify(str_contains($projectGraph, 'id="connections-data"') && str_contains($projectGraph, 'data-connections-svg'), 'Interactive graph SSR hooks');
// GitHub API pages must never partially overwrite a previously valid snapshot.
$searchPage = static function (array $items, int $total): array {
    return ['items' => $items, 'total_count' => $total, 'incomplete_results' => false];
};
$searchItems = array_map(static fn (int $id): array => [
    'html_url' => 'https://github.com/cakephp/cakephp/pull/' . $id,
], range(1, 100));
$pages = new GithubSearchPageCollector();
verify($pages->append($searchPage($searchItems, 101), 1, 3), 'GitHub Search first full page requests next page');
verify(!$pages->append($searchPage([['html_url' => 'https://github.com/cakephp/cakephp/pull/101']], 101), 2, 3),
    'GitHub Search final page closes snapshot');
verify(count($pages->complete()) === 101, 'GitHub Search collects complete 101-item snapshot');
$pages = new GithubSearchPageCollector();
verify(!$pages->append($searchPage([], 0), 1, 3) && $pages->complete() === [],
    'GitHub Search distinguishes valid empty result');
$invalidSearch = [
    [$searchPage([['html_url' => 'https://github.com/a/b/pull/1']], 2), 1, 3],
    [array_replace($searchPage([], 0), ['incomplete_results' => true]), 1, 3],
    [$searchPage([], 1001), 1, 3],
    [$searchPage($searchItems, 101), 1, 1],
];
foreach ($invalidSearch as [$payload, $page, $maxPages]) {
    try {
        (new GithubSearchPageCollector())->append($payload, $page, $maxPages);
        verify(false, 'GitHub Search rejects malformed/incomplete page');
    } catch (InvalidArgumentException) {
        verify(true, 'GitHub Search rejects malformed/incomplete page');
    }
}
$pages = new GithubSearchPageCollector();
$pages->append($searchPage($searchItems, 101), 1, 3);
try {
    $pages->append($searchPage([['html_url' => 'https://github.com/cakephp/cakephp/pull/20']], 101), 2, 3);
    verify(false, 'GitHub Search duplicate across pages rejected');
} catch (InvalidArgumentException) {
    verify(true, 'GitHub Search duplicate across pages rejected');
}
$repoItems = array_map(static fn (int $id): array => ['full_name' => 'Harfusha/repo' . $id], range(1, 100));
$repoPages = new GithubRepositoryPageCollector();
verify($repoPages->append($repoItems, 1, 5), 'Repo pagination continues after full page');
verify(!$repoPages->append([['full_name' => 'Harfusha/repo101']], 2, 5), 'Repo pagination detects short final page');
verify(count($repoPages->complete()) === 101, 'Repo listing collected without pagination gap');
$repoPages = new GithubRepositoryPageCollector();
$repoPages->append($repoItems, 1, 5);
try {
    $repoPages->append([['full_name' => 'Harfusha/repo50']], 2, 5);
    verify(false, 'Repeated repository page detected');
} catch (InvalidArgumentException) {
    verify(true, 'Repeated repository page detected');
}
try {
    (new GithubRepositoryPageCollector())->complete();
    verify(false, 'Unfinished public repository listing rejected');
} catch (InvalidArgumentException) {
    verify(true, 'Unfinished public repository listing rejected');
}
restore_error_handler();
printf("Standalone checks: %d PASS, %d FAIL\n", $passed, $failed);
exit($failed === 0 ? 0 : 1);
