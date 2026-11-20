<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\PublicSitemap;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PublicSitemapTest extends TestCase
{
    public function testAllLanguagesAndAlternatesArePresent(): void
    {
        $xml = PublicSitemap::render('https://portfolio.example', ['', 'projects', 'projects/local-ai-hub']);
        self::assertSame(6, substr_count($xml, '<url>'));
        self::assertStringContainsString('hreflang="cs"', $xml);
        self::assertStringContainsString('hreflang="en"', $xml);
        self::assertStringContainsString('hreflang="x-default"', $xml);
        self::assertStringContainsString('/cs/projects/local-ai-hub', $xml);
    }

    public function testRoutesCannotContainTraversalOrDuplicates(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PublicSitemap::render('https://portfolio.example', ['', '../private']);
    }

    public function testDuplicateRoutesFailClosed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PublicSitemap::render('https://portfolio.example', ['projects', 'projects']);
    }

    public function testIncludesLastmodWhenProvided(): void
    {
        $xml = PublicSitemap::render(
            'https://portfolio.example',
            ['projects', 'journal'],
            ['projects' => '2026-11-20', 'journal' => '2026-11-19'],
        );
        self::assertStringContainsString('<lastmod>2026-11-20</lastmod>', $xml);
        self::assertStringContainsString('<lastmod>2026-11-19</lastmod>', $xml);
    }

    public function testRejectsInvalidLastmodFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PublicSitemap::render('https://portfolio.example', ['projects'], ['projects' => 'invalid-date']);
    }
}
