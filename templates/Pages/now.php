<?php
declare(strict_types=1);
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var list<array{repository:string,description:string,repo_url:string,pushed_at:string}> $recentRepos */
/** @var array{source:string,last_sync:?string} $provenance */
$githubSynced = $provenance['source'] === 'github-public-api';
$githubUnavailable = $provenance['source'] === 'unavailable';
?>
<section class="page-hero shell">
  <span class="eyebrow"><?= $lang->value('AKTUÁLNÍ ZÁZNAMY / GITHUB','RECENT RECORDS / GITHUB') ?></span>
  <h1><?= $lang->value('Co se teď mění.','What is changing now.') ?></h1>
  <p><?= $lang->value('Nedávné veřejné změny repozitářů. Osobní poznámky a kontext patří do vývojového deníku.','Recent public repository changes. Personal notes and context belong in the development journal.') ?></p>
  <div class="page-meta"><?= $githubSynced ? $lang->value('VEŘEJNÉ GITHUB API', 'PUBLIC GITHUB API') : ($githubUnavailable ? $lang->value('DATA NEDOSTUPNÁ', 'DATA UNAVAILABLE') : $lang->value('HISTORICKÝ SNÍMEK', 'REVIEWED SNAPSHOT')) ?> <span>·</span>
  <?php if ($provenance['last_sync'] !== null): ?><?= $lang->value('Poslední úspěšný import: ', 'Last successful import: ') ?><?= Html::escape($provenance['last_sync']) ?>
  <?php elseif ($githubUnavailable): ?><?= $lang->value('GitHub data nejsou dostupná; staré záznamy nezobrazujeme', 'GitHub data unavailable; historical entries are withheld') ?>
  <?php else: ?><?= $lang->value('Automatická synchronizace zatím není potvrzena','Automated synchronization has not been confirmed') ?><?php endif; ?></div>
</section>
<section class="shell section">
  <div class="section-heading"><div><span class="eyebrow"><?= $lang->value('VEŘEJNÝ ZDROJ', 'PUBLIC SOURCE') ?></span><h2><?= $lang->value('Poslední aktualizace repozitářů','Latest repository updates') ?></h2><p><?= $lang->value('Čas posledního push, nikoliv důkaz že teď někdo pracuje.','Last pushed timestamps, not proof of current activity.') ?></p></div><a class="inline-link" href="https://github.com/Harfusha" target="_blank" rel="noopener noreferrer">GitHub ↗</a></div>
  <div class="activity-list">
  <?php foreach ($recentRepos as $repo): ?>
    <article class="activity-item"><span class="activity-icon" aria-hidden="true">↳</span><div class="activity-body"><div class="activity-repo"><?= Html::escape($repo['repository']) ?></div><?php if ($repo['description'] !== '' && $repo['description'] !== $repo['repository']): ?><strong><?= Html::escape($repo['description']) ?></strong><?php endif; ?></div><div class="activity-side"><time><?= Html::escape(substr($repo['pushed_at'],0,16)) ?> UTC</time></div><a class="activity-arrow" href="<?= Html::escape(Html::safeUrl($repo['repo_url'])) ?>" target="_blank" rel="noopener noreferrer" aria-label="GitHub">↗</a></article>
  <?php endforeach; ?>
  </div>
</section>
<section class="shell section">
