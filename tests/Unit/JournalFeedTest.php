<?php
declare(strict_types=1);
namespace App\Test\Unit;
use App\Domain\JournalFeed;
use App\Support\Language;
use PHPUnit\Framework\TestCase;
final class JournalFeedTest extends TestCase
{
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
