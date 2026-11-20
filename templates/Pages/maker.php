<?php
declare(strict_types=1);
use App\Support\Language;
/** @var Language $lang */
?>
<section class="page-hero shell maker-hero">
    <div>
        <h1><?= $lang->value('Od skenu k tisku', 'From scan to print') ?></h1>
        <p><?= $lang->value('Rozměr na displeji se musí shodovat s rozměrem na stole. Začni známou vzdáleností a podle ní zkontroluj model, než ho pošleš k tisku.', 'The part on your screen needs to match the part on the bench. Start with a known distance and use it to check the model before printing.') ?></p>
        <a class="btn btn-primary" href="<?= $lang->path('/playground#station-scale-check') ?>"><?= $lang->value('Vyzkoušet přepočet měřítka', 'Try the scale calculator') ?> <span aria-hidden="true">↗</span></a>
    </div>
    <figure class="maker-visual">
        <img src="/assets/img/scale-studio.webp" width="1448" height="1086" alt="<?= $lang->value('Ilustrace oranžového dílu a posuvného měřítka', 'Illustration of an orange part and a caliper') ?>">
        <figcaption><?= $lang->value('Referenční délka pomáhá odhadnout rozměry ve stejné rovině.', 'A measured reference helps estimate dimensions in the same plane.') ?></figcaption>
    </figure>
</section>
<section class="section shell maker-workflow" aria-labelledby="maker-flow-title">
    <h2 id="maker-flow-title"><?= $lang->value('Tři kontroly před tiskem', 'Three checks before printing') ?></h2>
    <ol class="maker-steps">
        <li>
            <h3><?= $lang->value('Změř referenci na předmětu', 'Measure a reference on the object') ?></h3>
            <p><?= $lang->value('Zadej známou vzdálenost mezi body. Stejný počet pixelů může bez kalibrace znamenat různé skutečné rozměry.', 'Enter a known distance between points. Without calibration, the same number of pixels can represent different physical dimensions.') ?></p>
            <a class="inline-link" href="<?= $lang->path('/playground#station-scale-check') ?>"><?= $lang->value('Změnit měřítko v ukázce', 'Change the scale in the demo') ?> <span aria-hidden="true">↗</span></a>
        </li>
        <li>
            <h3><?= $lang->value('Porovnej model s předmětem', 'Check the model against the object') ?></h3>
            <p><?= $lang->value('U otvorů, dosedacích ploch a dalších důležitých tvarů porovnej model se skutečným měřením. STEP nebo DXF je výstup ke kontrole.', 'Compare holes, mating surfaces and other important geometry against real measurements. STEP or DXF output is available for review.') ?></p>
            <a class="inline-link" href="<?= $lang->path('/projects/scan2cad') ?>"><?= $lang->value('Jak postupuje Scan2CAD Studio', 'How Scan2CAD Studio works') ?> <span aria-hidden="true">↗</span></a>
        </li>
        <li>
            <h3><?= $lang->value('Připrav model k posouzení', 'Prepare the model for review') ?></h3>
            <p><?= $lang->value('Přilož model, rozměry a požadavky na materiál. Návrh Tiskov ukazuje, kudy tyto podklady projdou od poptávky k ruční kontrole.', 'Include the model, dimensions and material requirements. The Tiskov proposal shows how that information moves from a request to manual review.') ?></p>
            <a class="inline-link" href="<?= $lang->path('/playground#station-tiskov-queue') ?>"><?= $lang->value('Vyzkoušet model fronty', 'Try the queue model') ?> <span aria-hidden="true">↗</span></a>
            <a class="inline-link" href="<?= $lang->path('/projects/tiskov') ?>"><?= $lang->value('Studie Tiskov', 'Tiskov case study') ?> <span aria-hidden="true">↗</span></a>
        </li>
    </ol>
</section>
