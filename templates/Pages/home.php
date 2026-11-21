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
/** @var array{source:string,last_sync:?string} $provenance */
?>
<section class="shell workbench-hero portfolio-home-hero" aria-labelledby="workbench-title">
    <div class="workbench-intro">
        <p class="workbench-author">Adam Halfar <span aria-hidden="true">/</span> Harfusha</p>
        <h1 id="workbench-title"><?= $lang->value('Navrhuju backendové systémy, nástroje pro vývojáře a automatizaci.', 'I build backend systems, developer tools and automation.') ?></h1>
        <p><?= $lang->value('Zaměřuju se na čistou doménovou architekturu v PHP a CakePHP, deterministické kódovací nástroje a infrastrukturu jako kód. Stavím systémy, kde má každá komponenta jasné hranice a doložitelná technická rozhodnutí.', 'I focus on clean domain architecture in PHP and CakePHP, deterministic developer tooling, and infrastructure as code. Building reliable backend systems with explicit boundaries and verifiable architectural decisions.') ?></p>
        <div class="hero-buttons">
            <a class="btn btn-primary" href="#selected-work"><?= $lang->value('Vybrané projekty', 'Selected work') ?></a>
            <a class="btn btn-ghost" href="<?= $lang->path('/open-source') ?>"><?= $lang->value('Open-source práce', 'Open source work') ?></a>
        </div>
        <a class="workbench-text-link" href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer"><?= $lang->value('Profil a kontaktní možnosti na GitHubu', 'GitHub profile and contact options') ?> <span aria-hidden="true">↗</span></a>
    </div>
    <a class="workbench-cover" href="<?= $lang->path('/projects') ?>">
        <figure>
            <picture>
                <source type="image/webp" srcset="/assets/img/developer-workbench.webp">
                <img src="/assets/img/developer-workbench.png" width="1380" height="900" fetchpriority="high" alt="<?= $lang->value('Přehled inženýrského workbench prostředí, backendové architektury v CakePHP a nástrojů', 'Overview of the engineering workbench, CakePHP backend architecture, and tools') ?>">
            </picture>
            <figcaption><span><?= $lang->value('Ekosystém projektů · backend, automatizace a vývojářské nástroje', 'Project ecosystem · backend, automation and developer tools') ?></span></figcaption>
        </figure>
    </a>
</section>

<section class="section shell portfolio-section" id="selected-work" aria-labelledby="selected-work-title">
    <div class="section-heading">
        <div>
            <p class="eyebrow"><?= $lang->value('PHP · CAKEPHP · VÝVOJÁŘSKÉ NÁSTROJE', 'PHP · CAKEPHP · DEVELOPER TOOLS') ?></p>
            <h2 id="selected-work-title"><?= $lang->value('Vybraná práce', 'Selected work') ?></h2>
            <p><?= $lang->value('Architektura kódovacích agentů, desktopové synchronizace a správy infrastruktury.', 'Architecture of coding agent tooling, desktop synchronization, and infrastructure operations.') ?></p>
        </div>
        <a class="inline-link" href="<?= $lang->path('/projects') ?>"><?= $lang->value('Všech devět projektů', 'All nine projects') ?></a>
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
        <div><strong><?= $projectCount ?></strong><span><?= $lang->value('vybraných projektů', 'selected projects') ?></span></div>
        <div><strong><?= $mergedPrCount ?></strong><span><?= $lang->value('sloučených PR v přehledu', 'merged PRs in this feed') ?></span></div>
        <div><strong><?= count($recentRepos) ?></strong><span><?= $lang->value('nedávno aktualizovaných repozitářů', 'recently updated repositories') ?></span></div>
        <div class="stat-note"><?= match ($provenance['source']) {
            'github-public-api' => $lang->value('Počty vycházejí z posledního úspěšného veřejného importu GitHubu.', 'Counts come from the latest successful public GitHub import.'),
            'unavailable' => $lang->value('Údaje z GitHubu jsou dočasně nedostupné.', 'GitHub data is temporarily unavailable.'),
            default => $lang->value('Počty vycházejí z uloženého přehledu.', 'Counts come from the stored activity snapshot.'),
        } ?><?php if ($provenance['last_sync'] !== null): ?> <time><?= Html::escape($provenance['last_sync']) ?></time><?php endif; ?></div>
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
