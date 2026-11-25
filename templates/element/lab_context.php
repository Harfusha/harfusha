<?php
declare(strict_types=1);

use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
?>
<section class="shell project-preview lab-shell" id="ai-context-playground" aria-labelledby="context-lab-title">
    <div class="lab-header">
        <div>
            <span class="eyebrow"><?= $lang->value('INTERAKTIVNÍ EXPERIMENT · LOCAL AI HUB', 'INTERACTIVE LAB · LOCAL AI HUB') ?></span>
            <h2 id="context-lab-title"><?= $lang->value('AI Context Playground: Porovnání strategií vytěžování kontextu', 'AI Context Playground: Retrieval Strategy Comparison') ?></h2>
            <p><?= $lang->value('Deterministické srovnání spotřeby tokenů, latence a přesnosti při různých způsobech předávání kontextu lokálnímu modelu. Založeno na syntetickém benchmarku nad repozitářem.', 'Deterministic comparison of token budgets, latency, and accuracy across context retrieval strategies for local LLMs. Derived from synthetic repository benchmark fixtures.') ?></p>
        </div>
        <div class="lab-badge-group">
            <span class="badge badge-neutral"><?= $lang->value('Offline benchmark fixture', 'Offline benchmark fixture') ?></span>
            <span class="badge badge-success"><?= $lang->value('Ground truth ověřeno', 'Ground truth verified') ?></span>
        </div>
    </div>

    <div class="context-lab-shell requires-js" data-context-lab>
        <div class="lab-scenario-selector" role="group" aria-label="<?= $lang->value('Scénáře vyhledávání v kódu', 'Code retrieval scenarios') ?>">
            <button type="button" class="tab-btn active" data-context-scenario="symbol-def" aria-pressed="true">
                <span class="tab-badge badge-neutral">01</span>
                <span><?= $lang->value('Definice třídy (LoginLimiter)', 'Class definition (LoginLimiter)') ?></span>
            </button>
            <button type="button" class="tab-btn" data-context-scenario="multi-ref" aria-pressed="false">
                <span class="tab-badge badge-neutral">02</span>
                <span><?= $lang->value('Křížové reference (TrustedProxy)', 'Cross-references (TrustedProxy)') ?></span>
            </button>
            <button type="button" class="tab-btn" data-context-scenario="ambiguous" aria-pressed="false">
                <span class="tab-badge badge-neutral">03</span>
                <span><?= $lang->value('Nejednoznačný dotaz (Chybějící symbol)', 'Ambiguous symbol (Missing)') ?></span>
            </button>
        </div>

        <div class="context-display-grid">
            <div class="context-strategy-card" data-strategy="whole-repo">
                <div class="strategy-header">
                    <h3><?= $lang->value('Celý repozitář (Brute force)', 'Whole Repository (Brute force)') ?></h3>
                    <span class="strategy-badge badge-error"><?= $lang->value('Neefektivní', 'Wasteful') ?></span>
                </div>
                <div class="metric-row">
                    <span class="metric-label"><?= $lang->value('Spotřeba tokenů:', 'Token budget:') ?></span>
                    <strong class="metric-val text-error" data-metric-tokens>45,200</strong>
                </div>
                <div class="metric-row">
                    <span class="metric-label"><?= $lang->value('Odhadovaná latence:', 'Est. latency:') ?></span>
                    <span class="metric-val" data-metric-latency>1,850 ms</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label"><?= $lang->value('Přesnost odpovědi:', 'Answer accuracy:') ?></span>
                    <span class="metric-val" data-metric-accuracy>45% (<?= $lang->value('Halucinace v šumu', 'Noise hallucinations') ?>)</span>
                </div>
                <p class="strategy-desc" data-strategy-desc>
                    <?= $lang->value('Předání všech souborů vyčerpá kontextové okno a zahltí model nepodstatným kódem.', 'Injecting all files exhausts the context window and drowns the model in irrelevant tokens.') ?>
                </p>
            </div>

            <div class="context-strategy-card" data-strategy="lexical">
                <div class="strategy-header">
                    <h3><?= $lang->value('Lexikální hledání (BM25 / Grep)', 'Lexical Search (BM25 / Grep)') ?></h3>
                    <span class="strategy-badge badge-warning"><?= $lang->value('Částečné', 'Partial') ?></span>
                </div>
                <div class="metric-row">
                    <span class="metric-label"><?= $lang->value('Spotřeba tokenů:', 'Token budget:') ?></span>
                    <strong class="metric-val text-warning" data-metric-tokens>3,400</strong>
                </div>
                <div class="metric-row">
                    <span class="metric-label"><?= $lang->value('Odhadovaná latence:', 'Est. latency:') ?></span>
                    <span class="metric-val" data-metric-latency>42 ms</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label"><?= $lang->value('Přesnost odpovědi:', 'Answer accuracy:') ?></span>
                    <span class="metric-val" data-metric-accuracy>72% (<?= $lang->value('Zahrnuje komentáře a shody', 'Includes comments & strings') ?>)</span>
                </div>
                <p class="strategy-desc" data-strategy-desc>
                    <?= $lang->value('Hledá textové řetězce, ale nerozlišuje definici symbolu od výskytu v komentářích.', 'Matches raw substrings without distinguishing symbol definitions from docblocks.') ?>
                </p>
            </div>

            <div class="context-strategy-card card-featured" data-strategy="ast-index">
                <div class="strategy-header">
                    <h3><?= $lang->value('Index symbolů (Local AI Hub AST)', 'Symbol Index (Local AI Hub AST)') ?></h3>
                    <span class="strategy-badge badge-success"><?= $lang->value('Optimální', 'Optimal') ?></span>
                </div>
                <div class="metric-row">
                    <span class="metric-label"><?= $lang->value('Spotřeba tokenů:', 'Token budget:') ?></span>
                    <strong class="metric-val text-success" data-metric-tokens>340</strong>
                </div>
                <div class="metric-row">
                    <span class="metric-label"><?= $lang->value('Odhadovaná latence:', 'Est. latency:') ?></span>
                    <span class="metric-val" data-metric-latency>8 ms</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label"><?= $lang->value('Přesnost odpovědi:', 'Answer accuracy:') ?></span>
                    <span class="metric-val" data-metric-accuracy>99% (<?= $lang->value('Přesný řez AST stromem', 'Exact AST slice') ?>)</span>
                </div>
                <p class="strategy-desc" data-strategy-desc>
                    <?= $lang->value('Okamžitý řez přesně ohraničenou definicí symbolu s ověřenou proveniencí path:start:end.', 'Extracts the exact symbol slice with verified byte-bounded path:start:end provenance.') ?>
                </p>
            </div>
        </div>

        <div class="context-provenance-box">
            <div class="provenance-header">
                <span class="eyebrow"><?= $lang->value('OVĚŘENÁ PROVENIENCE ŘEZU', 'VERIFIED SLICE PROVENANCE') ?></span>
                <span class="provenance-path" data-provenance-path>src/Security/LoginLimiter.php:13-340</span>
            </div>
            <pre class="code-content" role="region" tabindex="0" aria-label="<?= $lang->value('Ukázka extrahovaného řezu', 'Extracted slice preview') ?>"><code data-provenance-code>final readonly class LoginLimiter
{
    public function consume(string $address): bool { ... }
    public function clear(string $address): bool { ... }
}</code></pre>
        </div>

        <div class="context-disclaimer">
            <strong><?= $lang->value('Metodika měření:', 'Benchmarking methodology:') ?></strong>
            <?= $lang->value('Statická měření vycházejí z testovacího repozitáře o 144 souborech. Výsledky simulují deterministické chování vyhledávacího enginu bez nutnosti spouštět externí LLM.', 'Measurements derived from fixed synthetic 144-file repository fixtures. Demonstrates deterministic retrieval efficiency without running live inference.') ?>
        </div>
    </div>

    <noscript>
        <div class="lab-fallback">
            <h3><?= $lang->value('Tabulkové porovnání strategií kontextu', 'Context Strategy Comparison Table') ?></h3>
            <table class="table-fallback">
                <thead>
                    <tr>
                        <th><?= $lang->value('Strategie', 'Strategy') ?></th>
                        <th><?= $lang->value('Spotřeba tokenů', 'Token Budget') ?></th>
                        <th><?= $lang->value('Latence', 'Latency') ?></th>
                        <th><?= $lang->value('Přesnost', 'Accuracy') ?></th>
                        <th><?= $lang->value('Výsledek', 'Outcome') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?= $lang->value('Celý repozitář', 'Whole repo') ?></strong></td>
                        <td>45,200</td>
                        <td>1,850 ms</td>
                        <td>45%</td>
                        <td><?= $lang->value('Vyčerpání kontextu, vysoké riziko halucinací', 'Exhausts context, high hallucination risk') ?></td>
                    </tr>
                    <tr>
                        <td><strong><?= $lang->value('Lexikální (BM25)', 'Lexical (BM25)') ?></strong></td>
                        <td>3,400</td>
                        <td>42 ms</td>
                        <td>72%</td>
                        <td><?= $lang->value('Nerozlišuje komentáře od výkonného kódu', 'Matches substrings and comments indiscriminately') ?></td>
                    </tr>
                    <tr>
                        <td><strong><?= $lang->value('AST Index (Hub)', 'AST Index (Hub)') ?></strong></td>
                        <td>340</td>
                        <td>8 ms</td>
                        <td>99%</td>
                        <td><?= $lang->value('Přesný řez definice symbolu s proveniencí', 'Exact symbol slice with verified provenance') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </noscript>
</section>
