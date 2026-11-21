<?php
declare(strict_types=1);
use App\Support\Language;
/** @var Language $lang */
?>
<section class="page-hero shell maker-hero">
    <div>
        <p class="eyebrow"><?= $lang->value('3D SKENOVÁNÍ · PŘÍPRAVA TISKU', '3D SCANNING · PRINT PREPARATION') ?></p>
        <h1><?= $lang->value('Dva samostatné projekty pro práci s 3D modely.', 'Two separate projects for working with 3D models.') ?></h1>
        <p><?= $lang->value('Jeden zkoumá rekonstrukci CAD modelu ze skenu. Druhý navrhuje, jak oddělit příjem tiskové poptávky od jejího zpracování.', 'One explores CAD reconstruction from a scan. The other proposes separating print-request intake from processing.') ?></p>
    </div>
    <figure class="maker-visual">
        <img src="/assets/img/scale-studio.webp" width="1448" height="1086" loading="lazy" alt="<?= $lang->value('Ilustrace oranžového dílu a posuvného měřítka', 'Illustration of an orange part and a caliper') ?>">
        <figcaption><?= $lang->value('Ilustrace, nikoli výsledek měření nebo skenování.', 'Illustration, not a measurement or scan result.') ?></figcaption>
    </figure>
</section>
<section class="section shell maker-workflow" aria-labelledby="maker-projects-title">
    <h2 id="maker-projects-title"><?= $lang->value('Dva odlišné projekty', 'Two distinct projects') ?></h2>
    <div class="maker-project-grid">
        <article>
            <div class="maker-project-meta"><strong>Scan2CAD Studio</strong><span class="status beta"><?= $lang->value('Beta', 'Beta') ?></span></div>
            <h3><?= $lang->value('Od skenu k pracovnímu CAD modelu', 'From scan to a working CAD model') ?></h3>
            <p><?= $lang->value('Importuje 3D sken, vyžaduje potvrzené měřítko a připravuje model k další kontrole.', 'Imports a 3D scan, requires a confirmed scale and prepares a model for further review.') ?></p>
            <a class="inline-link" href="<?= $lang->path('/projects/scan2cad') ?>"><?= $lang->value('Rozsah a stav projektu', 'Project scope and status') ?> <span aria-hidden="true">↗</span></a>
        </article>
        <article>
            <div class="maker-project-meta"><strong>Tiskov</strong><span class="status proposal"><?= $lang->value('Návrh', 'Proposal') ?></span></div>
            <h3><?= $lang->value('Přijmout poptávku, zpracovat ji později', 'Accept a request, process it later') ?></h3>
            <p><?= $lang->value('Návrh počítá s frontou a samostatným procesem pro přípravu modelu k tisku.', 'The proposal uses a queue and a separate process to prepare a model for printing.') ?></p>
            <a class="inline-link" href="<?= $lang->path('/projects/tiskov') ?>"><?= $lang->value('Rozsah a stav projektu', 'Project scope and status') ?> <span aria-hidden="true">↗</span></a>
        </article>
    </div>
</section>
<section class="shell section reading-footer" aria-labelledby="maker-decisions-title">
    <div>
        <h2 id="maker-decisions-title"><?= $lang->value('Technická rozhodnutí v 3D projektech', 'Engineering decisions in 3D projects') ?></h2>
        <p><?= $lang->value('Proč polygonová síť není parametrický CAD model a proč má být příprava tisku oddělena od webového serveru.', 'Why a polygon mesh is not a parametric CAD model and why print slicing belongs off the web tier.') ?></p>
    </div>
    <a class="inline-link" href="<?= $lang->path('/decisions') ?>"><?= $lang->value('Prohlédnout technická rozhodnutí', 'Browse engineering decisions') ?> <span aria-hidden="true">↗</span></a>
</section>
