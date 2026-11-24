<?php
declare(strict_types=1);

use App\Support\Language;

/** @var \Cake\View\View $this */
/** @var bool $serverError */
$path = $this->getRequest()->getUri()->getPath();
$lang = new Language(preg_match('~^/en(?:/|$)~', $path) === 1 ? 'en' : 'cs');
$title = $serverError
    ? $lang->value('Služba je dočasně nedostupná', 'Service temporarily unavailable')
    : $lang->value('Tahle stránka se nenašla', 'Page not found');
$this->setLayout('error');
$this->assign('language', $lang->code);
$this->assign('title', $title . ' | Adam Halfar');
?>
<section class="shell page-hero recovery-page" aria-labelledby="error-title">
    <h1 id="error-title"><?= $title ?></h1>
    <p><?= $serverError
        ? $lang->value('Požadavek se nepodařilo dokončit. Zkuste stránku načíst později.', 'The request could not be completed. Try loading the page again later.')
        : $lang->value('Požadovaná adresa neexistuje nebo byla přesunuta. Pokračujte do katalogu veřejných projektů nebo na úvod.', 'The requested address does not exist or has been moved. Continue to the public project catalog or home.') ?></p>
    <div class="hero-buttons">
        <a class="btn btn-primary" href="<?= $lang->path('/projects') ?>"><?= $lang->value('Katalog projektů', 'Project catalog') ?></a>
        <a class="btn btn-ghost" href="<?= $lang->path() ?>"><?= $lang->value('Úvodní stránka', 'Home') ?></a>
    </div>
</section>
