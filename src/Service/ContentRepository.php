<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Project;
use App\Domain\ProjectCatalog;
use App\Model\Table\GithubActivityTable;
use App\Model\Table\GithubPublicReposTable;
use App\Model\Table\JournalEntriesTable;
use App\Model\Table\ProjectOverridesTable;
use App\Model\Table\SyncRunsTable;
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
    private readonly ProjectCatalog $catalog;

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
                );
            }, $base);
        } catch (Throwable $error) {
            error_log('Project catalogue ORM fallback: ' . get_class($error));

            return $base;
        }
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

    /** @return list<array<string,mixed>> */
    public function journal(): array
    {
        $rows = $this->catalog->journal();
        if ($this->hasDatabase()) {
            try {
                $entities = TableRegistry::getTableLocator()->get(JournalEntriesTable::class)
                    ->find()->where(['is_public' => true])->orderByDesc('published_at')->limit(60)->all();
                // Keep DB records separate until every row has been validated.
                // A broken row must not publish a partially read journal.
                $publishedRows = [];
                foreach ($entities as $record) {
                    $published = $record->get('published_at');
                    $publishedRows[] = [
                        'id' => self::requiredId($record->get('id')),
                        'title_cs' => $record->get('title_cs'), 'title_en' => $record->get('title_en'),
                        'body_cs' => $record->get('body_cs'), 'body_en' => $record->get('body_en'),
                        'category' => $record->get('category'), 'source_url' => $record->get('source_url'),
                        'published_at' => $published instanceof DateTimeInterface
                            ? $published->format('Y-m-d H:i:s')
                            : '',
                    ];
                }
                $rows = array_merge($rows, $publishedRows);
            } catch (Throwable $error) {
                error_log('Journal ORM fallback: ' . get_class($error));
            }
        }
        // Editorial changes should appear immediately in the latest workshop feed.
        usort($rows, static fn(array $a, array $b): int => strcmp(
            is_string($b['published_at'] ?? null) ? $b['published_at'] : '',
            is_string($a['published_at'] ?? null) ? $a['published_at'] : '',
        ));

        return $rows;
    }

    /** @return list<array<string,mixed>> */
    public function activity(): array
    {
        if (!$this->hasDatabase()) {
            return $this->catalog->contributions();
        }
        // Rows are not publishable until a complete GitHub import has an
        // authoritative sync receipt. Partial/manual cache rows stay private.
        $source = $this->githubProvenance()['source'];
        if ($source === 'reviewed-snapshot') {
            return $this->catalog->contributions();
        }
        if ($source !== 'github-public-api') {
            return [];
        }
        try {
            $entities = TableRegistry::getTableLocator()->get(GithubActivityTable::class)
                ->find()->orderByDesc('published_at')->limit(500)->all();
            $rows = [];
            foreach ($entities as $record) {
                $created = $record->get('published_at');
                $rows[] = [
                    'source_url' => $record->get('source_url'), 'title' => $record->get('title'),
                    'repository' => $record->get('repository'), 'kind' => $record->get('kind'),
                    'state' => $record->get('state'),
                    'published_at' => $created instanceof DateTimeInterface ? $created->format('Y-m-d H:i:s') : '',
                    'merged_at' => $record->get('merged_at') instanceof DateTimeInterface
                        ? $record->get('merged_at')->format('Y-m-d H:i:s') : null,
                    'closed_at' => $record->get('closed_at') instanceof DateTimeInterface
                        ? $record->get('closed_at')->format('Y-m-d H:i:s') : null,
                ];
            }

            // Even an empty completed import is authoritative.
            return $rows;
        } catch (Throwable $error) {
            // Database driver messages may contain bound private field values.
            error_log('Activity ORM unavailable: ' . get_class($error));

            return [];
        }
    }

    /**
     * Publication provenance, not an invented real-time server status.
     *
     * @return array{source:string,last_sync:?string}
     */
    public function githubProvenance(): array
    {
        if ($this->hasDatabase()) {
            try {
                $run = TableRegistry::getTableLocator()->get(SyncRunsTable::class)
                    ->find()->where(['source' => 'github'])->orderByDesc('completed_at')->first();
                if ($run !== null) {
                    $time = $run->get('completed_at');

                    return [
                        'source' => 'github-public-api',
                        'last_sync' => $time instanceof DateTimeInterface
                            ? $time->format('Y-m-d H:i:s') . ' UTC'
                            : null,
                    ];
                }
            } catch (Throwable $error) {
                error_log('Sync provenance unavailable: ' . get_class($error));

                return ['source' => 'unavailable', 'last_sync' => null];
            }
        }

        return ['source' => 'reviewed-snapshot', 'last_sync' => null];
    }

    /** @return list<array{repository:string,description:string,repo_url:string,pushed_at:string}> */
    public function publicRepoUpdates(): array
    {
        if ($this->hasDatabase()) {
            // A cache row alone cannot authorize a public GitHub listing.
            $source = $this->githubProvenance()['source'];
            if ($source === 'unavailable') {
                return [];
            }
            if ($source === 'github-public-api') {
                try {
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
                } catch (Throwable $error) {
                    error_log('Repo feed ORM unavailable: ' . get_class($error));

                    return [];
                }
            }
        }

        // Before the first successful import only reviewed static data is public.
        return [
            [
                'repository' => 'Harfusha/MutaUI',
                'description' => 'GitHub snapshot 2026-11-12',
                'repo_url' => 'https://github.com/Harfusha/MutaUI',
                'pushed_at' => '2026-11-12 18:53:55',
            ],
            [
                'repository' => 'Harfusha/local-ai-hub',
                'description' => 'GitHub snapshot 2026-11-12',
                'repo_url' => 'https://github.com/Harfusha/local-ai-hub',
                'pushed_at' => '2026-11-12 11:45:05',
            ],
        ];
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
     * Extract a required nonempty string from a validated record.
     */
    private static function requiredString(mixed $value, string $column): string
    {
        if (!is_string($value)) {
            throw new UnexpectedValueException('Invalid persisted string: ' . $column);
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
