<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property string $slug
 * @property string $summary_cs
 * @property string $summary_en
 * @property ?string $problem_cs
 * @property ?string $problem_en
 * @property ?string $solution_cs
 * @property ?string $solution_en
 * @property string $status
 */
final class ProjectOverride extends Entity
{
    /**
     * @var array<string,bool>
     */
    protected array $_accessible = [
        'slug' => true, 'summary_cs' => true, 'summary_en' => true,
        'problem_cs' => true, 'problem_en' => true,
        'solution_cs' => true, 'solution_en' => true,
        'status' => true,
    ];
}
