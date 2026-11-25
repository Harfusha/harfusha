<?php
declare(strict_types=1);
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var App\Domain\Project $project */
/** @var list<array{slug:string,name:string,type:string}> $related */
/** @var list<App\Domain\EngineeringDecision> $decisions */
/** @var list<App\Domain\EngineeringClaim> $evidence */
require dirname(__DIR__) . '/element/portfolio_case_reading.php';
[$caseProblem, $caseApproach, $caseEvidence, $caseEvidenceCopy] = $caseReading;
$sourceHref = !$project->private && $project->repo !== '' ? Html::safeUrl($project->repo) : null;
$networkMapHref = $project->slug === 'home-infrastructure' ? $lang->path('/homelab#network-model') : null;
$featureHeading = $project->status === 'prototype'
    ? $lang->value('Oblasti, které prototyp zkoumá', 'Areas explored by the prototype')
    : $lang->value('Hlavní funkce', 'Key features');
$showFeatureList = $project->status !== 'record' && !in_array($project->slug, ['scan2cad', 'tiskov'], true);
$showDecisionSummary = $decisions !== [] && !in_array($project->slug, ['scan2cad', 'tiskov'], true);
$stackHeading = match ($project->slug) {
    'tiskov' => $lang->value('NÁVRHOVÁ SKLADBA', 'PROPOSED STACK'),
    'discord-bot' => $lang->value('TECHNOLOGIE PROVOZU', 'OPERATIONS STACK'),
    default => $lang->value('TECHNOLOGIE', 'TECH STACK'),
};
?>
<section class="page-hero shell project-detail-hero" data-case-status="<?= Html::escape($project->status) ?>">
    <div class="case-intro">
        <a class="back-link" href="<?= $lang->path('/projects') ?>"><?= $lang->value('← Všechny projekty', '← All projects') ?></a>
        <div class="detail-kicker">
            <span class="category"><?= Html::escape($project::categoryLabel($project->category, $lang)) ?></span>
            <span class="status <?= Html::escape($project->status) ?>"><?= Html::escape($project->statusLabel($lang)) ?></span>
            <span><?= Html::escape($project->year) ?></span>
        </div>
        <h1><?= Html::escape($project->name) ?></h1>
        <p><?= Html::escape($project->summary($lang)) ?></p>
        <div class="detail-links">
            <?php if ($sourceHref !== null): ?>
                <a class="btn btn-primary" href="<?= Html::escape($sourceHref) ?>" target="_blank" rel="noopener noreferrer"><?= $lang->value('Otevřít zdrojový kód', 'View source code') ?> <span aria-hidden="true">↗</span></a>
            <?php endif; ?>
            <?php if ($networkMapHref !== null): ?>
                <a class="btn btn-primary" href="<?= Html::escape($networkMapHref) ?>"><?= $lang->value('Prozkoumat mapu závislostí', 'Explore the dependency map') ?> <span aria-hidden="true">↓</span></a>
            <?php endif; ?>
            <?php if ($project->slug === 'discord-bot'): ?>
                <a class="btn btn-primary" href="<?= $lang->path('/homelab#node-pi') ?>"><?= $lang->value('Zobrazit Raspberry Pi v mapě sítě', 'View Raspberry Pi on network map') ?> <span aria-hidden="true">→</span></a>
            <?php endif; ?>
            <?php if ($project->slug === 'scan2cad'): ?>
                <a class="btn btn-primary" href="<?= $lang->path('/decisions#scan-to-cad-boundary') ?>"><?= $lang->value('Technické rozhodnutí: Mesh vs CAD', 'Engineering decision: Mesh vs CAD') ?> <span aria-hidden="true">→</span></a>
                <a class="btn btn-ghost" href="<?= $lang->path('/maker') ?>"><?= $lang->value('Přehled 3D projektů', '3D projects overview') ?></a>
            <?php elseif ($project->slug === 'tiskov'): ?>
                <a class="btn btn-primary" href="<?= $lang->path('/decisions#slicing-worker') ?>"><?= $lang->value('Technické rozhodnutí: Asynchronní slicing', 'Engineering decision: Async slicing') ?> <span aria-hidden="true">→</span></a>
                <a class="btn btn-ghost" href="<?= $lang->path('/maker') ?>"><?= $lang->value('Přehled 3D projektů', '3D projects overview') ?></a>
            <?php endif; ?>
            <?php if (in_array($project->slug, ['woodbound', 'shpotify', 'softwareinc-multiplayer'], true)): ?>
                <a class="btn btn-primary" href="<?= $lang->path('/connections#map-project-' . $project->slug) ?>"><?= $lang->value('Zobrazit v tematické skupině', 'View in project group') ?> <span aria-hidden="true">→</span></a>
            <?php endif; ?>
            <?php if ($project->slug === 'local-ai-hub'): ?>
                <a class="inline-link" href="#hub-preview-title"><?= $lang->value('Diagram architektury', 'Architecture diagram') ?> <span aria-hidden="true">↓</span></a>
                <a class="inline-link" href="#ai-context-playground"><?= $lang->value('Context Playground', 'Context Playground') ?> <span aria-hidden="true">↓</span></a>
            <?php endif; ?>
            <?php if ($project->slug === 'scan2cad'): ?>
                <a class="inline-link" href="#mesh-to-cad-explorer"><?= $lang->value('Mesh to CAD Explorer', 'Mesh to CAD Explorer') ?> <span aria-hidden="true">↓</span></a>
            <?php endif; ?>
            <?php if ($project->slug === 'harfusha-lab'): ?>
                <a class="inline-link" href="#harfusha-lab-preview-title"><?= $lang->value('Diagram architektury', 'Architecture diagram') ?> <span aria-hidden="true">↓</span></a>
                <a class="inline-link" href="#request-lifecycle-lab"><?= $lang->value('Request Lifecycle Lab', 'Request Lifecycle Lab') ?> <span aria-hidden="true">↓</span></a>
                <a class="inline-link" href="#database-concurrency-lab"><?= $lang->value('Database Concurrency Lab', 'Database Concurrency Lab') ?> <span aria-hidden="true">↓</span></a>
            <?php endif; ?>
            <?php if ($project->slug === 'mutaui'): ?>
                <a class="inline-link" href="#mutaui-preview-title"><?= $lang->value('Snímky aplikace', 'Application screenshots') ?> <span aria-hidden="true">↓</span></a>
            <?php endif; ?>
            <?php if ($project->slug === 'woodbound'): ?>
                <a class="inline-link" href="#project-source-visual-title"><?= $lang->value('Vývojový záběr', 'Development capture') ?> <span aria-hidden="true">↓</span></a>
            <?php endif; ?>
        </div>
    </div>
    <section class="case-evidence" aria-labelledby="case-evidence-title">
        <h2 id="case-evidence-title"><?= Html::escape($caseEvidence) ?></h2>
        <p><?= Html::escape($caseEvidenceCopy) ?></p>
        <?php if (!empty($evidence)): ?>
        <div class="evidence-ledger" aria-label="<?= $lang->value('Strukturovaný registr ověřitelných důkazů', 'Structured verifiable evidence ledger') ?>">
            <h3 class="evidence-ledger-title"><?= $lang->value('Ověřitelná technická tvrzení a podklady', 'Verifiable technical claims and evidence') ?></h3>
            <div class="evidence-items">
                <?php foreach ($evidence as $claim): ?>
                <div class="evidence-record" data-claim-id="<?= Html::escape($claim->id) ?>">
                    <p class="evidence-claim-text"><strong><?= $lang->value('Tvrzení:', 'Claim:') ?></strong> <?= Html::escape($lang->value($claim->claim['cs'], $claim->claim['en'])) ?></p>
                    <ul class="evidence-list">
                        <?php foreach ($claim->evidence as $item): ?>
                        <li class="evidence-item status-<?= Html::escape($item->verificationStatus) ?> kind-<?= Html::escape($item->kind) ?>">
                            <div class="evidence-item-header">
                                <span class="badge badge-kind"><?= Html::escape(strtoupper($item->kind)) ?></span>
                                <span class="badge badge-status"><?= $item->verificationStatus === 'verified' ? $lang->value('Ověřeno', 'Verified') : $lang->value('Neověřeno', 'Unverified') ?></span>
                                <?php if ($item->revision !== null): ?>
                                <code class="evidence-revision"><?= Html::escape($item->revision) ?></code>
                                <?php endif; ?>
                                <?php if ($item->verifiedAt !== null): ?>
                                <time class="evidence-date" datetime="<?= Html::escape($item->verifiedAt) ?>"><?= Html::escape($item->verifiedAt) ?></time>
                                <?php endif; ?>
                            </div>
                            <p class="evidence-method"><?= Html::escape($item->method) ?></p>
                            <?php if ($item->limitations !== []): ?>
                            <div class="evidence-limitations">
                                <span class="limitation-label"><?= $lang->value('Rozsah a omezení:', 'Scope & limitations:') ?></span>
                                <ul>
                                    <?php foreach ($item->limitations as $limitation): ?>
                                    <li><?= Html::escape($limitation) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                            <?php if ($item->sourceUrl !== null): ?>
                            <a class="evidence-source-link inline-link" href="<?= Html::escape(Html::safeUrl($item->sourceUrl)) ?>" target="_blank" rel="noopener noreferrer">
                                <?= $lang->value('Otevřít veřejný zdrojový odkaz', 'Open public source link') ?> ↗
                            </a>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>
