<?php
declare(strict_types=1);

namespace App\Domain;

use App\Support\Language;
use InvalidArgumentException;

/** Reviewer-approved design rationale, intentionally separate from activity metrics. */
final readonly class EngineeringDecision
{
    public function __construct(
        public string $id,
        public string $project,
        public string $status,
        public string $titleCs,
        public string $titleEn,
        public string $contextCs,
        public string $contextEn,
        public string $decisionCs,
        public string $decisionEn,
        public string $tradeoffCs,
        public string $tradeoffEn,
        public string $evidenceCs,
        public string $evidenceEn,
    ) {
        if (!in_array($status, ['documented', 'prototype', 'planned'], true)) {
            throw new InvalidArgumentException('Invalid engineering decision stage');
        }
    }

    /** @param array<string, mixed> $row */
    public static function fromArray(array $row): self
    {
        return new self(
            self::textField($row, 'id'),
            self::textField($row, 'project'),
            self::textField($row, 'status'),
            self::textField($row, 'title_cs'),
            self::textField($row, 'title_en'),
            self::textField($row, 'context_cs'),
            self::textField($row, 'context_en'),
            self::textField($row, 'decision_cs'),
            self::textField($row, 'decision_en'),
            self::textField($row, 'tradeoff_cs'),
            self::textField($row, 'tradeoff_en'),
            self::textField($row, 'evidence_cs'),
            self::textField($row, 'evidence_en'),
        );
    }

    /** @param array<string, mixed> $row */
    private static function textField(array $row, string $key): string
    {
        $value = $row[$key] ?? null;
        if (!is_string($value)) {
            throw new InvalidArgumentException('Invalid decision field: ' . $key);
        }

        return $value;
    }

    public function title(Language $lang): string
    {
        return $lang->value($this->titleCs, $this->titleEn);
    }

    public function context(Language $lang): string
    {
        return $lang->value($this->contextCs, $this->contextEn);
    }

    public function choice(Language $lang): string
    {
        return $lang->value($this->decisionCs, $this->decisionEn);
    }

    public function tradeoff(Language $lang): string
    {
        return $lang->value($this->tradeoffCs, $this->tradeoffEn);
    }

    public function evidence(Language $lang): string
    {
        return $lang->value($this->evidenceCs, $this->evidenceEn);
    }
}
