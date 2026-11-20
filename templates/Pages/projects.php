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
    <p><?= $lang->value('Vývojářské nástroje, domácí infrastruktura a experimenty s 3D modely. U každého projektu najdeš problém, zvolený postup a stav ověření.', 'Developer tools, home infrastructure and experiments with 3D models. Each project explains the problem, the approach and what I’ve verified.') ?></p>
    <div class="page-hero-links">
        <a class="inline-link" href="<?= $lang->path('/connections') ?>"><?= $lang->value('Co je ověřené', 'What’s been verified') ?> <span aria-hidden="true">↗</span></a>
        <a class="inline-link" href="<?= $lang->path('/decisions') ?>"><?= $lang->value('Proč jsem zvolil tento postup', 'Why I chose these approaches') ?> <span aria-hidden="true">↗</span></a>
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
        <h2 id="catalog-next-title"><?= $lang->value('Co už je ověřené?', 'What has been verified?') ?></h2>
        <p><?= $lang->value('Přehled výsledků ukazuje, co je doložené ve zdroji, co běží jako model a co ještě potřebuje ověření.', 'The results show what is documented in source, what runs as a model and what still needs verification.') ?></p>
    </div>
    <a class="inline-link" href="<?= $lang->path('/connections') ?>"><?= $lang->value('Porovnat výsledky', 'Compare results') ?> <span aria-hidden="true">↗</span></a>
</section>
