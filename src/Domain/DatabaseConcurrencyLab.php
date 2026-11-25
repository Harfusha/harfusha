<?php
declare(strict_types=1);

namespace App\Domain;

/**
 * Deterministic domain model, state machine, and fixtures for the Database Concurrency Lab.
 * Explains lost updates, optimistic locking, atomic row locks, and idempotency guarantees.
 */
final class DatabaseConcurrencyLab
{
    /**
     * Pure function simulating an unprotected read-modify-write resulting in a lost update.
     *
     * @return array{
     *     initial_value: int,
     *     final_value: int,
     *     expected_value: int,
     *     lost_update_occurred: bool,
     *     client_a_increment: int,
     *     client_b_increment: int
     * }
     */
    public static function simulateLostUpdate(int $initial = 100, int $incA = 10, int $incB = 10): array
    {
        // Step 1 & 2: Both clients read initial state
        $readA = $initial;
        $readB = $initial;

        // Step 3: Client A writes new value
        $writtenA = $readA + $incA; // 110
        $dbState = $writtenA;

        // Step 4: Client B writes based on stale read
        $writtenB = $readB + $incB; // 110 (overwrites Client A!)
        $dbState = $writtenB;

        return [
            'initial_value' => $initial,
            'final_value' => $dbState,
            'expected_value' => $initial + $incA + $incB,
            'lost_update_occurred' => $dbState !== ($initial + $incA + $incB),
            'client_a_increment' => $incA,
            'client_b_increment' => $incB,
        ];
    }

    /**
     * Attempt an optimistic write against a versioned row. Returns false on version conflict.
     */
    public static function commitOptimistic(int &$dbVal, int &$dbVersion, int $expectedVersion, int $delta): bool
    {
        if ($dbVersion !== $expectedVersion) {
            return false;
        }
        $dbVal += $delta;
        $dbVersion++;

        return true;
    }

    /**
     * Pure function simulating optimistic locking with version check and conflict retry.
     *
     * @return array{
     *     initial_value: int,
     *     final_value: int,
     *     final_version: int,
     *     conflicts_handled: int,
     *     retries: int,
     *     success: bool
     * }
     */
    public static function simulateOptimisticLocking(int $initial = 100, int $incA = 10, int $incB = 10): array
    {
        $dbVal = $initial;
        $dbVersion = 1;
        $conflicts = 0;
        $retries = 0;

        // Both clients read initial version
        $clientAVersion = $dbVersion;
        $clientBVersion = $dbVersion;

        // Client A commits first
        self::commitOptimistic($dbVal, $dbVersion, $clientAVersion, $incA);

        // Client B tries to commit with stale version -> fails with conflict
        $bCommitted = self::commitOptimistic($dbVal, $dbVersion, $clientBVersion, $incB);
        if (!$bCommitted) {
            $conflicts++;
            $retries++;
            // Client B refetches current state and retries
            $clientBVersion = $dbVersion;
            self::commitOptimistic($dbVal, $dbVersion, $clientBVersion, $incB);
        }

        return [
            'initial_value' => $initial,
            'final_value' => $dbVal,
            'final_version' => $dbVersion,
            'conflicts_handled' => $conflicts,
            'retries' => $retries,
            'success' => $dbVal === ($initial + $incA + $incB),
        ];
    }

    /**
     * Pure function simulating database atomic increment via single-statement row lock.
     *
     * @return array{
     *     initial_value: int,
     *     final_value: int,
     *     expected_value: int,
     *     success: bool
     * }
     */
    public static function simulateAtomicIncrement(int $initial = 100, int $incA = 10, int $incB = 10): array
    {
        $dbVal = $initial;

        // InnoDB row lock serializes operations:
        // Client A executes UPDATE ... SET val = val + :incA
        $dbVal += $incA;

        // Client B executes UPDATE ... SET val = val + :incB
        $dbVal += $incB;

        return [
            'initial_value' => $initial,
            'final_value' => $dbVal,
            'expected_value' => $initial + $incA + $incB,
            'success' => $dbVal === ($initial + $incA + $incB),
        ];
    }