</section>
<nav class="shell case-navigation" aria-label="<?= $project->status === 'record' ? $lang->value('Obsah záznamu', 'On this record') : $lang->value('Obsah případové studie', 'On this case study') ?>">
    <a href="#project-overview"><?= $lang->value('Přehled', 'Overview') ?></a>
    <a href="#case-evidence-title"><?= $lang->value('Důkazy a omezení', 'Evidence and limits') ?></a>
    <?php if ($project->slug === 'local-ai-hub'): ?>
    <a href="#ai-context-playground"><?= $lang->value('Context Playground', 'Context Playground') ?></a>
    <?php endif; ?>
    <?php if ($project->slug === 'scan2cad'): ?>
    <a href="#mesh-to-cad-explorer"><?= $lang->value('CAD Explorer', 'CAD Explorer') ?></a>
    <?php endif; ?>
    <?php if ($project->slug === 'harfusha-lab'): ?>
    <a href="#request-lifecycle-lab"><?= $lang->value('Lifecycle Lab', 'Lifecycle Lab') ?></a>
    <a href="#database-concurrency-lab"><?= $lang->value('Concurrency Lab', 'Concurrency Lab') ?></a>
    <?php endif; ?>
</nav>
<?php if ($project->slug === 'local-ai-hub'): ?>
<section class="shell project-preview" aria-labelledby="hub-preview-title">
 <div class="project-preview-copy">
  <span class="eyebrow"><?= $lang->value('ARCHITEKTURA SYSTÉMU', 'SYSTEM ARCHITECTURE') ?></span>
  <h2 id="hub-preview-title"><?= $lang->value('Architektura a pipeline Local AI Hubu', 'Local AI Hub Architecture & Pipeline') ?></h2>
  <p><?= $lang->value('Deterministický pipeline pro kódovací agenty: od indexace AST symbolů přes správu tokenového rozpočtu až po koordinaci úloh v SQLite WAL a volitelnou lokální inferenci.', 'A deterministic pipeline for coding agents: from AST symbol indexing to token budget management, SQLite WAL task coordination, and optional local inference.') ?></p>
  <a class="inline-link" href="https://github.com/Harfusha/local-ai-hub" target="_blank" rel="noopener noreferrer"><?= $lang->value('Repozitář a dokumentace na GitHubu', 'Repository and documentation on GitHub') ?> ↗</a>
 </div>
 <figure><picture>
  <source type="image/webp" srcset="/assets/img/local-ai-hub-architecture.webp">
  <img src="/assets/img/local-ai-hub-architecture.png" width="1380" height="900" loading="lazy" alt="<?= $lang->value('Architektonický diagram Local AI Hubu: agenti, MCP protokol, jádro a repozitář', 'Local AI Hub architectural blueprint: agents, MCP protocol, core engine, and repository') ?>">
 </picture><figcaption><?= $lang->value('Local AI Hub / architektura pipeline / MCP server', 'Local AI Hub / pipeline architecture / MCP server') ?></figcaption></figure>
