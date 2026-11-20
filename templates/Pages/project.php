<?php
declare(strict_types=1);
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var App\Domain\Project $project */
/** @var list<array{slug:string,name:string,type:string}> $related */
/** @var list<App\Domain\EngineeringDecision> $decisions */
require dirname(__DIR__) . '/element/portfolio_case_reading.php';
[$caseProblem, $caseApproach, $caseEvidence, $caseEvidenceCopy, $caseNextPath, $caseNext] = $caseReading;
[$primaryPath, $primaryText] = match ($project->slug) {
    'scan2cad' => ['/playground#station-scale-check', $lang->value('Přepočítat měřítko skenu', 'Calculate the scan scale')],
    'discord-bot' => ['#project-deepdive-title', $lang->value('Projít pravidla oprávnění', 'Review the permission rules')],
    default => [$caseNextPath, $caseNext],
};
$hasInteractiveModel = str_starts_with($primaryPath, '/playground#') || str_starts_with($primaryPath, '/homelab');
$primaryHref = str_starts_with($primaryPath, '#') ? $primaryPath : $lang->path($primaryPath);
$projectLimitations = [
    'home-infrastructure' => $lang->value('Popis vychází z dokumentace správy, nikoli z aktuálního monitoringu. Interaktivní mapa je model a nesahá na skutečná zařízení.', 'This description is based on management documentation, not live monitoring. The interactive map is a model and does not operate real devices.'),
    'woodbound' => $lang->value('Prototyp generování světa se stále ověřuje. Dostupné scény a testy samy o sobě nepotvrzují hotovou hratelnou hru.', 'The world-generation prototype is still being verified. Available scenes and tests do not by themselves establish a finished playable game.'),
    'scan2cad' => $lang->value('Výstup je CAD návrh ke kontrole. Správné měřítko a fyzické rozměry je nutné ověřit; výpočet nenahrazuje metrologické měření.', 'The output is a CAD candidate for review. Scale and physical dimensions need verification; the calculation does not replace metrology.'),
    'softwareinc-multiplayer' => $lang->value('Jde o rozpracovaný mod. Automatické testy nepotvrzují přijetí ve hře ani dokončený multiplayer.', 'This mod is in development. Automated tests do not establish in-game acceptance or completed multiplayer.'),
    'shpotify' => $lang->value('Projekt je ve vývoji. Propojení přehrávače a vzdáleného audio agenta pokračuje; skutečný Spotify poslech zatím není doložený.', 'The project is in development. Browser playback and the remote audio agent are still being integrated; actual Spotify listening has not yet been verified.'),
    'local-ai-hub' => $lang->value('Verze 6.1.0 je kandidát na vydání; produkční připravenost není deklarována.', 'Version 6.1.0 is a release candidate; production readiness is not claimed.'),
    'mutaui' => $lang->value('Mutagen CLI se instaluje zvlášť. Snímky zobrazují offline demo; ověření balíčku závisí na konkrétní platformě.', 'Mutagen CLI is installed separately. Screenshots show the offline demo; package verification depends on the platform.'),
    'tiskov' => $lang->value('Dokumentovaný návrh fronty a její model. Výpočet ceny, slicer a provoz tiskové služby zatím nemají ověření.', 'A documented queue proposal and simulation. Pricing, slicing and operation of a printing service remain unverified.'),
    'discord-bot' => $lang->value('Podklady tvoří katalogový záznam. Skutečné příkazy, oprávnění a provoz čekají na ověření ve zdroji.', 'The available material is a catalog record. The actual commands, permissions and operation still require source verification.'),
];
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
            <a class="btn btn-primary" href="<?= Html::escape($primaryHref) ?>"><?= Html::escape($primaryText) ?> <span aria-hidden="true">↗</span></a>
            <?php if (!$project->private && $project->repo !== ''): ?>
                <a class="btn btn-ghost" href="<?= Html::escape(Html::safeUrl($project->repo)) ?>" target="_blank" rel="noopener noreferrer"><?= $lang->value('Zdrojový kód', 'View source') ?> <span aria-hidden="true">↗</span></a>
            <?php endif; ?>
            <?php if ($project->slug === 'mutaui'): ?>
                <a class="inline-link" href="#mutaui-preview-title"><?= $lang->value('Snímky aplikace', 'Application screenshots') ?> <span aria-hidden="true">↓</span></a>
            <?php endif; ?>
        </div>
    </div>
    <div class="case-evidence" aria-labelledby="case-evidence-title">
        <h2 id="case-evidence-title"><?= Html::escape($caseEvidence) ?></h2>
        <p><?= Html::escape($caseEvidenceCopy) ?></p>
        <?php if (isset($projectLimitations[$project->slug])): ?>
        <div class="case-evidence-limit">
            <strong><?= $lang->value('Stav ověření', 'Verification status') ?></strong>
            <p><?= Html::escape($projectLimitations[$project->slug]) ?></p>
        </div>
        <?php endif; ?>
    </div>
