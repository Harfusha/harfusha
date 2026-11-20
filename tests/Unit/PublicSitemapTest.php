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

    public function testIncludesImagesWhenProvided(): void
    {
        $xml = PublicSitemap::render(
            'https://portfolio.example',
            ['projects/mutaui'],
            [],
            [
                'projects/mutaui' => [
                    ['loc' => '/assets/img/mutaui-overview-cs-light.webp', 'title' => 'MutaUI Overview'],
                ],
            ],
        );
        self::assertStringContainsString('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"', $xml);
        self::assertStringContainsString('<image:image>', $xml);
        self::assertStringContainsString(
            '<image:loc>https://portfolio.example/assets/img/mutaui-overview-cs-light.webp</image:loc>',
            $xml,
        );
        self::assertStringContainsString('<image:title>MutaUI Overview</image:title>', $xml);
    }

    public function testRejectsInvalidImageLocation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PublicSitemap::render(
            'https://portfolio.example',
            ['projects'],
            [],
            ['projects' => [['loc' => '../private.png']]],
        );
    }
}
