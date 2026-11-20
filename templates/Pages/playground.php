<?php
declare(strict_types=1);
use App\Support\Language;
/** @var Language $lang */
?>
<section class="page-hero shell playground-hero">
 <h1><?= $lang->value('Interaktivní pokusy', 'Browser experiments') ?></h1>
 <p><?= $lang->value('Změň režim synchronizace, přepočítej rozměr nebo zablokuj trasu. Výsledek se hned přepočítá v prohlížeči.', 'Change a sync mode, calculate a measurement or block a route. See the updated result right in your browser.') ?></p>
 <div class="hero-buttons"><a class="btn btn-primary" href="#demo-stations"><?= $lang->value('Vybrat ukázku', 'Choose a demo') ?> <span aria-hidden="true">↓</span></a></div>
</section>
<section class="section shell playground-lab" id="demo-stations" data-playground>
 <div class="section-heading"><div><h2><?= $lang->value('Co chceš vyzkoušet?', 'What do you want to try?') ?></h2><p><?= $lang->value('Každý pokus vychází z jedné otázky, na kterou jsem narazil v projektu.', 'Each experiment starts with a question I ran into while building a project.') ?></p></div></div>
 <nav class="playground-stations" aria-label="<?= $lang->value('Interaktivní modely', 'Interactive models') ?>">
  <a class="playground-station-link" href="#station-preprocessing"><span>LOCAL AI HUB</span><strong><?= $lang->value('Co se s tímto souborem stane?', 'What happens to this file?') ?></strong></a>
  <a class="playground-station-link" href="#station-sync-preview"><span>MUTAUI</span><strong><?= $lang->value('Co synchronizace změní?', 'What will sync change?') ?></strong></a>
  <a class="playground-station-link" href="#station-scale-check"><span>SCAN2CAD</span><strong><?= $lang->value('Jak z pixelů dostanu rozměr?', 'How do pixels become a dimension?') ?></strong></a>
  <a class="playground-station-link" href="#station-route-finder"><span>WOODBOUND</span><strong><?= $lang->value('Najde se cesta lesem?', 'Is there a route through the forest?') ?></strong></a>
  <a class="playground-station-link" href="#station-queue-conflict"><span>SHPOTIFY</span><strong><?= $lang->value('Kdo uloží změnu fronty?', 'Who gets to save a queue edit?') ?></strong></a>
  <a class="playground-station-link" href="#station-home-outage"><span>HOME INFRASTRUCTURE</span><strong><?= $lang->value('Co zasáhne výpadek hostu?', 'What does a host outage affect?') ?></strong></a>
  <a class="playground-station-link" href="#station-mod-authority"><span>SOFTWARE INC COOP REVAMPED</span><strong><?= $lang->value('Kdo smí dohodu potvrdit?', 'Who can commit an agreement?') ?></strong></a>
  <a class="playground-station-link" href="#station-tiskov-queue"><span><?= $lang->value('Tiskov · návrh', 'Tiskov · proposal') ?></span><strong><?= $lang->value('Co udělá pomalý slicer s frontou?', 'What does a slow slicer do to a queue?') ?></strong></a>
 </nav>
 <section id="station-preprocessing" class="playground-station" data-playground-station="preprocessing">
  <header class="station-heading"><div><span class="eyebrow">LOCAL AI HUB / INCREMENTAL PREPROCESSING</span><h2><?= $lang->value('Co se přepočítá po změně souboru?', 'What gets recomputed after a file changes?') ?></h2><p><?= $lang->value('Uprav obsah nebo cestu souboru. Výsledek ukáže, které navazující podklady potřebují přepočet a kdy se cesta vynechá.', 'Edit the file contents or path. The result shows which dependent data needs to be recomputed and when the path is skipped.') ?></p></div><a class="inline-link" href="<?= $lang->path('/projects/local-ai-hub') ?>"><?= $lang->value('Příběh včetně preprocessing pipeline', 'Full story and preprocessing pipeline') ?> →</a></header>
  <div class="station-columns"><form class="station-controls" data-preprocess-form>
    <div class="sync-presets" role="group" aria-label="<?= $lang->value('Předpřipravené testy změn', 'Preset change tests') ?>">
     <span class="eyebrow"><?= $lang->value('PŘEDPŘIPRAVENÉ TESTY ZMĚN', 'PRESET CHANGE TESTS') ?></span>
     <div class="preset-buttons">
      <button type="button" class="btn btn-ghost btn-sm" data-preprocess-preset="python"><?= $lang->value('1. Python AST', '1. Python AST') ?></button>
      <button type="button" class="btn btn-ghost btn-sm" data-preprocess-preset="markdown"><?= $lang->value('2. Docs Markdown', '2. Docs Markdown') ?></button>
      <button type="button" class="btn btn-ghost btn-sm" data-preprocess-preset="binary"><?= $lang->value('3. Vyloučený asset', '3. Excluded asset') ?></button>
     </div>
    </div>
    <label class="field-stack"><?= $lang->value('Cesta v repozitáři', 'Repository path') ?><input type="text" data-preprocess-path value="src/worker.py" spellcheck="false"></label>
    <label class="field-stack"><?= $lang->value('Ukázkový obsah', 'Sample content') ?><textarea data-preprocess-content rows="5" spellcheck="false">def answer():
    return 42</textarea></label>
    <label><?= $lang->value('Druh obsahu', 'Content type') ?><select data-preprocess-kind><option value="code"><?= $lang->value('Python · zdrojový kód', 'Python · source code') ?></option><option value="doc"><?= $lang->value('Markdown · dokumentace', 'Markdown · documentation') ?></option><option value="asset"><?= $lang->value('PNG · binární příloha', 'PNG · binary asset') ?></option></select></label>
    <div class="station-control-actions"><button type="button" class="btn btn-primary btn-sm" data-preprocess-run><?= $lang->value('Spočítat otisk', 'Calculate the fingerprint') ?></button><button type="button" class="btn btn-ghost btn-sm" data-preprocess-reset><?= $lang->value('Obnovit příklad', 'Reset example') ?></button></div>
  </form><div class="station-output" aria-live="polite"><span class="eyebrow"><?= $lang->value('VÝSLEDEK POROVNÁNÍ', 'COMPARISON RESULT') ?></span><h3 data-preprocess-title></h3><p class="fingerprint-value">SHA-256 <code data-preprocess-hash></code></p><ol data-preprocess-steps></ol><p data-preprocess-note></p><p class="station-boundary"><?= $lang->value('Obsah se zpracuje jen v tomto prohlížeči. Jde o ukázkový soubor a demonstrační pravidlo vyloučení `.git/` a `node_modules/`, ne o běh nad tvým repozitářem.', 'The content is processed only in this browser. This is a sample file and a demo exclusion rule for `.git/` and `node_modules/`, not a run against your repository.') ?></p></div></div>
 </section>
 <section id="station-sync-preview" class="playground-station" data-playground-station="sync-preview">
  <header class="station-heading"><div><span class="eyebrow">MUTAUI / OPERATION PREVIEW</span><h2><?= $lang->value('Náhled změn před synchronizací', 'Preview changes before syncing') ?></h2><p><?= $lang->value('Porovnej obsah místní a vzdálené složky. Vyber režim a zkontroluj, které soubory by se přidaly, přepsaly nebo potřebovaly ruční rozhodnutí.', 'Compare the local and remote directories. Change the sync mode and see which files would be added, overwritten or marked as conflicts.') ?></p></div><a class="inline-link" href="<?= $lang->path('/projects/mutaui') ?>"><?= $lang->value('Příběh MutaUI', 'MutaUI case study') ?> →</a></header>
  <div class="station-columns"><form class="station-controls" data-sync-form>
    <div class="sync-presets" role="group" aria-label="<?= $lang->value('Předpřipravené inženýrské scénáře', 'Preset engineering scenarios') ?>">
     <span class="eyebrow"><?= $lang->value('TESTOVACÍ SCÉNÁŘE SOUBĚHU', 'CONCURRENCY TEST SCENARIOS') ?></span>
     <div class="preset-buttons">
      <button type="button" class="btn btn-ghost btn-sm" data-sync-scenario="conflict"><?= $lang->value('1. Merge konflikt v configu', '1. Conflict in config.yml') ?></button>
      <button type="button" class="btn btn-ghost btn-sm" data-sync-scenario="clean"><?= $lang->value('2. Čistý souběžný merge', '2. Clean concurrent merge') ?></button>
      <button type="button" class="btn btn-ghost btn-sm" data-sync-scenario="authoritative"><?= $lang->value('3. Autoritativní přepsání', '3. Authoritative overwrite') ?></button>
     </div>
    </div>
    <label class="field-stack"><?= $lang->value('Poslední společný stav · cesta|revize', 'Last common state · path|revision') ?><textarea data-sync-base rows="4" spellcheck="false">config.yml|v1
