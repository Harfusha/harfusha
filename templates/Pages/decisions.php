<?php
declare(strict_types=1);

use App\Domain\EngineeringDecision;
use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
/** @var list<EngineeringDecision> $decisions */
$alternatives = [
  'local-ai-routing' => ['name' => 'GitHub Copilot repository context', 'url' => 'https://docs.github.com/en/enterprise-cloud%40latest/copilot/concepts/prompting-and-context/repository-indexing', 'summary' => $lang->value('Umí indexovat repozitář a odpovídat nad jeho strukturou. To je plnohodnotná alternativa pro mnoho běžných codebase dotazů.', 'Indexes repositories and answers about their structure. A valid alternative for many everyday codebase questions.'), 'scope' => $lang->value('Local AI Hub zkoumá lokální MCP pracovní tok: preprocessing, deterministické podklady a koordinaci napříč klienty. Zdroje neobsahují srovnávací test dokazující převahu.', 'Local AI Hub explores a local MCP workflow: preprocessing, deterministic evidence and coordination across clients. The sources contain no benchmark proving it is better.')],
  'mutaui-native' => ['name' => 'Mutagen CLI + Mutagen Sync Manager', 'url' => 'https://github.com/ryonwhyte/sitepact-mutagen-gui', 'summary' => $lang->value('Samotný Mutagen už nabízí sync i forwarding; existuje také samostatné desktopové GUI pro správu session.', 'Mutagen already provides sync and forwarding; a separate desktop GUI for session management also exists.'), 'scope' => $lang->value('MutaUI zkouší nativní Qt workflow nad Mutagen CLI. Není doloženo, že existující GUI nevyhovovalo konkrétní potřebě.', 'MutaUI explores a native Qt workflow over Mutagen CLI. The sources do not show that the existing GUI failed a specific need.')],
  'home-infrastructure-as-code' => ['name' => 'Ansible CLI / AWX', 'url' => 'https://docs.ansible.com/projects/awx/en/latest/index.html', 'summary' => $lang->value('Playbooky jdou spouštět přímo přes Ansible CLI; AWX přidává webové spouštění, přístupová práva a historii úloh.', 'Playbooks run directly through Ansible CLI; AWX adds web execution, permissions and job history.'), 'scope' => $lang->value('Podklady ukazují Ansible se Semaphore. Nepotřebuju proto vymýšlet vlastní automatizační platformu; otázka je bezpečné přizpůsobení konkrétním hostům.', 'Sources show Ansible with Semaphore. There is no reason to invent an automation platform; the work is adapting it safely to these hosts.')],
  'scan-to-cad-boundary' => ['name' => 'Autodesk ReCap Pro / Geomagic Design X', 'url' => 'https://www.autodesk.com/products/recap/overview/', 'summary' => $lang->value('ReCap pokrývá práci se skeny a fotogrammetrií; Geomagic Design X převádí data skenů na editovatelné CAD modely.', 'ReCap covers scan and photogrammetry workflows; Geomagic Design X converts scan data into editable CAD models.'), 'scope' => $lang->value('Prototyp se soustředí na brány pro jednotky a polohu kamery. Neprokazuje, že komerční nástroje jsou nedostatečné ani že sám už umí CAD konverzi.', 'The prototype focuses on unit and camera-pose gates. It does not prove commercial tools are inadequate or that it already performs CAD conversion.')],
  'slicing-worker' => ['name' => 'Craftcloud', 'url' => 'https://support.craftcloud3d.com/en/articles/210-how-does-craftcloud-work-and-what-services-are-provided', 'summary' => $lang->value('Craftcloud už řeší upload 3D modelu, volbu technologie, srovnání nabídek i objednávku.', 'Craftcloud already handles 3D model upload, technology selection, quote comparison and ordering.'), 'scope' => $lang->value('Tiskov je zatím jen návrh lokální fronty a Raspberry Pi workeru. Není důkaz, že vlastní aplikace přináší zákazníkovi něco navíc.', 'Tiskov is only a proposal for a local queue and Raspberry Pi worker. There is no evidence yet that a custom app adds value for a customer.')],
  'woodbound-world-first' => ['name' => 'Unity Terrain Tools / MapMagic 2', 'url' => 'https://assetstore.unity.com/packages/tools/terrain/mapmagic-2-165180', 'summary' => $lang->value('Unity rozšiřuje tvorbu terénu a MapMagic generuje procedurální mapy, terén i další prvky světa.', 'Unity extends terrain authoring and MapMagic generates procedural maps, terrain and world elements.'), 'scope' => $lang->value('Woodbound řeší i herní pravidla a použitelné trasy; zdroj ale zatím nedokládá přijatý hratelný svět. Nástroje pro terén jsou přirozená alternativa pro samotné tvoření krajiny.', 'Woodbound also deals with game rules and traversable routes; source does not yet prove an accepted playable world. Terrain tools are the natural alternative for landscape authoring itself.')],
  'spotify-multi-tenancy' => ['name' => 'Spotify Jam', 'url' => 'https://newsroom.spotify.com/2026-01-07/listening-activity-request-to-jam-messages-updates/', 'summary' => $lang->value('Jam už umožňuje více lidem přidávat skladby do sdílené fronty a poslouchat spolu.', 'Jam already lets multiple people add tracks to a shared queue and listen together.'), 'scope' => $lang->value('Zdroj Shpotify ukazuje verzování a idempotentní změny fronty. Neprokazuje, že Jam tuto potřebu nepokrývá; projekt je rozpracovaná technická varianta.', 'Shpotify source shows queue versioning and idempotent updates. It does not prove Jam fails this use case; this is an in-progress technical alternative.')],
  'softwareinc-agreement-boundary' => ['name' => 'Nativní multiplayer Software Inc.', 'url' => 'https://store.steampowered.com/app/362620/Software_Inc/', 'summary' => $lang->value('Hra už nabízí online i LAN co-op a PvP. Pro samotnou společnou hru tedy mod není potřeba.', 'The game already offers online and LAN co-op and PvP. The mod is not needed just to play together.'), 'scope' => $lang->value('Mod přidává aplikační dohody, role a ochranu proti opakovanému zpracování. Kandidát ověřil pouze úzký běh a obnovu syntetických cílů; hraní s lidmi zůstává neověřené.', 'The mod adds application-level agreements, roles and replay-safe processing. A candidate only checked a narrow run and recovery of synthetic targets; playing with people remains unverified.')],
];
$decisionStatusLabels = [
  'documented' => $lang->value('Doloženo ve zdroji', 'Documented in source'),
  'prototype' => $lang->value('Ověřuje prototyp', 'Being tested in a prototype'),
  'planned' => $lang->value('Plán', 'Planned'),
];
$decisionChapters = [
  'documented' => [
    $lang->value('Volby popsané ve zdroji', 'Choices described in source'),
    $lang->value('Repozitář popisuje zvolenou cestu i její cenu. Je to důkaz záměru a architektury, ne automatické potvrzení živého provozu.', 'The repository describes the chosen path and its cost. That documents intent and architecture; it does not automatically prove live operation.'),
  ],
  'prototype' => [
    $lang->value('Návrhy a prototypy v ověřování', 'Designs and prototypes under review'),
    $lang->value('Tady zdroj ukazuje návrh nebo prototyp. Otevřená zůstává fyzická kontrola, přijatá herní smyčka nebo ověření v běhu.', 'Here the source shows a design or prototype. Physical review, an accepted gameplay loop or runtime verification remains open.'),
  ],
  'planned' => [
    $lang->value('Další plánované otázky', 'Questions planned for later'),
    $lang->value('Nápady, u kterých zatím není hotová volba ani ověřovací důkaz.', 'Ideas without a settled choice or verification evidence yet.'),
  ],
];
$decisionsByStatus = [];
foreach ($decisions as $decision) {
  $decisionsByStatus[$decision->status][] = $decision;
}
?>
<section class="page-hero shell">
  <h1><?= $lang->value('Technická rozhodnutí', 'Engineering decisions') ?></h1>
  <p><?= $lang->value('Proč jsem v MutaUI zachoval Mutagen CLI? Kdy se u domácích služeb vyplatí Ansible? Rozklikni rozhodnutí a porovnej možnosti, které dávaly smysl.',
      'Why does MutaUI keep using the Mutagen CLI? When is Ansible worth the setup for home services? Open a decision to see what I chose and what the alternatives offered.') ?></p>
