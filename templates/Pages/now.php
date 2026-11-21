<?php
declare(strict_types=1);
use App\Domain\JournalFeed;
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var list<array{repository:string,description:string,repo_url:string,pushed_at:string}> $recentRepos */
/** @var list<array<string,mixed>> $journalEntries */
/** @var list<array<string,mixed>> $activity */
/** @var array{source:string,last_sync:?string} $provenance */
$githubSynced = $provenance['source'] === 'github-public-api';
$githubUnavailable = $provenance['source'] === 'unavailable';
$stateLabels = [
    'merged' => $lang->value('Sloučeno', 'Merged'),
    'completed' => $lang->value('Dokončeno', 'Completed'),
    'open' => $lang->value('Otevřeno', 'Open'),
    'closed' => $lang->value('Uzavřeno', 'Closed'),
];
$activity = array_map(static function (array $item): array {
    $eventType = match ($item['state'] ?? '') {
        'merged' => 'merged_at',
        'closed', 'completed' => 'closed_at',
        default => 'published_at',
    };
    if (empty($item[$eventType])) {
        $eventType = 'published_at';
    }

    return $item + ['event_type' => $eventType, 'event_date' => (string)($item[$eventType] ?? '')];
}, $activity);
usort($activity, static fn(array $a, array $b): int => strcmp($b['event_date'], $a['event_date']));
$activityYears = [];
foreach ($activity as $item) {
    $year = substr($item['event_date'], 0, 4);
    if (preg_match('/^\\d{4}$/D', $year) === 1) {
        $activityYears[$year] = ($activityYears[$year] ?? 0) + 1;
    }
}
krsort($activityYears, SORT_NUMERIC);
?>
<section class="page-hero shell">
  <h1><?= $lang->value('Co se změnilo', 'What’s changed') ?></h1>
  <p><?= $lang->value('Přehled nedávné práce: aktivní commity v repozitářích, technické poznámky v inženýrském deníku a sloučené pull requesty do open-source projektů.', 'Recent engineering activity: repository pushes, technical notes from the engineering journal, and merged pull requests across open-source tools.') ?></p>
  <div class="page-meta"><?= $githubSynced ? $lang->value('VEŘEJNÝ IMPORT GITHUBU', 'PUBLIC GITHUB IMPORT') : ($githubUnavailable ? $lang->value('DATA NEDOSTUPNÁ', 'DATA UNAVAILABLE') : $lang->value('RUČNĚ OVĚŘENÝ SNÍMEK', 'REVIEWED SNAPSHOT')) ?> <span>·</span>
  <?php if ($provenance['last_sync'] !== null): ?><?= $lang->value('Poslední úspěšný import: ', 'Last successful import: ') ?><?= Html::escape($provenance['last_sync']) ?>
  <?php elseif ($githubUnavailable): ?><?= $lang->value('Starší záznamy při výpadku nezobrazujeme.', 'Historical records stay hidden while the source is unavailable.') ?>
  <?php else: ?><?= $lang->value('Ověřená data z repozitářů.', 'Verified repository records.') ?><?php endif; ?></div>
</section>
<nav class="shell case-navigation" aria-label="<?= $lang->value('Obsah časové osy', 'On this timeline') ?>">
    <a href="#now-repositories-title"><?= $lang->value('Repozitáře', 'Repositories') ?></a>
    <a href="#now-build-title"><?= $lang->value('Zápisky', 'Journal entries') ?></a>
    <a href="#now-history-title"><?= $lang->value('Příspěvky', 'Contributions') ?></a>
</nav>
<div class="shell now-overview">
<section class="section" aria-labelledby="now-repositories-title">
  <div class="section-heading"><div><h2 id="now-repositories-title"><?= $lang->value('Poslední změny v repozitářích', 'Latest repository updates') ?></h2><p><?= $lang->value('Záznamy o posledním odeslání změn na GitHub.', 'The timestamp reflects the last recorded push to GitHub.') ?></p></div><a class="inline-link" href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer">GitHub ↗</a></div>
  <?php if ($recentRepos !== []): ?><div class="activity-list">
  <?php foreach ($recentRepos as $repo): ?><article class="activity-item"><span class="activity-icon" aria-hidden="true">↳</span><div class="activity-body"><div class="activity-repo"><a href="<?= Html::escape(Html::safeUrl($repo['repo_url'])) ?>" target="_blank" rel="noopener noreferrer"><?= Html::escape($repo['repository']) ?> <span aria-hidden="true">↗</span></a></div><?php if ($repo['description'] !== '' && $repo['description'] !== $repo['repository'] && !preg_match('/^GitHub snapshot \d{4}-\d{2}-\d{2}$/D', $repo['description'])): ?><strong><?= Html::escape($repo['description']) ?></strong><?php endif; ?></div><div class="activity-side"><time><?= Html::escape(substr($repo['pushed_at'],0,16)) ?> UTC</time></div></article><?php endforeach; ?>
  </div><?php else: ?><p class="empty-state"><?= $lang->value('Čerstvé push údaje teď nejsou dostupné.', 'Fresh push data is not available right now.') ?></p><?php endif; ?>
