<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Database\Connection;
use InvalidArgumentException;

/** Append-only write receipt inside the SAME transaction as the ORM mutation. */
final class EditorialAuditTrail
{
    private function __construct()
    {
    }

    /** @param array<string,mixed> $values */
    public static function digest(array $values): string
    {
        ksort($values, SORT_STRING);
        return hash('sha256', json_encode($values, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    /** @param array<string,mixed> $values */
    public static function record(Connection $db, string $type, string $key, string $action, array $values): void
    {
        if (!in_array($type, ['project', 'journal'], true) ||
            !in_array($action, ['update', 'create', 'publish', 'unpublish'], true) ||
            preg_match('/^[A-Za-z0-9-]{1,100}$/D', $key) !== 1) {
            throw new InvalidArgumentException('Invalid editorial audit metadata');
        }
        $db->execute('INSERT INTO editorial_audit (entity_type,entity_key,action,content_digest,occurred_at)
            VALUES (:type,:key,:action,:digest,UTC_TIMESTAMP())', [
            'type' => $type, 'key' => $key, 'action' => $action, 'digest' => self::digest($values),
        ]);
    }
}
