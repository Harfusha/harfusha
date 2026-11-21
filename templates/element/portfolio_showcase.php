<?php
declare(strict_types=1);

use App\Domain\Project;
use App\Support\Html;
use App\Support\Language;

/** @var Project $project */
/** @var Language $lang */
?>
<article class="showcase-card">
    <?php if ($project->slug === 'local-ai-hub'): ?>
    <figure class="showcase-visual showcase-source">
        <figcaption><?= $lang->value('VEŘEJNÝ ZDROJ · KÓD REPOZITÁŘE', 'PUBLIC SOURCE · REPOSITORY CODE') ?></figcaption>
        <div class="showcase-evidence">
            <span class="eyebrow">MCP · AST · SOURCE SLICES</span>
            <strong><?= $lang->value('Od dotazu k podkladu v repozitáři', 'From a question to repository evidence') ?></strong>
            <a href="<?= Html::escape(Html::safeUrl($project->repo)) ?>" target="_blank" rel="noopener noreferrer"><?= $lang->value('Prohlédnout zdroj', 'Browse source') ?> <span aria-hidden="true">↗</span></a>
        </div>
    </figure>
    <?php elseif ($project->slug === 'mutaui'): ?>
    <figure class="showcase-visual showcase-desktop">
        <picture>
            <source type="image/webp" srcset="<?= $lang->value('/assets/img/mutaui-overview-cs-light.webp', '/assets/img/mutaui-sync-en-light.webp') ?>">
            <img src="<?= $lang->value('/assets/img/mutaui-overview-cs-light.png', '/assets/img/mutaui-sync-en-light.png') ?>"
                width="1380" height="900" loading="lazy"
                alt="<?= $lang->value('Přehled synchronizačních relací a systémových prostředků v offline demu MutaUI', 'Sync session overview and system resources in MutaUI’s offline demo') ?>">
        </picture>
        <figcaption><?= $lang->value('Offline demo · ukázková data', 'Offline demo · sample data') ?></figcaption>
    </figure>
    <?php elseif ($project->slug === 'home-infrastructure'): ?>
    <figure class="showcase-visual showcase-topology">
        <img src="/assets/img/home-network-model.png" width="1280" height="831" loading="lazy" alt="<?= $lang->value('Statický náhled mapy s nedostupným routerem a navazujícími uzly', 'Static map preview with the router and dependent nodes marked unavailable') ?>">
        <figcaption><?= $lang->value('Model vybraných částí · bez živé telemetrie', 'Selected network model · no live telemetry') ?></figcaption>
    </figure>
    <?php endif; ?>
    <div class="showcase-copy">
        <span class="category"><?= Html::escape(Project::categoryLabel($project->category, $lang)) ?></span>
        <h3><a href="<?= $lang->path('/projects/' . $project->slug) ?>"><?= Html::escape($project->name) ?> <span aria-hidden="true">↗</span></a></h3>
        <p><?= Html::escape($project->summary($lang)) ?></p>
    </div>
</article>