</section>
<?php require dirname(__DIR__) . '/element/lab_context.php'; ?>
<?php endif; ?>
<?php if ($project->slug === 'harfusha-lab'): ?>
<section class="shell project-preview" aria-labelledby="harfusha-lab-preview-title">
 <div class="project-preview-copy">
  <span class="eyebrow"><?= $lang->value('ARCHITEKTURA SYSTÉMU', 'SYSTEM ARCHITECTURE') ?></span>
  <h2 id="harfusha-lab-preview-title"><?= $lang->value('Architektura a vrstvy Harfusha Lab', 'Harfusha Lab Architecture and Layers') ?></h2>
  <p><?= $lang->value('Server-side rendering v CakePHP 5: od reverzní proxy ochrany přes striktně typované doménové DTO a modelové vrstvy až po offline SSR náhled s nulovou chybovostí v PHPStan max.', 'Server-side rendering in CakePHP 5: from reverse proxy boundary defense to strictly typed domain DTOs, model layers, and offline SSR preview with zero errors under PHPStan max.') ?></p>
  <a class="inline-link" href="https://github.com/Harfusha/harfusha" target="_blank" rel="noopener noreferrer"><?= $lang->value('Zdrojový repozitář na GitHubu', 'Source repository on GitHub') ?> ↗</a>
 </div>
 <figure><picture>
  <source type="image/webp" srcset="/assets/img/developer-workbench.webp">
  <img src="/assets/img/developer-workbench.png" width="1380" height="900" loading="lazy" alt="<?= $lang->value('Architektura platformy Harfusha Lab: CakePHP 5 SSR, typové DTO a ověřitelná evidence', 'Harfusha Lab platform architecture: CakePHP 5 SSR, typed DTOs, and verifiable evidence') ?>">
 </picture><figcaption><?= $lang->value('Harfusha Lab / architektura platformy / CakePHP 5 SSR', 'Harfusha Lab / platform architecture / CakePHP 5 SSR') ?></figcaption></figure>
