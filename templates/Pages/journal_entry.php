<?php
declare(strict_types=1);

use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
/** @var array<string,mixed> $entry */
/** @var string $entryId */

$date = substr((string)($entry['published_at'] ?? ''), 0, 10);
$category = (string)($entry['category'] ?? '');
$projectSlug = (string)($entry['project'] ?? '');
$source = Html::safeUrl((string)($entry['source_url'] ?? ''));
?>
<section class="page-hero shell" aria-labelledby="journal-entry-title">
    <a class="back-link" href="<?= $lang->path('/journal#' . Html::escape($entryId)) ?>"><?= $lang->value('← Zpět do deníku', '← Back to journal') ?></a>
    <div class="detail-kicker">
        <time datetime="<?= Html::escape($date) ?>"><?= Html::escape($date) ?></time>
        <span class="category"><?= Html::escape($category) ?></span>
        <?php if ($projectSlug !== '' && $projectSlug !== 'open-source'): ?>
            <a class="journal-project-tag" href="<?= $lang->path('/projects/' . $projectSlug) ?>"><?= Html::escape($projectSlug) ?> ↗</a>
        <?php endif; ?>
    </div>
    <h1 id="journal-entry-title"><?= Html::escape((string)$entry['title_' . $lang->code]) ?></h1>
</section>

<section class="section shell journal-single-body">
    <article class="timeline-item" id="<?= Html::escape($entryId) ?>">
        <p><?= Html::escape((string)$entry['body_' . $lang->code]) ?></p>
        <?php if ($source !== '#'): ?>
            <?php if (str_starts_with($source, 'http')): ?>
                <a class="inline-link" href="<?= Html::escape($source) ?>" target="_blank" rel="noopener noreferrer"><?= $lang->value('Původní podklad', 'Original source') ?> <span aria-hidden="true">↗</span></a>
            <?php else: ?>
                <a class="inline-link" href="<?= $lang->path($source) ?>"><?= $lang->value('Doloženo v případové studii', 'Documented in case study') ?> <span aria-hidden="true">↗</span></a>
            <?php endif; ?>
        <?php endif; ?>
    </article>
</section>
