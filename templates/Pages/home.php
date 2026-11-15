<?php
declare(strict_types=1);
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var list<App\Domain\Project> $projects */
/** @var list<array<string,mixed>> $journal */
/** @var list<array<string,mixed>> $recentRepos */
/** @var int $projectCount */
/** @var int $mergedPrCount */
/** @var array{source:string,last_sync:?string} $provenance */
?>
<section class="hero shell">
 <div class="hero-copy reveal">
   <div class="live-label"><span class="pulse-dot"></span><span><?= $lang->value('Vítej v mé digitální dílně','Welcome to my digital workshop') ?></span></div>
   <p class="hero-kicker">ADAM HALFAR <span class="muted">/</span> SOFTWARE ENGINEER & MAKER</p>
   <h1><?= $lang->value('Nepišu jen kód.','I don’t just write code.') ?><br><span class="gradient-text"><?= $lang->value('Stavím systémy.', 'I build systems.') ?></span></h1>
   <p class="hero-description"><?= $lang->value('Backend, open source, lokální AI, domácí servery a věci, které lze vyrobit na 3D tiskárně. Tohle není jen seznam projektů. Je to místo, kde si můžeš prohlédnout, jak fungují.', 'Backend, open source, local AI, home servers and things you can actually 3D print. Not just a project list: a place to explore how things work.') ?></p>
   <div class="hero-buttons"><a class="btn btn-primary" href="<?= $lang->path('/homelab') ?>"><?= $lang->value('Vstoupit do laboratoře','Explore the lab') ?><span aria-hidden="true">↗</span></a><a class="btn btn-ghost" href="<?= $lang->path('/projects') ?>"><?= $lang->value('Procházet projekty','Browse projects') ?> <span aria-hidden="true">→</span></a></div>
   <div class="hero-micro"><span class="signal-bars" aria-hidden="true"><b></b><b></b><b></b><b></b></span><?= $lang->value('Postaveno pro objevování, ne pro chlubení.', 'Built to be explored, not admired from afar.') ?></div>
 </div>
 <div class="hero-visual reveal" aria-label="Interactive lab overview">
  <div class="hero-visual-header"><span><span class="window-dot"></span><span class="window-dot"></span><span class="window-dot"></span></span><span>workshop / overview</span><span class="tiny-live">INTERACTIVE</span></div>
  <div class="hero-network" data-hero-map>
   <svg viewBox="0 0 540 338" role="img" aria-label="Simplified interactive illustration of projects and infrastructure">
    <defs><linearGradient id="lineGradient" x1="0" x2="1"><stop stop-color="#32cfa7" stop-opacity="0.15"/><stop offset="1" stop-color="#6eb4f8" stop-opacity="0.78"/></linearGradient></defs>
    <g stroke="url(#lineGradient)" stroke-width="1.6" fill="none"><path d="M267 88L132 180L130 272"/><path d="M267 88L390 186L394 267"/><path d="M267 88L267 200"/><path d="M130 180L267 200L390 186"/></g>
    <g fill="#32cfa7"><circle cx="267" cy="88" r="5"/><circle cx="132" cy="180" r="3"/><circle cx="267" cy="200" r="3"/><circle cx="390" cy="186" r="3"/></g>
    <g class="network-activity"><circle cx="267" cy="88" r="13"/><circle cx="267" cy="88" r="23"/></g>
    <g font-size="12" font-family="monospace" fill="#aebcc9"><text x="220" y="58">harfusha.lab</text><text x="61" y="165">AI HUB</text><text x="239" y="237">HOMELAB</text><text x="390" y="168">MUTAUI</text><text x="57" y="298">MAKER</text><text x="392" y="295">OSS</text></g>
    <g fill="#0f2029" stroke="#36545d" stroke-width="1.6"><rect x="207" y="66" width="120" height="44" rx="10"/><rect x="80" y="177" width="102" height="44" rx="10"/><rect x="215" y="173" width="104" height="52" rx="10"/><rect x="349" y="185" width="93" height="44" rx="10"/><rect x="83" y="267" width="98" height="44" rx="10"/><rect x="344" y="264" width="101" height="44" rx="10"/></g>
    <g font-size="13" text-anchor="middle" fill="#e9f6f7" font-family="monospace"><text x="267" y="93">HOME</text><text x="131" y="204">AI</text><text x="267" y="204">SERVERS</text><text x="396" y="211">SYNC</text><text x="132" y="292">3D / CAD</text><text x="395" y="289">PRs</text></g>
   </svg>
   <div class="hero-visual-bottom"><span><i class="status-indicator"></i><?= $lang->value('Simulovaná mapa projektů','Project overview illustration') ?></span><a href="<?= $lang->path('/homelab') ?>">OPEN ↗</a></div>
  </div>
 </div>
