<?php
declare(strict_types=1);
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
    'open' => $lang->value('Otevřeno', 'Open'),
    'closed' => $lang->value('Dokončeno', 'Completed'),
];
$activityYears = [];
foreach ($activity as $item) {
    $year = substr((string)($item['published_at'] ?? ''), 0, 4);
    if (preg_match('/^\\d{4}$/D', $year) === 1) {
        $activityYears[$year] = ($activityYears[$year] ?? 0) + 1;
    }
}
krsort($activityYears, SORT_NUMERIC);
?>
<section class="page-hero shell">
  <span class="eyebrow"><?= $lang->value('PRÁCE · DNES A DŘÍV', 'WORK · NOW AND THEN') ?></span>
  <h1><?= $lang->value('Co jsem měnil naposledy — a co tomu předcházelo.', 'The latest changes, and what led up to them.') ?></h1>
  <p><?= $lang->value('Nahoře jsou poslední veřejné změny repozitářů. Níž je delší stopa: zápisky z práce a příspěvky, které vedou na původní záznam. „Právě teď“ neznamená živé sledování počítače.', 'The top shows the latest public repository changes. Below is the longer trail: notes from the work and contributions linked to their original records. “Now” does not mean a live feed from my computer.') ?></p>
  <div class="page-meta"><?= $githubSynced ? $lang->value('VEŘEJNÝ IMPORT GITHUBU', 'PUBLIC GITHUB IMPORT') : ($githubUnavailable ? $lang->value('DATA NEDOSTUPNÁ', 'DATA UNAVAILABLE') : $lang->value('RUČNĚ OVĚŘENÝ SNÍMEK', 'REVIEWED SNAPSHOT')) ?> <span>·</span>
  <?php if ($provenance['last_sync'] !== null): ?><?= $lang->value('Poslední úspěšný import: ', 'Last successful import: ') ?><?= Html::escape($provenance['last_sync']) ?>
  <?php elseif ($githubUnavailable): ?><?= $lang->value('Starší záznamy při výpadku nezobrazujeme.', 'Historical records stay hidden while the source is unavailable.') ?>
  <?php else: ?><?= $lang->value('Pravidelný import není potvrzen.', 'A recurring import is not confirmed.') ?><?php endif; ?></div>
</section>

<section class="shell section" aria-labelledby="now-repositories-title">
  <div class="section-heading"><div><span class="eyebrow"><?= $lang->value('POSLEDNÍ PUSH', 'LATEST PUSHES') ?></span><h2 id="now-repositories-title"><?= $lang->value('Které repozitáře se změnily naposledy', 'Repositories with the latest changes') ?></h2><p><?= $lang->value('Push čas říká, kdy se změnil repozitář. Neříká, že v něm právě teď pracuji.', 'A push timestamp says when a repository changed. It does not say I am working on it right now.') ?></p></div><a class="inline-link" href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer">GitHub ↗</a></div>
  <?php if ($recentRepos !== []): ?><div class="activity-list">
  <?php foreach ($recentRepos as $repo): ?><article class="activity-item"><span class="activity-icon" aria-hidden="true">↳</span><div class="activity-body"><div class="activity-repo"><?= Html::escape($repo['repository']) ?></div><?php if ($repo['description'] !== '' && $repo['description'] !== $repo['repository']): ?><strong><?= Html::escape($repo['description']) ?></strong><?php endif; ?></div><div class="activity-side"><time><?= Html::escape(substr($repo['pushed_at'],0,16)) ?> UTC</time></div><a class="activity-arrow" href="<?= Html::escape(Html::safeUrl($repo['repo_url'])) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= $lang->value('Otevřít repozitář na GitHubu', 'Open repository on GitHub') ?>">↗</a></article><?php endforeach; ?>
  </div><?php else: ?><p class="empty-state"><?= $lang->value('Čerstvé push údaje teď nejsou dostupné.', 'Fresh push data is not available right now.') ?></p><?php endif; ?>
</section>

<section class="shell section now-build-history" data-now-notes aria-labelledby="now-build-title">
  <div class="section-heading"><div><span class="eyebrow"><?= $lang->value('ZÁPISKY Z DÍLNY', 'WORKSHOP NOTES') ?></span><h2 id="now-build-title"><?= $lang->value('Co se v projektech skutečně změnilo', 'What actually changed in the projects') ?></h2><p><?= $lang->value('Krátké datované poznámky k rozhodnutím a změnám. Každá vede na podklad, pokud je veřejný.', 'Short, dated notes on decisions and changes. Each links to its source when one is public.') ?></p></div><a class="inline-link" href="<?= $lang->path('/journal') ?>"><?= $lang->value('Otevřít celý vývojový deník', 'Read the full development journal') ?> →</a></div>
  <ol class="now-build-list">
    <?php foreach ($journalEntries as $entry): $publishedAt = (string)($entry['published_at'] ?? ''); $sourceUrl = Html::safeUrl((string)($entry['source_url'] ?? '')); ?>
      <li class="now-build-entry">
        <time datetime="<?= Html::escape(substr($publishedAt, 0, 10)) ?>"><?= Html::escape(substr($publishedAt, 0, 10)) ?></time>
        <div><span class="eyebrow"><?= Html::escape(strtoupper((string)($entry['category'] ?? ''))) ?></span><h3><?= Html::escape($lang->value((string)$entry['title_cs'], (string)$entry['title_en'])) ?></h3><p><?= Html::escape($lang->value((string)$entry['body_cs'], (string)$entry['body_en'])) ?></p>
          <?php if ($sourceUrl !== ''): ?><a class="inline-link" href="<?= Html::escape($sourceUrl) ?>" target="_blank" rel="noopener noreferrer"><?= $lang->value('Zdroj', 'Source') ?> ↗</a><?php endif; ?>
        </div>
      </li>
    <?php endforeach; ?>
  </ol>
