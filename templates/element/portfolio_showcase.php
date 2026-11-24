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
    <figure class="showcase-visual showcase-architecture">
        <picture>
            <source type="image/webp" srcset="/assets/img/local-ai-hub-architecture.webp">
            <img src="/assets/img/local-ai-hub-architecture.png"
                width="1380" height="900" loading="lazy"
                alt="<?= $lang->value('Architektonický diagram Local AI Hub: MCP protokol, indexace AST symbolů a koordinace úloh', 'Local AI Hub architectural blueprint: MCP protocol, AST symbol indexing, and task coordination') ?>">
        </picture>
        <figcaption><?= $lang->value('Architektura MCP serveru · AST index a koordinace', 'MCP server architecture · AST index and coordination') ?></figcaption>
    </figure>
    <?php elseif ($project->slug === 'mutaui'): ?>
    <figure class="showcase-visual showcase-desktop">
        <picture>
            <source type="image/webp" srcset="<?= $lang->value('/assets/img/mutaui-overview-cs-light.webp', '/assets/img/mutaui-sync-en-light.webp') ?>">
            <img src="<?= $lang->value('/assets/img/mutaui-overview-cs-light.png', '/assets/img/mutaui-sync-en-light.png') ?>"
                width="1380" height="900" loading="lazy"
                alt="<?= $lang->value('Přehled synchronizačních relací a systémových prostředků v rozhraní MutaUI', 'Sync session overview and system resources in the MutaUI interface') ?>">
        </picture>
        <figcaption><?= $lang->value('Desktopové rozhraní · profily synchronizace Mutagen', 'Desktop interface · Mutagen sync profiles') ?></figcaption>
    </figure>
    <?php elseif ($project->slug === 'home-infrastructure'): ?>
    <figure class="showcase-visual showcase-topology">
        <img src="/assets/img/home-network-model.png" width="1280" height="831" loading="lazy" alt="<?= $lang->value('Topologický model domácí sítě s rolemi uzlů a závislostmi služeb', 'Home network topology model with node roles and service dependencies') ?>">
        <figcaption><?= $lang->value('Topologický model · závislosti služeb a uzlů', 'Topology model · service and node dependencies') ?></figcaption>
    </figure>
    <?php endif; ?>
    <div class="showcase-copy">
        <span class="category"><?= Html::escape(Project::categoryLabel($project->category, $lang)) ?></span>
        <h3><a href="<?= $lang->path('/projects/' . $project->slug) ?>"><?= Html::escape($project->name) ?> <span aria-hidden="true">↗</span></a></h3>
        <p class="showcase-summary"><?= Html::escape($project->summary($lang)) ?></p>
        <?php if ($project->slug === 'local-ai-hub'): ?>
        <p class="showcase-proof"><strong><?= $lang->value('Ověřený výsledek:', 'Verified outcome:') ?></strong> <?= $lang->value('Deterministický AST index vrací přesné výřezy kódu s tokenovým rozpočtem bez nutnosti volat model.', 'Deterministic AST indexing returns exact code slices within token budget without calling models.') ?></p>
        <?php elseif ($project->slug === 'mutaui'): ?>
        <p class="showcase-proof"><strong><?= $lang->value('Ověřený výsledek:', 'Verified outcome:') ?></strong> <?= $lang->value('Synchronizace souborů přes Mutagen s dohledem nad stavem démona a izolovaným offline demem.', 'Mutagen file synchronization with daemon supervision and an isolated offline demo.') ?></p>
        <?php elseif ($project->slug === 'home-infrastructure'): ?>
        <p class="showcase-proof"><strong><?= $lang->value('Ověřený výsledek:', 'Verified outcome:') ?></strong> <?= $lang->value('Interaktivní simulace výpadku uzlů ukazuje kaskádový dopad na DNS, proxy a úložiště.', 'Interactive node outage simulation demonstrates cascading impacts on DNS, proxy and storage.') ?></p>
        <?php endif; ?>
    </div>
</article>
