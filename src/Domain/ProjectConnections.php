<?php
declare(strict_types=1);

namespace App\Domain;

use App\Support\Language;
use InvalidArgumentException;

/** A reviewed, finite, public dependency/relationship graph. */
final readonly class ProjectConnections
{
    /** @return string */
    public static function label(string $type, Language $lang): string
    {
        return match ($type) {
            'runs-on' => $lang->value('provozní prostředí', 'runtime environment'),
            'assists-development' => $lang->value('podpora vývoje', 'development tooling'),
            'hosts' => $lang->value('hosting', 'hosting'),
            'worker-hosting' => $lang->value('provoz pracovního procesu', 'worker hosting'),
            'produces-models' => $lang->value('související CAD postup', 'related CAD workflow'),
            'print-workflow' => $lang->value('postup 3D tisku', '3D print workflow'),
            'media-infrastructure' => $lang->value('mediální infrastruktura', 'media infrastructure'),
            'game-development' => $lang->value('vývoj her', 'game development'),
            default => $lang->value('souvisí', 'related'),
        };
    }

    /**
     * @var list<array{from:string,to:string,type:string}>
     */
    public array $edges;

    /**
     * @param list<array<string,mixed>> $raw
     * @param list<\App\Domain\Project> $projects
     */
    public function __construct(array $raw, array $projects)
    {
        $known = [];
        foreach ($projects as $project) {
            $known[$project->slug] = true;
        }
        $edges = [];
        $seen = [];
        foreach ($raw as $edge) {
            $from = $edge['from'] ?? null;
            $to = $edge['to'] ?? null;
            $type = $edge['type'] ?? null;
            if (
                !is_string($from) || !is_string($to) || !is_string($type) ||
                !isset($known[$from], $known[$to]) || $from === $to ||
                preg_match('/^[a-z-]{2,40}$/D', $type) !== 1 ||
                isset($seen[$from . '|' . $to])
            ) {
                throw new InvalidArgumentException('Invalid/duplicate public project connection');
            }
            $seen[$from . '|' . $to] = true;
            $edges[] = ['from' => $from, 'to' => $to, 'type' => $type];
        }
        $this->edges = $edges;
    }
}
