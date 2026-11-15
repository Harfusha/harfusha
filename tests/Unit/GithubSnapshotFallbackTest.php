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
