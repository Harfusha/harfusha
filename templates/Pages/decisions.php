<?php
declare(strict_types=1);

use App\Domain\EngineeringDecision;
use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
/** @var list<EngineeringDecision> $decisions */
?>
<section class="page-hero shell">
  <span class="eyebrow">ENGINEERING / TRADE-OFFS</span>
  <h1><?= $lang->value('Rozhodnutí za kódem.', 'Decisions behind the code.') ?></h1>
  <p><?= $lang->value('Nejen co jsem postavil, ale proč jsem zvolil určitou cestu a co mě to stálo. Karty rozlišují návrh, prototyp a doloženou architekturu.',
      'Not just what I built, but why I chose a particular approach and which costs came with it. Cards distinguish design, prototypes and documented architecture.') ?></p>
  <div class="page-meta">CONTEXT <span>·</span> CHOICE <span>·</span> TRADE-OFF <span>·</span> EVIDENCE</div>
</section>
<section class="shell section" data-decision-browser>
  <div class="filter-bar">
    <label class="search-field"><span class="visually-hidden"><?= $lang->value('Hledat rozhodnutí', 'Search decisions') ?></span>
      <span aria-hidden="true">⌕</span><input data-search type="search" placeholder="<?= $lang->value('Najít problém, projekt nebo technologii…', 'Find a problem, project or technology…') ?>">
    </label>
    <select data-decision-status aria-label="<?= $lang->value('Stav rozhodnutí','Decision status') ?>">
      <option value="all"><?= $lang->value('Všechny stavy', 'All statuses') ?></option>
      <option value="documented"><?= $lang->value('Zdokumentovaná architektura', 'Documented architecture') ?></option>
      <option value="prototype"><?= $lang->value('Prototyp / rozpracováno', 'Prototype / in progress') ?></option>
      <option value="planned"><?= $lang->value('Plán', 'Planned') ?></option>
    </select>
    <span class="filter-count" data-count><?= count($decisions) ?> <?= $lang->value('rozhodnutí','decisions') ?></span>
  </div>
  <div class="decision-grid">
    <?php foreach ($decisions as $decision): ?>
      <details class="decision-card" data-decision data-status="<?= Html::escape($decision->status) ?>"
        data-search-text="<?= Html::escape($decision->project.' '.$decision->title($lang).' '.$decision->context($lang).' '.$decision->choice($lang)) ?>">
        <summary>
          <span class="eyebrow">DESIGN RECORD / <?= Html::escape(strtoupper($decision->status)) ?></span>
          <strong><?= Html::escape($decision->title($lang)) ?></strong>
          <span class="decision-context"><?= Html::escape($decision->context($lang)) ?></span>
          <span class="decision-more"><?= $lang->value('Rozbalit technické zdůvodnění', 'Explore the reasoning') ?> <span aria-hidden="true">+</span></span>
        </summary>
        <div class="decision-detail">
          <div><span class="eyebrow">01 / <?= $lang->value('VOLBA', 'CHOICE') ?></span><p><?= Html::escape($decision->choice($lang)) ?></p></div>
          <div><span class="eyebrow">02 / TRADE-OFF</span><p><?= Html::escape($decision->tradeoff($lang)) ?></p></div>
          <div><span class="eyebrow">03 / <?= $lang->value('DŮKAZY A LIMITY', 'EVIDENCE & LIMITS') ?></span><p><?= Html::escape($decision->evidence($lang)) ?></p></div>
          <a class="inline-link" href="<?= $lang->path('/projects/'.$decision->project) ?>"><?= $lang->value('Otevřít případovou studii','Read the related case study') ?> ↗</a>
        </div>
      </details>
    <?php endforeach; ?>
  </div>
  <p class="empty-state hidden" data-empty><?= $lang->value('Žádné rozhodnutí neodpovídá filtru.', 'No decisions match this filter.') ?></p>
  <div class="notice note-info"><strong><?= $lang->value('Záznam rozhodnutí není audit.', 'A design record is not an audit.') ?></strong>
    <p><?= $lang->value('Tyto záznamy jsou kurátorované technické poznámky a transparentně popisují omezení. Neznamenají automaticky bezpečnostní certifikaci ani provozní benchmark.',
    'These are curated engineering notes with explicit limitations, not security certifications or operational benchmarks.') ?></p>
  </div>
</section>
