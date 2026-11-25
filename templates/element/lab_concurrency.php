<?php
declare(strict_types=1);

use App\Domain\DatabaseConcurrencyLab;
use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
$concurrencyScenarios = DatabaseConcurrencyLab::scenarios();
?>
<section class="shell lab-interactive-section" id="database-concurrency-lab" aria-labelledby="concurrency-lab-title">
    <div class="section-heading">
        <div>
            <span class="eyebrow"><?= $lang->value('INTERAKTIVNÍ EXPERIMENT · INTEGRITA DAT', 'INTERACTIVE LAB · DATA INTEGRITY') ?></span>
            <h2 id="concurrency-lab-title"><?= $lang->value('Database Concurrency Lab', 'Database Concurrency Lab') ?></h2>
            <p><?= $lang->value('Prozkoumej chování souběžných transakcí v relační databázi. Simulátor krok za krokem vysvětluje ztracenou aktualizaci (lost update), optimistické zamykání přes revizní tokeny, atomické řádkové zámky v InnoDB a garance idempotence požadavků.', 'Explore concurrent transaction behavior in a relational database. This step-by-step simulator demonstrates lost updates, optimistic locking with version tokens, InnoDB atomic row locks, and request idempotency guarantees.') ?></p>
        </div>
    </div>

    <div class="lab-shell concurrency-lab-shell" data-concurrency-lab data-lang="<?= Html::escape($lang->code) ?>">
        <div class="lab-toolbar">
            <div class="lab-tabs" role="tablist" aria-label="<?= $lang->value('Výběr scénáře souběhu', 'Concurrency scenario selection') ?>">
                <?php foreach ($concurrencyScenarios as $i => $sc): ?>
                <button type="button" role="tab" class="tab<?= $i === 0 ? ' active' : '' ?>" id="tab-concurrency-<?= Html::escape($sc['id']) ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="panel-concurrency-<?= Html::escape($sc['id']) ?>" data-concurrency-tab="<?= Html::escape($sc['id']) ?>">
                    <span class="tab-badge badge-<?= strtolower(str_replace(['-', ' '], '_', $sc['badge'])) ?>"><?= Html::escape($sc['badge']) ?></span>
                    <span class="tab-name"><?= Html::escape(explode(':', $sc['title'][$lang->code])[0]) ?></span>
                </button>
                <?php endforeach; ?>
            </div>
            <div class="lab-toolbar-right">
                <button class="mini-button" type="button" data-concurrency-step-prev disabled aria-label="<?= $lang->value('Předchozí krok', 'Previous step') ?>">← <?= $lang->value('Zpět', 'Back') ?></button>
                <button class="mini-button btn-primary" type="button" data-concurrency-step-next aria-label="<?= $lang->value('Další krok', 'Next step') ?>"><?= $lang->value('Další krok', 'Next step') ?> →</button>
                <button class="mini-button" type="button" data-concurrency-reset aria-label="<?= $lang->value('Resetovat simulaci na začátek', 'Reset simulation to start') ?>"><?= $lang->value('Reset', 'Reset') ?></button>
            </div>
        </div>

        <div class="concurrency-panels">
            <?php foreach ($concurrencyScenarios as $i => $sc): ?>
            <div role="tabpanel" id="panel-concurrency-<?= Html::escape($sc['id']) ?>" aria-labelledby="tab-concurrency-<?= Html::escape($sc['id']) ?>" class="concurrency-panel<?= $i === 0 ? ' active' : '' ?>" data-concurrency-panel="<?= Html::escape($sc['id']) ?>"<?= $i !== 0 ? ' hidden' : '' ?>>
                <div class="concurrency-intro">
                    <div>
                        <h3><?= Html::escape($sc['title'][$lang->code]) ?></h3>
                        <p><?= Html::escape($sc['description'][$lang->code]) ?></p>
                    </div>
                    <div class="concurrency-result-preview">
                        <span class="result-label"><?= $lang->value('Konečný výsledek:', 'Final outcome:') ?></span>
                        <strong class="result-val"><?= Html::escape($sc['final_state']['result']) ?></strong>
                    </div>
                </div>

                <div class="concurrency-stepper-visual" data-step-container>
                    <div class="step-progress-indicator" role="status" aria-live="polite">
                        <span><?= $lang->value('Krok', 'Step') ?> <strong data-current-step-num>1</strong> <?= $lang->value('z', 'of') ?> <?= count($sc['steps']) ?></span>
                    </div>

                    <div class="step-actors-grid">
                        <div class="actor-card client-a">
                            <span class="actor-badge">Klient A</span>
                            <div class="actor-state" data-actor-a-state><?= Html::escape($sc['steps'][0]['client_a_state']) ?></div>
                        </div>
                        <div class="actor-card db-engine">
                            <span class="actor-badge">MySQL / MariaDB</span>
                            <div class="actor-state db-state-box" data-db-state><?= Html::escape($sc['steps'][0]['db_state']) ?></div>
                        </div>
                        <div class="actor-card client-b">
                            <span class="actor-badge">Klient B</span>
                            <div class="actor-state" data-actor-b-state><?= Html::escape($sc['steps'][0]['client_b_state']) ?></div>
                        </div>
                    </div>

                    <div class="step-action-display">
                        <div class="step-action-text" data-action-text>
                            <strong><?= Html::escape($sc['steps'][0]['actor']) ?>:</strong>
                            <span><?= Html::escape($sc['steps'][0]['action'][$lang->code]) ?></span>
                        </div>
                        <div class="step-sql-snippet">
                            <div class="code-header">
                                <span>SQL</span>
                                <span class="sql-status" data-sql-status><?= Html::escape($sc['steps'][0]['status']) ?></span>
                            </div>
                            <pre class="code-content" role="region" tabindex="0" aria-label="<?= sprintf($lang->value('SQL dotaz pro scénář %s', 'SQL query for scenario %s'), $sc['id']) ?>"><code data-sql-code><?= Html::escape($sc['steps'][0]['sql']) ?></code></pre>
                        </div>
                    </div>

                    <ol class="step-timeline-markers" role="list" aria-label="<?= $lang->value('Časová osa kroků', 'Steps timeline') ?>">
                        <?php foreach ($sc['steps'] as $sIdx => $step): ?>
                        <li class="marker-item<?= $sIdx === 0 ? ' current' : '' ?>" data-marker-index="<?= $sIdx ?>">
                            <button type="button" class="marker-btn" data-step-jump="<?= $sIdx ?>" aria-label="<?= sprintf($lang->value('Skočit na krok %d: %s', 'Jump to step %d: %s'), $step['step_number'], $step['actor']) ?>">
                                <?= $step['step_number'] ?>
                            </button>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </div>

                <div class="concurrency-isolation-note">
                    <strong><?= $lang->value('Architektonická poznámka:', 'Architectural note:') ?></strong>
                    <p><?= Html::escape($sc['isolation_note'][$lang->code]) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <script type="application/json" id="concurrency-lab-data"><?= Html::json($concurrencyScenarios) ?></script>

        <noscript>
            <div class="lab-fallback">
                <h3><?= $lang->value('Statický přehled scénářů souběžnosti', 'Text-only concurrency scenarios overview') ?></h3>
                <?php foreach ($concurrencyScenarios as $sc): ?>
                <h4><?= Html::escape($sc['title'][$lang->code]) ?> — <?= Html::escape($sc['final_state']['result']) ?></h4>
                <p><?= Html::escape($sc['description'][$lang->code]) ?></p>
                <table class="table-fallback">
                    <thead>
                        <tr>
                            <th><?= $lang->value('Krok', 'Step') ?></th>
                            <th><?= $lang->value('Akter', 'Actor') ?></th>
                            <th><?= $lang->value('Akce a SQL', 'Action & SQL') ?></th>
                            <th><?= $lang->value('Stav DB', 'DB State') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sc['steps'] as $step): ?>
                        <tr>
                            <td><?= $step['step_number'] ?></td>
                            <td><?= Html::escape($step['actor']) ?></td>
                            <td><?= Html::escape($step['action'][$lang->code]) ?><br><code><?= Html::escape($step['sql']) ?></code></td>
                            <td><?= Html::escape($step['db_state']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p><em><?= Html::escape($sc['isolation_note'][$lang->code]) ?></em></p>
                <?php endforeach; ?>
            </div>
        </noscript>
    </div>
</section>
