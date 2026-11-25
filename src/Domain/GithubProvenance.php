<?php
declare(strict_types=1);

namespace App\Domain;

use App\Support\Language;

/**
 * Typed provenance metadata for public GitHub activity and repository snapshots.
 * Explicitly separates fresh live imports, stale (>48h) imports, expired TTL imports,
 * unavailable storage, and dated manually reviewed fallback snapshots.
 */
final readonly class GithubProvenance
{
    public const int STALE_AFTER_HOURS = 48;
    public const int DEFAULT_MAX_PUBLIC_TTL_HOURS = 720;

    /**
     * @param 'github-public-api'|'reviewed-snapshot'|'unavailable' $source
     */
    public function __construct(
        public string $source,
        public ?string $lastSync,
        public ?int $ageHours,
        public bool $isStale,
        public ?string $snapshotDate = null,
        public bool $isExpired = false,
        public ?int $syncRunId = null,
        public ?string $errorReason = null,
    ) {
    }

    /**
     * Build provenance for an unavailable database or corrupt import state.
     */
    public static function unavailable(string $reason = 'storage-unavailable'): self
    {
        return new self('unavailable', null, null, true, null, false, null, $reason);
    }

    /**
     * Build provenance for the manually reviewed static fallback snapshot.
     */
    public static function reviewedSnapshot(string $snapshotDate = '2026-11-12'): self
    {
        return new self('reviewed-snapshot', null, null, false, $snapshotDate, false, null, null);
    }

    /**
     * Return array representation for legacy/template consumers.
     *
     * @return array{
     *     source:string,
     *     last_sync:?string,
     *     age_hours:?int,
     *     is_stale:bool,
     *     snapshot_date:?string,
     *     is_expired:bool,
     *     sync_run_id:?int,
     *     error_reason:?string
     * }
     */
    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'last_sync' => $this->lastSync,
            'age_hours' => $this->ageHours,
            'is_stale' => $this->isStale,
            'snapshot_date' => $this->snapshotDate,
            'is_expired' => $this->isExpired,
            'sync_run_id' => $this->syncRunId,
            'error_reason' => $this->errorReason,
        ];
    }

    /**
     * Return localized short badge label indicating freshness and source.
     */
    public function badgeLabel(Language $lang): string
    {
        if ($this->source === 'unavailable') {
            return $lang->value('DATA NEDOSTUPNÁ', 'DATA UNAVAILABLE');
        }
        if ($this->source === 'github-public-api') {
            if ($this->isExpired) {
                return $lang->value('EXPIRACE IMPORTU', 'EXPIRED IMPORT');
            }

            return $this->isStale
                ? $lang->value('ZASTARALÝ IMPORT GITHUBU', 'STALE GITHUB IMPORT')
                : $lang->value('VEŘEJNÝ IMPORT GITHUBU', 'PUBLIC GITHUB IMPORT');
        }

        return $lang->value('RUČNĚ OVĚŘENÝ SNÍMEK', 'REVIEWED SNAPSHOT');
    }

    /**
     * Return ISO 8601 timestamp string when available.
     */
    public function isoTimestamp(): ?string
    {
        if ($this->lastSync !== null) {
            $base = str_replace(' UTC', '', $this->lastSync);

            return str_replace(' ', 'T', $base) . 'Z';
        }

        return $this->snapshotDate;
    }
}
