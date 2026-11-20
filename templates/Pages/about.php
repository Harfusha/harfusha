<?php
declare(strict_types=1);

use App\Support\Language;

/** @var Language $lang */
?>
<section class="page-hero shell about-hero">
    <h1>Adam Halfar</h1>
    <p><?= $lang->value('V PHP a CakePHP stavím backendy a přispívám do knihoven, které sám používám. Vyvíjím taky nástroje pro práci s kódem a spravuju vlastní domácí síť.', 'I build backends in PHP and CakePHP and contribute to the libraries I use. I also build developer tools and run a home network.') ?></p>
</section>

<section class="section shell about-grid">
    <div>
        <h2><?= $lang->value('Jak pracuju', 'How I work') ?></h2>
        <p><?= $lang->value('Při návrhu backendu začínám u rozhraní a dat. Jasné kontrakty usnadňují změny a testy pomáhají ověřit, že systém funguje i po úpravách. Důležitá rozhodnutí dokumentuji, aby byla srozumitelná i po delší době.', 'I start backend design with interfaces and data. Clear contracts make changes easier, and tests help verify that the system still works after an update. I document important decisions so they remain understandable over time.') ?></p>
        <p><?= $lang->value('V Local AI Hubu jde výsledek hledání dohledat v kódu. U domácí sítě zase z dokumentace zjistím, které služby sdílejí jeden stroj a co obnovit jako první.', 'In Local AI Hub, lookup results point back to the code. My home network documentation shows which services share a machine and what needs to be restored first.') ?></p>
        <a class="btn btn-ghost" href="<?= $lang->path('/open-source') ?>"><?= $lang->value('Moje open-source příspěvky', 'My open-source contributions') ?> <span aria-hidden="true">↗</span></a>
    </div>
    <aside class="portfolio-profile" aria-labelledby="profile-links-title">
        <h2 id="profile-links-title"><?= $lang->value('Můj profil', 'My profile') ?></h2>
        <a href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer">
            <strong>GitHub</strong><span>@Harfusha ↗</span>
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
