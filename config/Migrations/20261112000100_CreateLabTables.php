<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

final class CreateLabTables extends AbstractMigration
{
    public function change(): void
    {
        $this->table('project_overrides', ['id' => false, 'primary_key' => ['slug']])
            ->addColumn('slug', 'string', ['limit' => 80])
            ->addColumn('summary_cs', 'text')
            ->addColumn('summary_en', 'text')
            ->addColumn('status', 'string', ['limit' => 20])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->create();
        $this->table('journal_entries')
            ->addColumn('title_cs', 'string', ['limit' => 240])
            ->addColumn('title_en', 'string', ['limit' => 240])
            ->addColumn('body_cs', 'text')
            ->addColumn('body_en', 'text')
            ->addColumn('category', 'string', ['limit' => 40])
            ->addColumn('source_url', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('is_public', 'boolean', ['default' => true])
            ->addColumn('published_at', 'datetime')
            ->addIndex(['is_public','published_at'])
            ->create();
        $this->table('github_activity', ['id' => false, 'primary_key' => ['source_url']])
            ->addColumn('source_url', 'string', ['limit' => 255])
            ->addColumn('title', 'string', ['limit' => 250])
            ->addColumn('repository', 'string', ['limit' => 180])
            ->addColumn('kind', 'string', ['limit' => 10])
            ->addColumn('state', 'string', ['limit' => 10])
            ->addColumn('published_at', 'datetime')
            ->addIndex(['published_at'])
            ->addIndex(['kind', 'state', 'published_at'])
            ->create();
        $this->table('github_public_repos', ['id' => false, 'primary_key' => ['repository']])
            ->addColumn('repository', 'string', ['limit' => 180])
            ->addColumn('description', 'string', ['limit' => 350, 'default' => ''])
            ->addColumn('repo_url', 'string', ['limit' => 255])
            ->addColumn('pushed_at', 'datetime')
            ->addIndex(['pushed_at'])
            ->create();
        $this->table('sync_runs')
            ->addColumn('source', 'string', ['limit' => 40])
            ->addColumn('item_count', 'integer', ['signed' => false])
            ->addColumn('completed_at', 'datetime')
            ->addIndex(['source','completed_at'])
            ->create();
    }
}
