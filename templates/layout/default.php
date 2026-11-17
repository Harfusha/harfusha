<?php
declare(strict_types=1);
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var string $title */
/** @var string $description */
/** @var string $content */
/** @var string $route */
$route = $route ?? '/';
$nav = [
    ['projects', $lang->value('Projekty', 'Projects')],
    ['connections', $lang->value('Přehled', 'Index')],
    ['homelab', 'Homelab'],
    ['playground', 'Playground'],
    ['open-source', 'Open Source'],
    ['now', $lang->value('Právě teď', 'Now')],
    ['about', $lang->value('O mně', 'About')],
];
$fullUrl = rtrim((string)Cake\Core\Configure::read('App.fullBaseUrl', 'https://example.com'), '/') . $lang->path($route);
?>
<!doctype html>
<html lang="<?= Html::escape($lang->code) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="color-scheme" content="dark light">
  <meta name="theme-color" content="#0a1118">
  <meta name="description" content="<?= Html::escape($description) ?>">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= Html::escape($title) ?>">
  <meta property="og:description" content="<?= Html::escape($description) ?>">
  <meta property="og:url" content="<?= Html::escape($fullUrl) ?>">
  <link rel="canonical" href="<?= Html::escape($fullUrl) ?>">
  <link rel="alternate" type="application/rss+xml" title="Harfusha Lab Journal" href="<?= $lang->path('/feed.xml') ?>">
  <link rel="alternate" hreflang="cs" href="<?= Html::escape(rtrim((string)Cake\Core\Configure::read('App.fullBaseUrl', 'https://example.com'), '/') . Html::path('cs',$route)) ?>">
  <link rel="alternate" hreflang="en" href="<?= Html::escape(rtrim((string)Cake\Core\Configure::read('App.fullBaseUrl', 'https://example.com'), '/') . Html::path('en',$route)) ?>">
  <link rel="alternate" hreflang="x-default" href="<?= Html::escape(rtrim((string)Cake\Core\Configure::read('App.fullBaseUrl', 'https://example.com'), '/') . Html::path('cs',$route)) ?>">
  <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="<?= Html::escape(Html::versionedAsset('assets/css/app.css')) ?>">
  <script src="<?= Html::escape(Html::versionedAsset('assets/js/lab-core.js')) ?>" defer></script>
  <script src="<?= Html::escape(Html::versionedAsset('assets/js/app.js')) ?>" defer></script>
  <title><?= Html::escape($title) ?></title>
</head>
<body data-language="<?= Html::escape($lang->code) ?>">
<a class="skip-link" href="#main"><?= $lang->value('Přejít na obsah','Skip to content') ?></a>
<div class="site-backdrop" aria-hidden="true"></div>
<header class="site-header">
  <div class="header-inner shell">
    <a class="brand" href="<?= $lang->path() ?>" aria-label="<?= $lang->value('Harfusha Lab – úvod', 'Harfusha Lab home') ?>"><span class="brand-mark" aria-hidden="true"><i></i><i></i><i></i></span><span>HARFUSHA<span class="brand-light">/LAB</span></span></a>
    <nav class="desktop-nav" aria-label="<?= $lang->value('Hlavní navigace', 'Main navigation') ?>">
      <?php foreach ($nav as [$slug,$text]): ?>
      <a href="<?= $lang->path('/'.$slug) ?>" <?= str_starts_with($route, '/'.$slug) ? 'aria-current="page"' : '' ?>><?= Html::escape($text) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="header-actions">
      <a class="lang-toggle" href="<?= Html::path($lang->code === 'en' ? 'cs' : 'en', $route) ?>" hreflang="<?= $lang->code === 'en' ? 'cs' : 'en' ?>" aria-label="<?= $lang->value('EN – přepnout do angličtiny', 'CZ – switch to Czech') ?>"><?= $lang->code === 'en' ? 'CZ' : 'EN' ?></a>
      <a class="github-chip" href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer" aria-label="<?= $lang->value('GitHub – profil', 'GitHub profile') ?>">GitHub <span aria-hidden="true">↗</span></a>
    </div>
    <details class="mobile-menu"><summary aria-label="<?= $lang->value('Otevřít navigaci','Open navigation') ?>"><span></span><span></span><span></span></summary>
      <nav aria-label="<?= $lang->value('Mobilní navigace', 'Mobile navigation') ?>">
        <?php foreach ($nav as [$slug,$text]): ?><a href="<?= $lang->path('/'.$slug) ?>"><?= Html::escape($text) ?></a><?php endforeach; ?>
        <a href="<?= Html::path($lang->code === 'en'?'cs':'en',$route) ?>"><?= $lang->value('Přepnout do angličtiny','Switch to Czech') ?></a>
      </nav>
    </details>
  </div>
</header>
<main id="main"><?= $this->fetch('content') ?></main>
<footer class="site-footer"><div class="shell footer-grid">
  <div><a class="brand" href="<?= $lang->path() ?>">HARFUSHA<span class="brand-light">/LAB</span></a><p><?= $lang->value('Software, domácí servery a nápady z dílny.', 'Software, home servers and ideas from the workshop.') ?></p></div>
  <div><span class="eyebrow"><?= $lang->value('PROZKOUMAT', 'EXPLORE') ?></span><a href="<?= $lang->path('/decisions') ?>"><?= $lang->value('Technická rozhodnutí','Engineering decisions') ?></a><a href="<?= $lang->path('/projects') ?>"><?= $lang->value('Všechny projekty', 'All projects') ?></a><a href="<?= $lang->path('/homelab') ?>">Home Lab Explorer</a><a href="<?= $lang->path('/open-source') ?>">Open Source</a></div>
  <div><span class="eyebrow"><?= $lang->value('SPOJENÍ', 'CONNECT') ?></span><a href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer">GitHub ↗</a><a href="<?= $lang->path('/about') ?>"><?= $lang->value('O mně', 'About me') ?></a><a href="<?= $lang->path('/journal') ?>">Workshop Log</a></div>
  <div class="footer-aside">© <?= date('Y') ?> Adam Halfar <p><?= $lang->value('Díky za návštěvu. Další projekty najdeš na GitHubu.', 'Thanks for visiting. Find more projects on GitHub.') ?></p></div>
</div></footer>
</body></html>
