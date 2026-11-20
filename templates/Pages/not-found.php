<?php
declare(strict_types=1);

/** @var App\Support\Language $lang */
?>
<section class="page-hero shell recovery-page">
    <p class="eyebrow">404</p>
    <h1><?= $lang->value('Stránka nenalezena', 'Page not found') ?></h1>
    <p><?= $lang->value('Odkaz může být starý nebo může obsahovat překlep. V katalogu najdete aktuální adresy projektů.', 'The link may be out of date or mistyped. The catalog lists the current project addresses.') ?></p>
    <div class="hero-buttons">
        <a class="btn btn-primary" href="<?= $lang->path('/projects') ?>"><?= $lang->value('Otevřít katalog projektů', 'Open the project catalog') ?> <span aria-hidden="true">↗</span></a>
        <a class="inline-link" href="<?= $lang->path() ?>"><?= $lang->value('Zpět na úvod', 'Back to the homepage') ?></a>
    </div>
</section>
