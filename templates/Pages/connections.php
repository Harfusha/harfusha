<?php
declare(strict_types=1);
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var list<App\Domain\Project> $projects */
$projectBySlug = [];
$categories = [];
foreach ($projects as $project) {
    $projectBySlug[$project->slug] = $project;
    $categories[$project->category] = $project::categoryLabel($project->category, $lang);
}
asort($categories, SORT_NATURAL | SORT_FLAG_CASE);
$states = [
    'active' => $lang->value('Aktivní vývoj', 'In active development'),
    'prototype' => $lang->value('Prototyp', 'Prototype'),
    'maintenance' => $lang->value('Údržba', 'Maintenance'),
];
$notes = [
    'local-ai-hub' => [$lang->value('Ještě před odpovědí modelu musí být jasné, který soubor se změnil a jaký důkaz z něj lze vrátit.', 'Before a model answers, we need to know which file changed and what evidence can be returned from it.'), $lang->value('Preprocessing počítá otisky a připravuje indexy po navazujících fázích. RAG ani model nepřebíjí zdrojový kód.', 'Preprocessing computes fingerprints and prepares indexes in dependent phases. Neither RAG nor a model outranks source code.'), $lang->value('Před modelovým dotazem', 'Before the model call')],
    'mutaui' => [$lang->value('Při synchronizaci chci před kliknutím vědět směr změny a vidět konflikt.', 'Before syncing, I want to know which way changes will go and see conflicts.'), $lang->value('MutaUI ovládá Mutagen CLI; nepíše vlastní synchronizační engine.', 'MutaUI controls Mutagen CLI; it does not implement another sync engine.'), $lang->value('Před zápisem', 'Before a write')],
    'scan2cad' => [$lang->value('Sken ukáže povrch součásti. Z výsledné geometrie ale sám neurčí správné plochy ani rozměry.', 'A scan captures a part surface. It does not decide the final faces or dimensions by itself.'), $lang->value('Měřítko, registrace a fotografie pomohou sestavit kandidáta; kontrola modelu zůstává nutná.', 'Scale, registration and photos support a candidate; the model still needs review.'), $lang->value('Co sken neřekne', 'What the scan cannot tell me')],
    'home-infrastructure' => [$lang->value('Když vypadne hlavní Pi, DNS a několik aplikací zmizí zároveň. NAS je další samostatná větev.', 'If the main Pi fails, DNS and several apps go down together. The NAS is a separate branch.'), $lang->value('Důležitá je hranice mezi hostem, službami na něm a daty uloženými mimo něj.', 'The important boundary is between a host, its services and data stored elsewhere.'), $lang->value('Kde se výpadek projeví', 'Where an outage shows up')],
    'shpotify' => [$lang->value('Dva lidé mohou upravit stejnou verzi fronty. Druhý zápis nesmí tiše přepsat první.', 'Two people can edit the same queue version. The second save must not silently overwrite the first.'), $lang->value('Zdroj popisuje verzování a idempotentní změny; přehrávání celé cesty ještě není ověřené.', 'The source describes versioning and idempotent updates; end-to-end playback is not verified.'), $lang->value('Při souběhu', 'When edits overlap')],
    'softwareinc-multiplayer' => [$lang->value('Hra už přenáší stav. Další vrstva má smysl jen tehdy, když přidá jasná pravidla pro dohody.', 'The game already moves state between players. Another layer is useful only if it adds clear agreement rules.'), $lang->value('Nadstavba existuje ve zdroji; přijetí jako funkčního multiplayerového modu doložené není.', 'The source describes an extension; accepted multiplayer behavior is not established.'), $lang->value('Co musí přidat mod', 'What the mod must add')],
    'woodbound' => [$lang->value('Vygenerovaný terén musí něco znamenat i pro pohyb, suroviny a cestu do sídla.', 'Generated terrain has to matter for movement, resources and the route back to a settlement.'), $lang->value('Zdroj spojuje navazující fáze světa; přijatá hratelná smyčka zatím doložená není.', 'The source links world-generation stages; an accepted gameplay loop is not yet evidenced.'), $lang->value('Kde se pravidla potkají', 'Where the rules meet')],
    'tiskov' => [$lang->value('Slicer může běžet dlouho. Návrh proto odděluje přijetí poptávky od výpočtu a lidského potvrzení.', 'Slicing can take a long time. The proposal separates request intake from computation and human approval.'), $lang->value('Je to popsaná fronta a Raspberry Pi worker, ne ověřená běžící služba.', 'This is a described queue and Raspberry Pi worker, not a verified running service.'), $lang->value('Zatím jen návrh', 'Still a proposal')],
    'discord-bot' => [$lang->value('V katalogu je příkazová a údržbová větev, ale chybí zdroj, podle kterého bych ověřil oprávnění i chování.', 'The catalog lists application and maintenance commands, but without source I cannot verify permissions or behavior.'), $lang->value('Nechávám projekt odděleně. Hlášení chyby ani katalog nejsou důkazem živého provozu.', 'I keep this project separate. A bug report and a catalog are not proof of live operation.'), $lang->value('Bez zdroje to netvrdím', 'Not claimed without source')],
];
$mapSections = [];
foreach ($projects as $project) {
    $mapSections[$project->category]['title'] = $categories[$project->category];
    $mapSections[$project->category]['slugs'][] = $project->slug;
}
ksort($mapSections, SORT_NATURAL | SORT_FLAG_CASE);
?>
<section class="page-hero shell project-map-hero">
  <span class="eyebrow"><?= $lang->value('PŘEHLED PRÁCE', 'WORK INDEX') ?></span>
  <h1><?= $lang->value('Co stavím, proč a jak to funguje.', 'What I build, why, and how it works.') ?></h1>
  <p><?= $lang->value('Najdeš tu devět různých věcí: aplikace, nástroje pro AI a vývoj, hru i domácí infrastrukturu. Rozdělil jsem je podle oboru; na každé kartě je rovnou problém a nejtěžší kus práce.', 'Here are nine different things: apps, AI and development tools, a game, and home infrastructure. I grouped them by field; each card goes straight to the problem and the hardest part.') ?></p>
  <div class="page-meta"><?= count($projects) ?> <?= $lang->value('PROJEKTŮ', 'PROJECTS') ?> <span>·</span> <?= count($mapSections) ?> <?= $lang->value('SKUTEČNÝCH OBLASTÍ', 'PROJECT AREAS') ?></div>
