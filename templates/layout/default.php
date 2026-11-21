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
    ['homelab', $lang->value('Domácí síť', 'Home lab')],
    ['open-source', $lang->value('Open-source práce', 'Open source work')],
    ['journal', $lang->value('Deník', 'Journal')],
    ['about', $lang->value('O mně', 'About')],
];
$fullUrl = rtrim((string)Cake\Core\Configure::read('App.fullBaseUrl', 'https://example.com'), '/') . $lang->path($route);
$ogImage = $ogImage ?? null;

if ($route === '/') {
    $ogImage = '/assets/img/mutaui-overview-en-dark.png';
    $ogImageWidth = 1380;
    $ogImageHeight = 900;
    $ogImageAlt = $lang->value('MutaUI – screenshot desktopové aplikace s ukázkovými daty', 'MutaUI desktop app screenshot with sample data');
} elseif ($route === '/homelab') {
    $ogImage = '/assets/img/home-network-model.png';
    $ogImageWidth = 1280;
    $ogImageHeight = 831;
    $ogImageAlt = $lang->value('Statický náhled kurátorované mapy domácí sítě', 'Static preview of the curated home-network map');
}

if (isset($project) && $project instanceof App\Domain\Project) {
    if ($project->slug === 'mutaui') {
        $ogImage = '/assets/img/mutaui-overview-en-dark.png';
        $ogImageWidth = 1380;
        $ogImageHeight = 900;
        $ogImageAlt = $lang->value('MutaUI – synchronizace a přesměrování portů', 'MutaUI – sync and port forwarding');
    } elseif ($project->slug === 'woodbound') {
        $ogImage = '/assets/img/woodbound-region-development.png';
        $ogImageWidth = 1280;
        $ogImageHeight = 720;
        $ogImageAlt = $lang->value('Woodbound – vývojový záběr lesní krajiny', 'Woodbound – development capture');
    }
}

$fullOgImageUrl = $ogImage === null ? null : (str_starts_with($ogImage, 'http') ? $ogImage : rtrim((string)Cake\Core\Configure::read('App.fullBaseUrl', 'https://example.com'), '/') . $ogImage);

$baseUrl = rtrim((string)Cake\Core\Configure::read('App.fullBaseUrl', 'https://example.com'), '/');
$jsonLdGraph = [
    [
        '@type' => 'Person',
        '@id' => $baseUrl . '/#person',
        'name' => 'Adam Halfar',
        'alternateName' => 'Harfusha',
        'url' => $baseUrl,
        'jobTitle' => $lang->value('Backendový vývojář', 'Backend Developer'),
        'sameAs' => [
            'https://github.com/Harfusha',
        ],
    ],
    [
        '@type' => 'WebSite',
        '@id' => $baseUrl . '/#website',
        'url' => $baseUrl,
        'name' => 'Harfusha Lab',
        'publisher' => [
            '@id' => $baseUrl . '/#person',
        ],
    ],
    [
        '@type' => 'WebPage',
        '@id' => $fullUrl,
        'url' => $fullUrl,
        'name' => $title,
        'description' => $description,
        'isPartOf' => [
            '@id' => $baseUrl . '/#website',
        ],
        'inLanguage' => $lang->code,
    ],
];

if (isset($project) && $project instanceof App\Domain\Project) {
    /** @var array<string,mixed> $softwareData */
    $softwareData = [
        '@type' => 'SoftwareSourceCode',
        'name' => $project->name,
        'description' => $project->summary($lang),
        'author' => [
            '@id' => $baseUrl . '/#person',
        ],
    ];
    if (!$project->private && $project->repo !== '') {
        $softwareData['codeRepository'] = Html::safeUrl($project->repo);
    }
    if ($project->stack !== []) {
        $softwareData['programmingLanguage'] = implode(', ', $project->stack);
    }
    $jsonLdGraph[] = $softwareData;
}

$jsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@graph' => $jsonLdGraph,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
?>
<!doctype html>
<html lang="<?= Html::escape($lang->code) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="color-scheme" content="light dark">
  <script>(function(){var t='light';try{var s=localStorage.getItem('theme');if(s==='light'||s==='dark')t=s;}catch(e){}document.documentElement.dataset.theme=t;})();</script>
  <link rel="preload" href="/assets/fonts/inter-latin.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="manifest" href="/manifest.webmanifest">
  <meta name="theme-color" content="#f4f0e6">
  <meta name="description" content="<?= Html::escape($description) ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Harfusha Lab">
  <meta property="og:title" content="<?= Html::escape($title) ?>">
  <meta property="og:description" content="<?= Html::escape($description) ?>">
  <meta property="og:url" content="<?= Html::escape($fullUrl) ?>">
  <?php if ($fullOgImageUrl !== null): ?>
  <meta property="og:image" content="<?= Html::escape($fullOgImageUrl) ?>">
  <meta property="og:image:width" content="<?= Html::escape((string)$ogImageWidth) ?>">
  <meta property="og:image:height" content="<?= Html::escape((string)$ogImageHeight) ?>">
  <meta property="og:image:alt" content="<?= Html::escape((string)$ogImageAlt) ?>">
  <?php endif; ?>
  <meta property="og:locale" content="<?= $lang->code === 'cs' ? 'cs_CZ' : 'en_US' ?>">
  <meta name="twitter:card" content="<?= $fullOgImageUrl === null ? 'summary' : 'summary_large_image' ?>">
  <meta name="twitter:title" content="<?= Html::escape($title) ?>">
  <meta name="twitter:description" content="<?= Html::escape($description) ?>">
  <?php if ($fullOgImageUrl !== null): ?>
  <meta name="twitter:image" content="<?= Html::escape($fullOgImageUrl) ?>">
  <meta name="twitter:image:alt" content="<?= Html::escape((string)$ogImageAlt) ?>">
  <?php endif; ?>
  <link rel="canonical" href="<?= Html::escape($fullUrl) ?>">
  <link rel="alternate" type="application/rss+xml" title="Harfusha Lab Journal" href="<?= $lang->path('/feed.xml') ?>">
  <link rel="alternate" hreflang="cs" href="<?= Html::escape(rtrim((string)Cake\Core\Configure::read('App.fullBaseUrl', 'https://example.com'), '/') . Html::path('cs',$route)) ?>">
  <link rel="alternate" hreflang="en" href="<?= Html::escape(rtrim((string)Cake\Core\Configure::read('App.fullBaseUrl', 'https://example.com'), '/') . Html::path('en',$route)) ?>">
  <link rel="alternate" hreflang="x-default" href="<?= Html::escape(rtrim((string)Cake\Core\Configure::read('App.fullBaseUrl', 'https://example.com'), '/') . Html::path('cs',$route)) ?>">
  <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="<?= Html::escape(Html::versionedAsset('assets/css/app.css')) ?>">
  <link rel="stylesheet" href="<?= Html::escape(Html::versionedAsset('assets/css/portfolio.css')) ?>">
  <script src="<?= Html::escape(Html::versionedAsset('assets/js/lab-core.js')) ?>" defer></script>
  <script src="<?= Html::escape(Html::versionedAsset('assets/js/app.js')) ?>" defer></script>
  <?php if ($jsonLd !== false): ?><script type="application/ld+json"><?= $jsonLd ?></script><?php endif; ?>
  <title><?= Html::escape($title) ?></title>
