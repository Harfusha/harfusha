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
    'operate' => ['title' => $lang->value('Nástroje a provoz', 'Tools and operations'), 'description' => $lang->value('Služby, nástroje a provozní informace pro konkrétní prostředí.', 'Services, tools and operational information for a specific environment.'), 'slugs' => ['local-ai-hub', 'home-infrastructure', 'discord-bot']],
    'candidate' => ['title' => $lang->value('Rekonstrukce a návrh', 'Reconstruction and design'), 'description' => $lang->value('Zpracovat zadání nebo data a ukázat výsledek, který ještě potřebuje kontrolu.', 'Process a request or input data and show a result that still needs review.'), 'slugs' => ['scan2cad', 'tiskov', 'woodbound']],
    'coordinate' => ['title' => $lang->value('Spolupráce a synchronizace', 'Collaboration and sync'), 'description' => $lang->value('Změny, na kterých musí být mezi lidmi nebo nástroji shoda.', 'Changes that people or tools need to agree on.'), 'slugs' => ['mutaui', 'shpotify', 'softwareinc-multiplayer']],
];
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
$outcomes = [
    'local-ai-hub' => $lang->value('Zdroj zpřístupňuje deterministické hledání a přesné zdrojové výřezy; nenahrazuje agenta ani testy.', 'Source provides deterministic search and exact source slices; it does not replace an agent or tests.'),
    'mutaui' => $lang->value('Aplikace ovládá Mutagen; obrázky ukazují offline demo se syntetickými daty.', 'The app controls Mutagen; screenshots show an offline demo with synthetic data.'),
    'scan2cad' => $lang->value('Výstupem je kandidátní geometrie; bez rozměrové kontroly nejde o hotový díl.', 'The output is candidate geometry; without dimensional checks, it is not a finished part.'),
    'home-infrastructure' => $lang->value('Repozitář dokumentuje konfiguraci a obnovu; mapa webu není živý monitoring.', 'The repository documents configuration and recovery; the site map is not live monitoring.'),
    'shpotify' => $lang->value('Fronta a souběžné změny jsou implementované; přijaté přehrávání celé cesty doložené není.', 'Queue and concurrent updates are implemented; accepted end-to-end playback is not evidenced.'),
    'softwareinc-multiplayer' => $lang->value('Zdroj obsahuje návrh rozšíření; přijaté ověření hry ani multiplayeru chybí.', 'Source contains an extension; accepted game or multiplayer validation is missing.'),
    'woodbound' => $lang->value('Generační části jsou ve zdroji; přijatý runtime ani hratelná smyčka doložené nejsou.', 'Generation stages exist in source; an accepted runtime or playable loop is not evidenced.'),
    'tiskov' => $lang->value('Jde o popsaný návrh fronty; běžící služba ani fyzické výsledky doložené nejsou.', 'This is a queue proposal; a running service or physical results are not evidenced.'),
    'discord-bot' => $lang->value('Katalog popisuje aplikační příkazy a údržbu. Přesné příkazy, oprávnění a běh čekají na ověření ve zdroji.', 'The catalog describes application commands and maintenance. The exact commands, permissions and operation still need source verification.'),
];
$mapSections = $themes;
?>
<section class="page-hero shell project-map-hero">
  <h1><?= $lang->value('Co je ověřené', 'What’s been verified') ?></h1>
  <p><?= $lang->value('Porovnej, co lze u projektů doložit zdrojovým kódem, co si můžeš vyzkoušet jako model a kde ještě chybí ověření v praxi.', 'Compare what the source shows, what you can try in a model and which claims still need real-world verification.') ?></p>
