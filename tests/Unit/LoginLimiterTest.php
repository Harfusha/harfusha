<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Security\LoginLimiter;
use App\Test\Support\MutableClock;
use Cake\TestSuite\TestCase;
use RuntimeException;

final class LoginLimiterTest extends TestCase
{
    /**
     * @var list<string>
     */
    private array $directories = [];

    protected function tearDown(): void
    {
        foreach ($this->directories as $directory) {
            $files = glob($directory . '/*');
            if ($files === false) {
                throw new RuntimeException('Could not enumerate test limiter files');
            }
            foreach ($files as $file) {
                unlink($file);
            }
            if (is_dir($directory)) {
                rmdir($directory);
            }
        }
        parent::tearDown();
    }

    private function directory(): string
    {
        $dir = sys_get_temp_dir() . '/harfusha-rate-' . bin2hex(random_bytes(8));
        $this->directories[] = $dir;

        return $dir;
    }

    public function testFiveAttemptsAreAtomicAndDifferentAddressesAreIsolated(): void
    {
        $dir = $this->directory();
        $limiter = new LoginLimiter($dir);
        for ($i = 0; $i < 5; $i++) {
            self::assertTrue($limiter->consume('203.0.113.15'));
        }
        self::assertFalse($limiter->consume('203.0.113.15'));
        self::assertTrue($limiter->consume('203.0.113.16'));
        self::assertTrue($limiter->clear('203.0.113.15'));
        self::assertTrue($limiter->consume('203.0.113.15'));
    }

    public function testWindowExpiresExactlyAfterFifteenMinutes(): void
    {
        $dir = $this->directory();
        $clockState = new MutableClock(1700000000);
        $clock = static fn(): int => $clockState->now();
        $limiter = new LoginLimiter($dir, $clock);
        for ($i = 0; $i < 5; $i++) {
            self::assertTrue($limiter->consume('198.51.100.18'));
        }
        $clockState->advance(899);
        self::assertFalse($limiter->consume('198.51.100.18'));
        $clockState->advance(1);
        self::assertTrue($limiter->consume('198.51.100.18'));
    }

    public function testShardCapacityFailsClosedWithoutEvictingAnotherAddress(): void
    {
        $dir = $this->directory();
        /** @var array<int, list<string>> $buckets */
        $buckets = [];
        /** @var list<string> $sameShard */
        $sameShard = [];
        $foundThree = false;
        for ($i = 0; $i < 300 && !$foundThree; $i++) {
            $address = '198.51.100.' . $i;
            $bucket = (int)hexdec(substr(hash('sha256', $address), 0, 2)) % 32;
            $buckets[$bucket] ??= [];
            $buckets[$bucket][] = $address;
            if (count($buckets[$bucket]) === 3) {
                $sameShard = $buckets[$bucket];
                $foundThree = true;
            }
        }
        self::assertCount(3, $sameShard);
        $limiter = new LoginLimiter($dir, null, 2);
        self::assertTrue($limiter->consume($sameShard[0]));
        self::assertTrue($limiter->consume($sameShard[1]));
        self::assertFalse($limiter->consume($sameShard[2]));
        for ($i = 0; $i < 4; $i++) {
            self::assertTrue($limiter->consume($sameShard[0]));
        }
        self::assertFalse($limiter->consume($sameShard[0]));
    }

    public function testCorruptFileAndNonDirectoryAlwaysDeny(): void
    {
        $dir = $this->directory();
        $address = '192.0.2.5';
        $limiter = new LoginLimiter($dir);
        self::assertTrue($limiter->consume($address));
        $files = glob($dir . '/*.json');
        self::assertIsArray($files);
        self::assertCount(1, $files);
        file_put_contents($files[0], '{invalid');
        self::assertFalse($limiter->consume($address));
        self::assertFalse($limiter->clear($address));
        self::assertSame('{invalid', file_get_contents($files[0]));
        self::assertFalse($limiter->consume(''));
        self::assertFalse($limiter->consume(str_repeat('x', 129)));

        $path = $this->directory();
        file_put_contents($path, 'not a directory');
        self::assertFalse((new LoginLimiter($path))->consume($address));
        unlink($path);
    }

    public function testOversizedFileAndSymlinkAreRejectedWithoutFollowingTarget(): void
    {
        $dir = $this->directory();
        $address = '192.0.2.99';
        $limiter = new LoginLimiter($dir);
        self::assertTrue($limiter->consume($address));
        $files = glob($dir . '/*.json');
        self::assertIsArray($files);
        self::assertCount(1, $files);
        $shardFile = $files[0];
        file_put_contents($shardFile, str_repeat('x', 262145));
        self::assertFalse($limiter->consume($address));

        unlink($shardFile);
        $target = $dir . '/unrelated.json';
        file_put_contents($target, '{}');
        self::assertTrue(symlink($target, $shardFile));
        self::assertFalse($limiter->consume($address));
        self::assertSame('{}', file_get_contents($target));
    }

    public function testClockRollbackDoesNotSilentlyResetCounters(): void
    {
        $clockState = new MutableClock(1700000000);
        $clock = static fn(): int => $clockState->now();
        $limiter = new LoginLimiter($this->directory(), $clock);
        self::assertTrue($limiter->consume('192.0.2.40'));
        $clockState->advance(-1);
        self::assertFalse($limiter->consume('192.0.2.40'));
        self::assertFalse($limiter->clear('192.0.2.40'));
    }

    public function testNumberOfFilesIsBoundedByShardCount(): void
    {
        $dir = $this->directory();
        $limiter = new LoginLimiter($dir);
        for ($i = 0; $i < 400; $i++) {
            self::assertTrue($limiter->consume('test-address-' . $i));
        }
        $files = glob($dir . '/login-*.json');
        self::assertIsArray($files);
        self::assertLessThanOrEqual(32, count($files));
    }
}
