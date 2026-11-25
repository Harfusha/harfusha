<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\EditorialInput;
use App\Domain\Project;
use App\Model\Table\JournalEntriesTable;
use App\Model\Table\ProjectOverridesTable;
use App\Security\AdminCredentials;
use App\Security\LoginLimiter;
use App\Security\TrustedProxy;
use App\Service\ContentRepository;
use App\Service\EditorialAuditTrail;
use App\Service\OperationsService;
use App\Support\Environment;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use DateTimeImmutable;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class AdminController extends AppController
{
    private const int SESSION_IDLE_SECONDS = 3600;
    private const int SESSION_ABSOLUTE_SECONDS = 28800;

    /**
     * Compute a deterministic optimistic-locking token for a project state.
     */
    public static function projectRevisionToken(Project $project): string
    {
        return hash('sha256', implode("\0", [
            $project->slug,
            $project->status,
            $project->summaryCs,
            $project->summaryEn,
            $project->problemCs,
            $project->problemEn,
            $project->solutionCs,
            $project->solutionEn,
        ]));
    }

    /**
     * Compute a deterministic optimistic-locking token for a journal row.
     *
     * @param array{id:int,category:string,title_cs:string,title_en:string,body_cs:string,body_en:string,is_public:bool} $entry
     */
    public static function journalRevisionToken(array $entry): string
    {
        return hash('sha256', implode("\0", [
            (string)$entry['id'],
            $entry['category'],
            $entry['title_cs'],
            $entry['title_en'],
            $entry['body_cs'],
            $entry['body_en'],
            $entry['is_public'] ? '1' : '0',
        ]));
    }

    /**
     * Check the server-side authenticated session, idle/absolute timeouts,
     * and credential-rotation binding (N-SEC-04).
     */
    private function authenticated(): bool
    {
        $session = $this->request->getSession();
        if ($session->read('lab.admin') !== true) {
            return false;
        }
        $now = time();
        $loginAt = $session->read('lab.admin_login_at');
        $lastSeen = $session->read('lab.admin_last_seen_at');
        if (!is_int($loginAt) || !is_int($lastSeen)) {
            if ($loginAt === null && $lastSeen === null) {
                $session->write('lab.admin_login_at', $now);
                $session->write('lab.admin_last_seen_at', $now);
                $loginAt = $now;
                $lastSeen = $now;
            } else {
                $this->clearAdminSession();

                return false;
            }
        }
        if ($now - $loginAt > self::SESSION_ABSOLUTE_SECONDS || $now - $lastSeen > self::SESSION_IDLE_SECONDS) {
            $this->clearAdminSession();

            return false;
        }
        $storedHash = Environment::get('ADMIN_PASSWORD_HASH');
        if (is_string($storedHash) && $storedHash !== '') {
            $sessionDigest = $session->read('lab.admin_cred_digest');
            $expectedDigest = AdminCredentials::sessionDigest($storedHash);
            if ($sessionDigest === null) {
                $session->write('lab.admin_cred_digest', $expectedDigest);
                $sessionDigest = $expectedDigest;
            }
            if (!is_string($sessionDigest) || !hash_equals($expectedDigest, $sessionDigest)) {
                $this->clearAdminSession();

                return false;
            }
        }
        $session->write('lab.admin_last_seen_at', $now);

        return true;
    }

    /**
     * Clear all admin session keys.
     */
    private function clearAdminSession(): void
    {
        $session = $this->request->getSession();
        $session->delete('lab.admin');
        $session->delete('lab.admin_login_at');
        $session->delete('lab.admin_last_seen_at');
        $session->delete('lab.admin_cred_digest');
        $session->delete('lab.saveConfirmed');
    }

    /**
     * The HTTP editor must enforce the same migration readiness gate as its
     * disabled buttons, even when a caller submits a form directly.
     */
    private function editableDatabase(OperationsService $operations): bool
    {
        return $operations->report()['database'] === 'ok';
    }

    /**
     * Read a validated scalar from an editorial form.
     */
    private function formValue(string $key, int $max): string
    {
        $value = $this->request->getData($key);
        if (!is_string($value) || strlen($value) > $max * 4) {
            return '';
        }
        $value = trim($value);

        return mb_strlen($value, 'UTF-8') <= $max ? $value : '';
    }

    /**
     * Render the private administrative dashboard.
     */
    public function index(ContentRepository $content, OperationsService $operationsService): Response
    {
        $this->viewBuilder()->disableAutoLayout();
        $this->response = $this->response->withHeader('Cache-Control', 'no-store')
            ->withHeader('X-Robots-Tag', 'noindex, nofollow');
        $csrfValue = $this->request->getAttribute('csrfToken');
        $this->set('csrf', is_string($csrfValue) ? $csrfValue : '');
        if (!$this->authenticated()) {
            $adminHash = Environment::get('ADMIN_PASSWORD_HASH');
            $this->set('enabled', is_string($adminHash) && $adminHash !== '');
            $this->set('loginError', $this->request->getQuery('error') !== null);

            return $this->render('login');
        }
        $session = $this->request->getSession();
        $this->set('saveConfirmed', $session->read('lab.saveConfirmed') === true);
        $session->delete('lab.saveConfirmed');
        $projects = $content->projects();
        $projectTokens = [];
        foreach ($projects as $project) {
            $projectTokens[$project->slug] = self::projectRevisionToken($project);
        }
        $journalEntries = $content->editorialJournal();
        $journalTokens = [];
        foreach ($journalEntries as $entry) {
            $journalTokens[$entry['id']] = self::journalRevisionToken($entry);
        }
        $this->set('projects', $projects);
        $this->set('projectTokens', $projectTokens);
        $operations = $operationsService->report();
        $this->set('dbEnabled', $operations['database'] === 'ok');
        $this->set('journalEntries', $journalEntries);
        $this->set('journalTokens', $journalTokens);
        $this->set('auditEntries', $content->editorialAudit());
        $this->set('operations', $operations);

        return $this->render('index');
    }

    /**
     * Validate credentials and create an authenticated session.
     */
    public function login(TrustedProxy $trustedProxy): Response
    {
        $this->request->allowMethod(['post']);
        $client = $trustedProxy->clientIp($this->request);
        $limiterDirectory = TMP . (Environment::get('APP_ENV') === 'testing' ? 'auth-test' : 'auth');
        $salt = Environment::get('SECURITY_SALT');
        $limiter = new LoginLimiter($limiterDirectory, null, 512, is_string($salt) && $salt !== '' ? $salt : null);
        if (!$limiter->consume($client)) {
            return $this->response->withStatus(429)->withHeader('Retry-After', '900')
                ->withStringBody('Too many login attempts');
        }
        $password = $this->request->getData('password');
        $stored = Environment::get('ADMIN_PASSWORD_HASH');
        if (!AdminCredentials::verify($password, $stored)) {
            return $this->redirect('/admin?error=1', 303) ?? $this->response;
        }
        if (!$limiter->clear($client)) {
            return $this->response->withStatus(503)->withStringBody('Login temporarily unavailable');
        }
        $now = time();
        $session = $this->request->getSession();
        $session->renew();
        $session->write('lab.admin', true);
        $session->write('lab.admin_login_at', $now);
        $session->write('lab.admin_last_seen_at', $now);
        $session->write('lab.admin_cred_digest', AdminCredentials::sessionDigest($stored));

        return $this->redirect('/admin', 303) ?? $this->response;
    }

    /**
     * Invalidate the active administrator session completely.
     */
    public function logout(): Response
    {
        $this->clearAdminSession();
        $this->request->getSession()->destroy();

        return $this->redirect('/admin', 303) ?? $this->response;
    }

    /**
     * Apply a validated editorial override to a project case study.
     */
    public function project(ContentRepository $content, OperationsService $operations): Response
    {
        $this->request->allowMethod(['post']);
        if (!$this->authenticated()) {
            return $this->response->withStatus(403)->withStringBody('Forbidden');
        }
        try {
            $data = EditorialInput::project($this->request->getData());
        } catch (InvalidArgumentException) {
            return $this->response->withStatus(422)->withStringBody('Invalid project update');
        }
        $currentProject = $content->findProject($data['slug']);
        if ($currentProject === null) {
            return $this->response->withStatus(422)->withStringBody('Unknown project');
        }
        if (!$this->editableDatabase($operations)) {
            return $this->response->withStatus(503)->withStringBody('Editorial storage unavailable');
        }
        $submittedToken = $this->request->getData('revision_token');
        if (is_string($submittedToken) && $submittedToken !== '') {
            $expectedToken = self::projectRevisionToken($currentProject);
            if (!hash_equals($expectedToken, $submittedToken)) {
                return $this->response->withStatus(409)->withStringBody(
                    'Conflict: this project was modified in another session. Reload /admin and retry.',
                );
            }
        }
        try {
            $table = TableRegistry::getTableLocator()->get(ProjectOverridesTable::class);
            $record = $table->find()->where(['slug' => $data['slug']])->first() ?? $table->newEmptyEntity();
            $db = ConnectionManager::get('default');
            if (!$db instanceof Connection) {
                throw new RuntimeException('Invalid editorial database');
            }
            $committed = $db->transactional(
                static function (Connection $connection) use ($table, $record, $data): bool {
                    try {
                        $table->saveOrFail($table->patchEntity($record, $data));
                        EditorialAuditTrail::record($connection, 'project', $data['slug'], 'update', $data);

                        return true;
                    } catch (Throwable $error) {
                        error_log('Project transaction rollback: ' . get_class($error));

                        return false;
                    }
                },
            );
            if ($committed !== true) {
                throw new RuntimeException('Project transaction failed');
            }
            $this->persistProjectOverrideGuard($data);
        } catch (Throwable $error) {
            error_log('Project update failed: ' . get_class($error));

            return $this->response->withStatus(503)->withStringBody('Database write failed');
        }

        $this->request->getSession()->write('lab.saveConfirmed', true);

        return $this->redirect('/admin?saved=1', 303) ?? $this->response;
    }

    /**
     * Create a new editorial journal entry.
     */
    public function journal(OperationsService $operations): Response
    {
        $this->request->allowMethod(['post']);
        if (!$this->authenticated()) {
            return $this->response->withStatus(403)->withStringBody('Forbidden');
        }
        try {
            $values = EditorialInput::journal($this->request->getData());
            $isPublic = EditorialInput::isPublic($this->request->getData('visibility'));
        } catch (InvalidArgumentException) {
            return $this->response->withStatus(422)->withStringBody('Invalid journal entry');
        }
        if (!$this->editableDatabase($operations)) {
            return $this->response->withStatus(503)->withStringBody('Editorial storage unavailable');
        }
        try {
            $table = TableRegistry::getTableLocator()->get(JournalEntriesTable::class);
            $record = $table->newEntity([...$values,
                'published_at' => new DateTimeImmutable('now'),
                'source_url' => '',
                'is_public' => $isPublic,
            ]);
            $db = ConnectionManager::get('default');
            if (!$db instanceof Connection) {
                throw new RuntimeException('Invalid editorial database');
            }
            $committed = $db->transactional(
                static function (Connection $connection) use ($table, $record, $values): bool {
                    try {
                        $table->saveOrFail($record);
                        $id = $record->get('id');
                        if (!is_int($id) && !(is_string($id) && ctype_digit($id))) {
                            return false;
                        }
                        EditorialAuditTrail::record($connection, 'journal', (string)$id, 'create', $values);

                        return true;
                    } catch (Throwable $error) {
                        error_log('Journal transaction rollback: ' . get_class($error));

                        return false;
                    }
                },
            );
            if ($committed !== true) {
                throw new RuntimeException('Journal transaction failed');
            }
        } catch (Throwable $error) {
            error_log('Journal update failed: ' . get_class($error));

            return $this->response->withStatus(503)->withStringBody('Database write failed');
        }

        $this->request->getSession()->write('lab.saveConfirmed', true);

        return $this->redirect('/admin?saved=1', 303) ?? $this->response;
    }

    /**
     * Existing entry can be updated or hidden without removing the audit history.
     */
    public function journalUpdate(OperationsService $operations): Response
    {
        $this->request->allowMethod(['post']);
        if (!$this->authenticated()) {
            return $this->response->withStatus(403)->withStringBody('Forbidden');
        }
        try {
            $id = EditorialInput::positiveId($this->request->getData('id'));
        } catch (InvalidArgumentException) {
            return $this->response->withStatus(422)->withStringBody('Invalid entry ID');
        }
        $action = $this->formValue('operation', 20);
        if (!in_array($action, ['save', 'publish', 'unpublish'], true)) {
            return $this->response->withStatus(422)->withStringBody('Invalid operation');
        }
        if (!$this->editableDatabase($operations)) {
            return $this->response->withStatus(503)->withStringBody('Editorial storage unavailable');
        }
        try {
            $table = TableRegistry::getTableLocator()->get(JournalEntriesTable::class);
            $entry = $table->find()->where(['id' => $id])->first();
            if ($entry === null) {
                return $this->response->withStatus(404)->withStringBody('Entry not found');
            }
            $submittedToken = $this->request->getData('revision_token');
            if (is_string($submittedToken) && $submittedToken !== '') {
                $asString = static fn(mixed $v): string => is_scalar($v) ? (string)$v : '';
                $currentRow = [
                    'id' => $id,
                    'category' => $asString($entry->get('category')),
                    'title_cs' => $asString($entry->get('title_cs')),
                    'title_en' => $asString($entry->get('title_en')),
                    'body_cs' => $asString($entry->get('body_cs')),
                    'body_en' => $asString($entry->get('body_en')),
                    'is_public' => in_array($entry->get('is_public'), [true, 1, '1'], true),
                ];
                if (!hash_equals(self::journalRevisionToken($currentRow), $submittedToken)) {
                    return $this->response->withStatus(409)->withStringBody(
                        'Conflict: this journal note was modified in another session. Reload /admin and retry.',
                    );
                }
            }
            /** @var array<string, mixed> $auditValues */
            $auditValues = ['is_public' => $action === 'publish'];
            if ($action === 'save') {
                try {
                    $values = EditorialInput::journal($this->request->getData());
                } catch (InvalidArgumentException) {
                    return $this->response->withStatus(422)->withStringBody('Invalid entry content');
                }
                $entry = $table->patchEntity($entry, $values);
                $auditValues = $values;
            } else {
                $status = $action === 'publish';
                $changes = ['is_public' => $status];
                if ($status && !in_array($entry->get('is_public'), [true, 1, '1'], true)) {
                    // A saved draft becomes an actual publication only now.
                    $changes['published_at'] = new DateTimeImmutable('now');
                }
                $entry = $table->patchEntity($entry, $changes);
            }
            $db = ConnectionManager::get('default');
            if (!$db instanceof Connection) {
                throw new RuntimeException('Invalid editorial database');
            }
            $committed = $db->transactional(
                static function (Connection $connection) use ($table, $entry, $id, $action, $auditValues): bool {
                    try {
                        $table->saveOrFail($entry);
                        EditorialAuditTrail::record(
                            $connection,
                            'journal',
                            (string)$id,
                            $action === 'save' ? 'update' : $action,
                            $auditValues,
                        );

                        return true;
                    } catch (Throwable $error) {
                        error_log('Journal transaction rollback: ' . get_class($error));

                        return false;
                    }
                },
            );
            if ($committed !== true) {
                throw new RuntimeException('Journal update transaction failed');
            }
        } catch (Throwable $error) {
            error_log('Journal update failed: ' . get_class($error));

            return $this->response->withStatus(503)->withStringBody('Database write failed');
        }

        $this->request->getSession()->write('lab.saveConfirmed', true);

        return $this->redirect('/admin?saved=1', 303) ?? $this->response;
    }

    /**
     * Persist last-committed project override snapshot atomically so a subsequent DB
     * outage does not revert an editorial status/claim correction (N-DATA-04).
     *
     * @param array{
     *     slug: string,
     *     status: string,
     *     summary_cs: string,
     *     summary_en: string,
     *     problem_cs: string,
     *     problem_en: string,
     *     solution_cs: string,
     *     solution_en: string
     * } $data
     */
    private function persistProjectOverrideGuard(array $data): void
    {
        if (Environment::get('APP_ENV') === 'testing') {
            return;
        }
        $guardFile = TMP . 'project-overrides-guard.json';
        if (is_link($guardFile)) {
            return;
        }
        $existing = [];
        if (is_file($guardFile)) {
            $raw = file_get_contents($guardFile);
            if (is_string($raw) && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $existing = $decoded;
                }
            }
        }
        $existing[$data['slug']] = [
            'status' => $data['status'],
            'summary_cs' => $data['summary_cs'],
            'summary_en' => $data['summary_en'],
            'problem_cs' => $data['problem_cs'],
            'problem_en' => $data['problem_en'],
            'solution_cs' => $data['solution_cs'],
            'solution_en' => $data['solution_en'],
        ];
        $tmp = $guardFile . '.tmp.' . bin2hex(random_bytes(6));
        $encoded = json_encode($existing, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        if (file_put_contents($tmp, $encoded) !== false) {
            rename($tmp, $guardFile);
        } elseif (is_file($tmp)) {
            unlink($tmp);
        }
    }
}
