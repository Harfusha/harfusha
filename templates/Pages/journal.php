<?php
declare(strict_types=1);

use App\Domain\JournalFeed;
use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
/** @var list<array<string,mixed>> $journal */
/** @var list<array<string,mixed>>|null $allJournal */
/** @var int|null $journalPage */
/** @var int|null $journalTotalPages */
/** @var int|null $journalTotalCount */
/** @var string|null $selectedYear */
/** @var string|null $selectedProject */
/** @var string|null $searchQuery */

$sourceForCounts = $allJournal ?? $journal;
$journalPage = $journalPage ?? 1;
$journalTotalPages = $journalTotalPages ?? 1;
$journalTotalCount = $journalTotalCount ?? count($journal);
$selectedYear = $selectedYear ?? 'all';
$selectedProject = $selectedProject ?? 'all';
$searchQuery = $searchQuery ?? '';

$journalYears = [];
$journalProjects = [];
foreach ($sourceForCounts as $entry) {
    $year = substr((string)($entry['published_at'] ?? ''), 0, 4);
    if (preg_match('/^\d{4}$/D', $year) === 1) {
        $journalYears[$year] = ($journalYears[$year] ?? 0) + 1;
    }
    $proj = (string)($entry['project'] ?? '');
    if ($proj !== '') {
        $journalProjects[$proj] = ($journalProjects[$proj] ?? 0) + 1;
    }
}
krsort($journalYears, SORT_NUMERIC);
ksort($journalProjects);
?>
<section class="page-hero shell">
    <h1><?= $lang->value('Vývojový deník', 'Development journal') ?></h1>
    <p><?= $lang->value('Co jsem změnil, proč a jak jsem výsledek ověřil. Zápisky můžeš filtrovat podle roku, projektu nebo klíčového slova.', 'What I changed, why it mattered and how I checked the result. Filter by year, project, or keyword.') ?></p>
    <div class="page-hero-links">
        <a class="inline-link" href="<?= $lang->path('/now') ?>"><?= $lang->value('Poslední změny', 'Recent changes') ?> <span aria-hidden="true">↗</span></a>
    </div>
</section>