</section>

<section class="shell section" aria-labelledby="now-history-title" data-now-activity>
  <div class="section-heading"><div><span class="eyebrow"><?= $lang->value('DOLOŽENÁ STOPA', 'SOURCE-BACKED TRAIL') ?></span><h2 id="now-history-title"><?= $lang->value('Historie veřejné práce', 'History of public work') ?></h2><p><?= $lang->value('Sloučené změny a dokončené issues. Každá položka vede na původní diskusi; „dokončeno“ tu znamená uzavřené jako hotové, ne neúspěch.', 'Merged changes and completed issues. Each item links to its original discussion; “completed” means finished, not failed.') ?></p></div><a class="inline-link" href="<?= $lang->path('/open-source') ?>"><?= $lang->value('Všechny příspěvky', 'All contributions') ?> →</a></div>
  <div data-activity-browser>
    <div class="filter-bar"><label class="search-field"><span class="visually-hidden"><?= $lang->value('Hledat v historii', 'Search history') ?></span><span aria-hidden="true">⌕</span><input type="search" data-search placeholder="<?= $lang->value('Hledat repozitář nebo změnu…', 'Search repository or change…') ?>"></label><select data-year aria-label="<?= $lang->value('Filtrovat historii podle roku', 'Filter history by year') ?>"><option value="all"><?= $lang->value('Všechny roky', 'All years') ?> · <?= count($activity) ?></option><?php foreach ($activityYears as $year => $count): ?><option value="<?= Html::escape($year) ?>"><?= Html::escape($year) ?> · <?= $count ?></option><?php endforeach; ?></select><select data-state aria-label="<?= $lang->value('Filtrovat podle stavu', 'Filter by status') ?>"><option value="all"><?= $lang->value('Všechny stavy', 'All statuses') ?></option><?php foreach ($stateLabels as $key => $label): ?><option value="<?= Html::escape($key) ?>"><?= Html::escape($label) ?></option><?php endforeach; ?></select><select data-kind aria-label="<?= $lang->value('Filtrovat podle typu', 'Filter by type') ?>"><option value="all"><?= $lang->value('PR i Issues', 'PRs and issues') ?></option><option value="pr">Pull Requests</option><option value="issue">Issues</option></select><span class="filter-count" data-count role="status" aria-live="polite" aria-atomic="true"><?= count($activity) ?> <?= $lang->value('záznamů', 'records') ?></span></div>
    <div class="activity-list">
    <?php foreach ($activity as $item): $url = Html::safeUrl((string)($item['source_url'] ?? '')); $state = (string)($item['state'] ?? ''); $kind = (string)($item['kind'] ?? ''); $stateLabel = $stateLabels[$state] ?? $state; $statusClass = $state === 'closed' && $kind === 'issue' ? 'completed' : $state; $title = (string)($item['title'] ?? ''); $repository = (string)($item['repository'] ?? ''); $publishedAt = (string)($item['published_at'] ?? ''); ?>
      <article class="activity-item" data-activity data-year="<?= Html::escape(substr($publishedAt, 0, 4)) ?>" data-state="<?= Html::escape($state) ?>" data-kind="<?= Html::escape($kind) ?>" data-search-text="<?= Html::escape(strtolower($repository . ' ' . $title)) ?>"><span class="activity-icon" aria-hidden="true"><?= $kind === 'issue' ? '#' : '↳' ?></span><div class="activity-body"><div class="activity-repo"><?= Html::escape($repository) ?> <span>· <?= Html::escape($kind) ?></span></div><strong><?= Html::escape($title) ?></strong></div><div class="activity-side"><span class="status <?= Html::escape($statusClass) ?>"><?= Html::escape($stateLabel) ?></span><time datetime="<?= Html::escape(substr($publishedAt, 0, 10)) ?>"><?= Html::escape(substr($publishedAt, 0, 10)) ?></time></div><a class="activity-arrow" href="<?= Html::escape($url) ?>" aria-label="<?= $lang->value('Otevřít původní záznam', 'Open original record') ?>" target="_blank" rel="noopener noreferrer">↗</a></article>
    <?php endforeach; ?>
    </div><p class="empty-state<?= $activity === [] ? '' : ' hidden' ?>" data-empty><?= $githubUnavailable ? $lang->value('Historie je skrytá, protože její zdroj není dostupný.', 'History is hidden because its source is unavailable.') : $lang->value('Pro tento filtr tu nic není.', 'No records match this filter.') ?></p>
  </div>
</section>
