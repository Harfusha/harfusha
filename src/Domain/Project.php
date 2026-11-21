<?php
declare(strict_types=1);

namespace App\Domain;

use App\Support\Language;
use InvalidArgumentException;

final readonly class Project
{
    /**
     * @param list<string> $stack
     * @param list<string> $features
     * @param list<string> $featuresCs
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
        public array $featuresCs = [],
    ) {
        if (ProjectStatus::tryFrom($status) === null) {
            throw new InvalidArgumentException('Invalid project status');
        }
        if (preg_match('/^[a-z0-9-]+$/D', $slug) !== 1) {
            throw new InvalidArgumentException('Invalid slug');
        }
    }

    /** @param array<string,mixed> $value */
    public static function fromArray(array $value): self
    {
        return new self(
            self::str($value, 'slug'),
            self::str($value, 'name'),
            self::str($value, 'category'),
            self::str($value, 'status'),
            (bool)($value['featured'] ?? false),
            (bool)($value['private'] ?? true),
            self::str($value, 'repo'),
            self::str($value, 'demo'),
            self::str($value, 'summary_cs'),
            self::str($value, 'summary_en'),
            self::str($value, 'problem_cs'),
            self::str($value, 'problem_en'),
            self::str($value, 'solution_cs'),
            self::str($value, 'solution_en'),
            self::strings($value, 'stack'),
            self::strings($value, 'features'),
            self::str($value, 'year'),
            self::strings($value, 'features_cs'),
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

    /** @return list<string> */
    public function localizedFeatures(Language $lang): array
    {
        return $lang->code === 'cs' && $this->featuresCs !== [] ? $this->featuresCs : $this->features;
    }

    /** @return string */
    public function statusLabel(Language $lang): string
    {
        return match ($this->status) {
            'active' => $lang->value('Aktivní', 'Active'),
            'beta' => $lang->value('Beta', 'Beta'),
            'prototype' => $lang->value('Prototyp', 'Prototype'),
            'proposal' => $lang->value('Návrh', 'Proposal'),
            'record' => $lang->value('Provozní záznam', 'Operations record'),
            'maintenance' => $lang->value('Údržba', 'Maintenance'),
            'archived' => $lang->value('Archiv', 'Archived'),
            default => $this->status,
        };
    }

    /** @return string */
    public static function categoryLabel(string $category, Language $lang): string
    {
        if ($lang->code === 'en') {
            return $category;
        }

        return match ($category) {
            'AI & Developer Tools' => 'AI a vývojářské nástroje',
            'Desktop & Tools' => 'Desktopové nástroje',
            'Infrastructure' => 'Infrastruktura',
            'Games & Simulation' => 'Hry a simulace',
            '3D & Engineering' => '3D a konstrukce',
            'Web Applications' => 'Webové aplikace',
            'Media & Apps' => 'Média a aplikace',
            'Bots & Automation' => 'Boti a automatizace',
            'Archive' => 'Archiv',
            default => $category,
        };
    }

    /**
     * Return the project's localized summary.
     */
    public function summary(Language $lang): string
    {
        return $lang->value($this->summaryCs, $this->summaryEn);
    }

    /**
     * Return the localized engineering problem statement.
     */
    public function problem(Language $lang): string
    {
        return $lang->value($this->problemCs, $this->problemEn);
    }

    /**
     * Return the localized engineering solution description.
     */
    public function solution(Language $lang): string
    {
        return $lang->value($this->solutionCs, $this->solutionEn);
    }
}
