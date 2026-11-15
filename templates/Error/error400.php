<?php
declare(strict_types=1);

/** @var \Cake\View\View $this */
/** @var string $message */
$this->setLayout('error');
$this->assign('title', 'Page unavailable | Harfusha Lab');
?>
<section class="shell page-hero" aria-labelledby="error-title">
  <span class="eyebrow">4XX / CLIENT ERROR</span>
  <h1 id="error-title">This page is not available.</h1>
  <p>The requested page may have moved or may not exist.</p>
  <a class="btn btn-primary" href="/cs/">Zpět na úvod / Back home</a>
</section>