</section>
<section class="stats-strip shell" aria-label="<?= $lang->value('Ověřitelné počty v katalogu', 'Verifiable catalog counts') ?>">
 <div><strong><?= $projectCount ?></strong><span><?= $lang->value('projektových studií v katalogu', 'curated project studies') ?></span></div>
 <div><strong><?= $mergedPrCount ?></strong><span><?= $lang->value('sloučených externích PR v přehledu', 'merged external PRs in the activity feed') ?></span></div>
 <div><strong><?= count($recentRepos) ?></strong><span><?= $lang->value('posledních aktualizací repozitářů', 'recent repository updates') ?></span></div>
 <div class="stat-note"><?= $provenance['source'] === 'github-public-api'
     ? $lang->value('Údaje z posledního úspěšného veřejného importu GitHubu; nejde o celoživotní statistiky.', 'Counts use the latest successful public GitHub import, not lifetime statistics.')
     : $lang->value('Údaje z kurátorovaného katalogu a veřejného historického snímku; nejde o živá ani celoživotní čísla.', 'Counts use the curated catalog and a reviewed historical snapshot, not live or lifetime statistics.') ?></div>
</section>
<section class="section shell">
 <div class="section-heading"><div><span class="eyebrow">01 / THE LAB</span><h2><?= $lang->value('Prozkoumej, jak věci fungují.','Explore how things work.') ?></h2><p><?= $lang->value('Některé systémy si můžeš rozkliknout, jiné přímo ovládat v bezpečné simulaci.', 'Inspect real architecture and try safe, interactive simulations.') ?></p></div><a class="inline-link" href="<?= $lang->path('/playground') ?>"><?= $lang->value('Všechny demonstrace','All demos') ?> ↗</a></div>
 <div class="explorer-grid">
  <a href="<?= $lang->path('/homelab') ?>" class="explorer-card explorer-primary"><span class="card-index">LAB / 001</span><div class="mini-network" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span><i></i><i></i><i></i></div><div><span class="category">INFRASTRUCTURE</span><h3>Home Lab Explorer</h3><p><?= $lang->value('Klikací mapa sítě. Vypni virtuální zařízení a sleduj dopad na služby.','A clickable network map. Disable virtual devices to inspect dependent services.') ?></p><span class="card-action"><?= $lang->value('Otevřít laboratoř', 'Open lab') ?> ↗</span></div></a>
  <a href="<?= $lang->path('/playground') ?>#ai" class="explorer-card"><span class="card-index">LAB / 002</span><div class="visual-rows" aria-hidden="true"><i></i><i></i><i></i><i></i></div><div><span class="category">LOCAL AI</span><h3>Agent Workflow</h3><p><?= $lang->value('Procházej krok za krokem, jak agent zjišťuje kontext a kontroluje změny.','Step through agent context discovery and verification.') ?></p><span class="card-action">EXPLORE ↗</span></div></a>
  <a href="<?= $lang->path('/playground') ?>#sync" class="explorer-card"><span class="card-index">LAB / 003</span><div class="visual-sync" aria-hidden="true"><b>LOCAL</b><span>⇆</span><b>REMOTE</b></div><div><span class="category">DESKTOP TOOLS</span><h3>Sync Sandbox</h3><p><?= $lang->value('Vyvolej změny, synchronizuj soubory a vyzkoušej konflikt.','Edit files, synchronize state and trigger a conflict.') ?></p><span class="card-action">EXPLORE ↗</span></div></a>
  <a href="<?= $lang->path('/playground') ?>#cad" class="explorer-card"><span class="card-index">LAB / 004</span><div class="visual-cube" aria-hidden="true"><span></span><i></i></div><div><span class="category">ENGINEERING</span><h3>3D Workbench</h3><p><?= $lang->value('Otáčej 3D geometrií nebo načti vlastní STL přímo ve svém prohlížeči.','Rotate geometry or load an STL file locally in your browser.') ?></p><span class="card-action">EXPLORE ↗</span></div></a>
 </div>
