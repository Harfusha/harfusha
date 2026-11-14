<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\PublicContentValidator;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;

final class PublicContentValidatorTest extends TestCase
{
    public function testAllCuratedFilesHaveValidShapes(): void
    {
        foreach (['projects','topology','connections','contributions','journal','maker','decisions','flows'] as $name) {
            $bytes = file_get_contents(ROOT . '/data/' . $name . '.json');
            self::assertIsString($bytes);
            $data = json_decode($bytes, true, 512, JSON_THROW_ON_ERROR);
            self::assertIsArray($data);
            PublicContentValidator::validate($name . '.json', $data);
            self::assertGreaterThan(0, count($data));
        }
    }

    public function testPrivateSourceLinkIsRejected(): void
    {
        $bytes = file_get_contents(ROOT . '/data/projects.json');
        self::assertIsString($bytes);
        $projects = json_decode($bytes, true, 512, JSON_THROW_ON_ERROR);
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

    public function testDuplicateJournalContentIsRejected(): void
    {
        $entries = [[
            'category'=>'Development','title_cs'=>'Změna','title_en'=>'Change',
            'body_cs'=>'Text','body_en'=>'Text','source_url'=>'',
            'published_at'=>'2026-11-12',
        ]];
        $this->expectException(InvalidArgumentException::class);
        PublicContentValidator::validate('journal.json', [$entries[0],$entries[0]]);
    }

    public function testMissingTopologyCoordinatesAreRejected(): void
    {
        $row = [
            'id'=>'server','name'=>'Host','kind'=>'compute',
            'description_cs'=>'Popis','description_en'=>'Description',
            'tech'=>['Linux'],'services'=>[],'depends'=>[],
        ];
        $this->expectException(InvalidArgumentException::class);
        PublicContentValidator::validate('topology.json', [$row]);
    }
}
