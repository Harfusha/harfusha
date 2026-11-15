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
        $connection->execute('CREATE TEMPORARY TABLE sync_runs (
            id INT AUTO_INCREMENT PRIMARY KEY, source VARCHAR(40),
            item_count INT UNSIGNED, completed_at DATETIME
        )');
        $connection->execute("INSERT INTO sync_runs (source,item_count,completed_at)
            VALUES ('github',0,UTC_TIMESTAMP())");
        try {
            $content = new ContentRepository();
            self::assertSame([], $content->activity());
            self::assertSame([], $content->publicRepoUpdates());
        } finally {
            $connection->execute('DROP TEMPORARY TABLE IF EXISTS sync_runs');
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

    public function testUncommittedGithubCacheRowsAreNotPublished(): void
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
            $connection->execute("INSERT INTO github_activity
                (source_url,title,repository,kind,state,published_at)
                VALUES ('https://github.com/cakephp/cakephp/pull/111',
                    'Not an approved import', 'cakephp/cakephp', 'pr', 'merged', UTC_TIMESTAMP())");
            $connection->execute("INSERT INTO github_public_repos
                (repository,description,repo_url,pushed_at)
                VALUES ('Harfusha/test-unapproved', 'Unapproved repository cache',
                    'https://github.com/Harfusha/test-unapproved', UTC_TIMESTAMP())");

            $unverified = new ContentRepository();
            self::assertSame('reviewed-snapshot', $unverified->githubProvenance()['source']);
            self::assertNotContains('Not an approved import', array_column($unverified->activity(), 'title'));
            self::assertNotContains(
                'Harfusha/test-unapproved',
                array_column($unverified->publicRepoUpdates(), 'repository'),
            );

            // A completed import receipt makes these very same rows eligible
            // for display. No receipt means a failed or unaudited partial write.
            $connection->execute(
                'INSERT INTO sync_runs (source,item_count,completed_at)
                    VALUES (:source,:count,UTC_TIMESTAMP())',
                ['source' => 'github', 'count' => 1],
            );
            $verified = new ContentRepository();
            self::assertSame('github-public-api', $verified->githubProvenance()['source']);
            self::assertSame('Not an approved import', $verified->activity()[0]['title']);
            self::assertSame('Harfusha/test-unapproved', $verified->publicRepoUpdates()[0]['repository']);
        } finally {
            $connection->rollback();
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