</section>
<?php require dirname(__DIR__) . '/element/lab_lifecycle.php'; ?>
<?php require dirname(__DIR__) . '/element/lab_concurrency.php'; ?>
<?php endif; ?>
<?php if ($project->slug === 'mutaui'): ?>
<section class="shell project-preview" aria-labelledby="mutaui-preview-title">
 <div class="project-preview-copy">
  <span class="eyebrow"><?= $lang->value('POHLED DO APLIKACE', 'INSIDE THE APPLICATION') ?></span>
  <h2 id="mutaui-preview-title"><?= $lang->value('Přesměrování portů v MutaUI', 'Sessions and services in MutaUI') ?></h2>
  <p><?= $lang->value('V rozhraní poznáš, odkud a kam relace přesměrovává provoz a v jakém je stavu. Snímek pochází z offline dema s ukázkovými daty a vypnutým ovládáním.', 'See the sync and forwarding sessions, the daemon and system resources in one window. This screenshot is from an offline demo with sample data and disabled actions.') ?></p>
  <a class="inline-link" href="https://github.com/Harfusha/MutaUI/blob/master/docs/screenshots.md" target="_blank" rel="noopener noreferrer"><?= $lang->value('Další snímky a jejich původ', 'More screenshots and their source') ?> ↗</a>
 </div>
 <figure><picture>
  <source type="image/webp" srcset="<?= $lang->value('/assets/img/mutaui-forward-cs-dark.webp', '/assets/img/mutaui-overview-en-dark.webp') ?>">
  <img src="<?= $lang->value('/assets/img/mutaui-forward-cs-dark.png', '/assets/img/mutaui-overview-en-dark.png') ?>" width="1380" height="900" loading="lazy" alt="<?= $lang->value('MutaUI s ukázkovými relacemi přesměrování portů', 'MutaUI overview with sample sessions and charts') ?>">
 </picture><figcaption><?= $lang->value('MutaUI / offline demo / ukázková data', 'MutaUI / offline demo / synthetic data') ?></figcaption></figure>