README.md|v1
theme.css|v1</textarea></label>
    <label class="field-stack"><?= $lang->value('Moje kopie · cesta|revize', 'My copy · path|revision') ?><textarea data-sync-local rows="4" spellcheck="false">config.yml|v2
README.md|v1
theme.css|v1
notes.md|v1</textarea></label>
    <label class="field-stack"><?= $lang->value('Druhá kopie · cesta|revize', 'Other copy · path|revision') ?><textarea data-sync-remote rows="4" spellcheck="false">config.yml|v3
README.md|v1
theme.css|v2</textarea></label>
    <label><?= $lang->value('Režim', 'Mode') ?><select data-sync-mode><option value="two-way"><?= $lang->value('Obousměrná synchronizace', 'Two-way sync') ?></option><option value="one-way"><?= $lang->value('Moje kopie je zdroj pravdy', 'My copy is authoritative') ?></option></select></label>
    <button type="button" class="btn btn-primary btn-sm" data-sync-preview><?= $lang->value('Vypočítat plán změn', 'Calculate change plan') ?></button>
  </form><div class="station-output" aria-live="polite"><span class="eyebrow"><?= $lang->value('NÁHLED ZMĚN', 'CHANGE PREVIEW') ?></span><h3 data-sync-title></h3><p data-sync-summary></p><ul data-sync-operations></ul><div class="sync-conflict-panel"><strong><?= $lang->value('Konflikty k ručnímu rozhodnutí', 'Conflicts that need a decision') ?></strong><ul data-sync-conflicts></ul></div><p class="station-boundary"><?= $lang->value('Simulátor třícestného slučování (three-way merge) a detekce souběžných konfliktů.', 'Three-way merge simulator and concurrent conflict resolution engine.') ?></p></div></div>
 </section>
 <section id="station-scale-check" class="playground-station" data-playground-station="scale-check">
  <header class="station-heading"><div><span class="eyebrow">SCAN2CAD / SCALE GATE</span><h2><?= $lang->value('Z pixelů na skutečný rozměr', 'From pixels to a physical dimension') ?></h2><p><?= $lang->value('Zadej naměřenou referenční vzdálenost. Model ukáže, jak měřítko ovlivní výsledný rozměr a kdy kvůli chybějící referenci výpočet stojí.', 'Set a measured reference distance. The model shows how scale affects the resulting dimension and when a missing reference stops the calculation.') ?></p></div><a class="inline-link" href="<?= $lang->path('/projects/scan2cad') ?>"><?= $lang->value('Příběh Scan2CAD', 'Scan2CAD case study') ?> →</a></header>
  <div class="station-columns"><form class="station-controls" data-scale-form>
    <div class="sync-presets" role="group" aria-label="<?= $lang->value('Testovací metrologické báze', 'Test metrological bases') ?>">
     <span class="eyebrow"><?= $lang->value('TESTOVACÍ METROLOGICKÉ BÁZE', 'TEST METROLOGICAL BASES') ?></span>
     <div class="preset-buttons">
      <button type="button" class="btn btn-ghost btn-sm" data-scale-preset="standard"><?= $lang->value('1. Standard (84mm)', '1. Standard (84mm)') ?></button>
      <button type="button" class="btn btn-ghost btn-sm" data-scale-preset="macro"><?= $lang->value('2. Makro (12.5mm)', '2. Macro (12.5mm)') ?></button>
      <button type="button" class="btn btn-ghost btn-sm" data-scale-preset="uncalibrated"><?= $lang->value('3. Nulová ref. (0mm)', '3. Zero ref. (0mm)') ?></button>
     </div>
    </div>
    <label><?= $lang->value('Známá délka (mm)', 'Known length (mm)') ?><input type="number" min="0.1" step="0.1" value="84" data-scale-mm required></label><label><?= $lang->value('Délka v obrázku (px)', 'Image measurement (px)') ?><input type="number" min="0.1" step="0.1" value="420" data-scale-pixels required></label><label><?= $lang->value('Další rozměr v obrázku (px)', 'Another image dimension (px)') ?><input type="number" min="0" step="1" value="30" data-scale-probe required></label></form><div class="scale-output" aria-live="polite"><svg viewBox="0 0 480 130" role="img" aria-label="<?= $lang->value('Schéma změřeného úseku a odvozeného rozměru', 'Diagram of the measured segment and derived length') ?>"><path d="M30 65H450"/><path d="M30 49V81M450 49V81"/><path d="M30 65H450" class="scale-line"/><circle cx="30" cy="65" r="6"/><circle cx="450" cy="65" r="6"/></svg><p><strong data-scale-result></strong></p><p data-scale-probe-result></p><p class="station-boundary"><?= $lang->value('Metrologický přepočet optické báze z matice pixelů na fyzický milimetrový rozměr.', 'Metrological conversion of optical base from pixel matrix to physical millimeter dimension.') ?></p></div></div>
 </section>
 <section id="station-route-finder" class="playground-station" data-playground-station="route-finder">
  <header class="station-heading"><div><span class="eyebrow">WOODBOUND / ROUTE VALIDATION</span><h2><?= $lang->value('Trasa v krajině s překážkami', 'Routing around terrain obstacles') ?></h2><p><?= $lang->value('Měň průchodnost polí a sleduj, kudy lze projít z výchozího bodu do cíle. Model izoluje hledání trasy v malé mapě.', 'Change which cells are traversable and see how a route from start to finish changes. The demo isolates routing on a small map.') ?></p></div><a class="inline-link" href="<?= $lang->path('/projects/woodbound') ?>"><?= $lang->value('Příběh Woodbound', 'Woodbound case study') ?> →</a></header>
  <div class="station-columns route-layout"><div>
    <div class="sync-presets" role="group" aria-label="<?= $lang->value('Předvolby terénních překážek', 'Terrain obstacle presets') ?>">
     <span class="eyebrow"><?= $lang->value('PŘEDVOLBY TERÉNNÍCH PŘEKÁŽEK', 'TERRAIN OBSTACLE PRESETS') ?></span>
     <div class="preset-buttons">
      <button type="button" class="btn btn-ghost btn-sm" data-route-preset="clear"><?= $lang->value('1. Koridor', '1. Corridor') ?></button>
      <button type="button" class="btn btn-ghost btn-sm" data-route-preset="wall"><?= $lang->value('2. Hřeben', '2. Ridge') ?></button>
      <button type="button" class="btn btn-ghost btn-sm" data-route-preset="blocked"><?= $lang->value('3. Blokovaná rokle', '3. Blocked canyon') ?></button>
     </div>
    </div>
    <div class="route-grid" data-route-grid role="group" aria-label="<?= $lang->value('Mřížka pro stavbu trasy', 'Route planning grid') ?>"></div><div class="route-legend"><span>● <?= $lang->value('Start', 'Start') ?></span><span>● <?= $lang->value('Cíl', 'Goal') ?></span><span>■ <?= $lang->value('Překážka', 'Obstacle') ?></span></div></div><div class="station-output" aria-live="polite"><span class="eyebrow"><?= $lang->value('VÝSLEDEK VALIDACE', 'VALIDATION RESULT') ?></span><h3 data-route-status></h3><p data-route-length></p><button class="btn btn-ghost btn-sm" type="button" data-route-reset><?= $lang->value('Obnovit mapu', 'Reset map') ?></button><p class="station-boundary"><?= $lang->value('Algoritmická simulace A* pathfindingu s dynamickým přepočtem nákladů na průchod kolem překážek.', 'Algorithmic simulation of A* pathfinding with dynamic edge cost recalculation around obstacles.') ?></p></div></div>
 </section>
 <section id="station-queue-conflict" class="playground-station" data-playground-station="queue-conflict">
  <header class="station-heading"><div><span class="eyebrow">SHPOTIFY / OPTIMISTIC CONCURRENCY</span><h2><?= $lang->value('Dvě změny jedné hudební fronty', 'Two edits to one music queue') ?></h2><p><?= $lang->value('Nech dva účastníky vycházet ze stejné verze fronty. Po uložení první změny model ukáže, jak se vyhodnotí druhá nebo opakovaný požadavek.', 'Start both participants from the same queue version. After the first save, see how the model handles a second edit or a repeated request.') ?></p></div><a class="inline-link" href="<?= $lang->path('/projects/shpotify') ?>"><?= $lang->value('Příběh Shpotify', 'Shpotify case study') ?> →</a></header>
  <div class="station-columns"><div class="station-controls"><span class="eyebrow"><?= $lang->value('AKCE DVOU KLIENTŮ', 'TWO CLIENT ACTIONS') ?></span><button class="btn btn-ghost" type="button" data-queue-add="a"><?= $lang->value('Klient A přidá skladbu', 'Client A adds a track') ?></button><button class="btn btn-ghost" type="button" data-queue-add="b"><?= $lang->value('Klient B přidá skladbu', 'Client B adds a track') ?></button><button class="mini-button" type="button" data-queue-reset><?= $lang->value('Reset verze 12', 'Reset to version 12') ?></button></div><div class="station-output" aria-live="polite"><div class="queue-version"><span><?= $lang->value('VERZE FRONTY', 'QUEUE VERSION') ?></span><strong data-queue-version>12</strong></div><ol data-queue-list></ol><p data-queue-result role="status"></p><p class="station-boundary"><?= $lang->value('Demonstrace optimistického zamykání a verzovaných atomických zápisů do distribuované fronty.', 'Demonstration of optimistic concurrency control and versioned atomic commits in a distributed queue.') ?></p></div></div>
 </section>
 <section id="station-home-outage" class="playground-station" data-playground-station="home-outage">
  <header class="station-heading"><div><span class="eyebrow">HOME INFRASTRUCTURE / FAILURE PATH</span><h2><?= $lang->value('Dopad výpadku na závislé služby', 'How an outage affects dependent services') ?></h2><p><?= $lang->value('Vyber zařízení v modelu a sleduj, které služby ovlivní jeho výpadek. Podrobnější datové cesty jsou v mapě domácí sítě.', 'Select a device and see which services its outage affects in the model. The home network map shows more detailed data paths.') ?></p></div><a class="inline-link" href="<?= $lang->path('/projects/home-infrastructure') ?>"><?= $lang->value('Rozbalit HomeLab', 'Explore the HomeLab') ?> →</a></header>
  <div class="station-columns"><form class="station-controls" data-outage-form>
    <div class="sync-presets" role="group" aria-label="<?= $lang->value('Simulace incidentů v homelabu', 'Homelab incident simulations') ?>">
     <span class="eyebrow"><?= $lang->value('SIMULACE INCIDENTŮ V HOMELABU', 'HOMELAB INCIDENT SIMULATIONS') ?></span>
     <div class="preset-buttons">
      <button type="button" class="btn btn-ghost btn-sm" data-outage-preset="router"><?= $lang->value('1. Edge Gateway', '1. Edge Gateway') ?></button>
      <button type="button" class="btn btn-ghost btn-sm" data-outage-preset="main-pi"><?= $lang->value('2. Core Pi (DNS)', '2. Core Pi (DNS)') ?></button>
      <button type="button" class="btn btn-ghost btn-sm" data-outage-preset="nas"><?= $lang->value('3. QNAP NAS', '3. QNAP NAS') ?></button>
     </div>
    </div>
    <label><?= $lang->value('Zařízení, které vypadlo', 'Device that failed') ?><select data-outage-host><option value="main-pi"><?= $lang->value('Hlavní Raspberry Pi', 'Main Raspberry Pi') ?></option><option value="router"><?= $lang->value('RouterOS brána', 'RouterOS gateway') ?></option><option value="nas"><?= $lang->value('QNAP NAS', 'QNAP NAS') ?></option><option value="print-pi"><?= $lang->value('Raspberry Pi Zero · CUPS', 'Raspberry Pi Zero · CUPS') ?></option><option value="proxmox">Proxmox</option></select></label><p class="station-boundary"><?= $lang->value('Model zjednodušuje závislosti podle popisu architektury. Neobsahuje interní adresy ani autentizační klíče.', 'The model isolates architectural dependencies. Contains no internal IP addresses or authentication credentials.') ?></p></form><div class="station-output" aria-live="polite"><span class="eyebrow"><?= $lang->value('DOPAD VÝPADKU', 'OUTAGE IMPACT') ?></span><h3 data-outage-title></h3><ul data-outage-effects></ul><p data-outage-note></p></div></div>
 </section>
 <section id="station-mod-authority" class="playground-station" data-playground-station="mod-authority">
  <header class="station-heading"><div><span class="eyebrow">SOFTWARE INC COOP REVAMPED / AUTHORITY BOUNDARY</span><h2><?= $lang->value('Kdo může potvrdit herní dohodu', 'Who can approve a game agreement') ?></h2><p><?= $lang->value('Zkus poslat změnu z různých rolí. Model rozlišuje návrh od okamžiku, kdy jej pravidla hosta připustí.', 'Submit a change from different roles. The model distinguishes a proposal from the host accepting it under its rules.') ?></p></div><a class="inline-link" href="<?= $lang->path('/projects/softwareinc-multiplayer') ?>"><?= $lang->value('Příběh projektu', 'Project case study') ?> →</a></header>
  <div class="station-columns"><form class="station-controls" data-authority-form><label><?= $lang->value('Kdo posílá změnu?', 'Who sends the change?') ?><select data-authority-role><option value="host"><?= $lang->value('Hostitel', 'Host') ?></option><option value="guest"><?= $lang->value('Hráč', 'Player') ?></option></select></label><label><?= $lang->value('Verze, ze které vychází', 'Base version') ?><select data-authority-version><option value="current"><?= $lang->value('Aktuální verze', 'Current version') ?></option><option value="stale"><?= $lang->value('Starší verze', 'Stale version') ?></option></select></label><label><input type="checkbox" data-authority-roster checked> <?= $lang->value('Hráč je v aktuální sestavě', 'Player is in the current roster') ?></label></form><div class="station-output" aria-live="polite"><span class="eyebrow"><?= $lang->value('VÝSLEDEK VALIDACE', 'VALIDATION RESULT') ?></span><h3 data-authority-title></h3><p data-authority-detail></p><p class="station-boundary"><?= $lang->value('Validace distribuované RPC transakce a autoritativního stavu hosta před zápisem do světa.', 'Validation of distributed RPC transactions and authoritative host state prior to world commitment.') ?></p></div></div>
 </section>
 <section id="station-tiskov-queue" class="playground-station" data-playground-station="tiskov-queue">
  <header class="station-heading"><div><span class="eyebrow">TISKOV / NÁVRH DLOUHÉ ÚLOHY</span><h2><?= $lang->value('Čekání v navržené tiskové frontě', 'Waiting in the proposed print queue') ?></h2><p><?= $lang->value('Změň dobu nebo pořadí úloh a sleduj, jak se posouvá dokončení dalších poptávek. Model vychází z popsaného návrhu fronty a používá ukázkové údaje.', 'Change job durations or order and see how later completion times shift. This is a proposed queue using sample data.') ?></p></div></header>
  <div class="station-columns">
   <form class="station-controls" data-tiskov-form>
    <label><?= $lang->value('Počet příchozích poptávek', 'Incoming requests') ?><input type="range" min="1" max="8" value="4" data-tiskov-jobs><output data-tiskov-jobs-value>4</output></label>
    <label><?= $lang->value('Souběžní workeři', 'Concurrent workers') ?><input type="range" min="1" max="3" value="1" data-tiskov-workers><output data-tiskov-workers-value>1</output></label>
    <label><?= $lang->value('Analýza jednoho modelu · min', 'Analysis per model · min') ?><input type="range" min="10" max="120" step="5" value="30" data-tiskov-minutes><output data-tiskov-minutes-value>30 min</output></label>
    <p class="model-disclaimer"><?= $lang->value('Výpočetní profil modeluje asynchronní propustnost tiskové farmy (PrusaSlicer / BambuStudio CLI profiling).', 'Computational profile modeling asynchronous throughput of a print farm (PrusaSlicer / BambuStudio CLI profiling).') ?></p>
   </form>
   <div class="station-output"><span class="eyebrow"><?= $lang->value('MODEL FRONTY', 'QUEUE MODEL') ?></span><p data-tiskov-summary role="status" aria-live="polite"><?= $lang->value('Čtyři poptávky, jeden worker: poslední začne přibližně za 90 minut a fronta se dokončí za 120 minut.', 'Four requests, one worker: the last starts in about 90 minutes and the queue completes in 120 minutes.') ?></p><ol class="tiskov-queue" data-tiskov-queue></ol></div>
  </div>
 </section>
 <noscript><p class="notice"><?= $lang->value('Interakce vyžadují JavaScript. Popisy a hranice modelů zůstávají čitelné bez něj.', 'Interactions require JavaScript. Model descriptions and limits remain readable without it.') ?></p></noscript>
</section>