<section class="section shell journal-layout" data-journal-filter aria-labelledby="journal-archive-title">
    <div class="journal-controls">
        <h2 id="journal-archive-title"><?= $lang->value('Archiv zápisků', 'Journal archive') ?></h2>
        <form class="journal-server-filter" method="get" action="<?= $lang->path('/journal') ?>">
            <label class="journal-search" data-journal-search-control>
                <span><?= $lang->value('Hledat v zápiscích', 'Search entries') ?></span>
                <input type="search" name="q" value="<?= Html::escape($searchQuery) ?>" data-journal-search placeholder="<?= $lang->value('Projekt, změna nebo téma…', 'Project, change or topic…') ?>">
            </label>
            <div class="journal-project-filter" data-journal-project-control>
                <select name="project" data-journal-project aria-label="<?= $lang->value('Filtrovat podle projektu', 'Filter by project') ?>">
                    <option value="all"<?= $selectedProject === 'all' ? ' selected' : '' ?>><?= $lang->value('Všechny projekty', 'All projects') ?> (<?= count($sourceForCounts) ?>)</option>
                    <?php foreach ($journalProjects as $pSlug => $pCount): ?>
                    <option value="<?= Html::escape($pSlug) ?>"<?= $selectedProject === $pSlug ? ' selected' : '' ?>><?= Html::escape($pSlug) ?> (<?= $pCount ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($selectedYear !== 'all'): ?>
            <input type="hidden" name="year" value="<?= Html::escape($selectedYear) ?>">
            <?php endif; ?>
            <noscript>
                <button class="btn btn-ghost btn-sm" type="submit"><?= $lang->value('Filtrovat', 'Filter') ?></button>
            </noscript>
        </form>
        <div class="lab-tabs journal-year-filter" role="group" aria-label="<?= $lang->value('Filtrovat deník podle roku', 'Filter journal by year') ?>">
            <a class="tab<?= $selectedYear === 'all' ? ' active' : '' ?>" href="<?= Html::escape($lang->path('/journal')) ?>" data-journal-year="all"<?= $selectedYear === 'all' ? ' aria-current="page"' : '' ?>><?= $lang->value('Všechny roky', 'All years') ?> <span><?= count($sourceForCounts) ?></span></a>
            <?php foreach ($journalYears as $year => $count): ?>
            <a class="tab<?= $selectedYear === $year ? ' active' : '' ?>" href="<?= Html::escape($lang->path('/journal') . '?year=' . $year) ?>" data-journal-year="<?= Html::escape($year) ?>"<?= $selectedYear === $year ? ' aria-current="page"' : '' ?>><?= Html::escape($year) ?> <span><?= $count ?></span></a>
            <?php endforeach; ?>
        </div>
        <p class="journal-filter-count" data-journal-count role="status" aria-live="polite" aria-atomic="true"><?= $journalTotalCount ?> <?= $lang->value('zápisků', 'entries') ?></p>
        <a class="inline-link" href="<?= $lang->path('/feed.xml') ?>"><?= $lang->value('Odebírat přes RSS', 'Follow via RSS') ?> <span aria-hidden="true">↗</span></a>
    </div>

    <div class="journal-timeline">
        <p class="empty-state" data-journal-empty<?= $journal !== [] ? ' hidden' : '' ?>><?= $lang->value('Pro tenhle výběr nejsou žádné zápisky.', 'No entries match this selection.') ?></p>
        <?php foreach ($journal as $entry):
            $source = Html::safeUrl((string)($entry['source_url'] ?? ''));
            $entryId = JournalFeed::entryId($entry);
            $legacyId = !isset($entry['id']) && is_string($entry['published_at'] ?? null) && is_string($entry['title_en'] ?? null)
                ? JournalFeed::legacyTitleHashId($entry)
                : null;
            $date = substr((string)($entry['published_at'] ?? ''), 0, 10);
            $category = (string)($entry['category'] ?? '');
            $projectSlug = (string)($entry['project'] ?? '');
            $category = $lang->code === 'cs' ? match ($category) {
                'Desktop' => 'Desktopové nástroje',
                'Games' => 'Hry',
                'Music' => 'Hudba',
                'Infrastructure' => 'Infrastruktura',
                default => $category,
            } : $category;
        ?>
        <article class="timeline-item" id="<?= Html::escape($entryId) ?>" data-journal-entry data-year="<?= Html::escape(substr($date, 0, 4)) ?>" data-project="<?= Html::escape($projectSlug) ?>"<?= $legacyId !== null && $legacyId !== $entryId ? ' data-legacy-id="' . Html::escape($legacyId) . '"' : '' ?>>
            <?php if ($legacyId !== null && $legacyId !== $entryId): ?>
            <span id="<?= Html::escape($legacyId) ?>" class="visually-hidden" aria-hidden="true"></span>
            <?php endif; ?>
            <header class="journal-entry-meta">
                <time datetime="<?= Html::escape($date) ?>"><?= Html::escape($date) ?></time>
                <?php if ($projectSlug !== '' && $projectSlug !== 'open-source'): ?>
                <a class="journal-project-tag" href="<?= $lang->path('/projects/' . $projectSlug) ?>"><?= Html::escape($projectSlug) ?> ↗</a>
                <?php endif; ?>
                <span class="category"><?= Html::escape($category) ?></span>
            </header>
            <div class="journal-title-row">
                <h3><a href="#<?= Html::escape($entryId) ?>"><?= Html::escape((string)$entry['title_' . $lang->code]) ?></a></h3>
                <button type="button" class="anchor-copy-btn" data-copy-anchor="<?= Html::escape($entryId) ?>" aria-label="<?= $lang->value('Kopírovat odkaz na zápisek', 'Copy link to entry') ?>" title="<?= $lang->value('Kopírovat odkaz', 'Copy link') ?>"><span class="anchor-icon" aria-hidden="true">#</span></button>
            </div>
            <p><?= Html::escape((string)$entry['body_' . $lang->code]) ?></p>
            <?php if ($source !== '#'): ?>
                <?php if (str_starts_with($source, 'http')): ?>
                <a class="inline-link" href="<?= Html::escape($source) ?>" target="_blank" rel="noopener noreferrer"><?= $lang->value('Původní podklad', 'Original source') ?> <span aria-hidden="true">↗</span></a>
                <?php else: ?>
                <a class="inline-link" href="<?= $lang->path($source) ?>"><?= $lang->value('Doloženo v případové studii', 'Documented in case study') ?> <span aria-hidden="true">↗</span></a>
                <?php endif; ?>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>

        <?php if ($journalTotalPages > 1): ?>
        <nav class="journal-pagination" aria-label="<?= $lang->value('Stránkování deníku', 'Journal pagination') ?>">
            <?php if ($journalPage > 1): ?>
                <a class="btn btn-ghost btn-sm" href="<?= Html::escape($lang->path('/journal') . '?page=' . ($journalPage - 1)) ?>"><?= $lang->value('← Novější', '← Newer') ?></a>
            <?php endif; ?>
            <span><?= $lang->value('Strana ' . $journalPage . ' z ' . $journalTotalPages, 'Page ' . $journalPage . ' of ' . $journalTotalPages) ?></span>
            <?php if ($journalPage < $journalTotalPages): ?>
                <a class="btn btn-ghost btn-sm" href="<?= Html::escape($lang->path('/journal') . '?page=' . ($journalPage + 1)) ?>"><?= $lang->value('Starší →', 'Older →') ?></a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
    </div>
</section>
