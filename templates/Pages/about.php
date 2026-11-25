<?php
declare(strict_types=1);

use App\Support\Language;

/** @var Language $lang */
?>
<section class="page-hero shell about-hero">
    <span class="eyebrow">PHP · CAKEPHP · DEVELOPER TOOLING</span>
    <h1>Adam Halfar</h1>
    <p><?= $lang->value('Navrhuju, jak aplikace ukládá a ověřuje data. U vývojářských nástrojů dbám na to, aby se každý výsledek dal dohledat ve zdroji a podložit testy.', 'I design how applications store and validate data. For developer tools, I ensure every result is traceable to source code and backed by verifiable tests.') ?></p>
</section>

<section class="section shell about-grid">
    <div>
        <h2><?= $lang->value('Inženýrský přístup a priority', 'Engineering focus and principles') ?></h2>
        <p><?= $lang->value('V backendovém vývoji stavím na přísných datových kontraktech, izolovaných aplikačních vrstvách a předvídatelném chování při selhání. Nenechávám architekturu náhodě: každá služba má jasné hranice a definované způsoby obnovy.', 'In backend engineering, I prioritize strict data contracts, decoupled application layers, and predictable failure behavior. Architecture is never accidental: every service features explicit boundaries and defined recovery paths.') ?></p>
        <p><?= $lang->value('V nástrojích jako Local AI Hub prosazuju determinismus: nejdřív rychlý AST index a přesné výřezy kódu, modely až na syntézu. V infrastruktuře spoléhám na verzované Ansible playbooky a minimální práva služeb.', 'In developer tools like Local AI Hub, I emphasize determinism: fast AST indexing and exact code slicing come first, models only for synthesis. In infrastructure, I rely on versioned Ansible playbooks and least-privilege service design.') ?></p>
        <p><?= $lang->value('Do open-source knihoven přispívám opravami a vylepšeními, která sám potřebuji v produkčním nasazení. Změny a jejich upstream původ mapuje přehled open-source.', 'I contribute bug fixes and performance enhancements directly to the upstream libraries I rely on in production. All contributions and their pull request records are tracked in the open-source feed.') ?></p>
        <a class="btn btn-ghost" href="<?= $lang->path('/open-source') ?>"><?= $lang->value('Vybrané upstream změny', 'Selected upstream changes') ?></a>
    </div>
    <aside class="portfolio-profile" aria-labelledby="profile-links-title">
        <h2 id="profile-links-title"><?= $lang->value('Můj profil a kontakt', 'Profile & Contact') ?></h2>
        <a href="mailto:adam.halfar@seznam.cz">
            <strong><?= $lang->value('E-mail', 'Email') ?></strong><span>adam.halfar@seznam.cz</span>
        </a>
        <a href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer">
            <strong><?= $lang->value('GitHub', 'GitHub') ?></strong><span>@Harfusha ↗</span>
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
            <p><?= $lang->value('V CakePHP a PHP 8.5 striktně odděluji doménové modely od prezentační vrstvy. Repozitáře poskytují typově bezpečná data a veřejný JSON koncový bod nabízí stejnou konzistenci externím klientům i offline renderu.', 'In CakePHP and PHP 8.5, I strictly decouple domain models from presentation layers. Repositories provide type-safe data structures, with public JSON endpoints ensuring consistency for external clients and offline SSR.') ?></p>
            <a class="inline-link" href="/api/v1/projects?lang=<?= $lang->code ?>"><?= $lang->value('Otevřít JSON katalog', 'Open the JSON catalog') ?> <span aria-hidden="true">↗</span></a>
        </article>
        <article>
            <h3><?= $lang->value('Nástroje pro vývojáře', 'Developer tools') ?></h3>
            <p><?= $lang->value('U Local AI Hubu je klíčová dohledatelnost ke zdroji. Deterministická část Hubu vrací dohledatelné výřezy zdrojového kódu. Při využití jazykového modelu lze odpověď porovnat s podklady a ověřit v SQLite WAL ledgeru.', 'With Local AI Hub, traceability is paramount. The deterministic part of the Hub retrieves traceable source code slices. When a language model is used, its output can be verified against the source and inspected in the SQLite WAL ledger.') ?></p>
            <a class="inline-link" href="<?= $lang->path('/projects/local-ai-hub') ?>"><?= $lang->value('Případová studie Local AI Hub', 'Local AI Hub case study') ?> <span aria-hidden="true">↗</span></a>
        </article>
        <article>
            <h3><?= $lang->value('Provoz a obnova', 'Operations and recovery') ?></h3>
            <p><?= $lang->value('Domácí infrastrukturu řídím deklarativně v Ansible. Deterministický model sítě mapuje závislosti služeb, což umožňuje simulovat výpadky uzlů a plánovat řízenou obnovu.', 'I manage home infrastructure declaratively in Ansible. A deterministic network topology models service dependencies, enabling outage simulations and structured recovery planning.') ?></p>
            <a class="inline-link" href="<?= $lang->path('/homelab#network-model') ?>"><?= $lang->value('Mapa domácí sítě', 'Home network map') ?> <span aria-hidden="true">↗</span></a>
        </article>
    </div>
</section>
