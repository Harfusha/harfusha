<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\EngineeringClaim;
use App\Domain\EngineeringEvidence;
use App\Domain\EvidenceRegistry;
use App\Domain\PublicContentValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EngineeringEvidenceTest extends TestCase
{
    public function testRejectsUnknownKind(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid evidence kind');
        new EngineeringEvidence(
            'id.test',
            'unsupported_kind',
            'public',
            'verified',
            null,
            null,
            null,
            null,
            'Method description',
            [],
        );
    }

    public function testRejectsUnknownVerificationStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid evidence verification status');
        new EngineeringEvidence(
            'id.test',
            'unit_test',
            'public',
            'invalid_status',
            null,
            null,
            null,
            null,
            'Method description',
            [],
        );
    }

    public function testRejectsUnknownVisibility(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid evidence visibility');
        new EngineeringEvidence(
            'id.test',
            'unit_test',
            'unknown_visibility',
            'verified',
            null,
            null,
            null,
            null,
            'Method description',
            [],
        );
    }

    public function testClaimValidatesSchemaVersionAndBilingualText(): void
    {
        $evidence = new EngineeringEvidence(
            'id.ev1',
            'unit_test',
            'public',
            'verified',
            'v1.0',
            'https://example.com',
            null,
            '2026-11-24',
            'Method text',
            ['limitation 1'],
        );

        $claim = new EngineeringClaim(
            1,
            'claim.test.v1',
            'test-proj',
            ['cs' => 'České tvrzení', 'en' => 'English claim'],
            [$evidence],
            ['state' => 'published', 'approved_content_hash' => 'abc'],
        );

        self::assertSame('claim.test.v1', $claim->id);
        self::assertTrue($claim->isPublished());
        self::assertSame($evidence, $claim->primaryEvidence());
        self::assertSame('České tvrzení', $claim->claim['cs']);
    }

    public function testClaimRejectsUnsupportedSchemaVersion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported evidence schema version: 2');
        new EngineeringClaim(
            2,
            'claim.test.v2',
            'test-proj',
            ['cs' => 'České', 'en' => 'English'],
            [],
            ['state' => 'published', 'approved_content_hash' => null],
        );
    }

    public function testRegistryLoadsCanonicalEvidenceDataset(): void
    {
        $path = ROOT . '/data/evidence.json';
        self::assertFileExists($path);
        $registry = EvidenceRegistry::fromFile($path);
        $all = $registry->all();
        self::assertNotEmpty($all);
        self::assertGreaterThanOrEqual(10, count($all));

        $hubClaims = $registry->forProject('local-ai-hub');
        self::assertNotEmpty($hubClaims);
        self::assertSame('hub.symbol-retrieval.v1', $hubClaims[0]->id);

        $primary = $hubClaims[0]->primaryEvidence();
        self::assertNotNull($primary);
        self::assertSame('release', $primary->kind);
        self::assertSame('v5.1.4', $primary->revision);

        $mutaClaims = $registry->forProject('mutaui');
        self::assertNotEmpty($mutaClaims);
        self::assertSame('v9.1.1', $mutaClaims[0]->primaryEvidence()?->revision);

        $harfushaClaims = $registry->forProject('harfusha-lab');
        self::assertNotEmpty($harfushaClaims);
        self::assertSame('harfusha-lab', $harfushaClaims[0]->projectId);
    }

    public function testPublicContentValidatorAcceptsEvidenceJson(): void
    {
        $raw = (string)file_get_contents(ROOT . '/data/evidence.json');
        /** @var list<array<string, mixed>> $decoded */
        $decoded = json_decode($raw, true);
        $this->expectNotToPerformAssertions();
        PublicContentValidator::validate('evidence.json', $decoded);
    }
}
