<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Service\GithubSyncService;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Http\TestSuite\HttpClientTrait;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;
use RuntimeException;

/**
 * The actual importer, HTTP client mocks and disposable MySQL transaction.
 * No real GitHub requests or permanent changes to the test database.
 */
final class GithubSyncIntegrationTest extends TestCase
{
    use HttpClientTrait;

    private static function db(): Connection
    {
        $db = ConnectionManager::get('default');
        if (!$db instanceof Connection) {
            throw new RuntimeException('Requires disposable migrated MySQL');
        }

        return $db;
    }

    /** @return array<string,mixed> */
    private static function event(string $url): array
    {
        $event = [
            'html_url' => $url,
            'title' => 'Reviewed remote contribution',
            'state' => 'closed',
            'created_at' => '2026-11-12T12:00:00Z',
            'closed_at' => '2026-11-12T13:00:00Z',
            'user' => ['login' => 'Harfusha'],
        ];
        if (str_contains($url, '/pull/')) {
            $event['pull_request'] = ['merged_at' => '2026-11-12T12:30:00Z'];
        }

        return $event;
    }

    /** @return array<string,mixed> */
    private static function repo(): array
    {
        return [
            'full_name' => 'Harfusha/local-ai-hub',
            'html_url' => 'https://github.com/Harfusha/local-ai-hub',
            'private' => false,
            'fork' => false,
            'pushed_at' => '2026-11-12T12:00:00Z',
            'description' => 'Public project',
        ];
    }

    /**
     * @param list<array<string,mixed>> $items
     */
    private function mockSearch(string $query, array $items): void
    {
        $url = 'https://api.github.com/search/issues?q=' . rawurlencode($query)
            . '&per_page=100&page=1&sort=created&order=desc';
        $payload = ['total_count' => count($items), 'incomplete_results' => false, 'items' => $items];
        $this->mockClientGet(
            $url,
            $this->newClientResponse(200, ['Content-Type: application/json'], json_encode($payload, JSON_THROW_ON_ERROR)),
        );
    }

    /** @param list<array<string,mixed>> $repos */
    private function mockRepositories(array $repos): void
    {
        $this->mockClientGet(
            'https://api.github.com/users/Harfusha/repos?type=owner&per_page=100&sort=pushed&page=1',
            $this->newClientResponse(200, ['Content-Type: application/json'], json_encode($repos, JSON_THROW_ON_ERROR)),
        );
    }

    /** @param list<array<string,mixed>> $repos */
    private function mockValidSearches(array $repos): void
    {
        $external = self::event('https://github.com/cakephp/cakephp/pull/99');
        $owned = self::event('https://github.com/Harfusha/local-ai-hub/pull/1');
        $issue = self::event('https://github.com/phpstan/phpstan/issues/42');
        $this->mockSearch('is:pr author:Harfusha', [$external, $owned]);
        $this->mockSearch('is:pr is:merged author:Harfusha', [$external, $owned]);
        $this->mockSearch('is:issue author:Harfusha', [$issue]);
        $this->mockRepositories($repos);
    }

    public function testCompleteMockedPublicImportAtomicallyReplacesBothCaches(): void
    {
        $db = self::db();
        $db->begin();
        try {
            $db->execute('DELETE FROM github_activity');
            $db->execute('DELETE FROM github_public_repos');
            $db->execute('DELETE FROM sync_runs WHERE source = :source', ['source' => 'github']);
            $db->execute("INSERT INTO github_activity
                (source_url,title,repository,kind,state,published_at)
                VALUES ('https://github.com/cakephp/cakephp/pull/1',
                    'Stale activity', 'cakephp/cakephp', 'pr', 'closed', UTC_TIMESTAMP())");
            $db->execute("INSERT INTO github_public_repos
                (repository,description,repo_url,pushed_at)
                VALUES ('Harfusha/stale', 'Stale repo',
                    'https://github.com/Harfusha/stale', UTC_TIMESTAMP())");

            $this->mockValidSearches([self::repo()]);
            self::assertSame(
                ['imported' => 2, 'merged' => 1, 'issues' => 1, 'repositories' => 1],
                (new GithubSyncService())->synchronize(),
            );

            $activity = $db->execute(
                'SELECT source_url, merged_at, closed_at FROM github_activity ORDER BY source_url',
            )->fetchAll('assoc');
            self::assertSame([
                [
                    'source_url' => 'https://github.com/cakephp/cakephp/pull/99',
                    'merged_at' => '2026-11-12 12:30:00', 'closed_at' => '2026-11-12 13:00:00',
                ],
                [
                    'source_url' => 'https://github.com/phpstan/phpstan/issues/42',
                    'merged_at' => null, 'closed_at' => '2026-11-12 13:00:00',
                ],
            ], $activity);
            $repos = $db->execute('SELECT repository FROM github_public_repos')->fetchAll('assoc');
            self::assertSame([['repository' => 'Harfusha/local-ai-hub']], $repos);
            $runs = $db->execute(
                'SELECT item_count FROM sync_runs WHERE source = :source ORDER BY id DESC LIMIT 1',
                ['source' => 'github'],
            )->fetchAll('assoc');
            self::assertCount(1, $runs);
            $run = $runs[0] ?? null;
            if (!is_array($run)) {
                throw new RuntimeException('Expected a single completed sync receipt');
            }
            $count = $run['item_count'] ?? null;
            if (!is_int($count) && !(is_string($count) && ctype_digit($count))) {
                throw new RuntimeException('Expected an integer sync receipt count');
            }
            self::assertSame(2, (int)$count);
        } finally {
            $db->rollback();
        }
    }

    public function testPrivateRepoResponsePreservesLastGoodCache(): void
    {
        $db = self::db();
        $db->begin();
        try {
            $db->execute('DELETE FROM github_activity');
            $db->execute("INSERT INTO github_activity
                (source_url,title,repository,kind,state,published_at)
                VALUES ('https://github.com/cakephp/cakephp/pull/1',
                    'Last known good', 'cakephp/cakephp', 'pr', 'closed', UTC_TIMESTAMP())");
            $private = self::repo();
            $private['private'] = true;
            $this->mockValidSearches([$private]);

            try {
                (new GithubSyncService())->synchronize();
                self::fail('Unexpectedly accepted a private upstream repository');
            } catch (InvalidArgumentException $error) {
                self::assertStringContainsString('Private repository', $error->getMessage());
            }
            self::assertSame(
                [['title' => 'Last known good']],
                $db->execute('SELECT title FROM github_activity')->fetchAll('assoc'),
            );
        } finally {
            $db->rollback();
        }
    }

    public function testOversizedResponseFailsBeforeJsonDecoding(): void
    {
        $url = 'https://api.github.com/search/issues?q=' . rawurlencode('is:pr author:Harfusha')
            . '&per_page=100&page=1&sort=created&order=desc';
        // An otherwise valid JSON document with excessive trailing whitespace.
        $this->mockClientGet(
            $url,
            $this->newClientResponse(200, ['Content-Type: application/json'], '[]' . str_repeat(' ', 8 * 1024 * 1024)),
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('safe JSON size limit');
        (new GithubSyncService())->synchronize();
    }
}
