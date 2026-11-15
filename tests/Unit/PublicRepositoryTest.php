<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\PublicRepository;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PublicRepositoryTest extends TestCase
{
    /** @return array<string,mixed> */
    private static function fixture(): array
    {
        return [
            'full_name' => 'Harfusha/local-ai-hub',
            'html_url' => 'https://github.com/Harfusha/local-ai-hub',
            'fork' => false,
            'private' => false,
            'description' => 'Typed coding-agent orchestration',
            'pushed_at' => '2026-11-12T11:45:05Z',
        ];
    }

    public function testAcceptsPublicOwnerAndCanonicalUrl(): void
    {
        $repos = PublicRepository::fromGithub([self::fixture()], 'Harfusha');
        self::assertCount(1, $repos);
        self::assertSame('Harfusha/local-ai-hub', $repos[0]->name);
        self::assertSame('2026-11-12 11:45:05', $repos[0]->pushedAt);
    }

    public function testRejectsImpossibleGitHubRepositoryPushDates(): void
    {
        $row = self::fixture();
        $row['pushed_at'] = '2026-11-31T11:45:05Z';
        $this->expectException(InvalidArgumentException::class);
        PublicRepository::fromGithub([$row], 'Harfusha');
    }

    public function testRejectsUnrenderablePublicRepositoryDescription(): void
    {
        $row = self::fixture();
        $row['description'] = "Hidden control \x01 in a public summary";
        $this->expectException(InvalidArgumentException::class);
        PublicRepository::fromGithub([$row], 'Harfusha');
    }

    public function testSkipsForkButKeepsOriginal(): void
    {
        $fork = self::fixture();
        $fork['full_name'] = 'Harfusha/cakephp';
        $fork['html_url'] = 'https://github.com/Harfusha/cakephp';
        $fork['fork'] = true;
        self::assertCount(1, PublicRepository::fromGithub([$fork, self::fixture()], 'Harfusha'));
    }

    public function testRejectsPrivateRepositoryRatherThanSilentlyFiltering(): void
    {
        $private = self::fixture();
        $private['private'] = true;
        $this->expectException(InvalidArgumentException::class);
        PublicRepository::fromGithub([self::fixture(), $private], 'Harfusha');
    }

    public function testRejectsImpersonatedRepository(): void
    {
        $other = self::fixture();
        $other['full_name'] = 'Attacker/local-ai-hub';
        $other['html_url'] = 'https://github.com/Attacker/local-ai-hub';
        $this->expectException(InvalidArgumentException::class);
        PublicRepository::fromGithub([$other], 'Harfusha');
    }

    public function testRejectsEmptyOrDuplicateListing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PublicRepository::fromGithub([self::fixture(), self::fixture()], 'Harfusha');
    }
}
