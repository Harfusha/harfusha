<?php
declare(strict_types=1);

namespace App\Support;

final readonly class Language
{
    public function __construct(public string $code)
    {
        if (!in_array($code, ['cs', 'en'], true)) {
            throw new \InvalidArgumentException('Unsupported language');
        }
    }

    public function value(string $cs, string $en): string
    {
        return $this->code === 'en' ? $en : $cs;
    }

    public function path(string $route = '/'): string
    {
        return Html::path($this->code, $route);
    }
}
