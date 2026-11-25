<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\EvidenceRegistry;
use App\Domain\GithubProvenance;
use App\Domain\GithubPublicSnapshot;
use App\Domain\JournalFeed;
use App\Domain\Project;
use App\Domain\ProjectCatalog;
use App\Model\Table\GithubActivityTable;
use App\Model\Table\GithubPublicReposTable;
use App\Model\Table\JournalEntriesTable;
use App\Model\Table\ProjectOverridesTable;
use App\Model\Table\SyncRunsTable;
use App\Support\Environment;
use Cake\Core\Configure;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use DateTimeInterface;
use Throwable;
use UnexpectedValueException;

/** ORM enrichment of a private, reviewed, static source of truth. */
final class ContentRepository
{
    private const string REVIEWED_SNAPSHOT_DATE = '2026-11-12';

    private readonly ProjectCatalog $catalog;
    private ?GithubPublicSnapshot $cachedSnapshot = null;

    /**
     * Initialize required services and explicitly typed dependencies.
     */
    public function __construct(?ProjectCatalog $catalog = null)
    {
        $this->catalog = $catalog ?? new ProjectCatalog(ROOT . '/data');
    }

    /** @return list<\App\Domain\Project> */
    public function projects(): array
    {
        $base = $this->catalog->projects();
        if (!$this->hasDatabase()) {
            return $base;
        }
        try {
            $overrides = [];
            $rows = TableRegistry::getTableLocator()->get(ProjectOverridesTable::class)->find()->all();
            foreach ($rows as $row) {
                $slug = $row->get('slug');
                if (is_string($slug)) {
                    $overrides[$slug] = $row;
                }
            }

            return array_map(static function (Project $project) use ($overrides): Project {
                $override = $overrides[$project->slug] ?? null;
                if ($override === null) {
                    return $project;
                }
                $summaryCs = $override->get('summary_cs');
                $summaryEn = $override->get('summary_en');
                $status = $override->get('status');
                $problemCs = $override->get('problem_cs');
                $problemEn = $override->get('problem_en');
                $solutionCs = $override->get('solution_cs');
                $solutionEn = $override->get('solution_en');
                if (!is_string($summaryCs) || !is_string($summaryEn) || !is_string($status)) {
                    return $project;
                }

                return new Project(
                    $project->slug,
                    $project->name,
                    $project->category,
                    $status,
                    $project->featured,
                    $project->private,
                    $project->repo,
                    $project->demo,
                    $summaryCs,
                    $summaryEn,
                    is_string($problemCs) && $problemCs !== '' ? $problemCs : $project->problemCs,
                    is_string($problemEn) && $problemEn !== '' ? $problemEn : $project->problemEn,
                    is_string($solutionCs) && $solutionCs !== '' ? $solutionCs : $project->solutionCs,
                    is_string($solutionEn) && $solutionEn !== '' ? $solutionEn : $project->solutionEn,
                    $project->stack,
                    $project->features,
                    $project->year,
                    $project->featuresCs,
                    $project->latestPublicRelease,
                    $project->latestPublicReleaseDate,
                    $project->releaseUrl,
                );
            }, $base);
        } catch (Throwable $error) {
            error_log('Project catalogue ORM fallback: ' . get_class($error));

            return $this->applyFallbackOverrideGuard($base);
        }
    }