</section>
<details class="shell project-gallery">
 <summary><?= $lang->value('Další snímek aplikace', 'Another application screenshot') ?></summary>
 <figure><picture>
  <source type="image/webp" srcset="<?= $lang->value('/assets/img/mutaui-overview-cs-light.webp', '/assets/img/mutaui-sync-en-light.webp') ?>">
  <img src="<?= $lang->value('/assets/img/mutaui-overview-cs-light.png', '/assets/img/mutaui-sync-en-light.png') ?>" width="1380" height="900" loading="lazy" alt="<?= $lang->value('Přehledové okno MutaUI s ukázkovými relacemi a grafy', 'MutaUI: sample synchronization session overview') ?>">
 </picture><figcaption><?= $lang->value('MutaUI / offline demo / ukázková data', 'MutaUI / offline demo / synthetic data') ?></figcaption></figure>
</details>
<?php endif; ?>
<?php
$sourceVisual = match ($project->slug) {
    'woodbound' => [
        '/assets/img/woodbound-region-development.png', '/assets/img/woodbound-region-development.webp', 1280, 720,
        $lang->value('Pracovní podoba lesní oblasti', 'Development scene: forest region'),
        $lang->value('Záběr z vývoje mapy zachycuje lesní oblast a jednu z tras.', 'This world-map development capture shows a forest region and one of its routes.'),
        $lang->value('Vývojový záběr z 8. 9. 2026.', 'Development capture from 8 Sep 2026.'),
        $lang->value('Vývojový záběr Woodboundu: lesní krajina a cesta', 'Woodbound development capture: forest landscape and road'),
    ],
    default => null,
};
if ($sourceVisual !== null):
    [$visualSrc, $visualWebp, $visualWidth, $visualHeight, $visualTitle, $visualDescription, $visualCaption, $visualAlt] = $sourceVisual;
?>
<section class="shell project-preview project-source-visual" data-project-source-image aria-labelledby="project-source-visual-title">
 <div class="project-preview-copy"><span class="eyebrow"><?= $lang->value('OBRÁZEK Z PODKLADŮ', 'SOURCE IMAGE') ?></span><h2 id="project-source-visual-title"><?= Html::escape($visualTitle) ?></h2><p><?= Html::escape($visualDescription) ?></p></div>
 <figure><picture>
  <source type="image/webp" srcset="<?= Html::escape($visualWebp) ?>">
  <img src="<?= Html::escape($visualSrc) ?>" width="<?= $visualWidth ?>" height="<?= $visualHeight ?>" loading="lazy" alt="<?= Html::escape($visualAlt) ?>">
 </picture><figcaption><?= Html::escape($visualCaption) ?></figcaption></figure>