</section>
<section class="section shell project-map" data-project-map-filter aria-label="<?= $lang->value('Projekty seskupené podle toho, co je potřeba ověřit', 'Projects grouped by what needs to be checked') ?>">
  <div class="project-map-catalog">
  <div class="project-map-choices">
    <label><span><?= $lang->value('Hledat projekt nebo problém', 'Search projects or problems') ?></span><input type="search" data-map-search placeholder="<?= $lang->value('Např. preprocessing, fronta, zálohy…', 'For example: preprocessing, queue, backups…') ?>"></label>
    <label><span><?= $lang->value('Tematický okruh', 'Thematic group') ?><select data-map-category><option value="all"><?= $lang->value('Všechny okruhy', 'All groups') ?></option><?php foreach ($mapSections as $value => $section): ?><option value="<?= Html::escape($value) ?>"><?= Html::escape($section['title']) ?></option><?php endforeach; ?></select></span></label>
    <label><span><?= $lang->value('Stav projektu', 'Project stage') ?><select data-map-state><option value="all"><?= $lang->value('Všechny stavy', 'All stages') ?></option><?php foreach ($states as $value => $label): ?><option value="<?= Html::escape($value) ?>"><?= Html::escape($label) ?></option><?php endforeach; ?></select></span></label>
  </div>
  <p class="project-map-status" data-map-status data-label="<?= $lang->value('projektů zobrazeno','projects shown') ?>" role="status" aria-live="polite" aria-atomic="true"><?= count($projects) ?> <?= $lang->value('projektů zobrazeno','projects shown') ?></p>
  <div class="project-map-domains">
    <?php foreach ($mapSections as $theme => $section): $sectionId = 'map-theme-' . $theme; ?>
    <section class="project-map-domain" data-map-domain="<?= Html::escape($sectionId) ?>" aria-labelledby="<?= Html::escape($sectionId) ?>">
        <header class="project-map-domain-heading"><h2 id="<?= Html::escape($sectionId) ?>"><?= Html::escape($section['title']) ?></h2><p><?= Html::escape($section['description']) ?></p></header>
      <div class="project-map-list">
      <?php foreach ($section['slugs'] as $slug): if (!isset($projectBySlug[$slug])) continue; $project = $projectBySlug[$slug]; [$problem, $hardPart, $hardLabel] = $notes[$slug]; ?>
        <article class="project-map-item" id="map-project-<?= Html::escape($slug) ?>" data-map-project data-theme="<?= Html::escape($theme) ?>" data-state="<?= Html::escape($project->status) ?>" data-search="<?= Html::escape($project->name . ' ' . $project->category . ' ' . $project::categoryLabel($project->category, $lang) . ' ' . $project->summary($lang) . ' ' . $problem . ' ' . $hardPart . ' ' . $outcomes[$slug]) ?>">
          <div class="project-map-item-heading"><span class="category"><?= Html::escape($project::categoryLabel($project->category, $lang)) ?></span><span class="status <?= Html::escape($project->status) ?>"><?= Html::escape($project->statusLabel($lang)) ?></span></div>
          <h3><a href="<?= $lang->path('/projects/' . $project->slug) ?>"><?= Html::escape($project->name) ?></a></h3>
          <p class="project-map-hard project-map-outcome"><span><?= $lang->value('Výsledek / limit', 'Result / limit') ?></span><?= Html::escape($outcomes[$slug]) ?></p>
          <details class="project-map-reason">
          <summary><?= $lang->value('Jak jsem k výsledku došel', 'What went into the result') ?></summary>
          <p class="project-map-hard project-map-problem"><span><?= $lang->value('Vstup / problém', 'Input / problem') ?></span><?= Html::escape($problem) ?></p>
          <p class="project-map-hard"><span><?= Html::escape($hardLabel) ?></span><?= Html::escape($hardPart) ?></p>
          </details>
          <a class="inline-link" href="<?= $lang->path('/projects/' . $project->slug) ?>"><?= $lang->value('Případová studie', 'Case study') ?> →</a>
        </article>
      <?php endforeach; ?>
      </div>
    </section>
    <?php endforeach; ?>
  </div>
  </div>
  <p class="project-map-empty" data-map-empty hidden><?= $lang->value('Výsledky pro tento výběr chybí. Zkuste jiný název, oblast nebo stav.', 'No project matches those filters. Try another name, area or stage.') ?></p>
</section>
