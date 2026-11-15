<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\EditorialInput;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EditorialInputTest extends TestCase
{
    public function testProjectInputIsTypedAndNormalized(): void
    {
        $input = EditorialInput::project([
            'slug' => 'local-ai-hub', 'status' => 'active',
            'summary_cs' => ' Ahoj ', 'summary_en' => 'Hello',
            'problem_cs' => '', 'problem_en' => '',
            'solution_cs' => '', 'solution_en' => '',
        ]);
        self::assertSame('Ahoj', $input['summary_cs']);
        self::assertSame('local-ai-hub', $input['slug']);
    }

    public function testNestedPayloadFailsClosed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        EditorialInput::journal(['category' => ['Injected']]);
    }

    public function testOversizedPayloadFailsClosed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        EditorialInput::project([
            'slug' => 'local-ai-hub', 'status' => 'active',
            'summary_cs' => str_repeat('x', 8001), 'summary_en' => 'Valid',
            'problem_cs' => '', 'problem_en' => '', 'solution_cs' => '', 'solution_en' => '',
        ]);
    }

    public function testJournalAcceptsUnicodeAndRejectsCategoryMetacharacters(): void
    {
        $input = EditorialInput::journal([
            'category' => 'Development', 'title_cs' => 'Český zápis',
            'title_en' => 'English note', 'body_cs' => 'Obsah', 'body_en' => 'Body',
        ]);
        self::assertSame('Český zápis', $input['title_cs']);
        $this->expectException(InvalidArgumentException::class);
        EditorialInput::journal([
            'category' => '<script>', 'title_cs' => 'A', 'title_en' => 'B',
            'body_cs' => 'C', 'body_en' => 'D',
        ]);
    }

    public function testJournalPublicationDecisionMustBeExplicit(): void
    {
        self::assertTrue(EditorialInput::isPublic('public'));
        self::assertFalse(EditorialInput::isPublic('draft'));
        foreach ([null, '', 'PUBLIC', 'published', false, true, [], ' private '] as $value) {
            try {
                EditorialInput::isPublic($value);
                self::fail('Invalid publication decision accepted');
            } catch (InvalidArgumentException) {
                // Invalid editor payloads cannot silently alter publication state.
            }
        }
    }

    public function testPositiveIdGuardsOverflow(): void
    {
        self::assertSame(42, EditorialInput::positiveId('42'));
        $this->expectException(InvalidArgumentException::class);
        EditorialInput::positiveId('9223372036854775808');
    }
}