    /**
     * Pure function simulating request idempotency with stored receipt tokens.
     *
     * @param list<string> $requestKeys
     * @return array{
     *     initial_value: int,
     *     final_value: int,
     *     total_requests: int,
     *     mutations_executed: int,
     *     replayed_cached_responses: int
     * }
     */
    public static function simulateIdempotency(int $initial, array $requestKeys, int $increment = 10): array
    {
        $dbVal = $initial;
        $processed = [];
        $mutations = 0;
        $replays = 0;

        foreach ($requestKeys as $key) {
            if (isset($processed[$key])) {
                // Key already seen: return cached receipt without mutation
                $replays++;
                continue;
            }

            // New key: execute mutation and record receipt
            $dbVal += $increment;
            $processed[$key] = [
                'status' => 200,
                'result_value' => $dbVal,
            ];
            $mutations++;
        }

        return [
            'initial_value' => $initial,
            'final_value' => $dbVal,
            'total_requests' => count($requestKeys),
            'mutations_executed' => $mutations,
            'replayed_cached_responses' => $replays,
        ];
    }

    /**
     * Complete scenario definitions with timeline steps and SQL snippets for UI.
     *
     * @return list<array{
     *     id: string,
     *     title: array{cs: string, en: string},
     *     badge: string,
     *     description: array{cs: string, en: string},
     *     initial_state: array{val: int, version?: int},
     *     final_state: array{val: int, version?: int, result: string},
     *     isolation_note: array{cs: string, en: string},
     *     steps: list<array{
     *         step_number: int,
     *         actor: string,
     *         action: array{cs: string, en: string},
     *         sql: string,
     *         db_state: string,
     *         client_a_state: string,
     *         client_b_state: string,
     *         status: string
     *     }>
     * }>
     */
    public static function scenarios(): array
    {
        return [
            [
                'id' => 'lost-update',
                'title' => [
                    'cs' => 'Bez ochrany: Ztracená aktualizace (Lost Update)',
                    'en' => 'Unprotected: Lost Update',
                ],
                'badge' => 'ANTI-PATTERN',
                'description' => [
                    'cs' => 'Klasický problém souběhu při odděleném čtení a zápisu (Read-Modify-Write). Klient A i B současně načtou hodnotu 100 a přičtou 10. Klient B přepíše změnu klienta A; výsledek je 110 namísto očekávaných 120.',
                    'en' => 'Classic race condition in decoupled Read-Modify-Write. Both Client A and B read value 100 and add 10. Client B silently overwrites Client A; the final state is 110 instead of the expected 120.',
                ],
                'initial_state' => ['val' => 100],
                'final_state' => ['val' => 110, 'result' => 'LOST UPDATE (10 lost)'],
                'isolation_note' => [
                    'cs' => 'Standardní úroveň REPEATABLE READ nebo READ COMMITTED v MySQL nezabrání přepsání řádku bez explicitního zamykání (FOR UPDATE) nebo verze.',
                    'en' => 'Standard REPEATABLE READ or READ COMMITTED in MySQL does not prevent lost updates without explicit row locking (FOR UPDATE) or version check.',
                ],
                'steps' => [
                    [
                        'step_number' => 1,
                        'actor' => 'Klient A',
                        'action' => [
                            'cs' => 'Klient A načte aktuální stav z databáze.',
                            'en' => 'Client A reads the current row from the database.',
                        ],
                        'sql' => 'SELECT val FROM counters WHERE id = 1;',
                        'db_state' => 'val: 100',
                        'client_a_state' => 'read: 100',
                        'client_b_state' => 'idle',
                        'status' => 'ok',
                    ],
                    [
                        'step_number' => 2,
                        'actor' => 'Klient B',
                        'action' => [
                            'cs' => 'Klient B souběžně načte tentýž řádek předtím, než Klient A stihne zapsat.',
                            'en' => 'Client B concurrently reads the exact same row before Client A commits.',
                        ],
                        'sql' => 'SELECT val FROM counters WHERE id = 1;',
                        'db_state' => 'val: 100',
                        'client_a_state' => 'read: 100',
                        'client_b_state' => 'read: 100',
                        'status' => 'stale_read',
                    ],
                    [
                        'step_number' => 3,
                        'actor' => 'Klient A',
                        'action' => [
                            'cs' => 'Klient A spočítá 100 + 10 = 110 a provede zápis.',
                            'en' => 'Client A computes 100 + 10 = 110 and updates the row.',
                        ],
                        'sql' => 'UPDATE counters SET val = 110 WHERE id = 1;',
                        'db_state' => 'val: 110',
                        'client_a_state' => 'committed: 110',
                        'client_b_state' => 'stale: 100',
                        'status' => 'committed',
                    ],
                    [
                        'step_number' => 4,
                        'actor' => 'Klient B',
                        'action' => [
                            'cs' => 'Klient B spočítá 100 + 10 = 110 a zapíše svou hodnotu. Změna klienta A je nenávratně přepsána!',
                            'en' => 'Client B computes 100 + 10 = 110 and writes. Client A\'s update is silently overwritten!',
                        ],
                        'sql' => 'UPDATE counters SET val = 110 WHERE id = 1;',
                        'db_state' => 'val: 110 (Chyba!)',
                        'client_a_state' => 'lost!',
                        'client_b_state' => 'committed: 110',
                        'status' => 'data_loss',
                    ],
                ],
            ],
            [
                'id' => 'optimistic-locking',
                'title' => [
                    'cs' => 'Optimistické zamykání: Kontrola verze (Optimistic Locking)',
                    'en' => 'Optimistic Locking: Version Token Guard',
                ],
                'badge' => 'RECOMMENDED',
                'description' => [
                    'cs' => 'Řádek nese verzi nebo revizní token (jako revision_token v administraci Harfusha Lab). Pokud se verze v DB změnila, zápis selže s 409 Conflict. Klient načte čerstvý stav a zopakuje operaci bez ztráty dat.',
                    'en' => 'The row contains a version token (like revision_token in Harfusha Lab admin). If the DB version changed, the update fails with 409 Conflict. The client refetches current state and retries safely.',
                ],
                'initial_state' => ['val' => 100, 'version' => 1],
                'final_state' => ['val' => 120, 'version' => 3, 'result' => 'CONSISTENT (120, v3)'],
                'isolation_note' => [
                    'cs' => 'Vhodné pro převážně čtecí scénáře a editace přes webové rozhraní. Nezatěžuje databázi dlouhotrvajícími zámky.',
                    'en' => 'Ideal for read-heavy scenarios and web editorial forms. Avoids holding long-lived database locks.',
                ],
                'steps' => [
                    [
                        'step_number' => 1,
                        'actor' => 'Klient A & B',
                        'action' => [
                            'cs' => 'Oba klienti načtou hodnotu 100 a verzi v1.',
                            'en' => 'Both clients read value 100 and version token v1.',
                        ],
                        'sql' => 'SELECT val, version FROM counters WHERE id = 1;',
                        'db_state' => 'val: 100, ver: 1',
                        'client_a_state' => 'val: 100, ver: 1',
                        'client_b_state' => 'val: 100, ver: 1',
                        'status' => 'ok',
                    ],
                    [
                        'step_number' => 2,
                        'actor' => 'Klient A',
                        'action' => [
                            'cs' => 'Klient A odešle update s podmínkou version = 1. Podmínka sedí, verze se zvýší na 2.',
                            'en' => 'Client A submits update conditioned on version = 1. Condition matches, version advances to 2.',
                        ],
                        'sql' => 'UPDATE counters SET val = 110, version = 2 WHERE id = 1 AND version = 1;',
                        'db_state' => 'val: 110, ver: 2',
                        'client_a_state' => 'committed (200 OK)',
                        'client_b_state' => 'stale: ver 1',
                        'status' => 'committed',
                    ],
                    [
                        'step_number' => 3,
                        'actor' => 'Klient B',
                        'action' => [
                            'cs' => 'Klient B odešle update s version = 1. Databáze vrátí 0 ovlivněných řádků (konflikt!). Aplikace vrací HTTP 409 Conflict.',
                            'en' => 'Client B submits update with version = 1. DB reports 0 rows affected (conflict!). App returns HTTP 409 Conflict.',
                        ],
                        'sql' => 'UPDATE counters SET val = 110, version = 2 WHERE id = 1 AND version = 1;',
                        'db_state' => 'val: 110, ver: 2',
                        'client_a_state' => 'done',
                        'client_b_state' => 'conflict! (0 rows affected)',
                        'status' => 'conflict_detected',
                    ],
                    [
                        'step_number' => 4,
                        'actor' => 'Klient B',
                        'action' => [
                            'cs' => 'Klient B načte aktuální stav (110, ver: 2), přičte 10 a zapíše s podmínkou version = 2. Uspěje!',
                            'en' => 'Client B refetches current state (110, ver: 2), adds 10, and updates with version = 2. Success!',
                        ],
                        'sql' => 'UPDATE counters SET val = 120, version = 3 WHERE id = 1 AND version = 2;',
                        'db_state' => 'val: 120, ver: 3',
                        'client_a_state' => 'done',
                        'client_b_state' => 'retry committed (200 OK)',
                        'status' => 'recovered',
                    ],
                ],
            ],
            [
                'id' => 'atomic-increment',
                'title' => [
                    'cs' => 'Atomický inkrement: Relační řádkový zámek (Atomic SQL)',
                    'en' => 'Atomic Increment: Row Lock Serialization',
                ],
                'badge' => 'HIGH-THROUGHPUT',
                'description' => [
                    'cs' => 'Přenesení inkrementu přímo do SQL příkazu (UPDATE counters SET val = val + 10). InnoDB serializuje exkluzivní řádkové zámky (X-locks). Oba inkrementy proběhnou bez aplikačního retry.',
                    'en' => 'Delegating the increment directly to SQL (UPDATE counters SET val = val + 10). InnoDB serializes exclusive row locks (X-locks). Both increments succeed without application-level retry.',
                ],
                'initial_state' => ['val' => 100],
                'final_state' => ['val' => 120, 'result' => 'CONSISTENT (120)'],
                'isolation_note' => [
                    'cs' => 'Ideální pro čítače, skladové zásoby nebo kvóty. Omezení: nelze před zápisem vyhodnotit složitá aplikační doménová pravidla.',
                    'en' => 'Ideal for counters, inventory balances, and quotas. Trade-off: cannot evaluate complex application domain invariants prior to commit.',
                ],
                'steps' => [
                    [
                        'step_number' => 1,
                        'actor' => 'Klient A & B',
                        'action' => [
                            'cs' => 'Oba klienti současně odešlou atomický SQL příkaz UPDATE counters SET val = val + 10.',
                            'en' => 'Both clients concurrently issue atomic SQL statement UPDATE counters SET val = val + 10.',
                        ],
                        'sql' => 'UPDATE counters SET val = val + 10 WHERE id = 1;',
                        'db_state' => 'val: 100',
                        'client_a_state' => 'waiting on X-lock',
                        'client_b_state' => 'waiting on X-lock',
                        'status' => 'lock_wait',
                    ],
                    [
                        'step_number' => 2,
                        'actor' => 'Klient A',
                        'action' => [
                            'cs' => 'InnoDB lock manager přidělí zámek řádku Klientu A. Hodnota se zvýší na 110. Klient A commituje a zámek uvolní.',
                            'en' => 'InnoDB lock manager grants row lock to Client A. Value increments to 110. Client A commits and releases lock.',
                        ],
                        'sql' => 'COMMIT;',
                        'db_state' => 'val: 110',
                        'client_a_state' => 'committed: 110',
                        'client_b_state' => 'acquiring row lock...',
                        'status' => 'committed',
                    ],
                    [
                        'step_number' => 3,
                        'actor' => 'Klient B',
                        'action' => [
                            'cs' => 'Klient B získává uvolněný zámek, přečte aktuálních 110 a přičte 10. Výsledek je 120 bez jediné ztráty.',
                            'en' => 'Client B acquires the released lock, reads committed 110, and adds 10. Result is 120 with zero data loss.',
                        ],
                        'sql' => 'COMMIT;',
                        'db_state' => 'val: 120',
                        'client_a_state' => 'done',
                        'client_b_state' => 'committed: 120',
                        'status' => 'success',
                    ],
                ],
            ],
            [
                'id' => 'idempotency-token',
                'title' => [
                    'cs' => 'Idempotence: Ochrana před opakovaným požadavkem (Replay Safety)',
                    'en' => 'Idempotency: Replay Safety via Unique Keys',
                ],
                'badge' => 'API GUARANTEE',
                'description' => [
                    'cs' => 'Při výpadku sítě nebo opakovaném odeslání požadavku klientem chrání databázi jedinečný Idempotency-Key. Druhý požadavek vrátí uloženou odpověď bez opakovaného zvýšení stavu.',
                    'en' => 'When network timeouts cause client retries, a unique Idempotency-Key protects against duplicate mutations. The second request returns the cached receipt without altering state.',
                ],
                'initial_state' => ['val' => 100],
                'final_state' => ['val' => 110, 'result' => 'MUTATION EXECUTED EXACTLY ONCE'],
                'isolation_note' => [
                    'cs' => 'Základní požadavek pro platební brány, transakční API a integrace webhooků.',
                    'en' => 'Fundamental requirement for payment gateways, transactional APIs, and webhook processing.',
                ],
                'steps' => [
                    [
                        'step_number' => 1,
                        'actor' => 'Klient',
                        'action' => [
                            'cs' => 'Klient odešle požadavek s hlavičkou Idempotency-Key: req_9f3a1.',
                            'en' => 'Client sends request with header Idempotency-Key: req_9f3a1.',
                        ],
                        'sql' => 'SELECT status, receipt FROM idempotency_keys WHERE key = "req_9f3a1";',
                        'db_state' => 'val: 100, key not found',
                        'client_a_state' => 'POST req_9f3a1',
                        'client_b_state' => 'idle',
                        'status' => 'processing',
                    ],
                    [
                        'step_number' => 2,
                        'actor' => 'Server & DB',
                        'action' => [
                            'cs' => 'V rámci jedné transakce server zvýší hodnotu na 110 a uloží potvrzení s klíčem req_9f3a1.',
                            'en' => 'Within a single transaction, server increments value to 110 and inserts receipt for req_9f3a1.',
                        ],
                        'sql' => 'INSERT INTO idempotency_keys (key, status, receipt) VALUES ("req_9f3a1", 200, "{\"val\":110}");',
                        'db_state' => 'val: 110, key saved',
                        'client_a_state' => 'network timeout / packet drop!',
                        'client_b_state' => 'idle',
                        'status' => 'committed',
                    ],
                    [
                        'step_number' => 3,
                        'actor' => 'Klient',
                        'action' => [
                            'cs' => 'Klient se domnívá, že požadavek selhal, a pošle ho znovu se STEJNÝM Idempotency-Key: req_9f3a1.',
                            'en' => 'Client suspects failure and retries with the IDENTICAL Idempotency-Key: req_9f3a1.',
                        ],
                        'sql' => 'SELECT status, receipt FROM idempotency_keys WHERE key = "req_9f3a1";',
                        'db_state' => 'val: 110, key found!',
                        'client_a_state' => 'RETRY req_9f3a1',
                        'client_b_state' => 'idle',
                        'status' => 'cache_hit',
                    ],
                    [
                        'step_number' => 4,
                        'actor' => 'Server',
                        'action' => [
                            'cs' => 'Server nalezne existující záznam a okamžitě vrátí uložené potvrzení. Hodnota v DB zůstává 110 (žádný dvojitý zásek!).',
                            'en' => 'Server finds existing record and immediately returns stored receipt. DB value remains 110 (zero double charge!).',
                        ],
                        'sql' => '-- No mutation executed. Returning cached receipt.',
                        'db_state' => 'val: 110 (Consistent)',
                        'client_a_state' => '200 OK (from idempotent cache)',
                        'client_b_state' => 'idle',
                        'status' => 'idempotent_success',
                    ],
                ],
            ],
        ];
    }
}