</section>
<nav class="shell case-navigation" aria-label="<?= $lang->value('Obsah případové studie', 'On this case study') ?>">
    <a href="#project-overview"><?= $lang->value('Přehled', 'Overview') ?></a>
    <a href="#project-deepdive-title"><?= $lang->value('Technický průchod', 'Technical walkthrough') ?></a>
    <a href="#project-experience"><?= $lang->value('Alternativy a důvody', 'Alternatives and rationale') ?></a>
</nav>
<?php if ($project->slug === 'mutaui'): ?>
<section class="shell project-preview" aria-labelledby="mutaui-preview-title">
 <div class="project-preview-copy">
  <span class="eyebrow"><?= $lang->value('POHLED DO APLIKACE', 'INSIDE THE APPLICATION') ?></span>
  <h2 id="mutaui-preview-title"><?= $lang->value('Přesměrování portů v MutaUI', 'Sessions and services in MutaUI') ?></h2>
  <p><?= $lang->value('V rozhraní poznáš, odkud a kam relace přesměrovává provoz a v jakém je stavu. Snímek pochází z offline dema s ukázkovými daty a vypnutým ovládáním.', 'See the sync and forwarding sessions, the daemon and system resources in one window. This screenshot is from an offline demo with sample data and disabled actions.') ?></p>
  <a class="inline-link" href="https://github.com/Harfusha/MutaUI/blob/master/docs/screenshots.md" target="_blank" rel="noopener noreferrer"><?= $lang->value('Další snímky a jejich původ', 'More screenshots and their source') ?> ↗</a>
 </div>
 <figure><img src="<?= $lang->value('/assets/img/mutaui-forward-cs-dark.png', '/assets/img/mutaui-overview-en-dark.png') ?>" width="1380" height="900" loading="lazy" alt="<?= $lang->value('MutaUI s ukázkovými relacemi přesměrování portů', 'MutaUI overview with sample sessions and charts') ?>"><figcaption><?= $lang->value('MutaUI / offline demo / ukázková data', 'MutaUI / offline demo / synthetic data') ?></figcaption></figure>
</section>
<details class="shell project-gallery">
 <summary><?= $lang->value('Další snímek aplikace', 'Another application screenshot') ?></summary>
 <figure><img src="<?= $lang->value('/assets/img/mutaui-overview-cs-light.png', '/assets/img/mutaui-sync-en-light.png') ?>" width="1380" height="900" loading="lazy" alt="<?= $lang->value('Přehledové okno MutaUI s ukázkovými relacemi a grafy', 'MutaUI: sample synchronization session overview') ?>"><figcaption><?= $lang->value('MutaUI / offline demo / ukázková data', 'MutaUI / offline demo / synthetic data') ?></figcaption></figure>