    /**
     * Apply last-committed redacted editorial overrides when DB goes unavailable (N-DATA-04)
     * so an editorial downgrade/correction is not silently undone by a DB outage.
     *
     * @param list<\App\Domain\Project> $base
     * @return list<\App\Domain\Project>
     */
    private function applyFallbackOverrideGuard(array $base): array
    {
        $guardFile = TMP . 'project-overrides-guard.json';
        if (!is_file($guardFile) || is_link($guardFile)) {
            return $base;
        }
        $raw = file_get_contents($guardFile);
        if (!is_string($raw) || $raw === '') {
            return $base;
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return $base;
        }

        return array_map(static function (Project $project) use ($decoded): Project {
            $override = $decoded[$project->slug] ?? null;
            if (!is_array($override)) {
                return $project;
            }
            $summaryCs = $override['summary_cs'] ?? null;
            $summaryEn = $override['summary_en'] ?? null;
            $status = $override['status'] ?? null;
            if (!is_string($summaryCs) || !is_string($summaryEn) || !is_string($status)) {
                return $project;
            }
            $problemCs = $override['problem_cs'] ?? null;
            $problemEn = $override['problem_en'] ?? null;
            $solutionCs = $override['solution_cs'] ?? null;
            $solutionEn = $override['solution_en'] ?? null;

            return new Project(
                $project->slug,
                $project->name,
                $project->category,
                $status,
                $project->featured,
                $project->private,
                $project->repo,
                $project->demo,
                $summaryCs,
                $summaryEn,
                is_string($problemCs) && $problemCs !== '' ? $problemCs : $project->problemCs,
                is_string($problemEn) && $problemEn !== '' ? $problemEn : $project->problemEn,
                is_string($solutionCs) && $solutionCs !== '' ? $solutionCs : $project->solutionCs,
                is_string($solutionEn) && $solutionEn !== '' ? $solutionEn : $project->solutionEn,
                $project->stack,
                $project->features,
                $project->year,
                $project->featuresCs,
                $project->latestPublicRelease,
                $project->latestPublicReleaseDate,
                $project->releaseUrl,
            );
        }, $base);
    }

    /** @return list<array{slug:string,name:string,type:string}> */
    public function relatedProjects(string $slug): array
    {
        $titles = [];
        foreach ($this->catalog->projects() as $project) {
            $titles[$project->slug] = $project->name;
        }
        $results = [];
        foreach ($this->catalog->connections() as $edge) {
            if ($edge['from'] !== $slug && $edge['to'] !== $slug) {
                continue;
            }
            $relatedSlug = $edge['from'] === $slug ? $edge['to'] : $edge['from'];
            $results[] = [
                'slug' => $relatedSlug,
                'name' => $titles[$relatedSlug] ?? $relatedSlug,
                'type' => $edge['type'],
            ];
        }

        return $results;
    }

    /** @return list<array{from:string,to:string,type:string}> */
    public function connections(): array
    {
        return $this->catalog->connections();
    }

    /**
     * Find a published project by its canonical slug.
     */
    public function findProject(string $slug): ?Project
    {
        foreach ($this->projects() as $project) {
            if ($project->slug === $slug) {
                return $project;
            }
        }

        return null;
    }

    /** @return list<\App\Domain\EngineeringDecision> */
    public function decisions(): array
    {
        return $this->catalog->decisions();
    }

    /** @return list<\App\Domain\NetworkFlow> */
    public function flows(): array
    {
        return $this->catalog->flows();
    }

    /** @return list<array<string,mixed>> */
    public function maker(): array
    {
        return $this->catalog->maker();
    }

    /** @return list<array<string,mixed>> */
    public function topology(): array
    {
        return $this->catalog->topology();
    }

    /**
     * Typed registry of verified engineering evidence (N-EVD-01).
     */
    public function evidence(): EvidenceRegistry
    {
        return $this->catalog->evidence();
    }

    /**
     * Return published engineering claims for a specific project.
     *
     * @return list<\App\Domain\EngineeringClaim>
     */
    public function projectEvidence(string $slug): array
    {
        return $this->catalog->evidence()->forProject($slug);
    }

