<?php
declare(strict_types=1);

namespace App\Domain;

use App\Support\Language;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use InvalidArgumentException;

/** Strict RSS 2.0 export of already-public engineering notes. No private drafts. */
final class JournalFeed
{
    private function __construct()
    {
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
        $xml .= '<description>' . self::escape($language->value('Ověřitelné technické zápisky.', 'Traceable engineering notes.')) . '</description>';
        $xml .= '<language>' . $language->code . '</language>';
        foreach (array_slice($entries, 0, 30) as $record) {
            $title = $record['title_' . $language->code] ?? null;
            $body = $record['body_' . $language->code] ?? null;
            $published = $record['published_at'] ?? null;
            if (!is_string($title) || !is_string($body) || !is_string($published)) {
                throw new InvalidArgumentException('Incomplete public journal record');
            }
            try {
                $date = (new DateTimeImmutable($published, new DateTimeZone('UTC')))
                    ->setTimezone(new DateTimeZone('UTC'))->format(DATE_RSS);
            } catch (Exception $exception) {
                throw new InvalidArgumentException('Invalid public journal timestamp', 0, $exception);
            }
            $link = $base . '/journal';
            $guid = $link . '#entry-' . hash('sha256', $published . $title . $body);
            $xml .= '<item><title>' . self::escape($title) . '</title>';
            $xml .= '<link>' . self::escape($link) . '</link>';
            $xml .= '<guid isPermaLink="false">' . self::escape($guid) . '</guid>';
            $xml .= '<description>' . self::escape($body) . '</description>';
            $xml .= '<pubDate>' . self::escape($date) . '</pubDate></item>';
        }

        return $xml . '</channel></rss>';
    }

    private static function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
