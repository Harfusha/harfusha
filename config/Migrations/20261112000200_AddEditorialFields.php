<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/** Add prose fields without invalidating an installation of the first release. */
final class AddEditorialFields extends BaseMigration
{
    public function change(): void
    {
        $this->table('project_overrides')
            ->addColumn('problem_cs', 'text', ['null' => true])
            ->addColumn('problem_en', 'text', ['null' => true])
            ->addColumn('solution_cs', 'text', ['null' => true])
            ->addColumn('solution_en', 'text', ['null' => true])
            ->update();
    }
}
