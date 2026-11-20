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
<section class="shell workbench-hero" aria-labelledby="workbench-title">
    <div class="workbench-intro">
        <p class="workbench-author">Adam Halfar <span aria-hidden="true">/</span> Harfusha</p>
        <h1 id="workbench-title"><?= $lang->value('Změň vstup.', 'Change an input.') ?><br><em><?= $lang->value('Sleduj výsledek.', 'See the result.') ?></em></h1>
        <p><?= $lang->value('Stavím vývojářské nástroje a spravuju domácí síť. Zkus přepočítat pixely z fotky, porovnat režimy synchronizace nebo zjistit, co se stane při výpadku úložiště.', 'I build developer tools and run a home network. Try converting pixels into a measurement, comparing sync modes or seeing what happens when storage goes offline.') ?></p>
        <div class="hero-buttons">
            <a class="btn btn-primary" href="#try-it"><?= $lang->value('Vybrat pokus', 'Choose an experiment') ?> <span aria-hidden="true">↓</span></a>
            <a class="workbench-text-link" href="#selected-work"><?= $lang->value('Co stavím', 'What I’m building') ?> <span aria-hidden="true">↘</span></a>
        </div>
    </div>
    <a class="workbench-cover" href="#scale-quick">
        <figure>
            <img src="/assets/img/scale-studio.webp" width="1448" height="1086" fetchpriority="high" alt="<?= $lang->value('Ilustrační snímek posuvného měřítka u oranžového výtisku', 'Illustration of a caliper measuring an orange 3D print') ?>">
            <figcaption>
                <span><?= $lang->value('Jak z pixelů dostaneš milimetry?', 'How do pixels become millimeters?') ?></span>
                <span aria-hidden="true">↗</span>
            </figcaption>
        </figure>
    </a>
</section>

<section class="experiment-band" id="try-it" aria-labelledby="experiments-title">
    <div class="shell">
        <div class="section-heading">
            <div>
                <h2 id="experiments-title"><?= $lang->value('Vyzkoušej si to', 'Try it yourself') ?></h2>
                <p><?= $lang->value('Vyber otázku, změň jeden vstup a porovnej výsledek přímo v prohlížeči.', 'Pick a question, change one input and compare the result in your browser.') ?></p>
            </div>
            <a class="inline-link" href="<?= $lang->path('/playground') ?>"><?= $lang->value('Všech osm pokusů', 'All eight experiments') ?> <span aria-hidden="true">↗</span></a>
        </div>
        <article class="quick-scale" id="scale-quick" data-quick-scale aria-labelledby="quick-scale-title">
            <div class="quick-scale-intro">
                <h3 id="quick-scale-title"><?= $lang->value('Kolik milimetrů má díl?', 'How many millimeters is the part?') ?></h3>
                <p><?= $lang->value('Úsek dlouhý 120 pixelů měří na skutečném dílu 30 mm. Zkus změnit referenci nebo délku měřeného úseku.', 'A 120-pixel reference measures 30 mm on the physical part. Change the reference or the length of the section you’re measuring.') ?></p>
                <a class="inline-link" href="<?= $lang->path('/playground#station-scale-check') ?>"><?= $lang->value('Celý pokus s měřítkem', 'Full scale experiment') ?> <span aria-hidden="true">↗</span></a>
            </div>
            <form class="quick-scale-form" data-scale-form>
                <fieldset disabled data-quick-scale-controls>
                    <legend class="visually-hidden"><?= $lang->value('Vstupy pro přepočet měřítka', 'Scale calculation inputs') ?></legend>
                    <label><?= $lang->value('Reference (px)', 'Reference (px)') ?><input type="number" min="0.01" step="any" value="120" data-scale-pixels required></label>
                    <label><?= $lang->value('Reference (mm)', 'Reference (mm)') ?><input type="number" min="0.01" step="any" value="30" data-scale-mm required></label>
                    <label><?= $lang->value('Měřený úsek (px)', 'Measured span (px)') ?><input type="number" min="0" step="any" value="240" data-scale-probe required></label>
                </fieldset>
                <output class="quick-scale-output">
                    <strong data-scale-probe-result><?= $lang->value('240 px odpovídá 60 mm', '240 px corresponds to 60 mm') ?></strong>
                    <span data-scale-result>4 px/mm</span>
                </output>
            </form>
        </article>
        <div class="experiment-grid">
            <a class="experiment-link experiment-sync" href="<?= $lang->path('/playground#station-sync-preview') ?>">
                <span class="experiment-topic"><?= $lang->value('Synchronizace', 'File sync') ?></span>
                <h3><?= $lang->value('Který soubor se přepíše?', 'Which file gets overwritten?') ?></h3>
                <p><?= $lang->value('Porovnej náhled jednosměrné a obousměrné synchronizace.', 'Compare previews for one-way and two-way sync.') ?></p>
                <span class="experiment-arrow" aria-hidden="true">↗</span>
            </a>
            <a class="experiment-link experiment-network" href="<?= $lang->path('/homelab#network-model') ?>">
                <span class="experiment-topic"><?= $lang->value('Domácí síť', 'Home network') ?></span>
                <h3><?= $lang->value('Poběží video bez NAS?', 'Will video play without the NAS?') ?></h3>
                <p><?= $lang->value('Vypni úložiště v modelu a sleduj cestu od klienta až k souboru.', 'Take storage offline in the model and follow the path from the client to the file.') ?></p>
                <span class="experiment-arrow" aria-hidden="true">↗</span>
            </a>
            <a class="experiment-link experiment-route" href="<?= $lang->path('/playground#station-route-finder') ?>">
                <span class="experiment-topic"><?= $lang->value('Hledání trasy', 'Pathfinding') ?></span>
                <h3><?= $lang->value('Najde se cesta kolem překážky?', 'Is there a route around the obstacle?') ?></h3>
                <p><?= $lang->value('Zablokuj jedno pole v krajině a nech trasu přepočítat.', 'Block one cell on the map and recalculate the route.') ?></p>
                <span class="experiment-arrow" aria-hidden="true">↗</span>
            </a>
        </div>
    </div>
