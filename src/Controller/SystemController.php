<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\JournalFeed;
use App\Domain\PublicSitemap;
use App\Service\ContentRepository;
use App\Service\GithubSyncService;
use App\Support\Environment;
use App\Support\GithubSyncFailure;
use App\Support\Html;
use App\Support\Language;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\Log\Log;
use RuntimeException;
use Throwable;

final class SystemController extends AppController
{
    /**
     * Return a minimal public liveness response.
     */
    public function health(): Response
    {
        return $this->response->withType('text')->withHeader('Cache-Control', 'no-store')->withStringBody('ok');
    }

    /**
     * Resolve the configured canonical web origin.
     */
    private function origin(): string
    {
        $value = Configure::read('App.fullBaseUrl');
        if (!is_string($value) || $value === '') {
            throw new RuntimeException('Missing canonical application origin');
        }

        return rtrim($value, '/');
    }

    /**
     * Return the discoverable localized website URLs.
     */
    public function sitemap(): Response
    {
        $origin = $this->origin();
        $pages = [
            '', 'projects', 'connections', 'decisions', 'homelab', 'maker',
            'playground', 'open-source', 'now', 'journal', 'about',
        ];
        foreach ((new ContentRepository())->projects() as $project) {
            $pages[] = 'projects/' . $project->slug;
        }

        return $this->response->withType('xml')
            ->withHeader('Cache-Control', 'public, max-age=3600, must-revalidate')
            ->withStringBody(PublicSitemap::render($origin, $pages));
    }

    /**
     * Return the public, read-only activity feed.
     */
    public function feed(): Response
    {
        $code = $this->request->getParam('lang');
        if (!is_string($code) || !in_array($code, ['cs', 'en'], true)) {
            throw new NotFoundException();
        }
        $xml = JournalFeed::render(
            $this->origin(),
            new Language($code),
            (new ContentRepository())->journal(),
        );

        return $this->response->withHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->withHeader('Cache-Control', 'no-store')
            ->withStringBody($xml);
    }

    /**
     * Authorize the scheduled GitHub synchronization endpoint.
     */
    public function sync(): Response
    {
        $secret = Environment::get('CRON_SECRET');
        $provided = $this->request->getHeaderLine('X-Cron-Token');
        if (!is_string($secret) || strlen($secret) < 32 || $provided === '' || !hash_equals($secret, $provided)) {
            return $this->response->withStatus(403)->withStringBody('Forbidden');
        }
        try {
            $summary = (new GithubSyncService())->synchronize();

            return $this->response->withType('json')->withStringBody(Html::json($summary));
        } catch (Throwable $error) {
            try {
                Log::error(
                    'GitHub synchronization failed: ' . get_class($error)
                    . ' [' . GithubSyncFailure::describe($error) . ']',
                );
            } catch (Throwable) {
                error_log('GitHub synchronization failed; application log unavailable');
            }

            return $this->response->withStatus(502)->withStringBody('Synchronization failed');
        }
    }
}
