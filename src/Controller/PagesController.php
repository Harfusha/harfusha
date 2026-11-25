<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\EngineeringDecision;
use App\Domain\JournalFeed;
use App\Domain\Project;
use App\Service\ContentRepository;
use App\Support\Language;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Normalizer;

final class PagesController extends AppController
{
    private const int JOURNAL_PER_PAGE = 60;

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
            str_starts_with($route, '/projects/') ||
            str_starts_with($route, '/journal/')
        ) {
            $this->response = $this->response->withHeader('Cache-Control', 'no-store');
        }
        $this->set([...$data,
            'lang' => $lang,
            'route' => $route,
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
     * Render the localized landing page using a single atomic GitHub snapshot (N-DATA-03).
     */
    public function home(ContentRepository $content): Response
    {
        $projects = $content->projects();
        $featured = array_values(array_filter(
            $projects,
            static fn(Project $p): bool => $p->featured,
        ));
        $snapshot = $content->githubSnapshot();
        $mergedPrCount = count(array_filter(
            $snapshot->activity,
            static fn(array $entry): bool => ($entry['kind'] ?? null) === 'pr'
                && ($entry['state'] ?? null) === 'merged',
        ));

        return $this->show(
            'home',
            'Adam Halfar | PHP backend a vývojářské nástroje',
            'Adam Halfar | PHP backend and developer tools',
            'Portfolio Adama Halfara: backendový vývoj v PHP a CakePHP, vývojářské nástroje, ' .
            'příspěvky do open-source knihoven a provozní automatizace.',
            'Adam Halfar’s portfolio: PHP and CakePHP backend engineering, developer tools, ' .
            'open-source contributions, and operations automation.',
            '/',
            [
                'projects' => array_slice($featured, 0, 4),
                'projectCount' => count($projects),
                'mergedPrCount' => $mergedPrCount,
                'journal' => array_slice($content->journal(), 0, 3),
                'recentRepos' => $snapshot->recentRepos,
                'provenance' => $snapshot->provenance->toArray(),
                'evidenceRegistry' => $content->evidence(),
            ],
        );
    }

    /**
     * List publicly visible projects.
     */
    public function projects(ContentRepository $content): Response
    {
        return $this->show(
            'projects',
            'Projekty | Harfusha',
            'Projects | Harfusha',
            'Případové studie vývojářských nástrojů, infrastruktury a technických prototypů.',
            'Case studies in developer tools, infrastructure and engineering prototypes.',
            '/projects',
            [
                'projects' => $content->projects(),
                'evidenceRegistry' => $content->evidence(),
            ],
        );
    }

    /**
     * Display a public project case study.
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

        return $this->show(
            'project',
            $project->name . ' | Harfusha Lab',
            $project->name . ' | Harfusha Lab',
            $project->summaryCs,
            $project->summaryEn,
            '/projects/' . $project->slug,
            [
                'project' => $project,
                'related' => $content->relatedProjects($project->slug),
                'decisions' => array_values(array_filter(
                    $content->decisions(),
                    static fn(EngineeringDecision $decision): bool => $decision->project === $project->slug,
                )),
                'evidence' => $content->projectEvidence($project->slug),
            ],
        );
    }

    /**
     * Render the independent project index.
     */
    public function connections(ContentRepository $content): Response
    {
        return $this->show(
            'connections',
            'Skupiny projektů | Harfusha',
            'Project groups | Harfusha',
            'Tematický přehled projektů a jejich současné fáze.',
            'A thematic index of projects and their current stages.',
            '/connections',
            ['projects' => $content->projects()],
        );
    }

    /**
     * Display the sanitized homelab topology and its simulations.
     */
    public function homelab(ContentRepository $content): Response
    {
        return $this->show(
            'homelab',
            'Mapa závislostí domácí sítě | Harfusha',
            'Home network dependency map | Harfusha',
            'Prozkoumej dokumentovaná zařízení, služby a dopady výpadků.',
            'Explore documented devices, services and outage impacts.',
            '/homelab',
            ['nodes' => $content->topology(), 'flows' => $content->flows()],
        );
    }

    /**
     * Keep old playground links useful by redirecting to the maintained network map.
     */
    public function playground(): Response
    {
        $code = $this->request->getParam('lang');
        $lang = is_string($code) && $code === 'en' ? 'en' : 'cs';

        return $this->redirect('/' . $lang . '/homelab', 301) ?? $this->response;
    }

    /**
     * Show the documented engineering trade-offs.
     */
    public function decisions(ContentRepository $content): Response
    {
        return $this->show(
            'decisions',
            'Technická rozhodnutí | Harfusha Lab',
            'Engineering decisions | Harfusha Lab',
            'Důvody, kompromisy a ověřitelné limity návrhu skutečných projektů.',
            'Rationale, trade-offs and stated limitations behind real projects.',
            '/decisions',
            ['decisions' => $content->decisions()],
        );
    }

    /**
     * Render the independent 3D scanning and print-preparation projects.
     */
    public function maker(): Response
    {
        return $this->show(
            'maker',
            '3D skeny a příprava tisku | Harfusha',
            '3D Scanning and Print Preparation | Harfusha',
            'Dva samostatné projekty: rekonstrukce CAD modelu ze skenu a návrh služby pro poptávky 3D tisku.',
            'Two separate projects: CAD reconstruction from a scan and a proposed service for 3D-print requests.',
            '/maker',
            [],
        );
    }

    /**
     * Show public upstream contributions.
     */
    public function openSource(ContentRepository $content): Response
    {
        $snapshot = $content->githubSnapshot();

        return $this->show(
            'open_source',
            'Open-source příspěvky | Harfusha Lab',
            'Open Source | Harfusha Lab',
            'Vybrané pull requesty a hlášení chyb do open-source projektů.',
            'Selected pull requests and bug reports in open-source projects.',
            '/open-source',
            [
                'activity' => $snapshot->activity,
                'provenance' => $snapshot->provenance->toArray(),
            ],
        );
    }

    /**
     * Show the current engineering workbench.
     */
    public function now(ContentRepository $content): Response
    {
        $snapshot = $content->githubSnapshot();

        return $this->show(
            'now',
            'Co se změnilo | Harfusha',
            'What’s Changed | Harfusha',
            'Přehled posledních zaznamenaných změn veřejných repozitářů.',
            'A view of the latest recorded changes in public repositories.',
            '/now',
            [
                'recentRepos' => $snapshot->recentRepos,
                'journalEntries' => $content->journal(),
                'activity' => $snapshot->activity,
                'provenance' => $snapshot->provenance->toArray(),
            ],
        );
    }

    /**
     * Display the published engineering journal with pagination and no-JS GET filters.
     */
    public function journal(ContentRepository $content): Response
    {
        $allEntries = $content->journal();
        $yearParam = $this->request->getQuery('year', 'all');
        $projectParam = $this->request->getQuery('project', 'all');
        $queryParam = $this->request->getQuery('q', '');
        $pageParam = $this->request->getQuery('page', '1');

        $selectedYear = is_string($yearParam) && preg_match('/^\d{4}$/D', $yearParam) === 1 ? $yearParam : 'all';
        $selectedProject = is_string($projectParam) && preg_match('/^[a-z0-9-]+$/D', $projectParam) === 1
            ? $projectParam : 'all';
        $searchQuery = is_string($queryParam) ? trim(mb_substr($queryParam, 0, 120, 'UTF-8')) : '';
        $normalizedQuery = self::foldDiacritics($searchQuery);

        $filtered = array_values(array_filter(
            $allEntries,
            static function (array $entry) use ($selectedYear, $selectedProject, $normalizedQuery): bool {
                $publishedAt = is_string($entry['published_at'] ?? null) ? $entry['published_at'] : '';
                $entryYear = substr($publishedAt, 0, 4);
                if ($selectedYear !== 'all' && $entryYear !== $selectedYear) {
                    return false;
                }
                $entryProject = is_string($entry['project'] ?? null) ? $entry['project'] : '';
                if ($selectedProject !== 'all' && $entryProject !== $selectedProject) {
                    return false;
                }
                if ($normalizedQuery !== '') {
                    $titleCs = is_string($entry['title_cs'] ?? null) ? $entry['title_cs'] : '';
                    $titleEn = is_string($entry['title_en'] ?? null) ? $entry['title_en'] : '';
                    $bodyCs = is_string($entry['body_cs'] ?? null) ? $entry['body_cs'] : '';
                    $bodyEn = is_string($entry['body_en'] ?? null) ? $entry['body_en'] : '';
                    $category = is_string($entry['category'] ?? null) ? $entry['category'] : '';
                    $haystack = self::foldDiacritics(
                        $titleCs . ' ' . $titleEn . ' ' . $bodyCs . ' ' . $bodyEn . ' ' . $category . ' ' . $entryProject,
                    );
                    if (!str_contains($haystack, $normalizedQuery)) {
                        return false;
                    }
                }

                return true;
            },
        ));

        $totalFiltered = count($filtered);
        $totalPages = max(1, (int)ceil($totalFiltered / self::JOURNAL_PER_PAGE));
        $page = is_string($pageParam) && ctype_digit($pageParam) ? max(1, min($totalPages, (int)$pageParam)) : 1;
        $pagedEntries = array_slice($filtered, ($page - 1) * self::JOURNAL_PER_PAGE, self::JOURNAL_PER_PAGE);

        return $this->show(
            'journal',
            'Vývojový deník | Harfusha Lab',
            'Development Journal | Harfusha Lab',
            'Poznámky k opravám, novým funkcím a technickým rozhodnutím.',
            'Notes on fixes, new features and engineering decisions.',
            '/journal',
            [
                'allJournal' => $allEntries,
                'journal' => $pagedEntries,
                'journalPage' => $page,
                'journalTotalPages' => $totalPages,
                'journalTotalCount' => $totalFiltered,
                'selectedYear' => $selectedYear,
                'selectedProject' => $selectedProject,
                'searchQuery' => $searchQuery,
            ],
        );
    }

    /**
     * Resolve a permanent individual journal entry URL (supports both stable IDs and legacy title-hash IDs).
     */
    public function journalEntry(ContentRepository $content): Response
    {
        $entryId = $this->request->getParam('entryId');
        if (!is_string($entryId)) {
            throw new NotFoundException();
        }
        $lang = $this->lang();
        foreach ($content->journal() as $entry) {
            $primaryId = JournalFeed::entryId($entry);
            $isLegacyMatch = false;
            if (!isset($entry['id']) && is_string($entry['published_at'] ?? null) && is_string($entry['title_en'] ?? null)) {
                $isLegacyMatch = JournalFeed::legacyTitleHashId($entry) === $entryId;
            }
            if ($primaryId === $entryId || $isLegacyMatch) {
                $titleCs = is_string($entry['title_cs'] ?? null) ? $entry['title_cs'] : '';
                $titleEn = is_string($entry['title_en'] ?? null) ? $entry['title_en'] : '';
                $bodyCs = is_string($entry['body_cs'] ?? null) ? $entry['body_cs'] : '';
                $bodyEn = is_string($entry['body_en'] ?? null) ? $entry['body_en'] : '';
                $entryTitle = $lang->value($titleCs, $titleEn);
                $entryBody = $lang->value($bodyCs, $bodyEn);

                return $this->show(
                    'journal_entry',
                    $entryTitle . ' | Vývojový deník',
                    $entryTitle . ' | Development Journal',
                    mb_substr($entryBody, 0, 160, 'UTF-8'),
                    mb_substr($entryBody, 0, 160, 'UTF-8'),
                    '/journal/' . $primaryId,
                    ['entry' => $entry, 'entryId' => $primaryId],
                );
            }
        }

        throw new NotFoundException();
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
            'Adam Halfar, backendový vývojář se zaměřením na PHP a CakePHP.',
            'Adam Halfar, a backend developer focused on PHP and CakePHP.',
            '/about',
        );
    }

    /**
     * Fold Unicode accents and lowercase for diacritic-insensitive Czech/English search (UX07).
     */
    private static function foldDiacritics(string $value): string
    {
        if ($value === '') {
            return '';
        }
        $decomposed = Normalizer::normalize($value, Normalizer::FORM_D);
        $stripped = is_string($decomposed)
            ? preg_replace('/\p{Mn}+/u', '', $decomposed)
            : $value;

        return mb_strtolower(is_string($stripped) ? $stripped : $value, 'UTF-8');
    }
}
