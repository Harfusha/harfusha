<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\GithubRepositoryPageCollector;
use App\Domain\GithubSearchPageCollector;
use App\Domain\GithubSnapshot;
use App\Domain\PublicRepository;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Client;
use RuntimeException;

/**
 * Read-only remote importer. The only network host is GitHub's public API.
 * The server never reads private repositories and the browser never calls GitHub.
 */
final class GithubSyncService
{
    private readonly Client $http;
    private readonly string $author;

    public function __construct(?Client $http = null)
    {
        $this->http = $http ?? new Client();
        $configuredAuthor = getenv('GITHUB_LOGIN');
        $author = is_string($configuredAuthor) && $configuredAuthor !== '' ? $configuredAuthor : 'Harfusha';
        if (preg_match('/^[A-Za-z0-9-]{1,39}$/D', $author) !== 1) {
            throw new RuntimeException('Invalid GitHub username');
        }
        $this->author = $author;
    }

    /** @return array{imported:int,merged:int,issues:int,repositories:int} */
    public function synchronize(): array
    {
        if (!(new ContentRepository())->hasDatabase()) {
            throw new RuntimeException('Database is not configured');
        }
        // A second HTTP/CLI cron must not interleave snapshots and transactions.
        $lockPath = TMP . 'github-sync.lock';
        $lock = fopen($lockPath, 'c+');
        if ($lock === false || !flock($lock, LOCK_EX | LOCK_NB)) {
            if (is_resource($lock)) {
                fclose($lock);
            }
            throw new RuntimeException('GitHub import already running or lock unavailable');
        }
        try {
            return $this->performSynchronization();
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /** @return array{imported:int,merged:int,issues:int,repositories:int} */
    private function performSynchronization(): array
    {
        $prs = $this->search('is:pr author:' . $this->author, 3);
        $merged = $this->search('is:pr is:merged author:' . $this->author, 3);
        $issues = $this->search('is:issue author:' . $this->author, 2);
        $records = GithubSnapshot::externalActivity($prs, $merged, $issues, $this->author);
        $repositoryPages = new GithubRepositoryPageCollector();
        for ($page = 1; $page <= 5; $page++) {
            $data = $this->get('https://api.github.com/users/' . rawurlencode($this->author) . '/repos?type=owner&per_page=100&sort=pushed&page=' . $page);
            if (!$repositoryPages->append($data, $page, 5)) {
                break;
            }
        }
        $repos = $repositoryPages->complete();
        // Validate the complete remote listing before starting a destructive refresh.
        $publicRepos = PublicRepository::fromGithub($repos, $this->author);
        $db = ConnectionManager::get('default');
        if (!$db instanceof Connection) {
            throw new RuntimeException('Unexpected database driver');
        }
        $repoCount = 0;
        // Atomic full replacement prevents stale records from surviving privacy/status changes.
        // Never start the transaction until *all* remote searches have completed.
        $db->transactional(function (Connection $connection) use ($records, $publicRepos, &$repoCount): void {
            $connection->execute('DELETE FROM github_activity');
            $connection->execute('DELETE FROM github_public_repos');
            foreach ($records as $event) {
                $connection->execute('INSERT INTO github_activity (source_url,title,repository,kind,state,published_at)
                    VALUES (:url,:title,:repo,:kind,:state,:published)
                    ', [
                        'url' => $event->url, 'title' => $event->title, 'repo' => $event->repository,
                        'kind' => $event->kind, 'state' => $event->state, 'published' => $event->createdAt,
                    ]);
            }
            foreach ($publicRepos as $repo) {
                $connection->execute('INSERT INTO github_public_repos (repository,description,repo_url,pushed_at)
                    VALUES (:repo,:description,:url,:pushed)', [
                        'repo' => $repo->name, 'description' => $repo->description,
                        'url' => $repo->url, 'pushed' => $repo->pushedAt,
                    ]);
                ++$repoCount;
            }
            $connection->execute(
                'INSERT INTO sync_runs (source,item_count,completed_at) VALUES (:source,:count,UTC_TIMESTAMP())',
                ['source' => 'github', 'count' => count($records)],
            );
        });

        return ['imported' => count($records), 'merged' => count($merged), 'issues' => count($issues), 'repositories' => $repoCount];
    }

    /** @return list<array<string,mixed>> */
    private function search(string $query, int $maxPages): array
    {
        $pages = new GithubSearchPageCollector();
        for ($page = 1; $page <= $maxPages; $page++) {
            $data = $this->get('https://api.github.com/search/issues?q=' . rawurlencode($query) . '&per_page=100&page=' . $page . '&sort=created&order=desc');
            if (!$pages->append($data, $page, $maxPages)) {
                break;
            }
        }

        return $pages->complete();
    }

    /** @return list<array<string, mixed>>|array<string, mixed>*/
    private function get(string $url): array
    {
        if (!str_starts_with($url, 'https://api.github.com/')) {
            throw new RuntimeException('API origin rejected');
        }
        $headers = ['Accept' => 'application/vnd.github+json', 'User-Agent' => 'HarfushaLabPublicImporter',
            'X-GitHub-Api-Version' => '2022-11-28'];
        // Intentionally unauthenticated: an authorized search may include private
        // activity. A public-only API view is required for a public portfolio.
        $response = $this->http->get($url, [], ['headers' => $headers, 'timeout' => 15, 'redirect' => 0]);
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('GitHub request failed with HTTP ' . $response->getStatusCode());
        }
        $json = $response->getJson();
        if (!is_array($json)) {
            throw new RuntimeException('Invalid API response');
        }
        if (array_is_list($json)) {
            $records = [];
            foreach ($json as $record) {
                if (!is_array($record)) {
                    throw new RuntimeException('Invalid API list entry');
                }
                /** @var array<string, mixed> $record */
                $records[] = $record;
            }

            return $records;
        }
        $object = [];
        foreach ($json as $key => $value) {
            if (!is_string($key)) {
                throw new RuntimeException('Invalid API object key');
            }
            $object[$key] = $value;
        }

        return $object;
    }
}
