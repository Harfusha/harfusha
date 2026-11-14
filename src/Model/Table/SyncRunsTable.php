<?php
declare(strict_types=1);
namespace App\Model\Table;
use Cake\ORM\Table;
final class SyncRunsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('sync_runs');
        $this->setPrimaryKey('id');
    }
}
