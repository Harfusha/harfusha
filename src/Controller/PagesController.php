<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\EngineeringDecision;
use App\Domain\Project;
use App\Service\ContentRepository;
use App\Support\Language;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

final class PagesController extends AppController
{
    private ?ContentRepository $content = null;

    /**
     * Resolve the curated public content repository.
     */
    private function content(): ContentRepository
    {
        return $this->content ??= new ContentRepository();
    }

    /**
     * Initialize controller components and request services.
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->content = new ContentRepository();
    }

    /**
     * Resolve the validated locale from the request.
     */
    private function lang(): Language
    {
        $code = $this->request->getParam('lang');
        if (!is_string($code) || !in_array($code, ['cs', 'en'], true)) {
            throw new NotFoundException();
        }

        return new Language($code);
    }

    /** @param array<string,mixed> $data */
    private function show(
        string $template,
        string $csTitle,
        string $enTitle,
        string $csDescription,
        string $enDescription,
        string $route,
        array $data = [],
    ): Response {
        $lang = $this->lang();
        // Mutable public content should not linger in shared caches following
        // an editorial unpublish or a completed GitHub import.
        if (
            in_array($route, ['/', '/projects', '/journal', '/now', '/open-source'], true) ||
            str_starts_with($route, '/projects/')
        ) {
            $this->response = $this->response->withHeader('Cache-Control', 'no-store');
        }
        $this->set([...$data,
            'lang' => $lang, 'route' => $route,
            'title' => $lang->value($csTitle, $enTitle),
            'description' => $lang->value($csDescription, $enDescription),
        ]);

        return $this->render($template);
    }

    /**
     * Redirect to the localized landing page.
     */
    public function redirectToHome(): Response
    {
        return $this->redirect('/cs/', 302) ?? $this->response;
    }

    /**
     * Render the localized landing page.
     */
    public function home(): Response
    {
        $projects = $this->content()->projects();
        $featured = array_values(array_filter(
            $projects,
            static fn(Project $p): bool => $p->featured,
        ));
        $mergedPrCount = count(array_filter(
            $this->content()->activity(),
            static fn(array $entry): bool => ($entry['kind'] ?? null) === 'pr'
                && ($entry['state'] ?? null) === 'merged',
        ));

        return $this->show(
            'home',
            'Harfusha Lab — Projekty, infrastruktura a experimenty',
            'Harfusha Lab — Projects, infrastructure & experiments',
            'Interaktivní dílna Adama Halfara.',
            'The interactive engineering workshop of Adam Halfar.',
            '/',
            [
                'projects' => array_slice($featured, 0, 4),
                'projectCount' => count($projects),
                'mergedPrCount' => $mergedPrCount,
                'journal' => array_slice($this->content()->journal(), 0, 3),
                'recentRepos' => $this->content()->publicRepoUpdates(),
                'provenance' => $this->content()->githubProvenance(),
            ],
        );
    }

    /**
     * List publicly visible projects.
     */
    public function projects(): Response
    {
        return $this->show(
            'projects',
            'Projekty | Harfusha Lab',
            'Projects | Harfusha Lab',
            'Software, experimenty, nástroje a hry.',
            'Software, experiments, tools and games.',
            '/projects',
            ['projects' => $this->content()->projects()],
        );
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
        $project = $this->content()->findProject($slug);
        if ($project === null) {
            throw new NotFoundException();
        }

        return $this->show(
            'project',
            $project->name . ' | Harfusha Lab',
            $project->name . ' | Harfusha Lab',
            $project->summaryCs,
            $project->summaryEn,
            '/projects/' . $project->slug,
            ['project' => $project, 'related' => $this->content()->relatedProjects($project->slug),
                'decisions' => array_values(array_filter(
                    $this->content()->decisions(),
                    static fn(EngineeringDecision $decision): bool => $decision->project === $project->slug,
                ))],
        );
    }

