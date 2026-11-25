<?php
declare(strict_types=1);

use App\Support\Html;
use App\Support\Language;

/**
 * Shared GitHub provenance notice used consistently across home, open-source, and now views.
 *
 * @var Language $lang
 * @var array{
 *     source:string,
 *     last_sync:?string,
 *     age_hours?:?int,
 *     is_stale?:bool,
 *     snapshot_date?:?string,
 *     is_expired?:bool
 * } $provenance
 */
$source = $provenance['source'];
$lastSync = $provenance['last_sync'] ?? null;
$ageHours = $provenance['age_hours'] ?? null;
$isStale = (bool)($provenance['is_stale'] ?? false);
$isExpired = (bool)($provenance['is_expired'] ?? false);
$snapshotDate = $provenance['snapshot_date'] ?? null;

$badgeClass = match (true) {
    $source === 'unavailable' => 'provenance-unavailable',
    $source === 'github-public-api' && ($isStale || $isExpired) => 'provenance-stale',
    $source === 'github-public-api' => 'provenance-fresh',
    default => 'provenance-snapshot',
};

$badgeLabel = match (true) {
    $source === 'unavailable' => $lang->value('DATA NEDOSTUPNÁ', 'DATA UNAVAILABLE'),
    $source === 'github-public-api' && $isExpired => $lang->value('EXPIRACE IMPORTU', 'EXPIRED IMPORT'),
    $source === 'github-public-api' && $isStale => $lang->value('ZASTARALÝ IMPORT GITHUBU', 'STALE GITHUB IMPORT'),
    $source === 'github-public-api' => $lang->value('VEŘEJNÝ IMPORT GITHUBU', 'PUBLIC GITHUB IMPORT'),
    default => $lang->value('RUČNĚ OVĚŘENÝ SNÍMEK', 'REVIEWED SNAPSHOT'),
};

$isoDatetime = null;
if (is_string($lastSync) && $lastSync !== '') {
    $isoDatetime = str_replace(' ', 'T', str_replace(' UTC', '', $lastSync)) . 'Z';
} elseif (is_string($snapshotDate) && $snapshotDate !== '') {
    $isoDatetime = $snapshotDate;
}
?>
<div class="provenance-notice <?= Html::escape($badgeClass) ?>">
    <span class="provenance-badge"><?= Html::escape($badgeLabel) ?></span>
    <span class="provenance-detail">
        <?php if ($source === 'unavailable'): ?>
            <?= $lang->value('Údaje z GitHubu jsou dočasně nedostupné; starší záznamy při výpadku nezobrazujeme.', 'GitHub data is temporarily unavailable; historical records stay hidden during an outage.') ?>
        <?php elseif ($source === 'github-public-api' && $isExpired): ?>
            <?= $lang->value('Dynamický import překročil povolené stáří a byl automaticky skryt.', 'Dynamic import exceeded maximum public age and was hidden automatically.') ?>
            <?php if ($lastSync !== null && $isoDatetime !== null): ?>
                <time datetime="<?= Html::escape($isoDatetime) ?>"><?= Html::escape($lastSync) ?></time>
            <?php endif; ?>
        <?php elseif ($source === 'github-public-api' && $isStale): ?>
            <?= $lang->value('Importovaný snímek je starší než 48 hodin:', 'Imported snapshot is older than 48 hours:') ?>
            <?php if ($lastSync !== null && $isoDatetime !== null): ?>
                <time datetime="<?= Html::escape($isoDatetime) ?>"><?= Html::escape($lastSync) ?></time>
            <?php endif; ?>
            <?php if (is_int($ageHours)): ?>
                (<?= $lang->value('před ' . $ageHours . ' h · zastaralé', $ageHours . 'h ago · stale') ?>)
            <?php endif; ?>
        <?php elseif ($source === 'github-public-api'): ?>
            <?= $lang->value('Poslední úspěšný veřejný import:', 'Latest successful public import:') ?>
            <?php if ($lastSync !== null && $isoDatetime !== null): ?>
                <time datetime="<?= Html::escape($isoDatetime) ?>"><?= Html::escape($lastSync) ?></time>
            <?php endif; ?>
            <?php if (is_int($ageHours)): ?>
                (<?= $lang->value('před ' . $ageHours . ' h · čerstvé', $ageHours . 'h ago · fresh') ?>)
            <?php endif; ?>
        <?php else: ?>
            <?= $lang->value('Kurátorovaný statický přehled pořízený ke dni:', 'Curated static snapshot captured on:') ?>
            <?php if ($snapshotDate !== null && $isoDatetime !== null): ?>
                <time datetime="<?= Html::escape($isoDatetime) ?>"><?= Html::escape($snapshotDate) ?></time>
            <?php endif; ?>
        <?php endif; ?>
    </span>
</div>
