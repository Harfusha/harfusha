<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

final class SyncRunsTable extends Table
{
    /**
     * Configure the CakePHP table schema and persistence metadata.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('sync_runs');
        $this->setPrimaryKey('id');
    }
}