</section>
<section class="shell decision-principle" aria-label="<?= $lang->value('Pravidlo pro vlastní projekty', 'Rule for custom projects') ?>">
  <span class="eyebrow"><?= $lang->value('SOUČASNÉ ROZHODOVACÍ PRAVIDLO', 'PRESENT DECISION RULE') ?></span>
  <p><?= $lang->value('Začínám s hotovým nástrojem. Vlastní řešení stavím jen tehdy, když vím, co mi chybí a co bude stát údržba. Pokud v repozitáři chybí původní zápis motivace, zůstává důvod vzniku označen jako neznámý a hodnotí se pouze popsaný technický problém.', 'I start with the tool that already exists. I build my own only when I can name what is missing and what maintenance will cost. Where repository records lack the original motivation, the origin is labeled unknown and only the documented technical problem is evaluated.') ?></p>
</section>
<section class="shell section" data-decision-browser>
  <div class="filter-bar">
    <label class="search-field"><span class="visually-hidden"><?= $lang->value('Hledat rozhodnutí', 'Search decisions') ?></span>
      <span aria-hidden="true">⌕</span><input data-search type="search" placeholder="<?= $lang->value('Najít problém, projekt nebo technologii…', 'Find a problem, project or technology…') ?>">
    </label>
    <select data-decision-status aria-label="<?= $lang->value('Stav rozhodnutí','Decision status') ?>">
      <option value="all"><?= $lang->value('Všechny stavy', 'All statuses') ?></option>
      <option value="documented"><?= $lang->value('Zdokumentovaná architektura', 'Documented architecture') ?></option>
      <option value="prototype"><?= $lang->value('Prototyp / rozpracováno', 'Prototype / in progress') ?></option>
      <option value="planned"><?= $lang->value('Plán', 'Planned') ?></option>
    </select>
    <button class="btn btn-ghost btn-sm" type="button" data-filter-reset hidden><?= $lang->value('Vymazat filtry', 'Clear filters') ?></button>
    <span class="filter-count" data-count role="status" aria-live="polite" aria-atomic="true"><?= count($decisions) ?> <?= $lang->value('rozhodnutí','decisions') ?></span>
  </div>
  <div class="decision-chapters">
    <?php foreach ($decisionChapters as $status => [$chapterTitle, $chapterCopy]): $records = $decisionsByStatus[$status] ?? []; if ($records === []) continue; ?>
    <section class="decision-chapter" data-filter-group="<?= Html::escape($status) ?>" aria-labelledby="decision-chapter-<?= Html::escape($status) ?>">
      <header><div><span class="eyebrow"><?= Html::escape($decisionStatusLabels[$status] ?? $status) ?></span><h2 id="decision-chapter-<?= Html::escape($status) ?>"><?= Html::escape($chapterTitle) ?></h2><p><?= Html::escape($chapterCopy) ?></p></div><strong><?= count($records) ?><span><?= $lang->value('ZÁZNAMY', 'RECORDS') ?></span></strong></header>
      <div class="decision-grid">
    <?php foreach ($records as $decision): $alternativePreview = $alternatives[$decision->id] ?? null; ?>
      <details class="decision-card" data-decision data-status="<?= Html::escape($decision->status) ?>"
        data-search-text="<?= Html::escape($decision->project.' '.$decision->title($lang).' '.$decision->context($lang).' '.$decision->choice($lang)) ?>">
        <summary>
          <strong><?= Html::escape($decision->title($lang)) ?></strong>
          <span class="decision-context"><?= Html::escape($decision->context($lang)) ?></span>
          <span class="decision-quick-choice"><span><?= $lang->value('VOLBA', 'CHOICE') ?></span><?= Html::escape($decision->choice($lang)) ?></span>
          <?php if ($alternativePreview !== null): ?><span class="decision-quick-alternative"><span><?= $lang->value('ALTERNATIVA', 'ALTERNATIVE') ?></span><?= Html::escape($alternativePreview['name']) ?></span><?php endif; ?>
          <span class="decision-more"><?= $lang->value('Kompromis a podklady', 'Trade-offs and evidence') ?> <span aria-hidden="true">+</span></span>
        </summary>
        <div class="decision-detail">
          <div><span class="eyebrow">01 / <?= $lang->value('PROBLÉM', 'PROBLEM') ?></span><p><?= Html::escape($decision->context($lang)) ?></p></div>
          <?php if ($alternativePreview !== null): $alternative = $alternativePreview; ?>
          <div class="decision-alternative"><span class="eyebrow">02 / <?= $lang->value('REÁLNÁ ALTERNATIVA', 'REAL ALTERNATIVE') ?></span><p><a href="<?= Html::escape($alternative['url']) ?>" target="_blank" rel="noopener noreferrer"><?= Html::escape($alternative['name']) ?> ↗</a> · <?= Html::escape($alternative['summary']) ?></p></div>
          <?php endif; ?>
          <div><span class="eyebrow">03 / <?= $lang->value('PODKLADY A EVIDENCE', 'EVIDENCE & SOURCES') ?></span><p><?= Html::escape($decision->evidence($lang)) ?></p></div>
          <div><span class="eyebrow">04 / <?= $lang->value('VOLBA ARCHITEKTURY', 'ARCHITECTURE CHOICE') ?></span><p><?= Html::escape($decision->choice($lang)) ?></p></div>
          <div><span class="eyebrow">05 / <?= $lang->value('KOMPROMIS A CENA', 'TRADE-OFF & COST') ?></span><p><?= Html::escape($decision->tradeoff($lang)) ?></p></div>
          <div><span class="eyebrow">06 / <?= $lang->value('VÝSLEDEK NEBO MEZERA', 'RESULT OR GAP') ?></span><p><?= Html::escape($alternativePreview['scope'] ?? $decision->choice($lang)) ?></p></div>
          <a class="inline-link" href="<?= $lang->path('/projects/'.$decision->project) ?>"><?= $lang->value('Otevřít případovou studii','Read the related case study') ?> ↗</a>
        </div>
      </details>
    <?php endforeach; ?>
      </div>
    </section>
    <?php endforeach; ?>
  </div>
  <p class="empty-state hidden" data-empty><?= $lang->value('Žádné rozhodnutí neodpovídá filtru.', 'No decisions match this filter.') ?></p>
  <div class="notice note-info"><strong><?= $lang->value('Rozhodnutí a výsledky', 'Decisions and results') ?></strong>
    <p><?= $lang->value('U každého rozhodnutí je uvedený podklad a jeho hranice. Pro srovnání toho, kam se projekty skutečně dostaly, použijte přehled výsledků.',
    'Each decision identifies its supporting evidence and its limits. The project results compare how far the individual projects progressed.') ?></p>
    <a class="inline-link" href="<?= $lang->path('/connections') ?>"><?= $lang->value('Přehled výsledků', 'Project results') ?> <span aria-hidden="true">↗</span></a>
  </div>
</section>
