<?php
declare(strict_types=1);

use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
?>
<section class="shell lab-interactive-section" id="type-safety-lab" aria-labelledby="type-safety-lab-title">
    <div class="section-heading">
        <div>
            <span class="eyebrow"><?= $lang->value('INTERAKTIVNÍ EXPERIMENT · STATICKÁ ANALÝZA', 'INTERACTIVE LAB · STATIC ANALYSIS') ?></span>
            <h2 id="type-safety-lab-title"><?= $lang->value('Type Safety Lab', 'Type Safety Lab') ?></h2>
            <p><?= $lang->value('Jak generické šablony a podmíněné návratové typy v PHP 8 eliminují zastarávající PHPDoc anotace a zajišťují nulovou chybovost v PHPStan max. Založeno na skutečném sloučeném příspěvku do jádra CakePHP (PR #18957).', 'How generics and conditional return types in PHP 8 eradicate drifting PHPDoc annotations and enforce zero errors under PHPStan max. Derived from a real merged contribution to the CakePHP core (PR #18957).') ?></p>
        </div>
    </div>

    <div class="lab-shell type-safety-lab-shell" data-type-safety-lab data-lang="<?= Html::escape($lang->code) ?>">
        <div class="lab-toolbar">
            <div class="lab-tabs" role="tablist" aria-label="<?= $lang->value('Režim typové kontroly', 'Type checking mode') ?>">
                <button type="button" role="tab" class="tab active" id="tab-ts-before" aria-selected="true" aria-controls="panel-ts-before" data-ts-tab="before">
                    <span class="tab-badge badge-warning"><?= $lang->value('Před úpravou', 'Before') ?></span>
                    <span><?= $lang->value('Obecný typ Table (bez generik)', 'General Table type (pre-generics)') ?></span>
                </button>
                <button type="button" role="tab" class="tab" id="tab-ts-after" aria-selected="false" aria-controls="panel-ts-after" data-ts-tab="after">
                    <span class="tab-badge badge-success"><?= $lang->value('Po úpravě', 'After (PR #18957)') ?></span>
                    <span><?= $lang->value('Generická šablona @template T', 'Generic template @template T') ?></span>
                </button>
            </div>
        </div>

        <div class="type-safety-panels">
            <div role="tabpanel" id="panel-ts-before" aria-labelledby="tab-ts-before" class="type-safety-panel active" data-ts-panel="before">
                <div class="ts-comparison-grid">
                    <div class="ts-code-side">
                        <div class="code-block">
                            <div class="code-header">
                                <span>src/Controller/UsersController.php (Legacy)</span>
                                <span class="code-tag">PHP 8.5 / CakePHP 5.0</span>
                            </div>
                            <pre class="code-content" role="region" tabindex="0" aria-label="<?= $lang->value('Kód před zavedením generik', 'Code before generics') ?>"><code><span class="code-comment">// <?= $lang->value('Volání bez generického typu vrací bázovou třídu Cake\ORM\Table', 'Call without generic type returns base Cake\ORM\Table class') ?></span>
$usersTable = $this->fetchTable(UsersTable::class);

<span class="code-comment">// <?= $lang->value('Metoda findActive() existuje pouze na UsersTable, nikoli na Table', 'Method findActive() exists on UsersTable, not on base Table') ?></span>
$activeUsers = $usersTable->findActive()->all();</code></pre>
                        </div>
                        <p class="ts-note">
                            <strong><?= $lang->value('Běžné křehké řešení:', 'Common brittle workaround:') ?></strong>
                            <?= $lang->value('Vývojáři byli nuceni vkládat ruční anotace <code>/** @var UsersTable $usersTable */</code>, které statický analyzátor nemohl ověřit proti skutečné továrně tabulek. Při refaktoringu názvu tabulky kód tiše zastarával.', 'Developers were forced to write manual docblocks <code>/** @var UsersTable $usersTable */</code>, which static analysis cannot verify against the table locator. During refactoring, these docblocks silently drifted.') ?>
                        </p>
                    </div>

                    <div class="ts-diagnostic-side">
                        <div class="diagnostic-box diagnostic-error">
                            <div class="diag-header">
                                <span class="diag-icon">✖</span>
                                <strong>PHPStan Level MAX (Strict rules)</strong>
                            </div>
                            <pre class="diag-output" role="region" tabindex="0" aria-label="<?= $lang->value('Chybový výstup PHPStan před úpravou', 'PHPStan error output before fix') ?>"><code>------ -----------------------------------------------------------
