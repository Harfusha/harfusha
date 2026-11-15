<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Public strings must remain valid UTF-8 and valid XML 1.0 text.
 * HTML escaping alone does not remove XML-forbidden control characters.
 */
final class PublicText
{
    /** Prevent instantiation of the stateless validator. */
    private function __construct()
    {
    }

    public static function assertRenderable(string $value): void
    {
        // XML 1.0 permits TAB, LF and CR but not the other C0 controls,
        // or the Unicode sentinel code points U+FFFE and U+FFFF.
        $invalid = preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x{FFFE}\x{FFFF}]/u', $value);
        if ($invalid !== 0) {
            throw new InvalidArgumentException('Text contains invalid Unicode or XML characters');
        }
    }
}
