<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\EditorialInput;
use App\Domain\JournalFeed;
use App\Domain\PublicContentValidator;
use App\Domain\PublicText;
use App\Support\Language;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PublicTextTest extends TestCase
{
    public function testAcceptsMultilingualTextAndLegitimateWhitespace(): void
    {
        PublicText::assertRenderable("Český text\nEnglish\trow\rnewline 😀");
        self::assertTrue(true);
    }

    public function testRejectsInvalidXmlControlsAndMalformedUtf8(): void
    {
        $invalid = ["A\x01B", "A\x0BB", "A\x1FB", "A\xEF\xBF\xBEB", "A\xFFB"];
        foreach ($invalid as $value) {
            try {
                PublicText::assertRenderable($value);
                self::fail('Forbidden Unicode character was accepted');
            } catch (InvalidArgumentException) {
                // Both malformed UTF-8 and XML-forbidden code points fail closed.
            }
        }
    }

    public function testUnrenderableEditorContentIsRejectedBeforePersistence(): void
    {
        $payload = [
            'category' => 'Engineering', 'title_cs' => 'Zápis', 'title_en' => 'Note',
            'body_cs' => "Řídicí znak \x01", 'body_en' => 'Valid body',
        ];
        $this->expectException(InvalidArgumentException::class);
        EditorialInput::journal($payload);
    }

    public function testUnrenderableCuratedContentIsRejectedBeforePublication(): void
    {
        $payload = [[
            'category' => 'Engineering', 'title_cs' => 'Zápis', 'title_en' => 'Note',
            'body_cs' => "Řídicí znak \x01", 'body_en' => 'Valid body',
            'source_url' => '', 'published_at' => '2026-11-12 12:00:00',
        ]];
        $this->expectException(InvalidArgumentException::class);
        PublicContentValidator::validate('journal.json', $payload);
    }

    public function testDirectRssRenderRejectsInvalidXml(): void
    {
        $payload = [[
            'title_cs' => 'Zápis', 'title_en' => 'Note',
            'body_cs' => "Řídicí znak \x01", 'body_en' => 'Valid body',
            'published_at' => '2026-11-12 12:00:00',
        ]];
        $this->expectException(InvalidArgumentException::class);
        JournalFeed::render('https://example.com', new Language('cs'), $payload);
    }
}
