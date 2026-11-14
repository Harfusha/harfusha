<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

final class GithubPublicReposTable extends Table
{
    /**
     * Configure the CakePHP table schema and persistence metadata.
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('github_public_repos');
        $this->setPrimaryKey('repository');
    }
}
