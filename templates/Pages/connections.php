<?php
declare(strict_types=1);
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var list<App\Domain\Project> $projects */
/** @var list<array{from:string,to:string,type:string}> $edges */
?>
<section class="page-hero shell"><span class="eyebrow">SYSTEMS / RELATIONSHIPS</span><h1><?= $lang->value('Jak do sebe projekty zapadají.', 'How the projects fit together.') ?></h1>
<p><?= $lang->value('Klikni na uzel a zjisti, které moje projekty, nástroje a domácí systémy spolupracují. Tato mapa je kurátorovaný model, nikoli živý monitoring.', 'Select a node and explore how my projects, tools and home infrastructure support each other. This is a curated model, not live monitoring.') ?></p>
<div class="page-meta">INTERACTIVE <span>·</span> SELECT A PROJECT <span>·</span> CURATED RELATIONSHIPS</div></section>
<section class="section shell" data-connections data-lang="<?= Html::escape($lang->code) ?>">
<div class="filter-bar"><label class="search-field"><span class="visually-hidden"><?= $lang->value('Hledat uzel','Find node') ?></span><span aria-hidden="true">⌕</span><input type="search" data-connection-search placeholder="<?= $lang->value('Najít projekt…','Find a project…') ?>" autocomplete="off"></label><button type="button" class="mini-button" data-connections-reset><?= $lang->value('Reset výběru','Reset selection') ?></button></div>
<p class="graph-scroll-hint"><?= $lang->value('Mapu můžeš posouvat do stran.', 'Scroll sideways to explore the map.') ?></p>
<div class="connection-layout"><div class="connection-canvas-wrap" tabindex="0" role="region" aria-label="<?= $lang->value('Posuvná mapa projektů', 'Scrollable project map') ?>"><svg viewBox="0 0 1060 680" data-connections-svg class="connections-svg" role="group" aria-label="<?= $lang->value('Interaktivní mapa projektů','Interactive project graph') ?>"></svg></div>
<aside class="lab-inspector connection-inspector" aria-live="polite"><span class="eyebrow">PROJECT INSPECTOR</span><h2 data-connections-name>—</h2><p data-connections-description><?= $lang->value('Klikni na uzel grafu a zobraz jeho vztahy.', 'Select a graph node to inspect its relationships.') ?></p><div class="lab-inspector-group"><span class="eyebrow">CONNECTED PROJECTS</span><ul class="service-list" data-connections-links></ul></div><a class="btn btn-primary hidden" data-connections-open href="#"><?= $lang->value('Otevřít případovou studii','Open case study') ?> ↗</a></aside></div>
<script type="application/json" id="connections-data"><?= Html::json(['nodes' => array_map(fn(App\Domain\Project $p): array => ['id'=>$p->slug, 'name'=>$p->name, 'category'=>App\Domain\Project::categoryLabel($p->category, $lang), 'description_cs'=>$p->summaryCs, 'description_en'=>$p->summaryEn], $projects), 'edges' => array_map(fn(array $edge): array => $edge + ['label' => App\Domain\ProjectConnections::label($edge['type'], $lang)], $edges), 'base' => $lang->path('/projects')]) ?></script>
</section>
