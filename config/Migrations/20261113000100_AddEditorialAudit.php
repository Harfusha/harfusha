<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

/** Append-only mutation receipts; intentionally excludes private editorial text. */
final class AddEditorialAudit extends AbstractMigration
{
    public function change(): void
    {
        $this->table('github_activity')->addIndex(['repository'])->update();
        $this->table('editorial_audit')
            ->addColumn('entity_type', 'string', ['limit' => 32])
            ->addColumn('entity_key', 'string', ['limit' => 100])
            ->addColumn('action', 'string', ['limit' => 32])
            ->addColumn('content_digest', 'string', ['limit' => 64])
            ->addColumn('occurred_at', 'datetime')
            ->addIndex(['occurred_at'])
            ->addIndex(['entity_type', 'entity_key', 'occurred_at'])
            ->create();
    }
}
