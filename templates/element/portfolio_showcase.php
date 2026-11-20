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
        <figcaption><?= $lang->value('Ukázkový dotaz: Kde se vybírají hlavní projekty?', 'Example query: Where are featured projects selected?') ?></figcaption>
        <pre><code>src/Controller/PagesController.php

static fn(Project $p): bool
    =&gt; $p-&gt;featured</code></pre>
    </figure>
    <?php elseif ($project->slug === 'mutaui'): ?>
    <figure class="showcase-visual showcase-desktop">
        <img src="<?= $lang->value('/assets/img/mutaui-forward-cs-dark.png', '/assets/img/mutaui-overview-en-dark.png') ?>"
            width="1380" height="900" loading="lazy"
            alt="<?= $lang->value('Přehled přesměrování portů v MutaUI', 'Session and service overview in MutaUI') ?>">
        <figcaption><?= $lang->value('Rozhraní offline dema s ukázkovými daty', 'Offline demo with sample data') ?></figcaption>
    </figure>
    <?php elseif ($project->slug === 'home-infrastructure'): ?>
    <figure class="showcase-visual showcase-topology">
        <figcaption><?= $lang->value('Model cesty požadavku na video', 'Modeled video request path') ?></figcaption>
        <ol>
            <li><strong><?= $lang->value('Router', 'Router') ?></strong><span><?= $lang->value('Síť', 'Network') ?></span></li>
            <li><strong>Jellyfin</strong><span><?= $lang->value('Přehrávání', 'Playback') ?></span></li>
            <li><strong>NAS</strong><span><?= $lang->value('Soubor', 'File') ?></span></li>
        </ol>
    </figure>
    <?php endif; ?>
    <div class="showcase-copy">
        <span class="category"><?= Html::escape(Project::categoryLabel($project->category, $lang)) ?></span>
        <h3><a href="<?= $lang->path('/projects/' . $project->slug) ?>"><?= Html::escape($project->name) ?> <span aria-hidden="true">↗</span></a></h3>
        <p><?= Html::escape($project->summary($lang)) ?></p>
    </div>
</article>
