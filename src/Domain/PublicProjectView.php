<?php
declare(strict_types=1);

namespace App\Domain;

use App\Support\Html;
use App\Support\Language;

/**
 * Allowlisted API representation: the public endpoint must never serialize
 * arbitrary ORM entities or accidentally disclose private repository URLs.
 */
final readonly class PublicProjectView
{
    /**
     * Initialize the immutable domain model with validated inputs.
     */
    public function __construct(private Project $project)
    {
    }

    /**
     * @return array{slug:string,name:string,category:string,status:string,featured:bool,summary:string,problem:string,solution:string,stack:list<string>,features:list<string>,year:string,repository:?string,demo:?string}
     */
    public function toArray(Language $language): array
    {
        $repository = !$this->project->private && Html::safeUrl($this->project->repo) !== '#'
            ? $this->project->repo
            : null;
        // Curated demos are local route IDs, never external URLs.
        $demo = match ($this->project->demo) {
            'ai', 'sync', 'world', 'cad' => $language->path('/playground') . '#' . $this->project->demo,
            'homelab' => $language->path('/homelab'),
            default => null,
        };

        return [
            'slug' => $this->project->slug,
            'name' => $this->project->name,
            'category' => $this->project->category,
            'status' => $this->project->status,
            'featured' => $this->project->featured,
            'summary' => $this->project->summary($language),
            'problem' => $this->project->problem($language),
            'solution' => $this->project->solution($language),
            'stack' => $this->project->stack,
            'features' => $this->project->localizedFeatures($language),
            'year' => $this->project->year,
            'repository' => $repository,
            'demo' => $demo,
        ];
    }
}
