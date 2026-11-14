<?php
declare(strict_types=1);
namespace App\Test\Unit;

use App\Support\Html;
use App\Support\Language;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class LanguageTest extends TestCase
{
    public function testTranslatedValuesAndSafePaths(): void
    {
        self::assertSame('English', (new Language('en'))->value('Česky', 'English'));
        self::assertSame('/cs/projects', Html::path('cs', '/projects'));
        self::assertSame('#', Html::safeUrl('javascript:alert(1)'));
        self::assertStringContainsString('\\u003C', Html::json(['html' => '</script>']));
    }

    public function testInvalidLanguageRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Language('xy');
    }
}
