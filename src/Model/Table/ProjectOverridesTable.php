<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

final class ProjectOverridesTable extends Table
{
    /**
     * Configure the CakePHP table schema and persistence metadata.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('project_overrides');
        $this->setPrimaryKey('slug');
    }

    /**
     * Define the validation constraints for incoming entity data.
     */
    public function validationDefault(Validator $validator): Validator
    {
        return $validator->scalar('slug')->maxLength('slug', 80)->requirePresence('slug', 'create')
            ->notEmptyString('slug')->scalar('summary_cs')->maxLength('summary_cs', 2000)
            ->notEmptyString('summary_cs')->scalar('summary_en')->maxLength('summary_en', 2000)
            ->notEmptyString('summary_en')
            ->allowEmptyString('problem_cs')->allowEmptyString('problem_en')
            ->allowEmptyString('solution_cs')->allowEmptyString('solution_en')->inList('status', ['active','prototype','maintenance','archived']);
    }
}
