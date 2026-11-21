<?php
declare(strict_types=1);

use App\Support\Language;

/** @var Language $lang */
?>
<section class="page-hero shell about-hero">
    <span class="eyebrow">PHP · CAKEPHP · DEVELOPER TOOLING</span>
    <h1>Adam Halfar</h1>
    <p><?= $lang->value('Jsem backendový vývojář z Česka. Většinu času pracuji v PHP a CakePHP. Záleží mi na tom, aby šlo i po měsících dohledat, proč systém funguje právě takhle.', 'I’m a backend engineer based in Czechia. Most of my work is in PHP and CakePHP. I care about being able to understand months later why a system works the way it does.') ?></p>
</section>

<section class="section shell about-grid">
    <div>
        <h2><?= $lang->value('Co mě zajímá', 'What I work on') ?></h2>
        <p><?= $lang->value('V backendu se soustředím na datová rozhraní, jasné hranice a chování při chybě. Mimo práci v PHP stavím nástroje pro vývojáře, zkouším lokální AI workflow a spravuju domácí služby.', 'In backend work, I focus on data contracts, clear boundaries and failure behavior. Outside PHP, I build developer tools, explore local AI workflows and maintain home services.') ?></p>
        <p><?= $lang->value('Do veřejných knihoven přispívám změnami, které sám potřebuji. Vybrané příklady a jejich původ najdeš v přehledu open source.', 'I contribute fixes and features I need to the libraries I use. Selected examples and their upstream records are in the open-source section.') ?></p>
        <a class="btn btn-ghost" href="<?= $lang->path('/open-source') ?>"><?= $lang->value('Vybrané upstream změny', 'Selected upstream changes') ?></a>
    </div>
    <aside class="portfolio-profile" aria-labelledby="profile-links-title">
        <h2 id="profile-links-title"><?= $lang->value('Můj profil', 'My profile') ?></h2>
        <a href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer">
            <strong><?= $lang->value('GitHub · profil a kontakt', 'GitHub · profile and contact') ?></strong><span>@Harfusha ↗</span>
        </a>
        <a href="<?= $lang->path('/projects') ?>">
            <strong><?= $lang->value('Projekty', 'Projects') ?></strong><span aria-hidden="true">↗</span>
        </a>
        <a href="<?= $lang->path('/journal') ?>">
            <strong><?= $lang->value('Vývojový deník', 'Development journal') ?></strong><span aria-hidden="true">↗</span>
        </a>
    </aside>
</section>

<section class="section shell profile-work" aria-labelledby="profile-work-title">
    <h2 id="profile-work-title"><?= $lang->value('Jak se to promítá do práce', 'What that looks like in practice') ?></h2>
    <div class="profile-work-list">
        <article>
            <h3><?= $lang->value('Backend a datová rozhraní', 'Backends and data contracts') ?></h3>
            <p><?= $lang->value('Ve veřejném katalogu odděluji data pro návštěvníky od interní správy. JSON rozhraní dává stejný přehled i klientům mimo web.', 'The public catalog separates visitor-facing data from administration. Its JSON endpoint gives non-web clients the same project overview.') ?></p>
            <a class="inline-link" href="/api/v1/projects?lang=<?= $lang->code ?>"><?= $lang->value('Otevřít JSON katalog', 'Open the JSON catalog') ?> <span aria-hidden="true">↗</span></a>
        </article>
        <article>
            <h3><?= $lang->value('Nástroje pro vývojáře', 'Developer tools') ?></h3>
            <p><?= $lang->value('U Local AI Hubu je důležitý návrat ke zdroji. Agent může dohledat soubor a konkrétní řádek, ze kterého odpověď vychází.', 'Local AI Hub makes it possible to trace a result back to the repository. An agent can find the file and line behind an answer.') ?></p>
            <a class="inline-link" href="<?= $lang->path('/projects/local-ai-hub') ?>"><?= $lang->value('Případová studie Local AI Hub', 'Local AI Hub case study') ?> <span aria-hidden="true">↗</span></a>
        </article>
        <article>
            <h3><?= $lang->value('Provoz a obnova', 'Operations and recovery') ?></h3>
            <p><?= $lang->value('U domácích služeb dokumentuji role zařízení a jejich závislosti. Mapa umožňuje projít cestu dat a nasimulovat výpadek.', 'I document device roles and dependencies for home services. The map lets you follow data paths and simulate an outage.') ?></p>
            <a class="inline-link" href="<?= $lang->path('/homelab#network-model') ?>"><?= $lang->value('Mapa domácí sítě', 'Home network map') ?> <span aria-hidden="true">↗</span></a>
        </article>
    </div>
</section>
