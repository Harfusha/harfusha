<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $category
 * @property string $title_cs
 * @property string $title_en
 * @property string $body_cs
 * @property string $body_en
 * @property bool $is_public
 * @property string $source_url
 * @property \Cake\I18n\DateTime $published_at
 */
final class JournalEntry extends Entity
{
    /**
     * @var array<string,bool>
     */
    protected array $_accessible = [
        'category' => true, 'title_cs' => true, 'title_en' => true,
        'body_cs' => true, 'body_en' => true, 'is_public' => true,
        'source_url' => true, 'published_at' => true,
    ];
}
