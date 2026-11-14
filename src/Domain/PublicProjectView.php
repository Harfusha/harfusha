<?php
declare(strict_types=1);

namespace App\Domain;

use App\Support\Language;
use App\Support\Html;

/**
 * Allowlisted API representation: the public endpoint must never serialize
 * arbitrary ORM entities or accidentally disclose private repository URLs.
 */
final readonly class PublicProjectView
{
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
        $demo = Html::safeUrl($this->project->demo) !== '#' ? $this->project->demo : null;

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
            'features' => $this->project->features,
            'year' => $this->project->year,
            'repository' => $repository,
            'demo' => $demo,
        ];
    }
}
