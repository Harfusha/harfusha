<?php
declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;

final readonly class Language
{
    /**
     * Initialize validated domain state and required dependencies.
     */
    public function __construct(public string $code)
    {
        if (!in_array($code, ['cs', 'en'], true)) {
            throw new InvalidArgumentException('Unsupported language');
        }
    }

    /**
     * Return the requested localized value.
     */
    public function value(string $cs, string $en): string
    {
        return $this->code === 'en' ? $en : $cs;
    }

    /**
     * Build a safe URL path for the selected locale.
     */
    public function path(string $route = '/'): string
    {
        return Html::path($this->code, $route);
    }
}
