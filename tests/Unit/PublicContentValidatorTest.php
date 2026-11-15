<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\PublicContentValidator;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;
use RuntimeException;

final class PublicContentValidatorTest extends TestCase
{
    public function testAllCuratedFilesHaveValidShapes(): void
    {
        foreach (['projects','topology','connections','contributions','journal','maker','decisions','flows'] as $name) {
            $bytes = file_get_contents(ROOT . '/data/' . $name . '.json');
            self::assertIsString($bytes);
            $data = json_decode($bytes, true, 512, JSON_THROW_ON_ERROR);
            $data = self::rows($data);
            PublicContentValidator::validate($name . '.json', $data);
            self::assertGreaterThan(0, count($data));
        }
    }

    public function testPrivateSourceLinkIsRejected(): void
    {
        $bytes = file_get_contents(ROOT . '/data/projects.json');
        self::assertIsString($bytes);
        $projects = json_decode($bytes, true, 512, JSON_THROW_ON_ERROR);
        $projects = self::rows($projects);
        if (!isset($projects[0])) {
            throw new RuntimeException('Expected at least one project');
        }
        $projects[0]['private'] = true;
        $this->expectException(InvalidArgumentException::class);
        PublicContentValidator::validate('projects.json', $projects);
    }

    public function testSpoofedContributionSourceIsRejected(): void
    {
        $rows = [[
            'source_url' => 'https://github.com.attacker.example/x/y/pull/3',
            'repository' => 'x/y', 'title' => 'bad', 'kind' => 'pr',
            'state' => 'merged', 'published_at' => '2026-11-12',
        ]];
        $this->expectException(InvalidArgumentException::class);
        PublicContentValidator::validate('contributions.json', $rows);
    }

    public function testPublicationDatesUseStrictCalendarAndTimeValidation(): void
    {
        $row = [
            'source_url' => 'https://github.com/cakephp/cakephp/pull/17827',
            'repository' => 'cakephp/cakephp', 'title' => 'Typed associations',
            'kind' => 'pr', 'state' => 'merged', 'published_at' => '2024-08-22',
        ];
        foreach (['2024-08-22', '2024-02-29', '2026-11-12 07:43:52', '2026-11-12T07:43:52Z'] as $date) {
            PublicContentValidator::validate('contributions.json', [[...$row, 'published_at' => $date]]);
        }
        $rejected = 0;
        foreach (
            [
                'tomorrow', '2026-02-30', '2026-13-01', '2026-11-15 24:00:00',
                '2026-11-15 13:99:00', '2026-11-15T25:25:00Z', '1999-12-31',
            ] as $date
        ) {
            try {
                PublicContentValidator::validate('contributions.json', [[...$row, 'published_at' => $date]]);
            } catch (InvalidArgumentException) {
                $rejected++;
            }
        }
        self::assertSame(7, $rejected);
    }

    public function testDuplicateJournalContentIsRejected(): void
    {
        $entries = [[
            'category' => 'Development','title_cs' => 'Změna','title_en' => 'Change',
            'body_cs' => 'Text','body_en' => 'Text','source_url' => '',
            'published_at' => '2026-11-12',
        ]];
        $this->expectException(InvalidArgumentException::class);
        PublicContentValidator::validate('journal.json', [$entries[0],$entries[0]]);
    }

    /** @return list<array<string, mixed>> */
    private static function rows(mixed $value): array
    {
        if (!is_array($value) || !array_is_list($value)) {
            throw new RuntimeException('Expected JSON list');
        }
        $rows = [];
        foreach ($value as $row) {
            if (!is_array($row)) {
                throw new RuntimeException('Expected JSON object record');
            }
            /** @var array<string, mixed> $row */
            $rows[] = $row;
        }

        return $rows;
    }

    public function testMissingTopologyCoordinatesAreRejected(): void
    {
        $row = [
            'id' => 'server','name' => 'Host','kind' => 'compute',
            'description_cs' => 'Popis','description_en' => 'Description',
            'tech' => ['Linux'],'services' => [],'depends' => [],
        ];
        $this->expectException(InvalidArgumentException::class);
        PublicContentValidator::validate('topology.json', [$row]);
    }
}
