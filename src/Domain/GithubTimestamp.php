<?php
declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * GitHub JSON timestamps must be canonical UTC RFC 3339 timestamps.
 * PHP's permissive DateTime constructor accepts relative dates and normalizes
 * impossible dates (such as February 30); never trust that for an import.
 */
final class GithubTimestamp
{
    private function __construct()
    {
    }

    /** Normalize a strict YYYY-MM-DDTHH:MM:SSZ timestamp for MySQL UTC storage. */
    public static function normalize(mixed $value): string
    {
        if (
            !is_string($value) ||
            preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}Z$/D', $value) !== 1
        ) {
            throw new InvalidArgumentException('Invalid GitHub UTC timestamp format');
        }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s\Z', $value, new DateTimeZone('UTC'));
        if ($date === false || $date->format('Y-m-d\TH:i:s\Z') !== $value) {
            throw new InvalidArgumentException('Invalid GitHub UTC calendar date');
        }

        return $date->format('Y-m-d H:i:s');
    }
}
