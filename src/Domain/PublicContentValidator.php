<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Boundary validator for all manually curated content. These files become public HTML;
 * reject incomplete records instead of silently converting them into empty strings.
 * This is deliberately independent of CakePHP to run during a pre-deployment audit.
 */
final class PublicContentValidator
{
    /** @param list<array<string,mixed>> $records */
    public static function validate(string $name, array $records): void
    {
        if (count($records) > 2000) {
            throw new InvalidArgumentException('Public dataset exceeds safe record limit: ' . $name);
        }
        $seen = [];
        foreach ($records as $index => $record) {
            $label = $name . '[' . $index . ']';
            $key = match ($name) {
                'projects.json' => self::project($record, $label),
                'topology.json' => self::topology($record, $label),
                'connections.json' => self::connection($record, $label),
                'contributions.json' => self::contribution($record, $label),
                'journal.json' => self::journal($record, $label),
                'maker.json' => self::maker($record, $label),
                'decisions.json' => self::decision($record, $label),
                'flows.json' => self::flow($record, $label),
                default => throw new InvalidArgumentException('Unrecognized public dataset'),
            };
            if (isset($seen[$key])) {
                throw new InvalidArgumentException('Duplicated identifier: ' . $label);
            }
            $seen[$key] = true;
        }
    }

