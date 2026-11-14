<?php
declare(strict_types=1);

/** @var \Cake\View\View $this */
$this->setLayout('error');
$this->assign('title', 'Service temporarily unavailable | Harfusha Lab');
?>
<section class="shell page-hero" aria-labelledby="error-title">
  <span class="eyebrow">500 / SERVER ERROR</span>
  <h1 id="error-title">Something went wrong.</h1>
  <p>The request could not be completed. Please try again later.</p>
  <a class="btn btn-primary" href="/cs/">Zpět na úvod / Back home</a>
</section>
