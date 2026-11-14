<?php
declare(strict_types=1);

namespace App\Security;

use Closure;
use InvalidArgumentException;
use JsonException;
use stdClass;
use Throwable;

// phpcs:disable Generic.PHP.NoSilencedErrors.Discouraged -- Filesystem calls intentionally fail closed;
// each suppressed operation is explicitly checked and denied on failure.

/**
 * Bounded, fail-closed login throttle for shared hosting without Redis.
 *
 * There are at most 32 shard files, each containing at most 512 active IP
 * identities. One lock covers both checking and recording a login attempt,
 * so concurrent requests cannot all pass the old check-then-record race.
 *
 * Exhausted/corrupt/unwritable storage denies access; it never silently
 * disables the protection. Only opaque SHA-256 address digests are persisted.
 */
final readonly class LoginLimiter
{
    private const int SHARD_COUNT = 32;
    private const int WINDOW_SECONDS = 900;
    private const int MAX_ATTEMPTS = 5;
    private const int MAX_SHARD_BYTES = 262144;
    private const int DEFAULT_MAX_IDENTITIES = 512;

    /**
     * @var \Closure():int
     */
    private Closure $clock;

    /**
     * @param (\Closure():int)|null $clock Test-only deterministic clock.
     */
    public function __construct(
        private string $directory,
        ?Closure $clock = null,
        private int $maxIdentitiesPerShard = self::DEFAULT_MAX_IDENTITIES,
    ) {
        if ($maxIdentitiesPerShard < 1 || $maxIdentitiesPerShard > self::DEFAULT_MAX_IDENTITIES) {
            throw new InvalidArgumentException('Invalid per-shard identity limit');
        }
        $this->clock = $clock ?? static fn(): int => time();
    }

    /**
     * Atomically reserves one login attempt, regardless of whether credentials
     * will later be valid. Returns false on throttling or ANY storage failure.
     */
    public function consume(string $address): bool
    {
        return $this->mutate($address, false);
    }

    /**
     * Reset the counter after a successful login. Authentication must not be
     * granted when this operation fails, otherwise an attacker could bypass
     * the throttle by forcing storage failures.
     */
    public function clear(string $address): bool
    {
        return $this->mutate($address, true);
    }

    /**
     * Atomically update the bounded on-disk login throttle.
     */
    private function mutate(string $address, bool $clear): bool
    {
        if ($address === '' || strlen($address) > 128 || str_contains($address, "\0")) {
            return false;
        }
        if (!$this->ensurePrivateDirectory()) {
            return false;
        }
        $identity = hash('sha256', $address);
        $shard = (int)hexdec(substr($identity, 0, 2)) % self::SHARD_COUNT;
        $path = rtrim($this->directory, '/\\') . '/login-' . $shard . '.json';

        // A symlink is never a valid limiter database. Store TMP outside webroot.
        if (self::pathIsLink($path)) {
            return false;
        }
        $handle = @fopen($path, 'c+b');
        if ($handle === false) {
            return false;
        }
        try {
            if (!@flock($handle, LOCK_EX)) {
                return false;
            }
            // Check the open descriptor still points to this actual path, not
            // a symlink or a replacement racing the advisory file lock.
            clearstatcache(true, $path);
            $pathname = @lstat($path);
            $descriptor = @fstat($handle);
            if (
                !is_array($pathname) || !is_array($descriptor) ||
                $pathname['ino'] !== $descriptor['ino'] || $pathname['dev'] !== $descriptor['dev']
            ) {
                return false;
            }
            $now = ($this->clock)();
            $entries = $this->read($handle, $now);
            if ($entries === null) {
                return false;
            }
            if ($clear) {
                unset($entries[$identity]);
            } else {
                $attempts = $entries[$identity] ?? [];
                if (count($attempts) >= self::MAX_ATTEMPTS) {
                    return false;
                }
                if ($attempts === [] && count($entries) >= $this->maxIdentitiesPerShard) {
                    // Never evict an active identity: that would make flooding
                    // the bucket a way to reset other users' lockouts.
                    return false;
                }
                $attempts[] = $now;
                $entries[$identity] = $attempts;
            }

            return $this->write($handle, $entries);
        } catch (Throwable) {
            return false;
        } finally {
            @flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    /**
     * Detect symlinks before opening security-sensitive state.
     */
    private static function pathIsLink(string $path): bool
    {
        clearstatcache(true, $path);

        return is_link($path);
    }

    /**
     * Check that persistent throttle storage is private and writable.
     */
    private function ensurePrivateDirectory(): bool
    {
        if (is_link($this->directory)) {
            return false;
        }
        if (!is_dir($this->directory) && !@mkdir($this->directory, 0700, true) && !is_dir($this->directory)) {
            return false;
        }
        clearstatcache(true, $this->directory);
        $info = @lstat($this->directory);
        if (!is_array($info) || ($info['mode'] & 0170000) !== 0040000) {
            return false;
        }

        return is_writable($this->directory);
    }

    /** @param resource $handle
     * @return array<string,list<int>>|null
     */
    private function read($handle, int $now): ?array
    {
        if (@fseek($handle, 0, SEEK_SET) !== 0) {
            return null;
        }
        $raw = @stream_get_contents($handle, self::MAX_SHARD_BYTES + 1);
        if (!is_string($raw) || strlen($raw) > self::MAX_SHARD_BYTES) {
            return null;
        }
        if ($raw === '') {
            return [];
        }
        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }
        if (
            !is_array($decoded) || array_is_list($decoded) && $decoded !== [] ||
            count($decoded) > $this->maxIdentitiesPerShard
        ) {
            return null;
        }
        $clean = [];
        foreach ($decoded as $key => $timestamps) {
            if (
                !is_string($key) || preg_match('/^[a-f0-9]{64}$/D', $key) !== 1 ||
                !is_array($timestamps) || !array_is_list($timestamps) ||
                count($timestamps) > self::MAX_ATTEMPTS
            ) {
                return null;
            }
            $recent = [];
            foreach ($timestamps as $stamp) {
                if (!is_int($stamp) || $stamp < 0) {
                    return null;
                }
                // Clock rollback is a storage anomaly, not a reason to
                // discard an active lockout and silently allow more attempts.
                if ($stamp > $now) {
                    return null;
                }
                if ($stamp > $now - self::WINDOW_SECONDS) {
                    $recent[] = $stamp;
                }
            }
            if ($recent !== []) {
                $clean[$key] = $recent;
            }
        }

        return $clean;
    }

    /** @param resource $handle
     * @param array<string,list<int>> $entries
     */
    private function write($handle, array $entries): bool
    {
        $json = json_encode($entries === [] ? new stdClass() : $entries, JSON_THROW_ON_ERROR);
        if (strlen($json) > self::MAX_SHARD_BYTES || @fseek($handle, 0, SEEK_SET) !== 0 || !@ftruncate($handle, 0)) {
            return false;
        }
        $length = strlen($json);
        $offset = 0;
        while ($offset < $length) {
            $written = @fwrite($handle, substr($json, $offset));
            if ($written === false || $written === 0) {
                return false;
            }
            $offset += $written;
        }

        return @fflush($handle);
    }
}

// phpcs:enable Generic.PHP.NoSilencedErrors.Discouraged
