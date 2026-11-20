<?php
declare(strict_types=1);

use App\Support\Language;

/** @var \Cake\View\View $this */
/** @var bool $serverError */
$path = $this->getRequest()->getUri()->getPath();
$lang = new Language(preg_match('~^/en(?:/|$)~', $path) === 1 ? 'en' : 'cs');
$title = $serverError
    ? $lang->value('Služba je dočasně nedostupná', 'Service temporarily unavailable')
    : $lang->value('Tuto stránku nelze zobrazit', 'This page is unavailable');
$this->setLayout('error');
$this->assign('language', $lang->code);
$this->assign('title', $title . ' | Adam Halfar');
?>
<section class="shell page-hero recovery-page" aria-labelledby="error-title">
    <h1 id="error-title"><?= $title ?></h1>
    <p><?= $serverError
        ? $lang->value('Požadavek se nepodařilo dokončit. Zkuste stránku načíst později.', 'The request could not be completed. Try loading the page again later.')
        : $lang->value('Adresa může být neaktuální nebo může být stránka dočasně nepřístupná. V katalogu najdete veřejné projekty.', 'The address may be outdated or the page may be unavailable. The catalog lists the public projects.') ?></p>
    <div class="hero-buttons">
        <a class="btn btn-primary" href="<?= $lang->path() ?>"><?= $lang->value('Úvod portfolia', 'Portfolio home') ?></a>
        <a class="inline-link" href="<?= $lang->path('/projects') ?>"><?= $lang->value('Katalog projektů', 'Project catalog') ?> <span aria-hidden="true">↗</span></a>
    </div>
</section>
