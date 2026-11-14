<?php
declare(strict_types=1);

namespace App\Domain;

use App\Support\Language;

final readonly class Project
{
    /**
     * @param list<string> $stack
     * @param list<string> $features
     */
    public function __construct(
        public string $slug,
        public string $name,
        public string $category,
        public string $status,
        public bool $featured,
        public bool $private,
        public string $repo,
        public string $demo,
        public string $summaryCs,
        public string $summaryEn,
        public string $problemCs,
        public string $problemEn,
        public string $solutionCs,
        public string $solutionEn,
        public array $stack,
        public array $features,
        public string $year,
    ) {
        if (ProjectStatus::tryFrom($status) === null) {
            throw new \InvalidArgumentException('Invalid project status');
        }
        if (preg_match('/^[a-z0-9-]+$/D', $slug) !== 1) {
            throw new \InvalidArgumentException('Invalid slug');
        }
    }

    /** @param array<string,mixed> $value */
    public static function fromArray(array $value): self
    {
        return new self(
            self::str($value, 'slug'), self::str($value, 'name'), self::str($value, 'category'),
            self::str($value, 'status'), (bool)($value['featured'] ?? false), (bool)($value['private'] ?? true),
            self::str($value, 'repo'), self::str($value, 'demo'), self::str($value, 'summary_cs'),
            self::str($value, 'summary_en'), self::str($value, 'problem_cs'), self::str($value, 'problem_en'),
            self::str($value, 'solution_cs'), self::str($value, 'solution_en'),
            self::strings($value, 'stack'), self::strings($value, 'features'), self::str($value, 'year'),
        );
    }

    /** @param array<string,mixed> $values */
    private static function str(array $values, string $key): string
    {
        return is_string($values[$key] ?? null) ? $values[$key] : '';
    }

    /**
     * @param array<string,mixed> $values
     * @return list<string>
     */
    private static function strings(array $values, string $key): array
    {
        $input = $values[$key] ?? [];
        if (!is_array($input)) {
            return [];
        }
        $strings = [];
        foreach ($input as $item) {
            if (is_string($item)) {
                $strings[] = $item;
            }
        }
        return $strings;
    }

    public function summary(Language $lang): string
    {
        return $lang->value($this->summaryCs, $this->summaryEn);
    }

    public function problem(Language $lang): string
    {
        return $lang->value($this->problemCs, $this->problemEn);
    }

    public function solution(Language $lang): string
    {
        return $lang->value($this->solutionCs, $this->solutionEn);
    }
}