</details>
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
<section class="section shell project-case" id="project-overview" aria-label="<?= $lang->value('Přehled studie', 'Case study overview') ?>"><div class="case-main"><div class="case-block"><h2><?= Html::escape($caseProblem) ?></h2><p><?= Html::escape($project->problem($lang)) ?></p></div><div class="case-block"><h2><?= Html::escape($caseApproach) ?></h2><p><?= Html::escape($project->solution($lang)) ?></p></div><div class="case-block"><h2><?= $lang->value('Hlavní funkce','Key features') ?></h2><ul class="feature-list"><?php foreach ($project->localizedFeatures($lang) as $feature): ?><li><?= Html::escape($feature) ?></li><?php endforeach; ?></ul></div>
</div><div class="case-sidebar"><div class="sidebar-panel"><span class="eyebrow"><?= $lang->value('TECHNOLOGIE', 'TECH STACK') ?></span><div class="stack-list stack-large"><?php foreach ($project->stack as $tech): ?><span><?= Html::escape($tech) ?></span><?php endforeach; ?></div></div><div class="sidebar-panel"><span class="eyebrow"><?= $lang->value('DOSTUPNOST', 'VISIBILITY') ?></span><h3><?= $project->private ? $lang->value('Podklady k projektu','Project materials') : $lang->value('Veřejný repozitář na GitHubu', 'Public GitHub Repository') ?></h3><p><?= $project->private ? $lang->value('Tato stránka shrnuje veřejně sdílené podklady k projektu.', 'This page summarizes the project materials available to the public.') : $lang->value('Zdrojový kód, hlášení a historie změn jsou dostupné na GitHubu.', 'Explore the source, issues and change history on GitHub.') ?></p></div><?php if ($related !== []): ?><div class="sidebar-panel"><span class="eyebrow"><?= $lang->value('PROPOJENÉ PROJEKTY','RELATED PROJECTS') ?></span><ul class="service-list"><?php foreach ($related as $item): ?><li><a class="inline-link" href="<?= $lang->path('/projects/'.$item['slug']) ?>"><?= Html::escape($item['name']) ?></a><small> · <?= Html::escape(App\Domain\ProjectConnections::label($item['type'], $lang)) ?></small></li><?php endforeach; ?></ul><a class="inline-link" href="<?= $lang->path('/connections') ?>"><?= $lang->value('Prozkoumat celou mapu','Explore whole map') ?> ↗</a></div><?php endif; ?><?php if ($decisions !== []): ?><div class="sidebar-panel"><span class="eyebrow"><?= $lang->value('ARCHITEKTURA A KOMPROMISY','ARCHITECTURE & TRADE-OFFS') ?></span>
<?php foreach ($decisions as $decision): ?><div class="decision-summary"><h3><?= Html::escape($decision->title($lang)) ?></h3><p><?= Html::escape($decision->choice($lang)) ?></p><p><strong><?= $lang->value("Kompromis: ", "Trade-off: ") ?></strong><?= Html::escape($decision->tradeoff($lang)) ?></p><small><?= Html::escape($decision->evidence($lang)) ?></small></div><?php endforeach; ?>
<a class="inline-link" href="<?= $lang->path('/decisions') ?>"><?= $lang->value('Všechna technická rozhodnutí','All engineering decisions') ?> ↗</a></div><?php endif; ?></div></section>

<?php require dirname(__DIR__) . '/element/project_deepdive.php'; ?>
<?php require dirname(__DIR__) . '/element/project_example.php'; ?>
<?php require dirname(__DIR__) . '/element/project_experience.php'; ?>

<section class="shell section case-followup" aria-labelledby="case-next-title">
    <div>
        <p class="eyebrow"><?= $lang->value('Další krok', 'Next step') ?></p>
        <h2 id="case-next-title"><?= Html::escape($caseNext) ?></h2>
    </div>
    <a class="btn btn-primary" href="<?= $lang->path($caseNextPath) ?>">
        <?= $lang->value('Otevřít', 'Open') ?> <span aria-hidden="true">↗</span>
    </a>
</section>
