<?php
declare(strict_types=1);

use App\Domain\RequestLifecycleLab;
use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
$lifecycleScenarios = RequestLifecycleLab::scenarios();
?>
<section class="shell lab-interactive-section" id="request-lifecycle-lab" aria-labelledby="lifecycle-lab-title">
    <div class="section-heading">
        <div>
            <span class="eyebrow"><?= $lang->value('INTERAKTIVNÍ EXPERIMENT · ARCHITEKTURA SSR', 'INTERACTIVE LAB · SSR ARCHITECTURE') ?></span>
            <h2 id="lifecycle-lab-title"><?= $lang->value('PHP Request Lifecycle Lab', 'PHP Request Lifecycle Lab') ?></h2>
            <p><?= $lang->value('Prozkoumej reálnou trasu požadavku v CakePHP 5 od reverzní proxy a bezpečnostního middleware přes striktní směrování až po doménové DTO a podmíněnou ETag mezipaměť. Všechna data vycházejí z testovaných scénářů aplikace.', 'Inspect the real CakePHP 5 request trace from reverse proxy and security middleware through strict routing to domain DTOs and conditional ETag cache. All data derives from verified application scenarios.') ?></p>
        </div>
    </div>

    <div class="lab-shell lifecycle-lab-shell" data-lifecycle-lab data-lang="<?= Html::escape($lang->code) ?>">
        <div class="lab-toolbar">
            <div class="lab-tabs" role="tablist" aria-label="<?= $lang->value('Výběr scénáře požadavku', 'Request scenario selection') ?>">
                <?php foreach ($lifecycleScenarios as $i => $sc): ?>
                <button type="button" role="tab" class="tab<?= $i === 0 ? ' active' : '' ?>" id="tab-lifecycle-<?= Html::escape($sc['id']) ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="panel-lifecycle-<?= Html::escape($sc['id']) ?>" data-lifecycle-tab="<?= Html::escape($sc['id']) ?>">
                    <span class="tab-method method-<?= strtolower($sc['method']) ?>"><?= Html::escape($sc['method']) ?></span>
                    <span class="tab-slug"><?= Html::escape(explode(' ', $sc['title'][$lang->code])[0]) ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="lifecycle-panels">
            <?php foreach ($lifecycleScenarios as $i => $sc): ?>
            <div role="tabpanel" id="panel-lifecycle-<?= Html::escape($sc['id']) ?>" aria-labelledby="tab-lifecycle-<?= Html::escape($sc['id']) ?>" class="lifecycle-panel<?= $i === 0 ? ' active' : '' ?>" data-lifecycle-panel="<?= Html::escape($sc['id']) ?>"<?= $i !== 0 ? ' hidden' : '' ?>>
                <div class="lifecycle-scenario-header">
                    <div>
                        <h3><?= Html::escape($sc['title'][$lang->code]) ?></h3>
                        <p class="lifecycle-desc"><?= Html::escape($sc['description'][$lang->code]) ?></p>
                    </div>
                    <div class="lifecycle-status-badge status-<?= $sc['status'] ?>">
                        <span class="status-code"><?= $sc['status'] ?></span>
                        <span class="status-text"><?= Html::escape($sc['status_text']) ?></span>
                    </div>
                </div>

                <div class="lifecycle-pipeline-visual" role="region" aria-label="<?= $lang->value('Diagram kroků zpracování požadavku', 'Request processing pipeline diagram') ?>" tabindex="0">
                    <ol class="pipeline-track">
                        <?php foreach ($sc['pipeline'] as $stepIdx => $step): ?>
                        <li class="pipeline-step" data-step-index="<?= $stepIdx ?>">
                            <span class="step-num"><?= sprintf('%02d', $stepIdx + 1) ?></span>
                            <div class="step-card">
                                <span class="step-stage"><?= Html::escape(substr($step['stage'], 3)) ?></span>
                                <h4><?= Html::escape($step['title'][$lang->code]) ?></h4>
                                <code class="step-component"><?= Html::escape($step['component']) ?></code>
                                <p class="step-detail"><?= Html::escape($step['detail'][$lang->code]) ?></p>
                                <span class="step-outcome"><?= Html::escape($step['outcome']) ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </div>

                <div class="lifecycle-response-details">
                    <div class="response-headers-block">
                        <span class="eyebrow"><?= $lang->value('VYBRANÉ HTTP HLAVIČKY ODPOVĚDI', 'SELECTED HTTP RESPONSE HEADERS') ?></span>
                        <dl class="header-list">
                            <?php foreach ($sc['headers'] as $headerName => $headerVal): ?>
                            <div>
                                <dt><?= Html::escape($headerName) ?></dt>
                                <dd><code><?= Html::escape($headerVal) ?></code></dd>
                            </div>
                            <?php endforeach; ?>
                        </dl>
                    </div>
                    <div class="response-meta-block">
                        <span class="eyebrow"><?= $lang->value('REFERENČNÍ KÓD V REPOZITÁŘI', 'REFERENCE CODE IN REPOSITORY') ?></span>
                        <p class="code-ref-link">
                            <a class="inline-link" href="https://github.com/Harfusha/harfusha/blob/master/<?= Html::escape(explode('#', $sc['code_ref'])[0]) ?>" target="_blank" rel="noopener noreferrer">
                                <code><?= Html::escape($sc['code_ref']) ?></code> ↗
                            </a>
                        </p>
                        <p class="muted note-text"><?= $lang->value('Model vychází z integračních testů ControllerTest a HostGuardMiddlewareTest.', 'Model corresponds to integration tests in ControllerTest and HostGuardMiddlewareTest.') ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <noscript>
            <div class="lab-fallback">
                <h3><?= $lang->value('Statický textový přehled scénářů požadavků', 'Text-only request scenarios overview') ?></h3>
                <?php foreach ($lifecycleScenarios as $sc): ?>
                <h4><?= Html::escape($sc['title'][$lang->code]) ?> — <?= $sc['status'] ?> <?= Html::escape($sc['status_text']) ?></h4>
                <p><?= Html::escape($sc['description'][$lang->code]) ?></p>
                <ol>
                    <?php foreach ($sc['pipeline'] as $step): ?>
                    <li><strong><?= Html::escape($step['title'][$lang->code]) ?></strong> (<code><?= Html::escape($step['component']) ?></code>): <?= Html::escape($step['detail'][$lang->code]) ?> → <em><?= Html::escape($step['outcome']) ?></em></li>
                    <?php endforeach; ?>
                </ol>
                <?php endforeach; ?>
            </div>
        </noscript>
    </div>
</section>
