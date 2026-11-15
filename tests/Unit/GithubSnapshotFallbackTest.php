<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Service\ContentRepository;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use RuntimeException;

/**
 * Real MySQL regression: a successful empty import must not resurrect the
 * historical public snapshot. Changes are transactionally rolled back.
 */
final class GithubSnapshotFallbackTest extends TestCase
{
    public function testFailedGithubCacheReadsNeverResurrectHistoricActivity(): void
    {
        $connection = ConnectionManager::get('default');
        if (!$connection instanceof Connection) {
            throw new RuntimeException('Test requires the disposable MySQL database');
        }
        // Temporary tables shadow the real cache without modifying production
        // data or relying on an actual database outage during CI.
        $connection->execute('CREATE TEMPORARY TABLE github_activity (id BIGINT NOT NULL)');
        $connection->execute('CREATE TEMPORARY TABLE github_public_repos (id BIGINT NOT NULL)');
        try {
            $content = new ContentRepository();
            self::assertSame([], $content->activity());
            self::assertSame([], $content->publicRepoUpdates());
        } finally {
            $connection->execute('DROP TEMPORARY TABLE IF EXISTS github_activity');
            $connection->execute('DROP TEMPORARY TABLE IF EXISTS github_public_repos');
        }
    }

    public function testUnreadableSyncReceiptNeverResurrectsStaticSelection(): void
    {
        $connection = ConnectionManager::get('default');
        if (!$connection instanceof Connection) {
            throw new RuntimeException('Test requires the disposable MySQL database');
        }
        // Both cache tables are readable but empty; the receipt is corrupted.
        // An unknown import state must not be mistaken for "not yet imported".
        $connection->execute('CREATE TEMPORARY TABLE github_activity (
            source_url VARCHAR(255), title VARCHAR(250), repository VARCHAR(180),
            kind VARCHAR(10), state VARCHAR(10), published_at DATETIME
        )');
        $connection->execute('CREATE TEMPORARY TABLE github_public_repos (
            repository VARCHAR(180), description VARCHAR(350),
            repo_url VARCHAR(255), pushed_at DATETIME
        )');
        $connection->execute('CREATE TEMPORARY TABLE sync_runs (id BIGINT NOT NULL)');
        try {
            $content = new ContentRepository();
            self::assertSame('unavailable', $content->githubProvenance()['source']);
            self::assertSame([], $content->activity());
            self::assertSame([], $content->publicRepoUpdates());
        } finally {
            $connection->execute('DROP TEMPORARY TABLE IF EXISTS sync_runs');
            $connection->execute('DROP TEMPORARY TABLE IF EXISTS github_activity');
            $connection->execute('DROP TEMPORARY TABLE IF EXISTS github_public_repos');
        }
    }

    public function testCompletedEmptyGithubSyncOverridesStaticFallback(): void
    {
        $connection = ConnectionManager::get('default');
        if (!$connection instanceof Connection) {
            throw new RuntimeException('Test requires the disposable MySQL database');
        }
        $connection->begin();
        try {
            $connection->execute('DELETE FROM github_activity');
            $connection->execute('DELETE FROM github_public_repos');
            $connection->execute('DELETE FROM sync_runs WHERE source = :source', ['source' => 'github']);

            $uninitialized = new ContentRepository();
            self::assertNotEmpty($uninitialized->activity());
            self::assertNotEmpty($uninitialized->publicRepoUpdates());
            self::assertSame('reviewed-snapshot', $uninitialized->githubProvenance()['source']);

            $connection->execute(
                'INSERT INTO sync_runs (source, item_count, completed_at)
                    VALUES (:source, :count, UTC_TIMESTAMP())',
                ['source' => 'github', 'count' => 0],
            );

            $synchronized = new ContentRepository();
            self::assertSame('github-public-api', $synchronized->githubProvenance()['source']);
            self::assertSame([], $synchronized->activity());
            self::assertSame([], $synchronized->publicRepoUpdates());
        } finally {
            $connection->rollback();
        }
    }
}
