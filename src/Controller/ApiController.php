<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\Project;
use App\Domain\PublicProjectView;
use App\Service\ContentRepository;
use App\Support\Html;
use App\Support\Language;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

/**
 * Public, read-only, allowlisted API; no entity serialization or private URLs.
 * GET only. No cookies or credentials are required by API consumers.
 */
final class ApiController extends AppController
{
    /**
     * List publicly visible projects.
     */
    public function projects(): Response
    {
        $language = $this->language();
        $records = array_map(
            static fn(Project $project): array => (new PublicProjectView($project))->toArray($language),
            (new ContentRepository())->projects(),
        );

        return $this->json([
            'version' => '1',
            'language' => $language->code,
            'count' => count($records),
            'projects' => $records,
        ]);
    }

    /**
     * Display a public project or apply a validated editorial update.
     */
    public function project(): Response
    {
        $slug = $this->request->getParam('slug');
        if (!is_string($slug)) {
            throw new NotFoundException();
        }
        $project = (new ContentRepository())->findProject($slug);
        if ($project === null) {
            throw new NotFoundException();
        }
        $language = $this->language();

        return $this->json([
            'version' => '1',
            'language' => $language->code,
            'project' => (new PublicProjectView($project))->toArray($language),
        ]);
    }

    /**
     * Resolve a supported language for the API response.
     */
    private function language(): Language
    {
        $code = $this->request->getQuery('lang', 'cs');
        if (!is_string($code) || !in_array($code, ['cs', 'en'], true)) {
            throw new BadRequestException('Unsupported language');
        }

        return new Language($code);
    }

    /** @param array<string,mixed> $body */
    private function json(array $body): Response
    {
        $json = Html::json($body);
        $etag = '"' . hash('sha256', $json) . '"';
        $response = $this->response->withType('json')
            ->withHeader('ETag', $etag)
            ->withHeader('Cache-Control', 'public, max-age=120, must-revalidate')
            ->withHeader('Vary', 'Accept-Encoding');
        // If-None-Match uses weak comparison for GET (RFC 9110).
        // Accept a list of validators, weak validators, or the wildcard.
        $validators = explode(',', $this->request->getHeaderLine('If-None-Match'));
        foreach ($validators as $validator) {
            $candidate = trim($validator);
            if ($candidate === '*' || $candidate === $etag || $candidate === 'W/' . $etag) {
                return $response->withStatus(304);
            }
        }

        return $response->withStringBody($json);
    }
}
