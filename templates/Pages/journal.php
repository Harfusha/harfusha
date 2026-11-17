<?php
declare(strict_types=1);
use App\Domain\JournalFeed;
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var list<array<string,mixed>> $journal */
$journalYears = [];
foreach ($journal as $entry) {
    $year = substr((string)($entry['published_at'] ?? ''), 0, 4);
    if (preg_match('/^\\d{4}$/D', $year) === 1) {
        $journalYears[$year] = ($journalYears[$year] ?? 0) + 1;
    }
}
krsort($journalYears, SORT_NUMERIC);
?>
<section class="page-hero shell"><span class="eyebrow"><?= $lang->value('ZÁPISKY Z VÝVOJE','DEVELOPMENT NOTES') ?></span><h1><?= $lang->value('Změny, které stojí za vysvětlení.','Changes worth explaining.') ?></h1><p><?= $lang->value('Nejde o každý commit. Tady jsou změny, u kterých je vidět, co se řešilo a kam vedla oprava nebo rozhodnutí. Odkaz u každého záznamu vede na původní zdroj.', 'This is not every commit. These entries show what was being worked through and where a fix or decision led. Each one links to its original source.') ?></p><a class="inline-link" href="<?= $lang->path('/feed.xml') ?>"><?= $lang->value('Odebírat přes RSS','Follow via RSS') ?> ↗</a></section>
<section class="section shell" data-journal-filter>
  <div class="lab-tabs journal-year-filter" role="group" aria-label="<?= $lang->value('Filtrovat deník podle roku', 'Filter journal by year') ?>">
    <button class="tab active" type="button" data-journal-year="all" aria-pressed="true"><?= $lang->value('Všechny roky', 'All years') ?> <span><?= count($journal) ?></span></button>
    <?php foreach ($journalYears as $year => $count): ?><button class="tab" type="button" data-journal-year="<?= Html::escape($year) ?>" aria-pressed="false"><?= Html::escape($year) ?> <span><?= $count ?></span></button><?php endforeach; ?>
  </div>
  <p class="journal-filter-count" data-journal-count data-label="<?= $lang->value('zápisků', 'entries') ?>" role="status" aria-live="polite" aria-atomic="true"><?= count($journal) ?> <?= $lang->value('zápisků', 'entries') ?></p>
  <div class="journal-timeline"><?php foreach ($journal as $entry): $source = Html::safeUrl((string)($entry['source_url'] ?? '')); $entryId = JournalFeed::entryId($entry); $publishedAt = (string)($entry['published_at'] ?? ''); ?><article class="timeline-item" id="<?= Html::escape($entryId) ?>" data-journal-entry data-year="<?= Html::escape(substr($publishedAt, 0, 4)) ?>"><div class="timeline-marker"></div><time><?= Html::escape(substr($publishedAt,0,10)) ?></time><div class="timeline-copy"><span class="category"><?= Html::escape((string)$entry['category']) ?></span><h2><a href="#<?= Html::escape($entryId) ?>"><?= Html::escape((string)$entry['title_'.$lang->code]) ?></a></h2><p><?= Html::escape((string)$entry['body_'.$lang->code]) ?></p><?php if ($source !== '#'): ?><a class="inline-link" href="<?= Html::escape($source) ?>" target="_blank" rel="noopener noreferrer"><?= $lang->value('Otevřít původní změnu','Open original change') ?> ↗</a><?php endif; ?></div></article><?php endforeach; ?></div>
</section>