</section>
<section class="shell"><a class="connections-promo" href="<?= $lang->path('/connections') ?>"><span class="eyebrow">PROJECT KNOWLEDGE GRAPH</span><strong><?= $lang->value('Jak spolu moje projekty souvisí?','How do my projects connect?') ?></strong><span><?= $lang->value('Interaktivní mapa systémů a jejich vzájemných vazeb', 'An interactive map of systems and their relationships') ?> ↗</span></a></section>
<section class="shell"><a class="connections-promo" href="<?= $lang->path('/decisions') ?>"><span class="eyebrow">ARCHITECTURE DECISION EXPLORER</span><strong><?= $lang->value('Proč právě tohle řešení?', 'Why this design?') ?></strong><span><?= $lang->value('Problémy, rozhodnutí, kompromisy a skutečná omezení','Problems, decisions, trade-offs and real limitations') ?> ↗</span></a></section>
<section class="section shell">
 <div class="section-heading"><div><span class="eyebrow">02 / SELECTED WORK</span><h2><?= $lang->value('Projekty bez pozlátka.','Work beyond the pitch.') ?></h2><p><?= $lang->value('Proč vznikly, jak jsou postavené a co je na nich zatím nedokončené.', 'Why they exist, how they work and what remains unfinished.') ?></p></div><a class="inline-link" href="<?= $lang->path('/projects') ?>"><?= $lang->value('Katalog projektů','Project index') ?> ↗</a></div>
 <div class="project-grid">
 <?php foreach ($projects as $i => $project): ?><a class="project-card" href="<?= $lang->path('/projects/'.$project->slug) ?>"><div class="project-card-top"><span class="project-glyph glyph-<?= Html::escape($project->category === 'Infrastructure' ? 'infra' : ($project->category === 'AI & Developer Tools' ? 'ai' : 'tool')) ?>" aria-hidden="true">0<?= $i+1 ?></span><span class="status <?= Html::escape($project->status) ?>"><?= Html::escape(strtoupper($project->status)) ?></span></div><span class="category"><?= Html::escape($project->category) ?></span><h3><?= Html::escape($project->name) ?></h3><p><?= Html::escape($project->summary($lang)) ?></p><div class="stack-list"><?php foreach (array_slice($project->stack,0,3) as $tag): ?><span><?= Html::escape($tag) ?></span><?php endforeach; ?></div><span class="card-action"><?= $lang->value('Podrobnosti','Details') ?> ↗</span></a><?php endforeach; ?>
 </div>
</section>
<section class="shell recent-work" aria-label="Recent public repository activity"><div><span class="eyebrow">FRESH FROM GITHUB</span><h2><?= $lang->value('Poslední veřejná aktivita.','Recent public activity.') ?></h2><p><?= $lang->value('Poslední push veřejných vlastních repozitářů, nikoli živý záznam práce. Aktualizuje se pomocí naplánovaného importu.', 'Latest public original-repository pushes, not a live work session. Refreshed by scheduled import.') ?></p></div><div class="recent-items"><?php foreach (array_slice($recentRepos,0,3) as $repo): ?><a href="<?= Html::escape(Html::safeUrl((string)$repo['repo_url'])) ?>" target="_blank" rel="noopener noreferrer"><span class="recent-dot"></span><strong><?= Html::escape((string)$repo['repository']) ?></strong><time><?= Html::escape(substr((string)$repo['pushed_at'],0,16)) ?></time><span>↗</span></a><?php endforeach; ?></div></section>
<section class="section shell workshop-section"><div class="section-heading"><div><span class="eyebrow">03 / WORKSHOP LOG</span><h2><?= $lang->value('Co se právě staví.','What is being built.') ?></h2><p><?= $lang->value('Zápisy z vývoje místo marketingových oznámení.','Development notes instead of marketing announcements.') ?></p></div><a class="inline-link" href="<?= $lang->path('/journal') ?>">WORKSHOP LOG ↗</a></div><div class="journal-preview"><?php foreach ($journal as $entry): ?><article><span class="journal-date"><?= Html::escape(substr((string)$entry['published_at'],0,10)) ?></span><div><span class="category"><?= Html::escape((string)$entry['category']) ?></span><h3><?= Html::escape((string)$entry['title_'.$lang->code]) ?></h3><p><?= Html::escape((string)$entry['body_'.$lang->code]) ?></p></div></article><?php endforeach; ?></div></section>
<section class="shell ending-cta"><span class="eyebrow">THE WHOLE PICTURE</span><h2><?= $lang->value('Méně slov. Více fungujících věcí.','Less talk. More working things.') ?></h2><p><?= $lang->value('Podívej se na skutečné změny ve zdrojovém kódu, otestuj simulátory nebo projdi architekturu domácích služeb.','Inspect actual upstream code changes, try a simulator, or trace the architecture of home services.') ?></p><a class="btn btn-primary" href="<?= $lang->path('/open-source') ?>"><?= $lang->value('Procházet open source','Browse open source') ?> ↗</a></section>