    /**
     * Merged curated + published database journal entries with strict validation
     * and deterministic UTC timestamp + stable-ID ordering (N-DATA-05, N-DATA-07).
     *
     * @return list<array<string,mixed>>
     */
    public function journal(): array
    {
        $rows = $this->catalog->journal();
        if ($this->hasDatabase()) {
            try {
                $entities = TableRegistry::getTableLocator()->get(JournalEntriesTable::class)
                    ->find()
                    ->where(['is_public' => true])
                    ->orderByDesc('published_at')
                    ->orderByDesc('id')
                    ->limit(2000)
                    ->all();
                // Keep DB records separate until every row has been validated.
                // A broken row must not publish a partially read journal.
                $publishedRows = [];
                foreach ($entities as $record) {
                    $published = $record->get('published_at');
                    $publishedAt = $published instanceof DateTimeInterface
                        ? $published->format('Y-m-d H:i:s')
                        : '';
                    $titleCs = self::requiredNonEmptyUtf8($record->get('title_cs'), 'title_cs');
                    $titleEn = self::requiredNonEmptyUtf8($record->get('title_en'), 'title_en');
                    $bodyCs = self::requiredNonEmptyUtf8($record->get('body_cs'), 'body_cs');
                    $bodyEn = self::requiredNonEmptyUtf8($record->get('body_en'), 'body_en');
                    $category = self::requiredNonEmptyUtf8($record->get('category'), 'category');
                    JournalFeed::parseUtcTimestamp($publishedAt);

                    $publishedRows[] = [
                        'id' => self::requiredId($record->get('id')),
                        'title_cs' => $titleCs,
                        'title_en' => $titleEn,
                        'body_cs' => $bodyCs,
                        'body_en' => $bodyEn,
                        'category' => $category,
                        'source_url' => self::requiredString($record->get('source_url'), 'source_url'),
                        'published_at' => $publishedAt,
                    ];
                }
                $rows = array_merge($rows, $publishedRows);
            } catch (Throwable $error) {
                error_log('Journal ORM fallback: ' . get_class($error));
            }
        }

        usort($rows, static function (array $a, array $b): int {
            $tsA = is_string($a['published_at'] ?? null)
                ? JournalFeed::parseUtcTimestamp($a['published_at'])
                : 0;
            $tsB = is_string($b['published_at'] ?? null)
                ? JournalFeed::parseUtcTimestamp($b['published_at'])
                : 0;
            if ($tsA !== $tsB) {
                return $tsB <=> $tsA;
            }

            return strcmp(JournalFeed::entryId($b), JournalFeed::entryId($a));
        });

        return $rows;
    }

