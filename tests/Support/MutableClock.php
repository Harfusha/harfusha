<?php
declare(strict_types=1);

namespace App\Test\Support;

/**
 * Deterministic, mutable clock used to verify time-based security logic.
 */
final class MutableClock
{
    /**
     * Initialize the clock at a known Unix timestamp.
     */
    public function __construct(private int $timestamp)
    {
    }

    /**
     * Return the current simulated Unix timestamp.
     */
    public function now(): int
    {
        return $this->timestamp;
    }

    /**
     * Move the simulated clock forwards or backwards by the given seconds.
     */
    public function advance(int $seconds): void
    {
        $this->timestamp += $seconds;
    }
}
