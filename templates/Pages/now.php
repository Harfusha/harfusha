<?php
declare(strict_types=1);
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var list<array{repository:string,description:string,repo_url:string,pushed_at:string}> $recentRepos */
/** @var list<array<string,mixed>> $journal */
/** @var array{source:string,last_sync:?string} $provenance */
$githubSynced = $provenance['source'] === 'github-public-api';
?>
<section class="page-hero shell">
  <span class="eyebrow">NOW / WORK IN PROGRESS / EVIDENCE</span>
  <h1><?= $lang->value('Co právě vzniká.','What I am building now.') ?></h1>
  <p><?= $lang->value('Žádný falešný stav „online“ ani domnělý živý přenos. Tohle je veřejná historie GitHubu a ručně schvalované poznámky z vývoje.','No fabricated online indicators or fake live stream. This page shows public GitHub history and manually reviewed engineering notes.') ?></p>
  <div class="page-meta"><?= $githubSynced ? 'PUBLIC GITHUB API' : 'REVIEWED SNAPSHOT' ?> <span>·</span>
  <?php if ($provenance['last_sync'] !== null): ?><?= $lang->value('Poslední úspěšný import: ', 'Last successful import: ') ?><?= Html::escape($provenance['last_sync']) ?>
  <?php else: ?><?= $lang->value('Automatická synchronizace zatím není potvrzena','Automated synchronization has not been confirmed') ?><?php endif; ?></div>
</section>
<section class="shell section">
  <div class="section-heading"><div><span class="eyebrow">PUBLIC SOURCE</span><h2><?= $lang->value('Poslední aktualizace repozitářů','Latest repository updates') ?></h2><p><?= $lang->value('Čas posledního push, nikoliv důkaz že teď někdo pracuje.','Last pushed timestamps, not proof of current activity.') ?></p></div><a class="inline-link" href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer">GitHub ↗</a></div>
  <div class="activity-list">
  <?php foreach ($recentRepos as $repo): ?>
    <article class="activity-item"><span class="activity-icon" aria-hidden="true">↳</span><div class="activity-body"><div class="activity-repo"><?= Html::escape($repo['repository']) ?></div><strong><?= Html::escape($repo['description'] !== '' ? $repo['description'] : $repo['repository']) ?></strong></div><div class="activity-side"><time><?= Html::escape(substr($repo['pushed_at'],0,16)) ?> UTC</time></div><a class="activity-arrow" href="<?= Html::escape(Html::safeUrl($repo['repo_url'])) ?>" target="_blank" rel="noopener noreferrer" aria-label="GitHub">↗</a></article>
  <?php endforeach; ?>
  </div>
</section>
<section class="shell section">
  <div class="section-heading"><div><span class="eyebrow">WORKSHOP NOTES</span><h2><?= $lang->value('Ověřitelné vývojové zápisky','Traceable development notes') ?></h2></div><a class="inline-link" href="<?= $lang->path('/journal') ?>"><?= $lang->value('Celý deník','Full journal') ?> ↗</a></div>
  <div class="journal-timeline">
  <?php foreach ($journal as $entry): ?>
    <article class="timeline-item"><div class="timeline-marker"></div><time><?= Html::escape(substr((string)$entry['published_at'],0,10)) ?></time><div class="timeline-copy"><span class="category"><?= Html::escape((string)$entry['category']) ?></span><h2><?= Html::escape((string)$entry['title_'.$lang->code]) ?></h2><p><?= Html::escape((string)$entry['body_'.$lang->code]) ?></p><?php $source = Html::safeUrl((string)($entry['source_url'] ?? '')); if ($source !== '#'): ?><a class="inline-link" href="<?= Html::escape($source) ?>" target="_blank" rel="noopener noreferrer">SOURCE ↗</a><?php endif; ?></div></article>
  <?php endforeach; ?>
  </div>
</section>
