<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\Project;
use App\Domain\PublicProjectView;
use App\Service\ContentRepository;
use App\Support\Html;
use App\Support\HttpConditionalResponder;
use App\Support\Language;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

/**
 * Public, read-only, allowlisted API; no entity serialization or private URLs.
 * GET/HEAD only. No cookies or credentials are required by API consumers.
 */
final class ApiController extends AppController
{
    /**
     * List publicly visible projects.
     */
    public function projects(ContentRepository $content): Response
    {
        $language = $this->language();
        $records = array_map(
            static fn(Project $project): array => (new PublicProjectView($project))->toArray($language),
            $content->projects(),
        );

        return $this->json([
            'version' => '1',
            'language' => $language->code,
            'count' => count($records),
            'projects' => $records,
        ]);
    }

    /**
     * Return a single allowlisted public project representation.
     */
    public function project(ContentRepository $content): Response
    {
        $slug = $this->request->getParam('slug');
        if (!is_string($slug)) {
            throw new NotFoundException();
        }
        $project = $content->findProject($slug);
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
     * Return published engineering evidence claims (N-EVD-01).
     */
    public function evidence(ContentRepository $content): Response
    {
        $projectSlug = $this->request->getQuery('project');
        $registry = $content->evidence();
        $claims = is_string($projectSlug) && $projectSlug !== ''
            ? $registry->forProject($projectSlug)
            : $registry->all();

        $records = [];
        foreach ($claims as $claim) {
            $records[] = $claim->toArray();
        }

        return $this->json([
            'version' => '1',
            'count' => count($records),
            'evidence' => $records,
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
        return HttpConditionalResponder::respond(
            $this->request,
            $this->response,
            Html::json($body),
            'application/json; charset=UTF-8',
            'no-store',
        );
    }
}
