<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property string $repository
 * @property string $description
 * @property string $repo_url
 * @property \Cake\I18n\DateTime $pushed_at
 */
final class GithubPublicRepo extends Entity
{
    /**
     * @var array<string,bool>
     */
    protected array $_accessible = ['*' => false];
}