</section>
<?php endif; ?>
<?php if ($project->slug === 'scan2cad'): ?>
<?php require dirname(__DIR__) . '/element/lab_mesh2cad.php'; ?>
<?php endif; ?>
<section class="section shell project-case" id="project-overview" aria-label="<?= $project->status === 'record' ? $lang->value('Přehled záznamu', 'Record overview') : $lang->value('Přehled studie', 'Case study overview') ?>"><div class="case-main"><div class="case-block"><h2><?= Html::escape($caseProblem) ?></h2><p><?= Html::escape($project->problem($lang)) ?></p></div><div class="case-block"><h2><?= Html::escape($caseApproach) ?></h2><p><?= Html::escape($project->solution($lang)) ?></p></div><?php if ($showFeatureList): ?><div class="case-block"><h2><?= Html::escape($featureHeading) ?></h2><ul class="feature-list"><?php foreach ($project->localizedFeatures($lang) as $feature): ?><li><?= Html::escape($feature) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
</div><div class="case-sidebar"><div class="sidebar-panel"><span class="eyebrow"><?= Html::escape($stackHeading) ?></span><div class="stack-list stack-large"><?php foreach ($project->stack as $tech): ?><span><?= Html::escape($tech) ?></span><?php endforeach; ?></div></div><?php if ($related !== []): ?><div class="sidebar-panel"><span class="eyebrow"><?= $lang->value('PROPOJENÉ PROJEKTY','RELATED PROJECTS') ?></span><ul class="service-list"><?php foreach ($related as $item): ?><li><a class="inline-link" href="<?= $lang->path('/projects/'.$item['slug']) ?>"><?= Html::escape($item['name']) ?></a><small> · <?= Html::escape(App\Domain\ProjectConnections::label($item['type'], $lang)) ?></small></li><?php endforeach; ?></ul><a class="inline-link" href="<?= $lang->path('/connections') ?>"><?= $lang->value('Prohlédnout skupiny projektů','Browse project groups') ?> ↗</a></div><?php endif; ?><?php if ($showDecisionSummary): ?><div class="sidebar-panel"><span class="eyebrow"><?= $lang->value('ARCHITEKTURA A KOMPROMISY','ARCHITECTURE & TRADE-OFFS') ?></span>
<?php foreach ($decisions as $decision): ?><div class="decision-summary"><h3><?= Html::escape($decision->title($lang)) ?></h3><p><?= Html::escape($decision->choice($lang)) ?></p><p><strong><?= $lang->value("Kompromis: ", "Trade-off: ") ?></strong><?= Html::escape($decision->tradeoff($lang)) ?></p><small><?= Html::escape($decision->evidence($lang)) ?></small></div><?php endforeach; ?>
<a class="inline-link" href="<?= $lang->path('/decisions') ?>"><?= $lang->value('Všechna technická rozhodnutí','All engineering decisions') ?> ↗</a></div><?php endif; ?>
<?php if (in_array($project->slug, ['scan2cad', 'tiskov'], true)): ?>
<div class="sidebar-panel">
    <span class="eyebrow"><?= $lang->value('KONTEXT PROJEKTU', 'PROJECT CONTEXT') ?></span>
    <p><?= $lang->value('Projekt je součástí tematického okruhu digitálního modelování a přípravy 3D tisku.', 'This project is part of the digital modeling and 3D printing preparation theme.') ?></p>
    <a class="inline-link" href="<?= $lang->path('/maker') ?>"><?= $lang->value('Přehled 3D projektů', '3D projects overview') ?> ↗</a>
    <br>
    <a class="inline-link" href="<?= $lang->path('/decisions#' . ($project->slug === 'scan2cad' ? 'scan-to-cad-boundary' : 'slicing-worker')) ?>"><?= $lang->value('Technické rozhodnutí', 'Engineering decision') ?> ↗</a>
</div>
<?php endif; ?>
<?php if ($project->slug === 'discord-bot'): ?>
<div class="sidebar-panel">
    <span class="eyebrow"><?= $lang->value('HOSTITELSKÁ INFRASTRUKTURA', 'HOST INFRASTRUCTURE') ?></span>
    <p><?= $lang->value('Služba běží jako izolovaná systemd jednotka na primárním uzlu sítě pod vyhrazeným systémovým účtem.', 'The service runs as an isolated systemd unit on the primary network node under a dedicated system user.') ?></p>
    <a class="inline-link" href="<?= $lang->path('/projects/home-infrastructure') ?>"><?= $lang->value('Projekt Home Infrastructure', 'Home Infrastructure project') ?> ↗</a>
    <br>
    <a class="inline-link" href="<?= $lang->path('/homelab#node-pi') ?>"><?= $lang->value('Zobrazit Raspberry Pi v mapě sítě', 'View Raspberry Pi on network map') ?> ↗</a>
</div>
<?php endif; ?>
</div></section>