</section>

<section class="section now-build-history" data-now-notes aria-labelledby="now-build-title">
    <div class="section-heading">
        <div>
            <h2 id="now-build-title"><?= $lang->value('Nové zápisky', 'Recent notes') ?></h2>
            <p><?= $lang->value('Vybrané záznamy z inženýrského deníku s odkazy na technický kontext a podklady.', 'Selected notes from the engineering journal with direct links to technical context and evidence.') ?></p>
        </div>
        <a class="inline-link" href="<?= $lang->path('/journal') ?>"><?= $lang->value('Celý deník', 'All journal entries') ?> <span aria-hidden="true">↗</span></a>
    </div>
    <?php $recentNotes = array_slice($journalEntries, 0, 4); $olderNotes = array_slice($journalEntries, 4); ?>
    <?php if ($recentNotes === []): ?><p class="empty-state"><?= $lang->value('Zatím tu nejsou žádné zápisky.', 'There are no notes yet.') ?></p><?php endif; ?>
    <?php foreach ([$recentNotes, $olderNotes] as $index => $notes): ?>
        <?php if ($notes === []) { continue; } ?>
        <?php if ($index === 1): ?>
            <details class="now-note-archive">
                <summary><?= $lang->value('Starší zápisky', 'Older notes') ?> (<?= count($notes) ?>)</summary>
        <?php endif; ?>
        <ol class="now-build-list">
            <?php foreach ($notes as $entry):
                $date = substr((string)($entry['published_at'] ?? ''), 0, 10);
                $sourceUrl = Html::safeUrl((string)($entry['source_url'] ?? ''));
                $entryUrl = $lang->path('/journal#' . JournalFeed::entryId($entry));
            ?>
                <li class="now-build-entry">
                    <time datetime="<?= Html::escape($date) ?>"><?= Html::escape($date) ?></time>
                    <div>
                        <h3><a href="<?= Html::escape($entryUrl) ?>"><?= Html::escape($lang->value((string)$entry['title_cs'], (string)$entry['title_en'])) ?></a></h3>
                        <?php if ($sourceUrl !== '#'): ?>
                            <?php if (str_starts_with($sourceUrl, 'http')): ?>
                                <a class="inline-link" href="<?= Html::escape($sourceUrl) ?>" target="_blank" rel="noopener noreferrer"><?= $lang->value('Původní podklad', 'Original source') ?> <span aria-hidden="true">↗</span></a>
                            <?php else: ?>
                                <a class="inline-link" href="<?= $lang->path($sourceUrl) ?>"><?= $lang->value('Případová studie a evidence', 'Case study and evidence') ?> <span aria-hidden="true">↗</span></a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
        <?php if ($index === 1): ?></details><?php endif; ?>
    <?php endforeach; ?>
</section>
</div>

