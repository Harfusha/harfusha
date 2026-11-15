<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\EditorialInput;
use App\Model\Table\JournalEntriesTable;
use App\Model\Table\ProjectOverridesTable;
use App\Security\AdminCredentials;
use App\Security\LoginLimiter;
use App\Service\ContentRepository;
use App\Service\EditorialAuditTrail;
use App\Service\OperationsService;
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
    /**
     * Check the server-side authenticated session.
     */
    private function authenticated(): bool
    {
        return $this->request->getSession()->read('lab.admin') === true;
    }

    /**
     * The HTTP editor must enforce the same migration readiness gate as its
     * disabled buttons, even when a caller submits a form directly.
     */
    private function editableDatabase(): bool
    {
        return (new OperationsService())->report()['database'] === 'ok';
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
    public function index(): Response
    {
        $this->viewBuilder()->disableAutoLayout();
        $this->response = $this->response->withHeader('Cache-Control', 'no-store')
            ->withHeader('X-Robots-Tag', 'noindex, nofollow');
        $csrfValue = $this->request->getAttribute('csrfToken');
        $this->set('csrf', is_string($csrfValue) ? $csrfValue : '');
        if (!$this->authenticated()) {
            $this->set('enabled', is_string(getenv('ADMIN_PASSWORD_HASH')) && getenv('ADMIN_PASSWORD_HASH') !== '');
            $this->set('loginError', $this->request->getQuery('error') !== null);

            return $this->render('login');
        }
        $content = new ContentRepository();
        // A query string is not evidence of a successful write. Show the
        // receipt only once, after the server actually commits an edit.
        $session = $this->request->getSession();
        $this->set('saveConfirmed', $session->read('lab.saveConfirmed') === true);
        $session->delete('lab.saveConfirmed');
        $this->set('projects', $content->projects());
        $operations = (new OperationsService())->report();
        // Editing requires a reachable database with all required migrations,
        // not merely the presence of DB_NAME in the environment.
        $this->set('dbEnabled', $operations['database'] === 'ok');
        $this->set('journalEntries', $content->editorialJournal());
        $this->set('auditEntries', $content->editorialAudit());
        $this->set('operations', $operations);

        return $this->render('index');
    }

    /**
     * Validate credentials and create an authenticated session.
     */
    public function login(): Response
    {
        $this->request->allowMethod(['post']);
        // Use the direct peer address. Never trust a user-supplied forwarding header for rate limits.
        $remoteIp = $this->request->getEnv('REMOTE_ADDR');
        $client = is_string($remoteIp) && filter_var($remoteIp, FILTER_VALIDATE_IP) !== false
            ? $remoteIp : 'unknown';
        $limiter = new LoginLimiter(TMP . 'auth');
        if (!$limiter->consume($client)) {
            return $this->response->withStatus(429)->withHeader('Retry-After', '900')
                ->withStringBody('Too many login attempts');
        }
        $password = $this->request->getData('password');
        $stored = getenv('ADMIN_PASSWORD_HASH');
        if (!AdminCredentials::verify($password, $stored)) {
            return $this->redirect('/admin?error=1', 303) ?? $this->response;
        }
        if (!$limiter->clear($client)) {
            return $this->response->withStatus(503)->withStringBody('Login temporarily unavailable');
        }
        $session = $this->request->getSession();
        $session->renew();
        $session->write('lab.admin', true);

        return $this->redirect('/admin', 303) ?? $this->response;
    }

    /**
     * Invalidate the active administrator session.
     */
    public function logout(): Response
    {
        $this->request->getSession()->delete('lab.admin');
        $this->request->getSession()->delete('lab.saveConfirmed');
        $this->request->getSession()->renew();

        return $this->redirect('/admin', 303) ?? $this->response;
    }

    /**
     * Display a public project or apply a validated editorial update.
     */
    public function project(): Response
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
        if ((new ContentRepository())->findProject($data['slug']) === null) {
            return $this->response->withStatus(422)->withStringBody('Unknown project');
        }
        if (!$this->editableDatabase()) {
            return $this->response->withStatus(503)->withStringBody('Editorial storage unavailable');
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
        } catch (Throwable $error) {
            error_log('Project update failed: ' . get_class($error));

            return $this->response->withStatus(503)->withStringBody('Database write failed');
        }

        $this->request->getSession()->write('lab.saveConfirmed', true);

        return $this->redirect('/admin?saved=1', 303) ?? $this->response;
    }

    /**
     * Display or update the published engineering journal.
     */
    public function journal(): Response
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
        if (!$this->editableDatabase()) {
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

    /** Existing entry can be updated or hidden without removing the audit history. */
    public function journalUpdate(): Response
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
        if (!in_array($action, ['save','publish','unpublish'], true)) {
            return $this->response->withStatus(422)->withStringBody('Invalid operation');
        }
        if (!$this->editableDatabase()) {
            return $this->response->withStatus(503)->withStringBody('Editorial storage unavailable');
        }
        try {
            $table = TableRegistry::getTableLocator()->get(JournalEntriesTable::class);
            $entry = $table->find()->where(['id' => $id])->first();
            if ($entry === null) {
                return $this->response->withStatus(404)->withStringBody('Entry not found');
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
}
