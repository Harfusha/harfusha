<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property string $source_url
 * @property string $title
 * @property string $repository
 * @property string $kind
 * @property string $state
 * @property \Cake\I18n\DateTime $published_at
 */
final class GithubActivity extends Entity
{
    /**
     * Public GitHub data is written by the importer only, not mass assigned. @var array<string,bool>
     */
    protected array $_accessible = ['*' => false];
}
