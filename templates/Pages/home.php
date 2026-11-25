<?php
declare(strict_types=1);

use App\Domain\JournalFeed;
use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
/** @var list<App\Domain\Project> $projects */
/** @var list<array<string,mixed>> $journal */
/** @var list<array<string,mixed>> $recentRepos */
/** @var int $projectCount */
/** @var int $mergedPrCount */
/** @var array{source:string,last_sync:?string,age_hours:?int,is_stale:bool} $provenance */
?>
<section class="shell workbench-hero portfolio-home-hero" aria-labelledby="workbench-title">
    <div class="workbench-intro">
        <p class="workbench-author">Adam Halfar <span aria-hidden="true">/</span> Harfusha</p>
        <h1 id="workbench-title"><?= $lang->value('PHP backend a vývojářské nástroje', 'PHP backend and developer tools') ?></h1>
        <p><?= $lang->value('Typově bezpečné aplikace v CakePHP, deterministické nástroje pro kódovací agenty a infrastruktura jako kód.', 'Type-safe PHP applications in CakePHP, deterministic tooling for coding agents, and infrastructure as code.') ?></p>
        <div class="hero-buttons">
            <a class="btn btn-primary" href="#selected-work"><?= $lang->value('Vybrané projekty', 'Selected work') ?></a>
            <a class="btn btn-ghost" href="mailto:portfolio@adam-halfar.cz"><?= $lang->value('Kontakt', 'Contact') ?></a>
        </div>
        <a class="workbench-text-link" href="<?= $lang->path('/open-source') ?>"><?= $lang->value('Open-source příspěvky →', 'Open-source contributions →') ?></a>
    </div>
    <figure class="workbench-cover workbench-cover-figure">
        <picture>
            <source type="image/webp" srcset="/assets/img/developer-workbench.webp">
            <img src="/assets/img/developer-workbench.png" width="1380" height="900" fetchpriority="high" alt="<?= $lang->value('Přehled inženýrského workbench prostředí, backendové architektury v CakePHP a nástrojů', 'Overview of the engineering workbench, CakePHP backend architecture, and tools') ?>">
        </picture>
        <figcaption><span><?= $lang->value('Ekosystém projektů · backend, automatizace a vývojářské nástroje', 'Project ecosystem · backend, automation and developer tools') ?></span></figcaption>
    </figure>
</section>

<section class="section shell portfolio-section" id="selected-work" aria-labelledby="selected-work-title">
    <div class="section-heading">
        <div>
            <p class="eyebrow"><?= $lang->value('PHP · CAKEPHP · VÝVOJÁŘSKÉ NÁSTROJE', 'PHP · CAKEPHP · DEVELOPER TOOLS') ?></p>
            <h2 id="selected-work-title"><?= $lang->value('Vybraná práce', 'Selected work') ?></h2>
            <p><?= $lang->value('Architektura kódovacích agentů, desktopové synchronizace a správy infrastruktury.', 'Architecture of coding agent tooling, desktop synchronization, and infrastructure operations.') ?></p>
        </div>
        <a class="inline-link" href="<?= $lang->path('/projects') ?>"><?= $lang->value('Všechny projekty', 'All projects') ?></a>
    </div>
    <div class="showcase-grid showcase-grid-featured">
        <?php foreach ($projects as $project): ?>
            <?php require dirname(__DIR__) . '/element/portfolio_showcase.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<?php if ($journal !== []): ?>
<section class="section shell portfolio-section" aria-labelledby="portfolio-journal-title">
    <div class="section-heading">
        <div>
            <p class="eyebrow"><?= $lang->value('POZNÁMKY K VÝVOJI', 'ENGINEERING NOTES') ?></p>
            <h2 id="portfolio-journal-title"><?= $lang->value('Co jsem se naučil při stavbě', 'What I learned while building') ?></h2>
        </div>
        <a class="inline-link" href="<?= $lang->path('/journal') ?>"><?= $lang->value('Celý deník', 'Full journal') ?></a>
    </div>
    <div class="journal-preview">
        <?php foreach (array_slice($journal, 0, 3) as $entry): ?>
        <article>
            <time class="journal-date" datetime="<?= Html::escape(substr((string)$entry['published_at'], 0, 10)) ?>"><?= Html::escape(substr((string)$entry['published_at'], 0, 10)) ?></time>
            <div>
                <h3><a href="<?= Html::escape($lang->path('/journal') . '#' . JournalFeed::entryId($entry)) ?>"><?= Html::escape((string)$entry['title_' . $lang->code]) ?></a></h3>
                <p><?= Html::escape((string)$entry['body_' . $lang->code]) ?></p>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="section shell portfolio-section portfolio-activity" aria-labelledby="portfolio-activity-title">
    <div class="section-heading">
        <div>
            <p class="eyebrow"><?= $lang->value('VEŘEJNÁ AKTIVITA', 'PUBLIC ACTIVITY') ?></p>
            <h2 id="portfolio-activity-title"><?= $lang->value('Příspěvky a poslední změny', 'Contributions and recent changes') ?></h2>
        </div>
        <a class="inline-link" href="<?= $lang->path('/open-source') ?>"><?= $lang->value('Prohlédnout příspěvky', 'Browse contributions') ?></a>
    </div>
    <div class="stats-strip">
        <div><strong><?= $projectCount ?></strong><span><?= $lang->value('veřejně zdokumentovaných projektů', 'documented projects in catalog') ?></span></div>
        <div><strong><?= $mergedPrCount ?></strong><span><?= $lang->value('sloučených PR v zobrazeném výběru', 'merged PRs in displayed selection') ?></span></div>
        <div><strong><?= count($recentRepos) ?></strong><span><?= $lang->value('repozitářů v zobrazeném výběru', 'repositories in displayed selection') ?></span></div>
        <div class="stat-note">
            <?php require dirname(__DIR__) . '/element/github_provenance_notice.php'; ?>
        </div>
    </div>
    <?php if ($recentRepos !== []): ?>
    <div class="recent-items">
        <?php foreach (array_slice($recentRepos, 0, 3) as $repo): ?>
        <a href="<?= Html::escape(Html::safeUrl((string)$repo['repo_url'])) ?>" target="_blank" rel="noopener noreferrer">
            <strong><?= Html::escape((string)$repo['repository']) ?></strong>
            <time><?= Html::escape(substr((string)$repo['pushed_at'], 0, 10)) ?></time>
            <span aria-hidden="true">↗</span>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
