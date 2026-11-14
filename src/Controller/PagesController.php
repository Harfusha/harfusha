<?php
declare(strict_types=1);
namespace App\Controller;

use App\Service\ContentRepository;
use App\Support\Language;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

final class PagesController extends AppController
{
    private ContentRepository $content;

    public function initialize(): void
    {
        parent::initialize();
        $this->content = new ContentRepository();
    }

    private function lang(): Language
    {
        $code = $this->request->getParam('lang');
        if (!is_string($code) || !in_array($code, ['cs', 'en'], true)) {
            throw new NotFoundException();
        }
        return new Language($code);
    }

    /** @param array<string,mixed> $data */
    private function show(string $template, string $csTitle, string $enTitle, string $csDescription, string $enDescription, string $route, array $data = []): Response
    {
        $lang = $this->lang();
        $this->set([...$data,
            'lang' => $lang, 'route' => $route,
            'title' => $lang->value($csTitle, $enTitle),
            'description' => $lang->value($csDescription, $enDescription),
        ]);
        return $this->render($template);
    }

    public function redirectToHome(): Response
    {
        return $this->redirect('/cs/', 302);
    }

    public function home(): Response
    {
        $featured = array_values(array_filter($this->content->projects(), static fn ($p): bool => $p->featured));
        return $this->show('home', 'Harfusha Lab — Projekty, infrastruktura a experimenty', 'Harfusha Lab — Projects, infrastructure & experiments',
            'Interaktivní dílna Adama Halfara.', 'The interactive engineering workshop of Adam Halfar.', '/', [
                'projects' => array_slice($featured, 0, 4),
                'journal' => array_slice($this->content->journal(), 0, 3),
                'recentRepos' => $this->content->publicRepoUpdates(),
            ]);
    }

    public function projects(): Response
    {
        return $this->show('projects', 'Projekty | Harfusha Lab', 'Projects | Harfusha Lab',
            'Software, experimenty, nástroje a hry.', 'Software, experiments, tools and games.',
            '/projects', ['projects' => $this->content->projects()]);
    }

    public function project(): Response
    {
        $slug = $this->request->getParam('slug');
        if (!is_string($slug)) {
            throw new NotFoundException();
        }
        $project = $this->content->findProject($slug);
        if ($project === null) {
            throw new NotFoundException();
        }
        return $this->show('project', $project->name . ' | Harfusha Lab', $project->name . ' | Harfusha Lab',
            $project->summaryCs, $project->summaryEn,
            '/projects/' . $project->slug, ['project' => $project, 'related' => $this->content->relatedProjects($project->slug),
                'decisions' => array_values(array_filter($this->content->decisions(),
                    static fn ($decision): bool => $decision->project === $project->slug))]);
    }

    public function connections(): Response
    {
        return $this->show('connections', 'Mapa projektů | Harfusha Lab', 'Project network | Harfusha Lab',
            'Interaktivní mapa vazeb mezi projekty, nástroji a infrastrukturou.',
            'An interactive map of how my projects, tools and infrastructure fit together.',
            '/connections', ['projects' => $this->content->projects(), 'edges' => $this->content->connections()]);
    }

    public function homelab(): Response
    {
        return $this->show('homelab', 'Interaktivní homelab | Harfusha Lab', 'Interactive home lab | Harfusha Lab',
            'Prozkoumej síť, služby a simulaci výpadků.', 'Explore the network, services and failure simulations.',
            '/homelab', ['nodes' => $this->content->topology(), 'flows' => $this->content->flows()]);
    }

    public function playground(): Response
    {
        return $this->show('playground', 'Interaktivní playground | Harfusha Lab', 'Interactive playground | Harfusha Lab',
            'Funkční ukázky vývojových projektů.', 'Functional interactive project demos.', '/playground');
    }

    public function decisions(): Response
    {
        return $this->show('decisions', 'Technická rozhodnutí | Harfusha Lab', 'Engineering decisions | Harfusha Lab',
            'Důvody, kompromisy a ověřitelné limity návrhu skutečných projektů.',
            'Rationale, trade-offs and stated limitations behind real projects.',
            '/decisions', ['decisions' => $this->content->decisions()]);
    }

    public function maker(): Response
    {
        return $this->show('maker', 'Maker Lab | Harfusha Lab', 'Maker Lab | Harfusha Lab',
            '3D tisk, technické experimenty a fyzické prototypy.',
            '3D printing, hardware experiments and physical prototypes.',
            '/maker', ['items' => $this->content->maker()]);
    }

    public function openSource(): Response
    {
        return $this->show('open-source', 'Open Source | Harfusha Lab', 'Open Source | Harfusha Lab',
            'Reálné příspěvky do dalších projektů.', 'Real upstream contributions.', '/open-source',
            ['activity' => $this->content->activity()]);
    }

    public function now(): Response
    {
        return $this->show('now', 'Právě vzniká | Harfusha Lab', 'Now building | Harfusha Lab',
            'Doložené aktuální změny, vývojový deník a veřejná aktivita.',
            'Traceable current work, engineering notes and public repository activity.', '/now', [
                'recentRepos' => $this->content->publicRepoUpdates(),
                'journal' => array_slice($this->content->journal(), 0, 6),
                'provenance' => $this->content->githubProvenance(),
            ]);
    }

    public function journal(): Response
    {
        return $this->show('journal', 'Workshop log | Harfusha Lab', 'Workshop log | Harfusha Lab',
            'Co se právě vyvíjí a proč.', 'What is being built and why.', '/journal',
            ['journal' => $this->content->journal()]);
    }

    public function about(): Response
    {
        return $this->show('about', 'O mně | Harfusha Lab', 'About | Harfusha Lab',
            'Backendový vývojář, open-source přispěvatel a maker.', 'Backend developer, open-source contributor and maker.', '/about');
    }
}