    /**
     * Read GitHub provenance, activity, and recent repository updates from
     * a single consistent generation snapshot (N-DATA-03, N-SEC-08).
     */
    public function githubSnapshot(): GithubPublicSnapshot
    {
        if ($this->cachedSnapshot !== null) {
            return $this->cachedSnapshot;
        }

        if (!$this->hasDatabase()) {
            return $this->cachedSnapshot = new GithubPublicSnapshot(
                GithubProvenance::reviewedSnapshot(self::REVIEWED_SNAPSHOT_DATE),
                $this->catalog->contributions(),
                $this->staticRepoSnapshot(),
            );
        }

        $connection = ConnectionManager::get('default');
        $startedTransaction = false;
        if ($connection instanceof Connection && !$connection->inTransaction()) {
            try {
                $connection->execute('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
                $connection->begin();
                $startedTransaction = true;
            } catch (Throwable $error) {
                error_log('Snapshot transaction begin fallback: ' . get_class($error));
                $startedTransaction = false;
            }
        }

        try {
            $run = TableRegistry::getTableLocator()->get(SyncRunsTable::class)
                ->find()
                ->where(['source' => 'github'])
                ->orderByDesc('completed_at')
                ->first();

            if ($run === null) {
                return $this->cachedSnapshot = new GithubPublicSnapshot(
                    GithubProvenance::reviewedSnapshot(self::REVIEWED_SNAPSHOT_DATE),
                    $this->catalog->contributions(),
                    $this->staticRepoSnapshot(),
                );
            }

            $time = $run->get('completed_at');
            $lastSync = $time instanceof DateTimeInterface
                ? $time->format('Y-m-d H:i:s') . ' UTC'
                : null;
            $ageHours = null;
            $isStale = false;
            $isExpired = false;
            $maxTtlHours = $this->publicGithubTtlHours();
            if ($time instanceof DateTimeInterface) {
                $ageSeconds = time() - $time->getTimestamp();
                $ageHours = max(0, intdiv($ageSeconds, 3600));
                $isStale = $ageSeconds > GithubProvenance::STALE_AFTER_HOURS * 3600;
                $isExpired = $ageSeconds > $maxTtlHours * 3600;
            }
            $runId = $run->get('id');
            $syncRunId = is_int($runId) ? $runId : (is_string($runId) && ctype_digit($runId) ? (int)$runId : null);

            $provenance = new GithubProvenance(
                'github-public-api',
                $lastSync,
                $ageHours,
                $isStale,
                null,
                $isExpired,
                $syncRunId,
                null,
            );

            // Enforce public TTL on unrefreshed dynamic imports (N-SEC-08):
            // if a remote repo became private since an ancient import, stop serving stale dynamic records.
            if ($isExpired) {
                return $this->cachedSnapshot = new GithubPublicSnapshot($provenance, [], []);
            }

            $activity = $this->readDatabaseActivity();
            $repos = $this->readDatabaseRepos();

            return $this->cachedSnapshot = new GithubPublicSnapshot($provenance, $activity, $repos);
        } catch (Throwable $error) {
            error_log('GitHub public snapshot unavailable: ' . get_class($error));

            return $this->cachedSnapshot = new GithubPublicSnapshot(
                GithubProvenance::unavailable(get_class($error)),
                [],
                [],
            );
        } finally {
            if ($startedTransaction && $connection->inTransaction()) {
                try {
                    $connection->commit();
                } catch (Throwable) {
                    // Read-only consistent snapshot transaction closure.
                }
            }
        }
    }

    /** @return list<array<string,mixed>> */
    public function activity(): array
    {
        return $this->githubSnapshot()->activity;
    }

    /**
     * Typed provenance DTO for callers that prefer strongly typed objects.
     */
    public function githubProvenanceDto(): GithubProvenance
    {
        return $this->githubSnapshot()->provenance;
    }

    /**
     * Publication provenance, not an invented real-time server status.
     *
     * @return array{
     *     source:string,
     *     last_sync:?string,
     *     age_hours:?int,
     *     is_stale:bool,
     *     snapshot_date:?string,
     *     is_expired:bool,
     *     sync_run_id:?int,
     *     error_reason:?string
     * }
     */
    public function githubProvenance(): array
    {
        return $this->githubSnapshot()->provenance->toArray();
    }

    /** @return list<array{repository:string,description:string,repo_url:string,pushed_at:string}> */
    public function publicRepoUpdates(): array
    {
        return $this->githubSnapshot()->recentRepos;
    }

    /** @return list<array<string,mixed>> */
    private function readDatabaseActivity(): array
    {
        $entities = TableRegistry::getTableLocator()->get(GithubActivityTable::class)
            ->find()->orderByDesc('published_at')->limit(500)->all();
        $rows = [];
        foreach ($entities as $record) {
            $created = $record->get('published_at');
            $rows[] = [
                'source_url' => $record->get('source_url'),
                'title' => $record->get('title'),
                'repository' => $record->get('repository'),
                'kind' => $record->get('kind'),
                'state' => $record->get('state'),
                'published_at' => $created instanceof DateTimeInterface ? $created->format('Y-m-d H:i:s') : '',
                'merged_at' => $record->get('merged_at') instanceof DateTimeInterface
                    ? $record->get('merged_at')->format('Y-m-d H:i:s') : null,
                'closed_at' => $record->get('closed_at') instanceof DateTimeInterface
                    ? $record->get('closed_at')->format('Y-m-d H:i:s') : null,
            ];
        }

        return $rows;
    }

    /** @return list<array{repository:string,description:string,repo_url:string,pushed_at:string}> */
    private function readDatabaseRepos(): array
    {
        $entities = TableRegistry::getTableLocator()->get(GithubPublicReposTable::class)
            ->find()->orderByDesc('pushed_at')->limit(9)->all();
        $rows = [];
        foreach ($entities as $record) {
            $date = $record->get('pushed_at');
            $rows[] = [
                'repository' => self::requiredString($record->get('repository'), 'repository'),
                'description' => self::requiredString($record->get('description'), 'description'),
                'repo_url' => self::requiredString($record->get('repo_url'), 'repo_url'),
                'pushed_at' => $date instanceof DateTimeInterface ? $date->format('Y-m-d H:i:s') : '',
            ];
        }

        return $rows;
    }

    /** @return list<array{repository:string,description:string,repo_url:string,pushed_at:string}> */
    private function staticRepoSnapshot(): array
    {
        return [
            [
                'repository' => 'Harfusha/MutaUI',
                'description' => 'Reviewed static snapshot (2026-11-12)',
                'repo_url' => 'https://github.com/Harfusha/MutaUI',
                'pushed_at' => '2026-11-12 18:53:55',
            ],
            [
                'repository' => 'Harfusha/local-ai-hub',
                'description' => 'Reviewed static snapshot (2026-11-12)',
                'repo_url' => 'https://github.com/Harfusha/local-ai-hub',
                'pushed_at' => '2026-11-12 11:45:05',
            ],
        ];
    }

    /**
     * Resolve maximum allowed public TTL in hours for unrefreshed dynamic GitHub imports.
     */
    private function publicGithubTtlHours(): int
    {
        $raw = Environment::get('PUBLIC_GITHUB_TTL_HOURS');
        if (is_string($raw) && ctype_digit($raw) && (int)$raw >= 48) {
            return (int)$raw;
        }

        return GithubProvenance::DEFAULT_MAX_PUBLIC_TTL_HOURS;
    }

    /**
     * @return list<array{
     *     id:int,category:string,title_cs:string,title_en:string,
     *     body_cs:string,body_en:string,is_public:bool
     * }>
     */
    public function editorialJournal(): array
    {
        if (!$this->hasDatabase()) {
            return [];
        }
        try {
            $entries = TableRegistry::getTableLocator()->get(JournalEntriesTable::class)
                ->find()->orderByDesc('id')->limit(30)->all();
            $result = [];
            foreach ($entries as $entry) {
                $result[] = [
                    'id' => self::requiredId($entry->get('id')),
                    'category' => self::requiredString($entry->get('category'), 'category'),
                    'title_cs' => self::requiredString($entry->get('title_cs'), 'title_cs'),
                    'title_en' => self::requiredString($entry->get('title_en'), 'title_en'),
                    'body_cs' => self::requiredString($entry->get('body_cs'), 'body_cs'),
                    'body_en' => self::requiredString($entry->get('body_en'), 'body_en'),
                    'is_public' => $entry->get('is_public') === true || $entry->get('is_public') === 1,
                ];
            }

            return $result;
        } catch (Throwable $error) {
            error_log('Editorial journal could not be listed: ' . get_class($error));

            return [];
        }
    }

    /**
     * Redacted, append-only editorial change receipts; content is never duplicated.
     *
     * @return list<array{type:string,key:string,action:string,date:string,digest:string}>
     */
    public function editorialAudit(): array
    {
        if (!$this->hasDatabase()) {
            return [];
        }
        try {
            $db = ConnectionManager::get('default');
            if (!$db instanceof Connection) {
                throw new UnexpectedValueException('Unsupported database connection');
            }
            $query = $db->execute('SELECT entity_type,entity_key,action,content_digest,occurred_at
                FROM editorial_audit ORDER BY id DESC LIMIT 30');
            $output = [];
            foreach ($query->fetchAll('assoc') as $row) {
                if (!is_array($row)) {
                    throw new UnexpectedValueException('Malformed editorial audit record');
                }
                $output[] = [
                    'type' => self::requiredString($row['entity_type'] ?? null, 'entity_type'),
                    'key' => self::requiredString($row['entity_key'] ?? null, 'entity_key'),
                    'action' => self::requiredString($row['action'] ?? null, 'action'),
                    'date' => self::requiredString($row['occurred_at'] ?? null, 'occurred_at'),
                    'digest' => self::requiredString($row['content_digest'] ?? null, 'content_digest'),
                ];
            }

            return $output;
        } catch (Throwable $error) {
            error_log('Editorial audit history unavailable: ' . get_class($error));

            return [];
        }
    }

    /**
     * Extract a required string from a validated record.
     */
    private static function requiredString(mixed $value, string $column): string
    {
        if (!is_string($value)) {
            throw new UnexpectedValueException('Invalid persisted string: ' . $column);
        }

        return $value;
    }

    /**
     * Extract a required non-empty UTF-8 string from a persisted database record.
     */
    private static function requiredNonEmptyUtf8(mixed $value, string $column): string
    {
        if (!is_string($value) || trim($value) === '' || !mb_check_encoding($value, 'UTF-8')) {
            throw new UnexpectedValueException('Invalid persisted UTF-8 string: ' . $column);
        }

        return $value;
    }

    /**
     * Extract a required persistent positive integer identifier.
     */
    private static function requiredId(mixed $value): int
    {
        if (is_int($value) && $value > 0) {
            return $value;
        }
        if (is_string($value) && ctype_digit($value) && (int)$value > 0) {
            return (int)$value;
        }
        throw new UnexpectedValueException('Invalid persisted editorial id');
    }

    /**
     * Check if the persistent database is configured and available.
     */
    public function hasDatabase(): bool
    {
        return (bool)Configure::read('Lab.databaseEnabled', false);
    }
}
