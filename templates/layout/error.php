<?php
declare(strict_types=1);
use App\Support\Html;
/** @var \Cake\View\View $this */
$language = $this->fetch('language') === 'en' ? 'en' : 'cs';
?>
<!doctype html>
<html lang="<?= $language ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="dark">
  <meta name="theme-color" content="#0d1019">
  <meta name="robots" content="noindex, nofollow">
  <title><?= htmlspecialchars($this->fetch('title') ?: 'Harfusha Lab', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= Html::escape(Html::versionedAsset('assets/css/app.css')) ?>">
  <link rel="stylesheet" href="<?= Html::escape(Html::versionedAsset('assets/css/portfolio.css')) ?>">
</head>
<body data-language="<?= $language ?>">
  <header class="site-header"><div class="header-inner shell">
    <a class="brand" href="<?= Html::path($language) ?>">Adam Halfar</a>
  </div></header>
  <main id="main-content"><?= $this->fetch('content') ?></main>
</body>
</html>
