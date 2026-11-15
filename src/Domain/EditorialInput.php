<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Pure, typed input boundary for the editorial control panel.
 * Reject invalid payloads before they touch the Cake ORM or database.
 * No user-supplied strings are interpolated into error messages.
 */
final class EditorialInput
{
    /**
     * Initialize the immutable domain model with validated inputs.
     */
    private function __construct()
    {
    }

    /**
     * @return array{
     *     slug:string,summary_cs:string,summary_en:string,status:string,
     *     problem_cs:string,problem_en:string,solution_cs:string,solution_en:string
     * }
     */
    public static function project(mixed $payload): array
    {
        $data = self::object($payload);
        $slug = self::field($data, 'slug', 80, true);
        if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug) !== 1) {
            throw new InvalidArgumentException('Invalid project slug');
        }
        $status = self::field($data, 'status', 20, true);
        if (!in_array($status, ['active', 'prototype', 'maintenance', 'archived'], true)) {
            throw new InvalidArgumentException('Invalid project status');
        }

        return [
            'slug' => $slug,
            'summary_cs' => self::field($data, 'summary_cs', 2000, true),
            'summary_en' => self::field($data, 'summary_en', 2000, true),
            'status' => $status,
            'problem_cs' => self::field($data, 'problem_cs', 3000, false),
            'problem_en' => self::field($data, 'problem_en', 3000, false),
            'solution_cs' => self::field($data, 'solution_cs', 3000, false),
            'solution_en' => self::field($data, 'solution_en', 3000, false),
        ];
    }

    /**
     * @return array{category:string,title_cs:string,title_en:string,body_cs:string,body_en:string}
     */
    public static function journal(mixed $payload): array
    {
        $data = self::object($payload);
        $category = self::field($data, 'category', 40, true);
        if (preg_match('/^[A-Za-z0-9 &-]{1,40}$/D', $category) !== 1) {
            throw new InvalidArgumentException('Invalid journal category');
        }

        return [
            'category' => $category,
            'title_cs' => self::field($data, 'title_cs', 240, true),
            'title_en' => self::field($data, 'title_en', 240, true),
            'body_cs' => self::field($data, 'body_cs', 4000, true),
            'body_en' => self::field($data, 'body_en', 4000, true),
        ];
    }

    /**
     * Require an explicit publication decision instead of silently treating
     * malformed or missing values as a draft or accidentally publishing them.
     */
    public static function isPublic(mixed $visibility): bool
    {
        return match ($visibility) {
            'draft' => false,
            'public' => true,
            default => throw new InvalidArgumentException('Invalid journal visibility'),
        };
    }

    /**
     * Parse a strictly positive persisted identifier.
     */
    public static function positiveId(mixed $value): int
    {
        if (
            !is_string($value) || preg_match('/^[1-9][0-9]{0,17}$/D', $value) !== 1 ||
            filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false
        ) {
            throw new InvalidArgumentException('Invalid ID');
        }

        return (int)$value;
    }

    /** @return array<string,mixed> */
    private static function object(mixed $value): array
    {
        if (!is_array($value)) {
            throw new InvalidArgumentException('Invalid form payload');
        }
        $output = [];
        foreach ($value as $key => $item) {
            if (is_string($key)) {
                $output[$key] = $item;
            }
        }

        return $output;
    }

    /** @param array<string,mixed> $data */
    private static function field(array $data, string $key, int $maxChars, bool $required): string
    {
        $value = $data[$key] ?? null;
        if (
            !is_string($value)
            || strlen($value) > $maxChars * 4
            || str_contains($value, "\0")
            || preg_match('//u', $value) !== 1
        ) {
            throw new InvalidArgumentException('Invalid text value');
        }
        $value = trim($value);
        PublicText::assertRenderable($value);
        $length = function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
        if ($length > $maxChars || ($required && $value === '')) {
            throw new InvalidArgumentException('Invalid text length');
        }

        return $value;
    }
}
