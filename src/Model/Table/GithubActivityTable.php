<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

final class GithubActivityTable extends Table
{
    /**
     * Configure the CakePHP table schema and persistence metadata.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('github_activity');
        $this->setPrimaryKey('source_url');
    }
}