</head>
<body data-language="<?= Html::escape($lang->code) ?>">
<a class="skip-link" href="#main"><?= $lang->value('Přejít na obsah','Skip to content') ?></a>
<div class="site-backdrop" aria-hidden="true"></div>
<header class="site-header">
  <div class="header-inner shell">
    <a class="brand" href="<?= $lang->path() ?>" aria-label="<?= $lang->value('Adam Halfar – úvod', 'Adam Halfar home') ?>"><span class="brand-mark" aria-hidden="true">H</span><span>Harfusha</span></a>
    <nav class="desktop-nav" aria-label="<?= $lang->value('Hlavní navigace', 'Main navigation') ?>">
      <?php foreach ($nav as [$slug,$text]): ?>
      <a href="<?= $lang->path('/'.$slug) ?>" <?= str_starts_with($route, '/'.$slug) ? 'aria-current="page"' : '' ?>><?= Html::escape($text) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="header-actions">
      <button class="theme-toggle" type="button" data-theme-toggle aria-label="<?= $lang->value('Přepnout barevný motiv', 'Toggle color theme') ?>" title="<?= $lang->value('Přepnout světlý / tmavý režim', 'Toggle light / dark mode') ?>"><span class="theme-toggle-icon" aria-hidden="true">◐</span></button>
      <a class="lang-toggle" href="<?= Html::path($lang->code === 'en' ? 'cs' : 'en', $route) ?>" hreflang="<?= $lang->code === 'en' ? 'cs' : 'en' ?>" aria-label="<?= $lang->value('EN – přepnout do angličtiny', 'CZ – switch to Czech') ?>"><?= $lang->code === 'en' ? 'CZ' : 'EN' ?></a>
      <a class="github-chip" href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer" aria-label="<?= $lang->value('GitHub – profil', 'GitHub profile') ?>">GitHub <span aria-hidden="true">↗</span></a>
    </div>
    <details class="mobile-menu"><summary aria-label="<?= $lang->value('Otevřít navigaci','Open navigation') ?>"><span></span><span></span><span></span></summary>
      <nav aria-label="<?= $lang->value('Mobilní navigace', 'Mobile navigation') ?>">
        <?php foreach ($nav as [$slug,$text]): ?><a href="<?= $lang->path('/'.$slug) ?>" <?= str_starts_with($route, '/'.$slug) ? 'aria-current="page"' : '' ?>><?= Html::escape($text) ?></a><?php endforeach; ?>
        <a href="<?= $lang->path('/now') ?>" <?= $route === '/now' ? 'aria-current="page"' : '' ?>><?= $lang->value('Časová osa','Work timeline') ?></a>
      </nav>
    </details>
  </div>
</header>
<main id="main"><?= $this->fetch('content') ?></main>
<footer class="site-footer"><div class="shell footer-grid">
  <div><a class="brand" href="<?= $lang->path() ?>">Adam Halfar</a><p><?= $lang->value('Backendový vývoj a vývojářské nástroje', 'Backend development and developer tools') ?></p></div>
  <div><span class="eyebrow"><?= $lang->value('PRÁCE', 'WORK') ?></span><a href="<?= $lang->path('/projects') ?>"><?= $lang->value('Všechny projekty', 'All projects') ?></a><a href="<?= $lang->path('/connections') ?>"><?= $lang->value('Projektové skupiny', 'Project groups') ?></a><a href="<?= $lang->path('/decisions') ?>"><?= $lang->value('Technická rozhodnutí', 'Engineering decisions') ?></a><a href="<?= $lang->path('/homelab') ?>"><?= $lang->value('Model domácí sítě', 'Home network model') ?></a><a href="<?= $lang->path('/maker') ?>"><?= $lang->value('3D skenování a tisk', '3D scanning and printing') ?></a></div>
  <div><span class="eyebrow"><?= $lang->value('AKTIVITA', 'ACTIVITY') ?></span><a href="<?= $lang->path('/journal') ?>"><?= $lang->value('Vývojový deník', 'Development journal') ?></a><a href="<?= $lang->path('/now') ?>"><?= $lang->value('Časová osa', 'Work timeline') ?></a><a href="<?= $lang->path('/open-source') ?>"><?= $lang->value('Open-source práce', 'Open source work') ?></a><a href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer">GitHub <span aria-hidden="true">↗</span></a></div>
  <div class="footer-aside">© <?= date('Y') ?> Adam Halfar</div>
</div></footer>
</body></html>