</section>

<section class="section shell portfolio-section" id="selected-work" aria-labelledby="selected-work-title">
    <div class="section-heading">
        <div>
            <h2 id="selected-work-title"><?= $lang->value('Co stavím', 'What I’m building') ?></h2>
            <p><?= $lang->value('Jaký problém řeším, jak nástroj funguje a co jsem ověřil.', 'The problem each tool tackles, how it works and what I’ve verified.') ?></p>
        </div>
        <a class="inline-link" href="<?= $lang->path('/projects') ?>"><?= $lang->value('Všechny projekty', 'All projects') ?> <span aria-hidden="true">↗</span></a>
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
        <h2 id="portfolio-journal-title"><?= $lang->value('Vývojový deník', 'Development journal') ?></h2>
        <a class="inline-link" href="<?= $lang->path('/journal') ?>"><?= $lang->value('Všechny zápisky', 'All entries') ?> <span aria-hidden="true">↗</span></a>
    </div>
    <div class="journal-preview">
        <?php foreach (array_slice($journal, 0, 3) as $entry): ?>
        <article>
            <time class="journal-date"><?= Html::escape(substr((string)$entry['published_at'], 0, 10)) ?></time>
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
        <h2 id="portfolio-activity-title"><?= $lang->value('Co se změnilo na GitHubu', 'Recent GitHub activity') ?></h2>
        <a class="inline-link" href="<?= $lang->path('/now') ?>"><?= $lang->value('Časová osa', 'Work timeline') ?> <span aria-hidden="true">↗</span></a>
    </div>
    <div class="stats-strip">
        <div><strong><?= $projectCount ?></strong><span><?= $lang->value('projektů v portfoliu', 'curated project studies') ?></span></div>
        <div><strong><?= $mergedPrCount ?></strong><span><?= $lang->value('sloučených pull requestů v přehledu', 'merged external PRs in the activity feed') ?></span></div>
        <div><strong><?= count($recentRepos) ?></strong><span><?= $lang->value('nedávno aktualizovaných repozitářů', 'recent repository updates') ?></span></div>
        <div class="stat-note"><?= match ($provenance['source']) {
            'github-public-api' => $lang->value('GitHub údaje vycházejí z posledního úspěšného importu.', 'GitHub figures are based on the latest successful import.'),
            'unavailable' => $lang->value('Údaje z GitHubu jsou dočasně nedostupné.', 'GitHub data is temporarily unavailable.'),
            default => $lang->value('GitHub údaje vycházejí z uloženého přehledu.', 'GitHub figures are based on a reviewed snapshot.'),
        } ?></div>
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
