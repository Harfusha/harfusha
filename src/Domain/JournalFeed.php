<?php
declare(strict_types=1);

namespace App\Domain;

use App\Support\Language;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/** Strict RSS 2.0 export of already-public engineering notes. No private drafts. */
final class JournalFeed
{
    /**
     * Initialize validated domain state and required dependencies.
     */
    private function __construct()
    {
    }

    /**
     * A language-independent, stable anchor for the visible journal entry.
     * Database ids survive title edits; curated entries prefer an immutable
     * `stable_id` when present, falling back to the legacy title+date hash.
     *
     * @param array<string,mixed> $record
     */
    public static function entryId(array $record): string
    {
        $id = $record['id'] ?? null;
        if (is_int($id) && $id > 0) {
            return 'entry-db-' . $id;
        }
        $stableId = $record['stable_id'] ?? null;
        if (is_string($stableId) && preg_match('/^entry-[a-z0-9-]{6,64}$/D', $stableId) === 1) {
            return $stableId;
        }

        return self::legacyTitleHashId($record);
    }

    /**
     * Compute the historical title-based anchor hash so legacy deep links remain resolvable.
     *
     * @param array<string,mixed> $record
     */
    public static function legacyTitleHashId(array $record): string
    {
        $published = $record['published_at'] ?? null;
        $title = $record['title_en'] ?? null;
        if (!is_string($published) || $published === '' || !is_string($title) || $title === '') {
            throw new InvalidArgumentException('Incomplete journal entry identity');
        }

        return 'entry-' . substr(hash('sha256', $published . chr(0) . $title), 0, 20);
    }

    /**
     * Parse a strict UTC publication date into a Unix timestamp for deterministic sorting.
     */
    public static function parseUtcTimestamp(string $value): int
    {
        $utc = new DateTimeZone('UTC');
        foreach (['!Y-m-d H:i:s', '!Y-m-d\TH:i:s\Z', '!Y-m-d'] as $format) {
            $parsed = DateTimeImmutable::createFromFormat($format, $value, $utc);
            $errors = DateTimeImmutable::getLastErrors();
            $noErrors = $errors === false
                || ($errors['warning_count'] === 0 && $errors['error_count'] === 0);
            if ($parsed instanceof DateTimeImmutable && $noErrors) {
                return $parsed->getTimestamp();
            }
        }

        throw new InvalidArgumentException('Invalid journal publication date');
    }

    /** @param list<array<string,mixed>> $entries */
    public static function render(string $origin, Language $language, array $entries): string
    {
        if (preg_match('~^https?://[a-z0-9.-]+(?::[0-9]{1,5})?$~iD', $origin) !== 1) {
            throw new InvalidArgumentException('Invalid RSS origin');
        }
        $base = rtrim($origin, '/') . '/' . $language->code;
        $title = $language->value('Harfusha Lab — Vývojový deník', 'Harfusha Lab — Engineering journal');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0"><channel>';
        $xml .= '<title>' . self::escape($title) . '</title>';
        $xml .= '<link>' . self::escape($base . '/journal') . '</link>';
        $xml .= '<description>'
            . self::escape($language->value('Ověřitelné technické zápisky.', 'Traceable engineering notes.'))
            . '</description>';
        $xml .= '<language>' . $language->code . '</language>';
        foreach (array_slice($entries, 0, 30) as $record) {
            $entryTitle = $record['title_' . $language->code] ?? null;
            $body = $record['body_' . $language->code] ?? null;
            $published = $record['published_at'] ?? null;
            if (!is_string($entryTitle) || !is_string($body) || !is_string($published)) {
                throw new InvalidArgumentException('Incomplete public journal record');
            }
            PublicText::assertRenderable($entryTitle);
            PublicText::assertRenderable($body);
            $date = self::rssDate($published);
            $link = $base . '/journal#' . self::entryId($record);
            $xml .= '<item><title>' . self::escape($entryTitle) . '</title>';
            $xml .= '<link>' . self::escape($link) . '</link>';
            $xml .= '<guid isPermaLink="true">' . self::escape($link) . '</guid>';
            $xml .= '<description>' . self::escape($body) . '</description>';
            $xml .= '<pubDate>' . self::escape($date) . '</pubDate></item>';
        }

        return $xml . '</channel></rss>';
    }

    /**
     * Format reviewed publication dates without DateTime's lenient rollover
     * of impossible dates or relative strings such as "next Tuesday".
     */
    private static function rssDate(string $value): string
    {
        return gmdate(DATE_RSS, self::parseUtcTimestamp($value));
    }

    /**
     * Encode untrusted text for safe inclusion in this output format.
     */
    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
