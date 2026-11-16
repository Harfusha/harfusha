<?php
declare(strict_types=1);
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var App\Domain\Project $project */
/** @var list<array{slug:string,name:string,type:string}> $related */
/** @var list<App\Domain\EngineeringDecision> $decisions */
?>
<section class="page-hero shell project-detail-hero"><a class="back-link" href="<?= $lang->path('/projects') ?>">← <?= $lang->value('Všechny projekty','All projects') ?></a><div class="detail-kicker"><span class="category"><?= Html::escape($project::categoryLabel($project->category, $lang)) ?></span><span class="status <?= Html::escape($project->status) ?>"><?= Html::escape($project->statusLabel($lang)) ?></span><span><?= Html::escape($project->year) ?></span></div><h1><?= Html::escape($project->name) ?></h1><p><?= Html::escape($project->summary($lang)) ?></p><div class="detail-links"><?php if (!$project->private && $project->repo !== ''): ?><a class="btn btn-primary" href="<?= Html::escape(Html::safeUrl($project->repo)) ?>" target="_blank" rel="noopener noreferrer"><?= $lang->value('Zdrojový kód','View source') ?> ↗</a><?php endif; ?><?php if ($project->demo !== ''): ?><a class="btn btn-ghost" href="<?= $lang->path($project->demo === 'homelab' ? '/homelab' : '/playground#'.$project->demo) ?>"><?= $lang->value('Vyzkoušet demonstraci','Try interactive demo') ?> →</a><?php endif; ?><?php if ($project->slug === 'mutaui'): ?><a class="btn btn-ghost" href="https://github.com/Harfusha/MutaUI/blob/master/docs/screenshots.md" target="_blank" rel="noopener noreferrer"><?= $lang->value('Snímky aplikace','Application screenshots') ?> ↗</a><?php endif; ?></div></section>
<?php if ($project->slug === 'mutaui'): ?>
<section class="shell project-preview" aria-labelledby="mutaui-preview-title">
 <div class="project-preview-copy">
  <span class="eyebrow"><?= $lang->value('POHLED DO APLIKACE', 'INSIDE THE APPLICATION') ?></span>
  <h2 id="mutaui-preview-title"><?= $lang->value('Synchronizace na jednom místě.', 'Sync at a glance.') ?></h2>
  <p><?= $lang->value('Přehled relací, služby a prostředků systému v desktopovém rozhraní. Snímek pochází z offline dema se smyšlenými daty; ovládání je v něm vypnuté.', 'Sessions, the daemon and system resources in one desktop view. This screenshot is from an offline demo with synthetic data and disabled actions.') ?></p>
  <a class="inline-link" href="https://github.com/Harfusha/MutaUI/blob/master/docs/screenshots.md" target="_blank" rel="noopener noreferrer"><?= $lang->value('Další snímky a jejich původ', 'More screenshots and their source') ?> ↗</a>
 </div>
 <figure><img src="<?= $lang->value('/assets/img/mutaui-overview-cs-light.png', '/assets/img/mutaui-overview-en-dark.png') ?>" width="1380" height="900" loading="lazy" alt="<?= $lang->value('Přehledové okno MutaUI s ukázkovými relacemi a grafy', 'MutaUI overview with sample sessions and charts') ?>"><figcaption><?= $lang->value('MutaUI / offline demo / ukázková data', 'MutaUI / offline demo / synthetic data') ?></figcaption></figure>
</section>
<section class="shell project-preview" aria-labelledby="mutaui-second-preview-title">
 <div class="project-preview-copy"><span class="eyebrow"><?= $lang->value('RELACE V DETAILU', 'SESSIONS IN DETAIL') ?></span><h2 id="mutaui-second-preview-title"><?= $lang->value('Přesměrování a synchronizace', 'Forwarding and synchronization') ?></h2><p><?= $lang->value('Další pohled do skutečného rozhraní offline dema. Cesty, názvy a stavy jsou ukázkové; tlačítka v demu neprovádějí operace.', 'Another view of the actual offline demo interface. Paths, names and states are synthetic; demo buttons do not perform operations.') ?></p></div>
 <figure><img src="<?= $lang->value('/assets/img/mutaui-forward-cs-dark.png', '/assets/img/mutaui-sync-en-light.png') ?>" width="1380" height="900" loading="lazy" alt="<?= $lang->value('MutaUI: ukázkový přehled přesměrování', 'MutaUI: sample synchronization session overview') ?>"><figcaption><?= $lang->value('MutaUI / offline demo / ukázková data', 'MutaUI / offline demo / synthetic data') ?></figcaption></figure>