    /**
     * Render the independent project index.
     */
    public function connections(): Response
    {
        return $this->show(
            'connections',
            'Přehled projektů | Harfusha Lab',
            'Project index | Harfusha Lab',
            'Samostatné projekty, jejich fáze a případové studie.',
            'Independent projects, their stages and case studies.',
            '/connections',
            ['projects' => $this->content()->projects()],
        );
    }

    /**
     * Display the sanitized homelab topology and its simulations.
     */
    public function homelab(): Response
    {
        return $this->show(
            'homelab',
            'Interaktivní homelab | Harfusha Lab',
            'Interactive home lab | Harfusha Lab',
            'Prozkoumej síť, služby a simulaci výpadků.',
            'Explore the network, services and failure simulations.',
            '/homelab',
            ['nodes' => $this->content()->topology(), 'flows' => $this->content()->flows()],
        );
    }

    /**
     * Render the browser-side engineering demonstrations.
     */
    public function playground(): Response
    {
        return $this->show(
            'playground',
            'Interaktivní playground | Harfusha Lab',
            'Interactive playground | Harfusha Lab',
            'Funkční ukázky vývojových projektů.',
            'Functional interactive project demos.',
            '/playground',
            ['projects' => $this->content()->projects()],
        );
    }

    /**
     * Show the documented engineering trade-offs.
     */
    public function decisions(): Response
    {
        return $this->show(
            'decisions',
            'Technická rozhodnutí | Harfusha Lab',
            'Engineering decisions | Harfusha Lab',
            'Důvody, kompromisy a ověřitelné limity návrhu skutečných projektů.',
            'Rationale, trade-offs and stated limitations behind real projects.',
            '/decisions',
            ['decisions' => $this->content()->decisions()],
        );
    }

    /**
     * Render the documented physical engineering projects.
     */
    public function maker(): Response
    {
        return $this->show(
            'maker',
            'Maker Lab | Harfusha Lab',
            'Maker Lab | Harfusha Lab',
            '3D tisk, technické experimenty a fyzické prototypy.',
            '3D printing, hardware experiments and physical prototypes.',
            '/maker',
            ['items' => $this->content()->maker()],
        );
    }

    /**
     * Show public upstream contributions.
     */
    public function openSource(): Response
    {
        return $this->show(
            'open_source',
            'Open Source | Harfusha Lab',
            'Open Source | Harfusha Lab',
            'Reálné příspěvky do dalších projektů.',
            'Real upstream contributions.',
            '/open-source',
            ['activity' => $this->content()->activity(),
                'provenance' => $this->content()->githubProvenance()],
        );
    }

    /**
     * Show the current engineering workbench.
     */
    public function now(): Response
    {
        return $this->show(
            'now',
            'Právě vzniká | Harfusha Lab',
            'Now building | Harfusha Lab',
            'Přehled posledních zaznamenaných změn veřejných repozitářů.',
            'A view of the latest recorded changes in public repositories.',
            '/now',
            [
                'recentRepos' => $this->content()->publicRepoUpdates(),
                'journalEntries' => $this->content()->journal(),
                'activity' => $this->content()->activity(),
                'provenance' => $this->content()->githubProvenance(),
            ],
        );
    }

    /**
     * Display or update the published engineering journal.
     */
    public function journal(): Response
    {
        return $this->show(
            'journal',
            'Workshop log | Harfusha Lab',
            'Workshop log | Harfusha Lab',
            'Co se právě vyvíjí a proč.',
            'What is being built and why.',
            '/journal',
            ['journal' => $this->content()->journal()],
        );
    }

    /**
     * Render the developer biography.
     */
    public function about(): Response
    {
        return $this->show(
            'about',
            'O mně | Harfusha Lab',
            'About | Harfusha Lab',
            'Backendový vývojář, open-source přispěvatel a maker.',
            'Backend developer, open-source contributor and maker.',
            '/about',
        );
    }
}