<section class="shell section" aria-labelledby="now-history-title" data-now-activity>
  <div class="section-heading"><div><h2 id="now-history-title"><?= $lang->value('Historie příspěvků', 'Contribution history') ?></h2><p><?= $lang->value('Seřazeno podle poslední zaznamenané události. Rozpracované příspěvky mají datum založení, sloučené a uzavřené datum dokončení, pokud je k dispozici.', 'Sorted by the last recorded event. Open contributions show when they were created. Merged and closed contributions show when that happened, where the date is available.') ?></p></div><a class="inline-link" href="<?= $lang->path('/open-source') ?>"><?= $lang->value('Všechny příspěvky', 'All contributions') ?> →</a></div>
  <div data-activity-browser>
    <div class="filter-bar"><label class="search-field"><span class="visually-hidden"><?= $lang->value('Hledat v historii', 'Search history') ?></span><span aria-hidden="true">⌕</span><input type="search" data-search placeholder="<?= $lang->value('Hledat repozitář nebo změnu…', 'Search repository or change…') ?>"></label><select data-year aria-label="<?= $lang->value('Filtrovat historii podle roku', 'Filter history by year') ?>"><option value="all"><?= $lang->value('Všechny roky', 'All years') ?> · <?= count($activity) ?></option><?php foreach ($activityYears as $year => $count): ?><option value="<?= Html::escape($year) ?>"><?= Html::escape($year) ?> · <?= $count ?></option><?php endforeach; ?></select><select data-state aria-label="<?= $lang->value('Filtrovat podle stavu', 'Filter by status') ?>"><option value="all"><?= $lang->value('Všechny stavy', 'All statuses') ?></option><?php foreach ($stateLabels as $key => $label): ?><option value="<?= Html::escape($key) ?>"><?= Html::escape($label) ?></option><?php endforeach; ?></select><select data-kind aria-label="<?= $lang->value('Filtrovat podle typu', 'Filter by type') ?>"><option value="all"><?= $lang->value('PR i Issues', 'PRs and issues') ?></option><option value="pr">Pull Requests</option><option value="issue">Issues</option></select><button class="btn btn-ghost btn-sm" type="button" data-filter-reset hidden><?= $lang->value('Vymazat filtry', 'Clear filters') ?></button><span class="filter-count" data-count role="status" aria-live="polite" aria-atomic="true"><?= count($activity) ?> <?= $lang->value('záznamů', 'records') ?></span></div>
    <div class="activity-list">
    <?php foreach ($activity as $item): $url = Html::safeUrl((string)($item['source_url'] ?? '')); $state = (string)($item['state'] ?? ''); $kind = (string)($item['kind'] ?? ''); $stateLabel = match ($state) { 'completed' => $lang->value('Dokončeno', 'Completed'), 'merged' => $lang->value('Sloučeno', 'Merged'), 'open' => $lang->value('Otevřeno', 'Open'), 'closed' => $lang->value('Uzavřeno', 'Closed'), default => $stateLabels[$state] ?? $state }; $statusClass = $state; $activityTitle = (string)($item['title'] ?? ''); $repository = (string)($item['repository'] ?? ''); $eventAt = $item['event_date']; $eventLabel = match ($item['event_type']) { 'merged_at' => $lang->value('Sloučeno', 'Merged'), 'closed_at' => $state === 'completed' ? $lang->value('Dokončeno', 'Completed') : $lang->value('Uzavřeno', 'Closed'), default => $lang->value('Založeno', 'Created') }; ?>
      <article class="activity-item" data-activity data-year="<?= Html::escape(substr($eventAt, 0, 4)) ?>" data-state="<?= Html::escape($state) ?>" data-kind="<?= Html::escape($kind) ?>" data-search-text="<?= Html::escape(strtolower($repository . ' ' . $activityTitle)) ?>"><span class="activity-icon" aria-hidden="true"><?= $kind === 'issue' ? '#' : '↳' ?></span><div class="activity-body"><div class="activity-repo"><?= Html::escape($repository) ?> <span>· <?= Html::escape($kind) ?></span></div><strong><?= Html::escape($activityTitle) ?></strong></div><div class="activity-side"><span class="status <?= Html::escape($statusClass) ?>"><?= Html::escape($stateLabel) ?></span><span class="activity-dates"><span><?= $eventLabel ?> <time datetime="<?= Html::escape(substr($eventAt, 0, 10)) ?>"><?= Html::escape(substr($eventAt, 0, 10)) ?></time></span></span></div><a class="activity-arrow" href="<?= Html::escape($url) ?>" aria-label="<?= $lang->value('Otevřít původní záznam', 'Open original record') ?>" target="_blank" rel="noopener noreferrer">↗</a></article>
    <?php endforeach; ?>
    </div><p class="empty-state<?= $activity === [] ? '' : ' hidden' ?>" data-empty><?= $githubUnavailable ? $lang->value('Historie je skrytá, protože její zdroj není dostupný.', 'History is hidden because its source is unavailable.') : $lang->value('Pro tento filtr tu nic není.', 'No records match this filter.') ?></p>
  </div>
</section>
