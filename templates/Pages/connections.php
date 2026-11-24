<?php
declare(strict_types=1);
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var list<App\Domain\Project> $projects */
$projectBySlug = [];
foreach ($projects as $project) {
    $projectBySlug[$project->slug] = $project;
}
$themes = [
    'operate' => ['title' => $lang->value('Vývojářské nástroje a provoz', 'Developer tools and operations'), 'description' => $lang->value('Indexování kódu, domácí infrastruktura a automatizace.', 'Code indexing, home infrastructure and automation.'), 'slugs' => ['local-ai-hub', 'home-infrastructure', 'discord-bot']],
    'candidate' => ['title' => $lang->value('Modelování a výroba', 'Modeling and making'), 'description' => $lang->value('Generování světa, rekonstrukce CAD modelů ze skenů a návrh procesu 3D tisku.', 'World generation, scan-to-CAD reconstruction and a proposed 3D-print workflow.'), 'slugs' => ['scan2cad', 'tiskov', 'woodbound']],
    'coordinate' => ['title' => $lang->value('Sdílený stav a synchronizace', 'Shared state and synchronization'), 'description' => $lang->value('Předávání změn mezi zařízeními, lidmi nebo herními klienty.', 'Changes shared across devices, people or game clients.'), 'slugs' => ['mutaui', 'shpotify', 'softwareinc-multiplayer']],
];
$states = [
    'active' => $lang->value('Aktivní vývoj', 'Active development'),
    'beta' => $lang->value('Beta', 'Beta'),
    'prototype' => $lang->value('Prototyp', 'Prototype'),
    'proposal' => $lang->value('Návrh', 'Proposal'),
    'record' => $lang->value('Provozní záznam', 'Operations record'),
];
?>
<section class="page-hero shell project-map-hero">
  <h1><?= $lang->value('Tři technické okruhy a společné inženýrské problémy', 'Three engineering themes and shared technical challenges') ?></h1>
  <p><?= $lang->value('Každá skupina řeší odlišný typ spolehlivosti: od dohledání zdrojového kódu přes synchronizaci a simulaci výpadků sítě až po fyzický 3D tisk.', 'Each group tackles a different aspect of reliability: from source code traceability and daemon sync to network failure simulation and physical 3D manufacturing.') ?></p>
</section>
<section class="section shell project-map" data-project-map-filter aria-label="<?= $lang->value('Tematické skupiny projektů', 'Project groups') ?>">
  <div class="project-map-catalog">
  <div class="project-map-choices">
    <label><span><?= $lang->value('Hledat projekt nebo problém', 'Search projects or problems') ?></span><input type="search" data-map-search placeholder="<?= $lang->value('Např. index, fronta, zálohy…', 'For example: index, queue, backups…') ?>"></label>
    <label><span><?= $lang->value('Tematický okruh', 'Thematic group') ?><select data-map-category><option value="all"><?= $lang->value('Všechny okruhy', 'All themes') ?></option><?php foreach ($themes as $value => $theme): ?><option value="<?= Html::escape($value) ?>"><?= Html::escape($theme['title']) ?></option><?php endforeach; ?></select></span></label>
    <label><span><?= $lang->value('Fáze projektu', 'Project stage') ?><select data-map-state><option value="all"><?= $lang->value('Všechny fáze', 'All stages') ?></option><?php foreach ($states as $value => $label): ?><option value="<?= Html::escape($value) ?>"><?= Html::escape($label) ?></option><?php endforeach; ?></select></span></label>
  </div>
  <p class="project-map-status" data-map-status data-label="<?= $lang->value('Zobrazené projekty:','Projects shown:') ?>" role="status" aria-live="polite" aria-atomic="true"><?= count($projects) ?> <?= $lang->value('zobrazených projektů','projects shown') ?></p>
  <div class="project-map-domains">
    <?php foreach ($themes as $themeId => $theme): $sectionId = 'map-theme-' . $themeId; ?>
    <section class="project-map-domain" data-map-domain="<?= Html::escape($sectionId) ?>" aria-labelledby="<?= Html::escape($sectionId) ?>">
      <header class="project-map-domain-heading"><h2 id="<?= Html::escape($sectionId) ?>"><?= Html::escape($theme['title']) ?></h2><p><?= Html::escape($theme['description']) ?></p></header>
      <div class="project-map-list">
      <?php foreach ($theme['slugs'] as $slug): if (!isset($projectBySlug[$slug])) continue; $project = $projectBySlug[$slug]; $searchText = $project->name . ' ' . $project->category . ' ' . $project::categoryLabel($project->category, $lang) . ' ' . $project->summary($lang) . ' ' . $project->problem($lang) . ' ' . $project->solution($lang); ?>
        <article class="project-map-item" id="map-project-<?= Html::escape($slug) ?>" data-map-project data-theme="<?= Html::escape($themeId) ?>" data-state="<?= Html::escape($project->status) ?>" data-search="<?= Html::escape($searchText) ?>">
          <div class="project-map-item-heading"><span class="category"><?= Html::escape($project::categoryLabel($project->category, $lang)) ?></span><span class="status <?= Html::escape($project->status) ?>"><?= Html::escape($project->statusLabel($lang)) ?></span></div>
          <h3><a href="<?= $lang->path('/projects/' . $project->slug) ?>"><?= Html::escape($project->name) ?></a></h3>
          <p class="project-map-summary"><?= Html::escape($project->summary($lang)) ?></p>
          <details class="project-map-reason">
            <summary><?= $lang->value('Problém a přístup', 'Problem and approach') ?></summary>
            <p class="project-map-hard project-map-problem"><span><?= $lang->value('Problém', 'Problem') ?></span><?= Html::escape($project->problem($lang)) ?></p>
            <p class="project-map-hard project-map-solution"><span><?= $lang->value('Přístup', 'Approach') ?></span><?= Html::escape($project->solution($lang)) ?></p>
          </details>
          <a class="inline-link" href="<?= $lang->path('/projects/' . $project->slug) ?>"><?= sprintf($lang->value('Případová studie %s', 'Case study: %s'), Html::escape($project->name)) ?> →</a>
        </article>
      <?php endforeach; ?>
      </div>
    </section>
    <?php endforeach; ?>
  </div>
  </div>
  <p class="project-map-empty" data-map-empty hidden><?= $lang->value('Žádný projekt tomuto filtru neodpovídá. Zkus jiný název, okruh nebo fázi.', 'No projects match these filters. Try another name, theme or stage.') ?></p>
</section>