</section>
<?php endif; ?>
<?php
$sourceVisual = match ($project->slug) {
    'tiskov' => [
        '/assets/img/tiskov-bambu-lab-a1.png', 1200, 630,
        $lang->value('Odhad tisku pro Bambu Lab A1', 'Estimate for Bambu Lab A1 printing'),
        $lang->value('Grafika pochází z podkladů projektu Tiskov. Popisuje orientační výpočet, ruční kontrolu a absenci online platby; nejde o snímek běžící objednávky.', 'This graphic comes from the Tiskov project assets. It describes an indicative estimate, manual review and no online payment; it is not a screenshot of a live order.'),
        $lang->value('Projektová grafika; nikoli screenshot aplikace nebo potvrzení konkrétní ceny.', 'Project graphic; not an application screenshot or confirmation of a specific price.'),
        $lang->value('Grafika Tiskov pro 3D tisk na Bambu Lab A1', 'Tiskov graphic for 3D printing on a Bambu Lab A1'),
    ],
    'woodbound' => [
        '/assets/img/woodbound-region-development.png', 1280, 720,
        $lang->value('Pracovní podoba lesní oblasti', 'Development scene: forest region'),
        $lang->value('Záběr pochází z vývojových podkladů mapy. Ukazuje pracovní scénu, ale nepotvrzuje přijetí ani hratelnost celého světa.', 'This scene comes from world-map development evidence. It shows a work in progress, not acceptance or playability of the complete world.'),
        $lang->value('Vývojový záběr z 8. 9. 2026; hratelnost celého světa není potvrzena.', 'Development capture from 8 Sep 2026; the complete world is not confirmed playable.'),
        $lang->value('Vývojový záběr Woodboundu: lesní krajina a cesta', 'Woodbound development capture: forest landscape and road'),
    ],
    default => null,
};
if ($sourceVisual !== null):
    [$visualSrc, $visualWidth, $visualHeight, $visualTitle, $visualDescription, $visualCaption, $visualAlt] = $sourceVisual;
?>
<section class="shell project-preview project-source-visual" data-project-source-image aria-labelledby="project-source-visual-title">
 <div class="project-preview-copy"><span class="eyebrow"><?= $lang->value('OBRÁZEK Z PODKLADŮ', 'SOURCE IMAGE') ?></span><h2 id="project-source-visual-title"><?= Html::escape($visualTitle) ?></h2><p><?= Html::escape($visualDescription) ?></p></div>
 <figure><img src="<?= Html::escape($visualSrc) ?>" width="<?= $visualWidth ?>" height="<?= $visualHeight ?>" loading="lazy" alt="<?= Html::escape($visualAlt) ?>"><figcaption><?= Html::escape($visualCaption) ?></figcaption></figure>
</section>
<?php endif; ?>
<?php require dirname(__DIR__) . '/element/project_example.php'; ?>
<?php require dirname(__DIR__) . '/element/project_deepdive.php'; ?>

