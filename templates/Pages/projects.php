<?php
declare(strict_types=1);

use App\Domain\Project;
use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
/** @var list<Project> $projects */
$categories = array_unique(array_map(static fn (Project $project): string => $project->category, $projects));
?>
<section class="page-hero shell">
    <h1><?= $lang->value('Projekty', 'Projects') ?></h1>
    <p><?= $lang->value('Výběr nástrojů, beta aplikace, prototypů i návrhů. U každého projektu je uvedený jeho současný stav a doložený rozsah.', 'A selection of tools, a beta app, prototypes and proposals. Each project states its current status and documented scope.') ?></p>
    <div class="page-hero-links">
        <a class="inline-link" href="<?= $lang->path('/connections') ?>"><?= $lang->value('Skupiny projektů', 'Project groups') ?> <span aria-hidden="true">↗</span></a>
        <a class="inline-link" href="<?= $lang->path('/decisions') ?>"><?= $lang->value('Technická rozhodnutí k projektům', 'Engineering decisions behind projects') ?> <span aria-hidden="true">↗</span></a>
    </div>
</section>

<section class="section shell" data-project-browser>
    <div class="filter-bar">
        <label class="search-field">
            <span class="visually-hidden"><?= $lang->value('Hledat projekt', 'Search projects') ?></span>
            <span aria-hidden="true">⌕</span>
            <input type="search" placeholder="<?= $lang->value('Hledat podle názvu nebo technologie…', 'Search by name or technology…') ?>" data-search autocomplete="off">
        </label>
        <select data-filter aria-label="<?= $lang->value('Kategorie', 'Category') ?>">
            <option value="all"><?= $lang->value('Všechny kategorie', 'All categories') ?></option>
            <?php foreach ($categories as $category): ?>
            <option value="<?= Html::escape(strtolower($category)) ?>"><?= Html::escape(Project::categoryLabel($category, $lang)) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-ghost btn-sm" type="button" data-filter-reset hidden><?= $lang->value('Vymazat filtry', 'Clear filters') ?></button>
        <span class="filter-count" data-count role="status" aria-live="polite" aria-atomic="true"><?= count($projects) ?> <?= $lang->value('projektů', 'projects') ?></span>
    </div>
    <div class="project-grid" data-results>
        <?php foreach ($projects as $project): ?>
            <?php require dirname(__DIR__) . '/element/portfolio_project_card.php'; ?>
        <?php endforeach; ?>
    </div>
    <p class="empty-state hidden" data-empty><?= $lang->value('Žádný projekt neodpovídá hledání.', 'No projects match your search.') ?></p>
</section>
<section class="shell section reading-footer" aria-labelledby="catalog-next-title">
    <div>
        <h2 id="catalog-next-title"><?= $lang->value('Jak číst stav projektů', 'How to read project status') ?></h2>
        <p><?= $lang->value('Veřejný zdroj, beta, prototyp, návrh a provozní záznam nejsou stejná úroveň důkazu. Rozsah i omezení uvádí stránka každého projektu.', 'Public source, beta, prototype, proposal and operations record are different levels of evidence. Each project page states its scope and limits.') ?></p>
    </div>
    <a class="inline-link" href="<?= $lang->path('/connections') ?>"><?= $lang->value('Prohlédnout tematické skupiny', 'Browse topic groups') ?> <span aria-hidden="true">↗</span></a>
</section>
