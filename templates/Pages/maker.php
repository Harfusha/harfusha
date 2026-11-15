<?php
declare(strict_types=1);

use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var list<array<string,mixed>> $items */
?>
<section class="page-hero shell">
 <span class="eyebrow">MAKER / ENGINEERING / FIELD NOTES</span>
 <h1><?= $lang->value('Věci mimo obrazovku.', 'Beyond the screen.') ?></h1>
 <p><?= $lang->value('Fyzické prototypy, 3D tisk a nápady, které vznikají v domácí dílně. Rozlišuji koncept od fungujícího výrobku a výpočty od skutečných testů.', 'Physical prototypes, 3D printing and ideas from a home workshop. A concept is not a finished product, and a calculation is not a load test.') ?></p>
 <div class="page-meta">PHYSICAL / PROTOTYPE / EXPERIMENTS <span>·</span> NO CERTIFICATION CLAIMS</div>
</section>
<section class="shell section" data-maker-browser>
  <div class="filter-bar">
    <label class="search-field"><span class="visually-hidden"><?= $lang->value('Hledat výrobek','Search maker projects') ?></span><span aria-hidden="true">⌕</span><input type="search" data-search placeholder="<?= $lang->value('Materiál, výrobek nebo technologie…','Material, project or technology…') ?>"></label>
    <select data-maker-status aria-label="<?= $lang->value('Stav projektu','Project status') ?>">
      <option value="all"><?= $lang->value('Všechny stavy','All stages') ?></option>
      <option value="in-use"><?= $lang->value('V provozu','In use') ?></option>
      <option value="prototype">Prototype</option>
      <option value="planned"><?= $lang->value('Plánované','Planned') ?></option>
    </select>
    <span class="filter-count" data-count role="status" aria-live="polite" aria-atomic="true"><?= count($items) ?> <?= $lang->value('záznamů','results') ?></span>
  </div>
  <div class="maker-grid">
  <?php foreach ($items as $item):
      $title = (string)$item['title_'.$lang->code];
      $description = (string)$item['summary_'.$lang->code];
      $method = (string)$item['method_'.$lang->code];
      $status = (string)$item['status'];
      $related = (string)$item['related'];
  ?>
   <article class="maker-card" data-maker data-status="<?= Html::escape($status) ?>" data-search-text="<?= Html::escape(strtolower($title.' '.$description.' '.$method.' '.implode(' ',(array)$item['tags']))) ?>">
     <div class="maker-card-visual maker-visual-<?= Html::escape((string)$item['visual']) ?>" aria-hidden="true"><div class="maker-geometry"><span></span><span></span><span></span><span></span></div><span class="maker-visual-label">ENGINEERING / <?= Html::escape((string)$item['id']) ?></span></div>
     <div class="maker-card-content"><div class="maker-card-meta"><span class="category"><?= Html::escape((string)$item['category']) ?></span><span class="status <?= $status === 'in-use' ? 'active' : Html::escape($status) ?>"><?= Html::escape($status) ?></span></div>
       <h2><?= Html::escape($title) ?></h2><p><?= Html::escape($description) ?></p>
       <details class="maker-details"><summary><?= $lang->value('Jak to řeším','Design considerations') ?></summary><p><?= Html::escape($method) ?></p></details>
       <div class="stack-list"><?php foreach ($item['tags'] as $tag): ?><span><?= Html::escape((string)$tag) ?></span><?php endforeach; ?></div>
       <a class="inline-link" href="<?= $lang->path('/projects/'.$related) ?>"><?= $lang->value('Související software / projekt','Related software / case study') ?> ↗</a>
     </div>
   </article>
  <?php endforeach; ?>
  </div>
  <p class="empty-state hidden" data-empty><?= $lang->value('Žádný výrobek neodpovídá filtru.','No matching projects.') ?></p>
</section>
<section class="shell section">
  <div class="triptych">
    <article class="info-card"><span>01 /</span><h3>Reproducibility</h3><p><?= $lang->value('Rozměry, materiály a nastavení mají smysl teprve tehdy, když je lze znovu ověřit.', 'Dimensions, materials and settings matter when they can be reproduced and verified.') ?></p></article>
    <article class="info-card"><span>02 /</span><h3>Failure modes</h3><p><?= $lang->value('Slicer, tolerance, vrstvy, teplo a pevnost – vše má své limity.', 'Slicer, tolerances, layers, heat and strength each have limitations.') ?></p></article>
    <article class="info-card"><span>03 /</span><h3>3D Workbench</h3><p><a href="<?= $lang->path('/playground') ?>#cad" class="inline-link"><?= $lang->value('Prohlédni si vlastní STL lokálně v prohlížeči.', 'Inspect your own STL locally in your browser.') ?> ↗</a></p></article>
  </div>
</section>
