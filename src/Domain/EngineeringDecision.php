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

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        return new self(
            (string)$row['id'], (string)$row['project'], (string)$row['status'],
            (string)$row['title_cs'], (string)$row['title_en'],
            (string)$row['context_cs'], (string)$row['context_en'],
            (string)$row['decision_cs'], (string)$row['decision_en'],
            (string)$row['tradeoff_cs'], (string)$row['tradeoff_en'],
            (string)$row['evidence_cs'], (string)$row['evidence_en'],
        );
    }

    public function title(Language $lang): string { return $lang->value($this->titleCs, $this->titleEn); }
    public function context(Language $lang): string { return $lang->value($this->contextCs, $this->contextEn); }
    public function choice(Language $lang): string { return $lang->value($this->decisionCs, $this->decisionEn); }
    public function tradeoff(Language $lang): string { return $lang->value($this->tradeoffCs, $this->tradeoffEn); }
    public function evidence(Language $lang): string { return $lang->value($this->evidenceCs, $this->evidenceEn); }
}
