<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

final class JournalEntriesTable extends Table
{
    /**
     * Configure the CakePHP table schema and persistence metadata.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('journal_entries');
        $this->setPrimaryKey('id');
    }

    /**
     * Define the validation constraints for incoming entity data.
     */
    public function validationDefault(Validator $validator): Validator
    {
        return $validator->scalar('title_cs')->maxLength('title_cs', 240)->notEmptyString('title_cs')
            ->scalar('title_en')->maxLength('title_en', 240)->notEmptyString('title_en')
            ->scalar('body_cs')->maxLength('body_cs', 4000)->notEmptyString('body_cs')
            ->scalar('body_en')->maxLength('body_en', 4000)->notEmptyString('body_en')
            ->scalar('category')->maxLength('category', 40)->notEmptyString('category')
            ->boolean('is_public')->dateTime('published_at');
    }
}