    /** @param array<string,mixed> $row */
    private static function project(array $row, string $label): string
    {
        $slug = self::string($row, 'slug', $label, 1, 80);
        if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug) !== 1) {
            throw new InvalidArgumentException('Invalid project slug: ' . $label);
        }
        foreach (
            [
            'name', 'category', 'status', 'year', 'repo', 'demo',
            'summary_cs', 'summary_en', 'problem_cs', 'problem_en',
            'solution_cs', 'solution_en',
            ] as $field
        ) {
            self::string($row, $field, $label, in_array($field, ['repo','demo'], true) ? 0 : 1, 3000);
        }
        $featured = $row['featured'] ?? null;
        $isPrivate = $row['private'] ?? null;
        if (!is_bool($featured) || !is_bool($isPrivate)) {
            throw new InvalidArgumentException('Project privacy and feature flags must be boolean: ' . $label);
        }
        if (ProjectStatus::tryFrom(self::string($row, 'status', $label, 1, 20)) === null) {
            throw new InvalidArgumentException('Unknown project status: ' . $label);
        }
        if (preg_match('/^20\d{2}$/D', self::string($row, 'year', $label, 4, 4)) !== 1) {
            throw new InvalidArgumentException('Invalid project year: ' . $label);
        }
        if (!in_array(self::string($row, 'demo', $label, 0, 30), ['','ai','sync','world','cad','homelab'], true)) {
            throw new InvalidArgumentException('Unknown project demonstration: ' . $label);
        }
        $repo = self::string($row, 'repo', $label, 0, 255);
        if ($isPrivate && $repo !== '') {
            throw new InvalidArgumentException('Private repository link is forbidden: ' . $label);
        }
        if ($repo !== '' && preg_match('~^https://github\.com/[A-Za-z0-9-]+/[A-Za-z0-9_.-]+$~D', $repo) !== 1) {
            throw new InvalidArgumentException('Unexpected repository URL: ' . $label);
        }
        self::stringList($row, 'stack', $label, 1, 30, 70);
        self::stringList($row, 'features', $label, 1, 30, 200);

        return $slug;
    }

    /** @param array<string,mixed> $row */
    private static function topology(array $row, string $label): string
    {
        $id = self::string($row, 'id', $label, 1, 32);
        if (
            preg_match('/^[a-z][a-z0-9-]*$/D', $id) !== 1
            || !in_array($row['kind'] ?? null, ['uplink','network','compute','storage','device'], true)
        ) {
            throw new InvalidArgumentException('Invalid topology identifier or kind: ' . $label);
        }
        foreach (['name','description_cs','description_en'] as $key) {
            self::string($row, $key, $label, 1, 1000);
        }
        foreach (['x','y'] as $coordinate) {
            $v = $row[$coordinate] ?? null;
            if (!is_int($v) || $v < 0 || $v > 1100) {
                throw new InvalidArgumentException('Invalid diagram coordinate: ' . $label);
            }
        }
        self::stringList($row, 'tech', $label, 0, 30, 80);
        self::stringList($row, 'services', $label, 0, 50, 100);
        self::stringList($row, 'depends', $label, 0, 30, 32);

        return $id;
    }

    /** @param array<string,mixed> $row */
    private static function flow(array $row, string $label): string
    {
        $id = self::string($row, 'id', $label, 1, 32);
        if (preg_match('/^[a-z][a-z0-9-]*$/D', $id) !== 1) {
            throw new InvalidArgumentException('Invalid public flow identifier: ' . $label);
        }
        foreach (['title_cs','title_en','description_cs','description_en'] as $key) {
            self::string($row, $key, $label, 1, 600);
        }
        $path = self::stringList($row, 'path', $label, 2, 16, 32);
        $stepsCs = self::stringList($row, 'steps_cs', $label, 2, 16, 200);
        $stepsEn = self::stringList($row, 'steps_en', $label, 2, 16, 200);
        if (count($path) !== count($stepsCs) || count($path) !== count($stepsEn)) {
            throw new InvalidArgumentException('Public flow steps do not match path: ' . $label);
        }

        return $id;
    }

    /** @param array<string,mixed> $row */
    private static function connection(array $row, string $label): string
    {
        $from = self::string($row, 'from', $label, 1, 80);
        $to = self::string($row, 'to', $label, 1, 80);
        self::string($row, 'type', $label, 2, 40);

        return $from . '>' . $to;
    }

    /** @param array<string,mixed> $row */
    private static function contribution(array $row, string $label): string
    {
        $url = self::string($row, 'source_url', $label, 1, 255);
        $repo = self::string($row, 'repository', $label, 1, 180);
        self::string($row, 'title', $label, 1, 250);
        if (
            !in_array($row['kind'] ?? null, ['pr','issue'], true)
            || !in_array($row['state'] ?? null, ['merged','open','closed'], true)
        ) {
            throw new InvalidArgumentException('Invalid contribution state: ' . $label);
        }
        $type = $row['kind'] === 'pr' ? 'pull' : 'issues';
        if (
            preg_match(
                '~^https://github\.com/([A-Za-z0-9-]+/[A-Za-z0-9_.-]+)/' . $type . '/[1-9][0-9]*$~D',
                $url,
                $m,
            ) !== 1
            || $m[1] !== $repo
        ) {
            throw new InvalidArgumentException('Contribution URL/repository mismatch: ' . $label);
        }
        self::date(self::string($row, 'published_at', $label, 10, 32), $label);

        return $url;
    }

    /** @param array<string,mixed> $row */
    private static function journal(array $row, string $label): string
    {
        foreach (['title_cs','title_en','body_cs','body_en','category'] as $key) {
            self::string($row, $key, $label, 1, $key === 'category' ? 40 : 4000);
        }
        $when = self::string($row, 'published_at', $label, 10, 32);
        self::date($when, $label);
        $link = self::string($row, 'source_url', $label, 0, 255);
        if (
            $link !== ''
            && preg_match(
                '~^https://github\.com/[A-Za-z0-9-]+/[A-Za-z0-9_.-]+(?:/[A-Za-z0-9_./%-]+)?$~D',
                $link,
            ) !== 1
        ) {
            throw new InvalidArgumentException('Unsafe journal source: ' . $label);
        }

        return $when . '|' . self::string($row, 'title_en', $label, 1, 4000);
    }

    /** @param array<string,mixed> $row */
    private static function maker(array $row, string $label): string
    {
        $id = self::string($row, 'id', $label, 1, 80);
        if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $id) !== 1) {
            throw new InvalidArgumentException('Invalid maker project ID: ' . $label);
        }
        foreach (
            [
            'title_cs', 'title_en', 'summary_cs', 'summary_en',
            'method_cs', 'method_en', 'category', 'related',
            ] as $key
        ) {
            self::string($row, $key, $label, 1, $key === 'related' ? 80 : 2000);
        }
        if (
            !in_array($row['status'] ?? null, ['planned','prototype','in-use'], true) ||
            !in_array($row['visual'] ?? null, ['spool','sun','gear','grid','code'], true)
        ) {
            throw new InvalidArgumentException('Unknown maker status/visual: ' . $label);
        }
        self::stringList($row, 'tags', $label, 1, 12, 60);

        return $id;
    }

    /** @param array<string,mixed> $row */
    private static function decision(array $row, string $label): string
    {
        $id = self::string($row, 'id', $label, 2, 80);
        $project = self::string($row, 'project', $label, 2, 80);
        if (
            preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $id) !== 1 ||
            preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $project) !== 1 ||
            !in_array($row['status'] ?? null, ['documented','prototype','planned'], true)
        ) {
            throw new InvalidArgumentException('Invalid decision identifier, status or project: ' . $label);
        }
        foreach (
            ['title_cs','title_en','context_cs','context_en','decision_cs','decision_en',
            'tradeoff_cs','tradeoff_en','evidence_cs','evidence_en'] as $field
        ) {
            self::string($row, $field, $label, 1, 1800);
        }

        return $id;
    }

    /** @param array<string,mixed> $row */
    private static function string(array $row, string $key, string $label, int $min, int $max): string
    {
        $value = $row[$key] ?? null;
        if (
            !is_string($value)
            || strlen($value) < $min
            || strlen($value) > $max * 4
            || ($min > 0 && trim($value) === '')
            || str_contains($value, "\0")
        ) {
            throw new InvalidArgumentException('Invalid field ' . $key . ': ' . $label);
        }
        // Length is measured in UTF-8 characters in production (mbstring requirement).
        if (function_exists('mb_strlen') && mb_strlen($value, 'UTF-8') > $max) {
            throw new InvalidArgumentException('Field too long ' . $key . ': ' . $label);
        }

        return $value;
    }

    /**
     * @param array<string,mixed> $row
     * @return list<string>
     */
    private static function stringList(array $row, string $key, string $label, int $min, int $max, int $maxChars): array
    {
        $items = $row[$key] ?? null;
        if (!is_array($items) || !array_is_list($items) || count($items) < $min || count($items) > $max) {
            throw new InvalidArgumentException('Invalid list ' . $key . ': ' . $label);
        }
        $validated = [];
        foreach ($items as $item) {
            $validated[] = self::string(['item' => $item], 'item', $label, 1, $maxChars);
        }

        return $validated;
    }

    /**
     * Validate a stable UTC publication date without PHP's permissive relative
     * date parser (which accepts strings like "tomorrow" and rolls over Feb 30).
     * Accepted: YYYY-MM-DD, YYYY-MM-DD HH:MM:SS, and RFC3339 UTC with Z.
     */
    private static function date(string $value, string $label): void
    {
        if (
            preg_match(
                '/^([0-9]{4})-([0-9]{2})-([0-9]{2})' .
                    '(?:(?: ([0-9]{2}):([0-9]{2}):([0-9]{2}))' .
                    '|(?:T([0-9]{2}):([0-9]{2}):([0-9]{2})Z))?$/D',
                $value,
                $matches,
            ) !== 1
        ) {
            throw new InvalidArgumentException('Invalid date format: ' . $label);
        }
        $year = (int)$matches[1];
        if ($year < 2000 || $year > 2100 || !checkdate((int)$matches[2], (int)$matches[3], $year)) {
            throw new InvalidArgumentException('Invalid calendar date: ' . $label);
        }
        $hour = ($matches[4] ?? '') !== '' ? (int)$matches[4] : (int)($matches[7] ?? 0);
        $minute = ($matches[5] ?? '') !== '' ? (int)$matches[5] : (int)($matches[8] ?? 0);
        $second = ($matches[6] ?? '') !== '' ? (int)$matches[6] : (int)($matches[9] ?? 0);
        if ($hour > 23 || $minute > 59 || $second > 59) {
            throw new InvalidArgumentException('Invalid publication time: ' . $label);
        }
    }
}