Line   src/Controller/UsersController.php
------ -----------------------------------------------------------
18     Call to an undefined method Cake\ORM\Table::findActive().
       💡 Method is defined on App\Model\Table\UsersTable,
          but fetchTable() declares return type Cake\ORM\Table.
------ -----------------------------------------------------------
[ERROR] Found 1 error</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div role="tabpanel" id="panel-ts-after" aria-labelledby="tab-ts-after" class="type-safety-panel" data-ts-panel="after" hidden>
                <div class="ts-comparison-grid">
                    <div class="ts-code-side">
                        <div class="code-block">
                            <div class="code-header">
                                <span>src/ORM/Locator/LocatorAwareTrait.php (PR #18957)</span>
                                <span class="code-tag">Generics & Type Inference</span>
                            </div>
                            <pre class="code-content" role="region" tabindex="0" aria-label="<?= $lang->value('Generická deklarace v jádře CakePHP', 'Generic declaration in CakePHP core') ?>"><code><span class="code-diff-add">/**
 * @template T of \Cake\ORM\Table
 * @param class-string&lt;T&gt;|string|null $alias Table class or alias
 * @return ($alias is class-string&lt;T&gt; ? T : \Cake\ORM\Table)
 */</span>
public function fetchTable(?string $alias = null): Table
{
    <span class="code-comment">// <?= $lang->value('Jádro CakePHP dynamicky lokalizuje tabulku', 'CakePHP core dynamically resolves table instance') ?></span>
    return $this->getTableLocator()->get($alias);
}</code></pre>
                        </div>
                        <p class="ts-note">
                            <strong><?= $lang->value('Přínos pro vývojáře:', 'Developer impact:') ?></strong>
                            <?= $lang->value('Při předání <code>UsersTable::class</code> PHPStan automaticky vyvodí, že návratová hodnota je přesně <code>UsersTable</code>. Okamžité našeptávání v IDE, 0 chyb a žádné zbytečné docblocky.', 'When passing <code>UsersTable::class</code>, PHPStan infers the return type as <code>UsersTable</code>. Instant IDE autocomplete, 0 errors, and zero docblock maintenance.') ?>
                        </p>
                    </div>

                    <div class="ts-diagnostic-side">
                        <div class="diagnostic-box diagnostic-success">
                            <div class="diag-header">
                                <span class="diag-icon">✔</span>
                                <strong>PHPStan Level MAX (Strict rules)</strong>
                            </div>
                            <pre class="diag-output" role="region" tabindex="0" aria-label="<?= $lang->value('Úspěšný výstup PHPStan po zavedení generik', 'Successful PHPStan output after generics') ?>"><code>[OK] No errors found!
Target type accurately inferred as:
\App\Model\Table\UsersTable

Method findActive() verified:
Cake\ORM\Query\SelectQuery&lt;App\Model\Entity\User&gt;
0 manual @var annotations required.</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ts-meta-footer">
            <div class="ts-upstream-link">
                <span><?= $lang->value('Upstream Pull Request:', 'Upstream Pull Request:') ?></span>
                <a class="inline-link" href="https://github.com/cakephp/cakephp/pull/18957" target="_blank" rel="noopener noreferrer">
                    cakephp/cakephp #18957 ↗
                </a>
            </div>
            <p class="ts-caveat">
                <strong><?= $lang->value('Hranice typové bezpečnosti:', 'Type safety boundary:') ?></strong>
                <?= $lang->value('Typová kontrola ověřuje strukturální integritu a shodu metod při překladu. Negarantuje však správnost databázového schématu, síťovou dostupnost ani sémantiku doménových dat.', 'Static type analysis proves structural contracts and signature compatibility at build time. It does not guarantee database schema validity, network availability, or runtime domain correctness.') ?>
            </p>
        </div>

        <noscript>
            <div class="lab-fallback">
                <h3><?= $lang->value('Textové srovnání typové kontroly', 'Text-only type safety comparison') ?></h3>
                <p><?= $lang->value('Před úpravou: fetchTable() vracel bázovou třídu Table, což způsobovalo chybu PHPStan při volání specifických metod tabulky.', 'Before: fetchTable() returned generic Table, causing PHPStan errors on custom table methods.') ?></p>
                <p><?= $lang->value('Po úpravě (PR #18957): díky generické šabloně @template T vrací metoda přímo typ specifikované tabulky.', 'After (PR #18957): with generic @template T, the method resolves directly to the requested table class.') ?></p>
            </div>
        </noscript>
    </div>
</section>
