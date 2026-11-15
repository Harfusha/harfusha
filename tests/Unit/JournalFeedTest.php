<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\JournalFeed;
use App\Support\Language;
use PHPUnit\Framework\TestCase;

final class JournalFeedTest extends TestCase
{
    public function testJournalPermalinksAreStableAcrossLanguagesAndBodyEdits(): void
    {
        $entry = [
            'published_at' => '2026-11-12 12:00:00',
            'title_cs' => 'Český nadpis',
            'title_en' => 'English heading',
            'body_cs' => 'Český text',
            'body_en' => 'English body',
        ];
        $id = JournalFeed::entryId($entry);
        self::assertMatchesRegularExpression('/^entry-[a-f0-9]{20}$/D', $id);
        self::assertSame($id, JournalFeed::entryId([...$entry, 'body_en' => 'Edited body']));
        self::assertSame('entry-db-42', JournalFeed::entryId([...$entry, 'id' => 42]));

        foreach (['cs', 'en'] as $code) {
            $link = 'https://portfolio.example/' . $code . '/journal#' . $id;
            $xml = JournalFeed::render('https://portfolio.example', new Language($code), [$entry]);
            self::assertStringContainsString('<link>' . $link . '</link>', $xml);
            self::assertStringContainsString('<guid isPermaLink="true">' . $link . '</guid>', $xml);
        }
    }

    public function testPublicationDateFormatsAreStrictAndUtc(): void
    {
        $entry = ['title_en' => 'Note', 'body_en' => 'Body'];
        foreach (
            ['2024-02-29', '2026-11-12 12:00:00', '2026-11-12T12:00:00Z'] as $when
        ) {
            $rss = JournalFeed::render(
                'https://portfolio.example',
                new Language('en'),
                [[...$entry, 'published_at' => $when]],
            );
            self::assertStringContainsString('<pubDate>', $rss);
            self::assertStringContainsString(' +0000</pubDate>', $rss);
        }
    }

    public function testImpossibleDatesCannotBeSilentlyNormalizedInRss(): void
    {
        $entry = ['title_en' => 'Note', 'body_en' => 'Body'];
        foreach (
            ['tomorrow', '2026-02-30', '2026-11-12 24:00:00', '2026-11-12T25:00:00Z'] as $when
        ) {
            try {
                JournalFeed::render(
                    'https://portfolio.example',
                    new Language('en'),
                    [[...$entry, 'published_at' => $when]],
                );
                self::fail('Noncanonical date accepted in public RSS');
            } catch (\InvalidArgumentException) {
                // No unchecked dates may reach an RSS client.
            }
        }
    }

    public function testXmlEscapesUserTextAndPreservesDates(): void
    {
        $feed = JournalFeed::render('https://portfolio.example', new Language('en'), [[
            'title_en' => 'A & B < C', 'body_en' => 'Safe <script>text</script>',
            'published_at' => '2026-11-12 12:00:00',
        ]]);
        self::assertStringContainsString('A &amp; B &lt; C', $feed);
        self::assertStringContainsString('Safe &lt;script&gt;', $feed);
        self::assertStringNotContainsString('<script>', $feed);
    }
}
