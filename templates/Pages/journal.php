<?php
declare(strict_types=1);

use App\Domain\JournalFeed;
use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
/** @var list<array<string,mixed>> $journal */
$journalYears = [];
$journalProjects = [];
foreach ($journal as $entry) {
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
    <p><?= $lang->value('Co jsem změnil, proč a jak jsem výsledek ověřil. Zápisky můžeš filtrovat podle roku nebo vyhledat podle projektu.', 'What I changed, why it mattered and how I checked the result. Browse by year or search for a project.') ?></p>
    <div class="page-hero-links">
        <a class="inline-link" href="<?= $lang->path('/now') ?>"><?= $lang->value('Související změny na GitHubu', 'Related changes on GitHub') ?> <span aria-hidden="true">↗</span></a>
    </div>
</section>

<section class="section shell journal-layout" data-journal-filter aria-labelledby="journal-archive-title">
    <div class="journal-controls">
        <h2 id="journal-archive-title"><?= $lang->value('Archiv zápisků', 'Journal archive') ?></h2>
        <label class="journal-search" data-journal-search-control hidden>
            <span><?= $lang->value('Hledat v zápiscích', 'Search entries') ?></span>
            <input type="search" data-journal-search placeholder="<?= $lang->value('Projekt, změna nebo téma…', 'Project, change or topic…') ?>">
        </label>
        <div class="journal-project-filter" data-journal-project-control hidden>
            <select data-journal-project aria-label="<?= $lang->value('Filtrovat podle projektu', 'Filter by project') ?>">
                <option value="all"><?= $lang->value('Všechny projekty', 'All projects') ?> (<?= count($journal) ?>)</option>
                <?php foreach ($journalProjects as $pSlug => $pCount): ?>
                <option value="<?= Html::escape($pSlug) ?>"><?= Html::escape($pSlug) ?> (<?= $pCount ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="lab-tabs journal-year-filter" role="group" aria-label="<?= $lang->value('Filtrovat deník podle roku', 'Filter journal by year') ?>">
            <button class="tab active" type="button" data-journal-year="all" aria-pressed="true"><?= $lang->value('Všechny roky', 'All years') ?> <span><?= count($journal) ?></span></button>
            <?php foreach ($journalYears as $year => $count): ?>
            <button class="tab" type="button" data-journal-year="<?= Html::escape($year) ?>" aria-pressed="false"><?= Html::escape($year) ?> <span><?= $count ?></span></button>
            <?php endforeach; ?>
        </div>
        <p class="journal-filter-count" data-journal-count role="status" aria-live="polite" aria-atomic="true"><?= count($journal) ?> <?= $lang->value('zápisků', 'entries') ?></p>
        <a class="inline-link" href="<?= $lang->path('/feed.xml') ?>"><?= $lang->value('Odebírat přes RSS', 'Follow via RSS') ?> <span aria-hidden="true">↗</span></a>
    </div>

    <div class="journal-timeline">
        <p class="empty-state" data-journal-empty<?= $journal !== [] ? ' hidden' : '' ?>><?= $lang->value('Pro tenhle výběr nejsou žádné zápisky.', 'No entries match this selection.') ?></p>
        <?php foreach ($journal as $entry):
            $source = Html::safeUrl((string)($entry['source_url'] ?? ''));
            $entryId = JournalFeed::entryId($entry);
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
        <article class="timeline-item" id="<?= Html::escape($entryId) ?>" data-journal-entry data-year="<?= Html::escape(substr($date, 0, 4)) ?>" data-project="<?= Html::escape($projectSlug) ?>">
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
    </div>
</section>
