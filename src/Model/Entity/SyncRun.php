<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $source
 * @property int $item_count
 * @property \Cake\I18n\DateTime $completed_at
 */
final class SyncRun extends Entity
{
    /**
     * @var array<string,bool>
     */
    protected array $_accessible = ['*' => false];
}
