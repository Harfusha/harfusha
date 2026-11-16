<?php
declare(strict_types=1);
use App\Support\Language;
/** @var Language $lang */
?>
<section class="page-hero shell">
  <span class="eyebrow">MAKER LAB</span>
  <h1><?= $lang->value('Dílna se připravuje.', 'The workshop is taking shape.') ?></h1>
  <p><?= $lang->value('Projekty z dílny postupně doplním, až k nim budu mít fotografie, podklady a pořádný popis.', 'I will add workshop projects as their photographs, evidence and descriptions are ready.') ?></p>
  <a class="inline-link" href="<?= $lang->path('/journal') ?>"><?= $lang->value('Vývojový deník', 'Workshop log') ?> ↗</a>
</section>
