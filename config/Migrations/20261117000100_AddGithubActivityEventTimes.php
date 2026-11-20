<?php
declare(strict_types=1);

use Migrations\BaseMigration;

final class AddGithubActivityEventTimes extends BaseMigration
{
    public function change(): void
    {
        $this->table('github_activity')
            ->addColumn('merged_at', 'datetime', ['null' => true])
            ->addColumn('closed_at', 'datetime', ['null' => true])
            ->update();
    }
}