<section class="section shell project-case"><div class="case-main"><div class="case-block"><span class="eyebrow">01 / <?= $lang->value('PROBLÉM','THE PROBLEM') ?></span><h2><?= $lang->value('Proč to vzniklo','Why it exists') ?></h2><p><?= Html::escape($project->problem($lang)) ?></p></div><div class="case-block"><span class="eyebrow">02 / <?= $lang->value('ŘEŠENÍ','THE APPROACH') ?></span><h2><?= $lang->value('Jak je to postavené','How it works') ?></h2><p><?= Html::escape($project->solution($lang)) ?></p></div><div class="case-block"><span class="eyebrow">03 / <?= $lang->value('FUNKCE','CAPABILITIES') ?></span><h2><?= $lang->value('Základní části','Key pieces') ?></h2><ul class="feature-list"><?php foreach ($project->localizedFeatures($lang) as $feature): ?><li><?= Html::escape($feature) ?></li><?php endforeach; ?></ul></div><?php
$projectLimitations = [
    'home-infrastructure' => $lang->value('Popis vychází z dokumentace správy, nikoli z aktuálního monitoringu. Interaktivní mapa je model a nesahá na skutečná zařízení.', 'This description is based on management documentation, not live monitoring. The interactive map is a model and does not operate real devices.'),
    'woodbound' => $lang->value('Prototyp generování světa se stále ověřuje. Dostupné scény a testy samy o sobě nepotvrzují hotovou hratelnou hru.', 'The world-generation prototype is still being verified. Available scenes and tests do not by themselves establish a finished playable game.'),
    'scan2cad' => $lang->value('Výstup je CAD návrh ke kontrole. Správné měřítko a fyzické rozměry je nutné ověřit; výpočet nenahrazuje metrologické měření.', 'The output is a CAD candidate for review. Scale and physical dimensions need verification; the calculation does not replace metrology.'),
    'softwareinc-multiplayer' => $lang->value('Jde o rozpracovaný mod. Automatické testy nepotvrzují přijetí ve hře ani dokončený multiplayer.', 'This mod is in development. Automated tests do not establish in-game acceptance or completed multiplayer.'),
    'shpotify' => $lang->value('Projekt je ve vývoji. Propojení přehrávače a vzdáleného audio agenta pokračuje; skutečný Spotify poslech zatím není doložený.', 'The project is in development. Browser playback and the remote audio agent are still being integrated; actual Spotify listening has not yet been verified.'),
    'local-ai-hub' => $lang->value('Verze 6.1.0 je kandidát na vydání; produkční připravenost není deklarována.', 'Version 6.1.0 is a release candidate; production readiness is not claimed.'),
    'mutaui' => $lang->value('Mutagen CLI se instaluje zvlášť. Snímky zobrazují offline demo; ověření balíčku závisí na konkrétní platformě.', 'Mutagen CLI is installed separately. Screenshots show the offline demo; package verification depends on the platform.'),
];
?>
<div class="notice note-info"><strong><?= $lang->value('Co je dobré vědět','What to know') ?></strong><p><?= Html::escape($projectLimitations[$project->slug] ?? $lang->value('Popis shrnuje dokumentaci projektu; jeho stav se může s vývojem měnit.', 'This description summarizes project documentation; its status can change as work continues.')) ?></p></div></div><aside class="case-sidebar"><div class="sidebar-panel"><span class="eyebrow"><?= $lang->value('TECHNOLOGIE', 'TECH STACK') ?></span><div class="stack-list stack-large"><?php foreach ($project->stack as $tech): ?><span><?= Html::escape($tech) ?></span><?php endforeach; ?></div></div><div class="sidebar-panel"><span class="eyebrow"><?= $lang->value('DOSTUPNOST', 'VISIBILITY') ?></span><h3><?= $project->private ? $lang->value('Soukromá případová studie','Private case study') : $lang->value('Veřejný repozitář na GitHubu', 'Public GitHub Repository') ?></h3><p><?= $project->private ? $lang->value('Zobrazeny jsou schválené popisy, nikoli privátní konfigurace nebo kód.', 'Only curated descriptions are shown, never private configuration or source.') : $lang->value('Otevři zdrojový kód, issues a historii změn na GitHubu.', 'Explore the source, issues and change history on GitHub.') ?></p></div><?php if ($related !== []): ?><div class="sidebar-panel"><span class="eyebrow"><?= $lang->value('PROPOJENÉ PROJEKTY','RELATED PROJECTS') ?></span><ul class="service-list"><?php foreach ($related as $item): ?><li><a class="inline-link" href="<?= $lang->path('/projects/'.$item['slug']) ?>"><?= Html::escape($item['name']) ?></a><small> · <?= Html::escape(App\Domain\ProjectConnections::label($item['type'], $lang)) ?></small></li><?php endforeach; ?></ul><a class="inline-link" href="<?= $lang->path('/connections') ?>"><?= $lang->value('Prozkoumat celou mapu','Explore whole map') ?> ↗</a></div><?php endif; ?><?php if ($decisions !== []): ?><div class="sidebar-panel"><span class="eyebrow"><?= $lang->value('ARCHITEKTURA A KOMPROMISY','ARCHITECTURE & TRADE-OFFS') ?></span>
<?php foreach ($decisions as $decision): ?><div class="decision-summary"><h3><?= Html::escape($decision->title($lang)) ?></h3><p><?= Html::escape($decision->choice($lang)) ?></p><p><strong><?= $lang->value("Kompromis: ", "Trade-off: ") ?></strong><?= Html::escape($decision->tradeoff($lang)) ?></p><small><?= Html::escape($decision->evidence($lang)) ?></small></div><?php endforeach; ?>
<a class="inline-link" href="<?= $lang->path('/decisions') ?>"><?= $lang->value('Prozkoumat technické rozhodnutí','Explore design decisions') ?> ↗</a></div><?php endif; ?><a class="sidebar-next" href="<?= $lang->path('/open-source') ?>"><?= $lang->value('Prozkoumat open source','Explore open source') ?> ↗</a></aside></section>
