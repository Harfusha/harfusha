<?php
declare(strict_types=1);
use App\Domain\JournalFeed;
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var list<array<string,mixed>> $journal */
?>
<section class="page-hero shell"><span class="eyebrow"><?= $lang->value('ZÁPISKY Z VÝVOJE','DEVELOPMENT NOTES') ?></span><h1><?= $lang->value('Vývojový deník.','Workshop log.') ?></h1><p><?= $lang->value('Milníky a poznámky k projektům, s odkazy na dostupné zdroje. Nové zápisky můžeš sledovat ve své RSS čtečce.', 'Project milestones and notes, with links to available sources. Follow new entries in your RSS reader.') ?></p><a class="inline-link" href="<?= $lang->path('/feed.xml') ?>"><?= $lang->value('Odebírat přes RSS','Follow via RSS') ?> ↗</a></section>
<section class="section shell"><div class="journal-timeline"><?php foreach ($journal as $entry): $source = Html::safeUrl((string)($entry['source_url'] ?? '')); $entryId = JournalFeed::entryId($entry); ?><article class="timeline-item" id="<?= Html::escape($entryId) ?>"><div class="timeline-marker"></div><time><?= Html::escape(substr((string)$entry['published_at'],0,10)) ?></time><div class="timeline-copy"><span class="category"><?= Html::escape((string)$entry['category']) ?></span><h2><a href="#<?= Html::escape($entryId) ?>"><?= Html::escape((string)$entry['title_'.$lang->code]) ?></a></h2><p><?= Html::escape((string)$entry['body_'.$lang->code]) ?></p><?php if ($source !== '#'): ?><a class="inline-link" href="<?= Html::escape($source) ?>" target="_blank" rel="noopener noreferrer"><?= $lang->value('Zdroj','Source') ?> ↗</a><?php endif; ?></div></article><?php endforeach; ?></div></section>
