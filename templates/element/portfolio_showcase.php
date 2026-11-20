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
        <figcaption><?= $lang->value('Ukázka MCP architektury a adaptivního indexu repozitáře', 'MCP architecture & adaptive repository indexing') ?></figcaption>
        <div class="code-block">
            <div class="code-header">
                <span>local_ai_repo / adaptive_context</span>
                <span class="code-tag">Python & MCP</span>
            </div>
            <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Ukázka kódu', 'Code snippet') ?>"><code>// Adaptive AST indexing & token economy
async def resolve_context(repo: Path, query: str):
    symbols = await ast_indexer.scan(repo, query)
    ledger  = await token_economizer.compact(symbols)
    return {"symbols": symbols, "tokens_saved": ledger.delta}</code></pre>
        </div>
    </figure>
    <?php elseif ($project->slug === 'mutaui'): ?>
    <figure class="showcase-visual showcase-desktop">
        <picture>
            <source type="image/webp" srcset="<?= $lang->value('/assets/img/mutaui-forward-cs-dark.webp', '/assets/img/mutaui-overview-en-dark.webp') ?>">
            <img src="<?= $lang->value('/assets/img/mutaui-forward-cs-dark.png', '/assets/img/mutaui-overview-en-dark.png') ?>"
                width="1380" height="900" loading="lazy"
                alt="<?= $lang->value('Přehled přesměrování portů v MutaUI', 'Session and service overview in MutaUI') ?>">
        </picture>
        <figcaption><?= $lang->value('Qt/PySide6 desktopový orchestrátor relací Mutagenu', 'Qt/PySide6 desktop session orchestrator for Mutagen') ?></figcaption>
    </figure>
    <?php elseif ($project->slug === 'home-infrastructure'): ?>
    <figure class="showcase-visual showcase-topology">
        <figcaption><?= $lang->value('Architektura systémových vrstev a datových cest', 'System layer architecture and data paths') ?></figcaption>
        <div class="code-block">
            <div class="code-header">
                <span>homelab_mesh.yaml</span>
                <span class="code-tag">Ansible & WireGuard</span>
            </div>
            <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Topologie sítě', 'Network topology') ?>"><code>[Gateway]  RouterOS ➔ WireGuard (UDP 51820)
[Cluster]  Raspberry Pi ➔ Unbound DNS / Semaphore / systemd
[Compute]  Proxmox VE ➔ Izolované servisní kontejnery
[Storage]  QNAP NAS ➔ SMB3 / Restic šifrované zálohy</code></pre>
        </div>
    </figure>
    <?php endif; ?>
    <div class="showcase-copy">
        <span class="category"><?= Html::escape(Project::categoryLabel($project->category, $lang)) ?></span>
        <h3><a href="<?= $lang->path('/projects/' . $project->slug) ?>"><?= Html::escape($project->name) ?> <span aria-hidden="true">↗</span></a></h3>
        <p><?= Html::escape($project->summary($lang)) ?></p>
    </div>
</article>
