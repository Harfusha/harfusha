<?php
declare(strict_types=1);

use App\Domain\EvidenceRegistry;
use App\Domain\Project;
use App\Support\Html;
use App\Support\Language;

/** @var Project $project */
/** @var Language $lang */
/** @var EvidenceRegistry|null $evidenceRegistry */
$claims = isset($evidenceRegistry) ? $evidenceRegistry->forProject($project->slug) : [];
$primaryClaim = $claims[0] ?? null;
$primaryEvidence = $primaryClaim?->primaryEvidence();
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
    <?php elseif ($project->slug === 'harfusha-lab'): ?>
    <figure class="showcase-visual showcase-architecture">
        <picture>
            <source type="image/webp" srcset="/assets/img/developer-workbench.webp">
            <img src="/assets/img/developer-workbench.png"
                width="1380" height="900" loading="lazy"
                alt="<?= $lang->value('Přehled architektury platformy Harfusha Lab: CakePHP 5 SSR, typové DTO a ověřitelná evidence', 'Architectural blueprint of Harfusha Lab platform: CakePHP 5 SSR, typed DTOs, and verifiable evidence') ?>">
        </picture>
        <figcaption><?= $lang->value('Architektura platformy · CakePHP 5 SSR a evidence ledger', 'Platform architecture · CakePHP 5 SSR & evidence ledger') ?></figcaption>
    </figure>
    <?php endif; ?>
    <div class="showcase-copy">
        <span class="category"><?= Html::escape(Project::categoryLabel($project->category, $lang)) ?></span>
        <h3><a href="<?= $lang->path('/projects/' . $project->slug) ?>"><?= Html::escape($project->name) ?> <span aria-hidden="true">↗</span></a></h3>
        <p class="showcase-summary"><?= Html::escape($project->summary($lang)) ?></p>
        <?php if ($primaryClaim !== null && $primaryEvidence !== null): ?>
        <div class="showcase-proof evidence-card" data-evidence-id="<?= Html::escape($primaryEvidence->id) ?>">
            <div class="evidence-header">
                <span class="evidence-badge status-<?= Html::escape($primaryEvidence->verificationStatus) ?> kind-<?= Html::escape($primaryEvidence->kind) ?>">
                    <span class="evidence-kind"><?= Html::escape(strtoupper($primaryEvidence->kind)) ?></span>
                    <span class="evidence-status"><?= $primaryEvidence->verificationStatus === 'verified' ? $lang->value('Ověřeno', 'Verified') : $lang->value('Neověřeno', 'Unverified') ?></span>
                </span>
                <?php if ($primaryEvidence->revision !== null): ?>
                <code class="evidence-revision"><?= Html::escape($primaryEvidence->revision) ?></code>
                <?php endif; ?>
            </div>
            <p class="evidence-claim"><?= Html::escape($lang->value($primaryClaim->claim['cs'], $primaryClaim->claim['en'])) ?></p>
            <?php if ($primaryEvidence->sourceUrl !== null): ?>
            <a class="evidence-link" href="<?= Html::escape(Html::safeUrl($primaryEvidence->sourceUrl)) ?>" target="_blank" rel="noopener noreferrer">
                <?= $lang->value('Zdroj důkazu ↗', 'Evidence source ↗') ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</article>
