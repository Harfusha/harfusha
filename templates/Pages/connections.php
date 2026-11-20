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
    'local-ai-hub' => [
        $lang->value('Před modelovým dotazem musí být jasné, který soubor se změnil a jaká fakta z něj lze doložit.', 'Before a model call, the system identifies changed files and deterministic repository facts.'),
        $lang->value('Preprocessing počítá SHA-256 hashe a připravuje indexy symbolů v navazujících fázích. Kódový index má prioritu před sémantickým RAGem.', 'Preprocessing computes SHA-256 hashes and prepares symbol indexes. Deterministic AST code indexes take precedence over semantic RAG.'),
        $lang->value('Deterministická evidence', 'Deterministic evidence'),
    ],
    'mutaui' => [
        $lang->value('Před synchronizací souborů je nutné znát přesný směr toku dat a včas odhalit potenciální kolize.', 'Before file sync, operators require explicit data flow direction and early collision detection.'),
        $lang->value('MutaUI ovládá Mutagen CLI přes bezpečný IPC socket a vizualizuje plánované změny před zápisem na disk.', 'MutaUI orchestrates Mutagen CLI over secure IPC, previewing staged changes before disk mutation.'),
        $lang->value('Před zápisem na disk', 'Prior to disk mutation'),
    ],
    'scan2cad' => [
        $lang->value('3D sken zachytí hustou povrchovou síť, ale postrádá parametrické geometrické tolerance součásti.', 'A 3D scan captures dense surface meshes but lacks parametric geometric engineering tolerances.'),
        $lang->value('Kalibrace měřítka, registrace a referenční makrofotografie vytvoří rozměrově ověřený STEP model pro CNC.', 'Scale calibration, point registration and macro photos produce dimensionally verified STEP models for CNC.'),
        $lang->value('Metrologické ověření', 'Metrological validation'),
    ],
    'home-infrastructure' => [
        $lang->value('Síťové jádro (MikroTik router a DNS sinkhole) musí zůstat dostupné i při restartu virtualizačních uzlů.', 'Network core (MikroTik routing and DNS sinkhole) must stay operational during compute node maintenance.'),
        $lang->value('Stav infrastruktury je deklarován v Ansible rolích a orchestrován přes Semaphore s automatickým zálohováním.', 'Infrastructure state is declared in Ansible roles and orchestrated via Semaphore with automated backups.'),
        $lang->value('Segmentace a dostupnost', 'Segmentation & resilience'),
    ],
    'shpotify' => [
        $lang->value('Při souběžné editaci fronty dvěma klienty nesmí dojít k tichému přepsání dat ani desynchronizaci přehrávače.', 'Concurrent queue edits by multiple clients must prevent silent data loss or playback desynchronization.'),
        $lang->value('Architektura využívá optimistické zamykání přes verzované snapshoty a WebSocket pub/sub model pro reálný čas.', 'Architecture employs optimistic concurrency via versioned snapshots and WebSocket pub/sub for real-time sync.'),
        $lang->value('Konzistence v reálném čase', 'Real-time consistency'),
    ],
    'softwareinc-multiplayer' => [
        $lang->value('Ekonomická simulace vyžaduje autoritativní validaci mezifiremních kontraktů a sdílených serverových farem.', 'Economic simulation requires authoritative validation of inter-company contracts and shared server farms.'),
        $lang->value('C# doménové jádro je odděleno od Unity enginu, což umožňuje deterministickou serializaci a RPC validaci.', 'C# domain core is decoupled from Unity engine, enabling deterministic state serialization and RPC validation.'),
        $lang->value('Autoritativní síťová vrstva', 'Authoritative network layer'),
    ],
    'woodbound' => [
        $lang->value('Procedurální terén musí garantovat logistickou schůdnost a dostupnost surovin napříč rozsáhlým světem.', 'Procedural terrain must guarantee traversable logistics corridors and resource reachability across large maps.'),
        $lang->value('Navazující pipeline propojuje Perlinův šum, hydraulickou erozi a A* trasování logistických cest podle sklonu.', 'Multi-stage pipeline combines Perlin noise, hydraulic erosion, and slope-aware A* logistics routing.'),
        $lang->value('Deterministická pipeline', 'Deterministic pipeline'),
    ],
    'tiskov' => [
        $lang->value('Náročný výpočetní slicing 3D modelů nesmí blokovat webové rozhraní pro příjem nových zakázek.', 'Compute-heavy 3D model slicing must never starve HTTP worker pools handling customer intake.'),
        $lang->value('Asynchronní architektura odděluje webový příjem od dedikovaných Raspberry Pi workerů spouštějících PrusaSlicer CLI.', 'Asynchronous architecture isolates web intake from dedicated Raspberry Pi workers running PrusaSlicer CLI.'),
        $lang->value('Asynchronní fronta úloh', 'Asynchronous job queue'),
    ],
    'discord-bot' => [
        $lang->value('Vzdálená správa služeb z mobilu vyžaduje přísnou kontrolu rolí (RBAC) bez vystavení nechráněného SSH portu.', 'Remote service orchestration from mobile devices demands strict RBAC without exposing raw SSH ports.'),
        $lang->value('Bot ověřuje digitální podpisy Discordu a předává povolené příkazy do odděleného auditovaného workeru.', 'The bot verifies Discord signatures and dispatches whitelisted commands to an audited execution worker.'),
        $lang->value('Auditovaná RBAC brána', 'Audited RBAC gateway'),
    ],
];
$outcomes = [
    'local-ai-hub' => $lang->value('Deterministické hledání, AST kódový index a přesné zdrojové výřezy pro agenty přes MCP rozhraní.', 'Deterministic code search, AST symbol indexing, and exact source slices for agents via MCP protocol.'),
    'mutaui' => $lang->value('Jednotná správa Mutagen relací s okamžitou detekcí konfliktů a vizualizací plánovaných změn před zápisem.', 'Unified Mutagen session manager with live conflict detection and staged change preview before write.'),
    'scan2cad' => $lang->value('Kombinace fotogrammetrické reference, kalibrace měřítka a vizualizace rozměrové nejistoty před exportem do CAD.', 'Photogrammetric reference, scale calibration, and uncertainty visualization prior to CAD export.'),
    'home-infrastructure' => $lang->value('Segmentovaná síťová topologie (VLAN, WireGuard, DNS sinkhole) plně deklarovaná v Ansible rolích.', 'Segmented network topology (VLAN, WireGuard, DNS sinkhole) fully declared via Ansible roles.'),
    'shpotify' => $lang->value('Kolaborativní poslechové místnosti s distribuovanou synchronizací fronty přes WebSocket a optimistické zamykání.', 'Collaborative listening rooms with WebSocket queue synchronization and optimistic concurrency control.'),
    'softwareinc-multiplayer' => $lang->value('Deterministická synchronizace stavu herního světa a mezifiremní B2B kontrakty přes autoritativní síťovou vrstvu.', 'Deterministic world state synchronization and inter-company contracts via authoritative networking.'),
    'woodbound' => $lang->value('Deterministický procedurální generátor terénu propojující výškové mapy, hydraulickou erozi a A* logistické trasy.', 'Deterministic procedural terrain pipeline combining heightmaps, hydraulic erosion, and A* logistics corridors.'),
    'tiskov' => $lang->value('Asynchronní architektura tiskové fronty s PrusaSlicer CLI workerem; odděluje výpočetní náročnost od HTTP rozhraní.', 'Asynchronous print queue architecture decoupling PrusaSlicer CLI compute from HTTP intake.'),
    'discord-bot' => $lang->value('Role-based (RBAC) orchestrace systémových služeb a push notifikace incidentů přes asynchronní worker.', 'Role-based (RBAC) service orchestration and incident alerting via decoupled async worker.'),
];
$mapSections = $themes;
?>
<section class="page-hero shell project-map-hero">
  <h1><?= $lang->value('Architektura a mapa projektů', 'Architecture and Project Map') ?></h1>
  <p><?= $lang->value('Systematický přehled devíti systémových projektů rozdělených podle domény: nástroje a provoz, rekonstrukce a návrh, spolupráce a distribuovaný stav.', 'A systematic overview of nine engineering projects grouped by operational domain: tools & operations, reconstruction & design, and distributed collaboration.') ?></p>
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
          <summary><?= $lang->value('Technické řešení a architektura', 'Technical solution & architecture') ?></summary>
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