</section>
<section class="section shell project-map" data-project-map-filter aria-label="<?= $lang->value('Projekty seskupené podle toho, co je potřeba ověřit', 'Projects grouped by what needs to be checked') ?>">
  <div class="project-map-catalog">
    <div class="section-heading"><div><span class="eyebrow"><?= count($mapSections) ?> <?= $lang->value('OBORŮ', 'FIELDS') ?></span><h2><?= $lang->value('Vyber oblast. Pak uvidíš konkrétní práci.', 'Pick a field. Then see the work itself.') ?></h2><p><?= $lang->value('Skupiny vycházejí z toho, co projekt dělá — ne z domnělých vazeb mezi repozitáři.', 'Groups follow what each project does, not imagined links between repositories.') ?></p></div></div>
  <div class="project-map-choices">
    <label><span><?= $lang->value('Hledat projekt nebo problém', 'Search projects or problems') ?></span><input type="search" data-map-search placeholder="<?= $lang->value('Např. preprocessing, fronta, zálohy…', 'For example: preprocessing, queue, backups…') ?>"></label>
    <label><span><?= $lang->value('Oblast', 'Area') ?><select data-map-category><option value="all"><?= $lang->value('Všechny oblasti', 'All areas') ?></option><?php foreach ($categories as $value => $label): ?><option value="<?= Html::escape($value) ?>"><?= Html::escape($label) ?></option><?php endforeach; ?></select></span></label>
    <label><span><?= $lang->value('Stav projektu', 'Project stage') ?><select data-map-state><option value="all"><?= $lang->value('Všechny stavy', 'All stages') ?></option><?php foreach ($states as $value => $label): ?><option value="<?= Html::escape($value) ?>"><?= Html::escape($label) ?></option><?php endforeach; ?></select></span></label>
  </div>
  <p class="project-map-status" data-map-status data-label="<?= $lang->value('projektů zobrazeno','projects shown') ?>" role="status" aria-live="polite" aria-atomic="true"><?= count($projects) ?> <?= $lang->value('projektů zobrazeno','projects shown') ?></p>
  <div class="project-map-domains">
    <?php foreach ($mapSections as $category => $section): $sectionId = 'map-category-' . trim((string)preg_replace('/[^a-z0-9]+/i', '-', $category), '-'); ?>
    <section class="project-map-domain" data-map-domain="<?= Html::escape($sectionId) ?>" aria-labelledby="<?= Html::escape($sectionId) ?>">
        <header class="project-map-domain-heading"><span><?= count($section['slugs']) ?> <?= $lang->value('PROJEKTŮ', 'PROJECTS') ?></span><div><h2 id="<?= Html::escape($sectionId) ?>"><?= Html::escape($section['title']) ?></h2></div><small><?= $lang->value('OBLAST', 'AREA') ?></small></header>
      <div class="project-map-list">
      <?php foreach ($section['slugs'] as $slug): if (!isset($projectBySlug[$slug])) continue; $project = $projectBySlug[$slug]; [$problem, $hardPart, $hardLabel] = $notes[$slug]; ?>
        <article class="project-map-item" id="map-project-<?= Html::escape($slug) ?>" data-map-project data-category="<?= Html::escape($project->category) ?>" data-state="<?= Html::escape($project->status) ?>" data-search="<?= Html::escape($project->name . ' ' . $project->category . ' ' . $project->summary($lang) . ' ' . $problem . ' ' . $hardPart) ?>">
          <div class="project-map-item-heading"><span class="category"><?= Html::escape($project::categoryLabel($project->category, $lang)) ?></span><span class="status <?= Html::escape($project->status) ?>"><?= Html::escape($project->statusLabel($lang)) ?></span></div>
          <h3><a href="<?= $lang->path('/projects/' . $project->slug) ?>"><?= Html::escape($project->name) ?></a></h3>
          <p class="project-map-problem"><?= Html::escape($problem) ?></p>
          <p class="project-map-hard"><span><?= Html::escape($hardLabel) ?></span><?= Html::escape($hardPart) ?></p>
          <a class="inline-link" href="<?= $lang->path('/projects/' . $project->slug) ?>"><?= $lang->value('Rozebrat projekt a jeho alternativy', 'Explore the project and its alternatives') ?> →</a>
        </article>
      <?php endforeach; ?>
      </div>
    </section>
    <?php endforeach; ?>
  </div>
  </div>
  <p class="project-map-empty" data-map-empty hidden><?= $lang->value('Tomu hledání nic neodpovídá. Zkus jiný název, oblast nebo stav.', 'No project matches those filters. Try another name, area or stage.') ?></p>
</section>
