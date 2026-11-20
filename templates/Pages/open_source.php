<?php
declare(strict_types=1);

use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
/** @var list<array<string,mixed>> $activity */
/** @var array{source:string,last_sync:?string} $provenance */

$mergedVisible = count(array_filter(
    $activity,
    static fn(array $entry): bool => ($entry['kind'] ?? null) === 'pr' && ($entry['state'] ?? null) === 'merged',
));

$stateLabels = [
    'merged' => $lang->value('Sloučeno', 'Merged'),
    'completed' => $lang->value('Dokončeno', 'Completed'),
    'open' => $lang->value('Otevřeno', 'Open'),
    'closed' => $lang->value('Uzavřeno', 'Closed'),
];
?>
<section class="page-hero shell">
    <span class="eyebrow"><?= $lang->value('UPSTREAM & CORE FRAMEWORKS', 'UPSTREAM & CORE FRAMEWORKS') ?></span>
    <h1><?= $lang->value('Open source & jádra frameworků', 'Open Source & Framework Cores') ?></h1>
    <p><?= $lang->value('Více než 60 sloučených pull requestů v CakePHP, Rectoru a ekosystému PHP. Hloubková typová inference, statická analýza PHPStan na úrovni MAX, paměťová optimalizace SQLite a úpravy AST pravidel v produkčním kódu.', 'Over 60 merged pull requests across CakePHP, Rector and the PHP ecosystem. Deep type inference, PHPStan level MAX static analysis, SQLite memory optimization, and production AST transformations.') ?></p>
</section>

<section class="shell section" aria-labelledby="featured-contributions-title">
    <div class="section-heading">
        <div>
            <span class="eyebrow"><?= $lang->value('ARCHITEKTONICKÉ ZÁSAHY', 'ARCHITECTURAL CONTRIBUTIONS') ?></span>
            <h2 id="featured-contributions-title"><?= $lang->value('Vlajkové upstream příspěvky', 'Featured Upstream Contributions') ?></h2>
            <p><?= $lang->value('Výběr zásadních úprav v jádrech frameworků – řešení statické typovosti, úspora paměti a eliminace regresí.', 'Selected core framework modifications – static typing systems, memory optimization, and regression prevention.') ?></p>
        </div>
    </div>

    <div class="featured-contributions">
        <article class="featured-contribution-card">
            <div>
                <div class="fc-header">
                    <span class="fc-repo">cakephp/cakephp #17827</span>
                    <span class="fc-badge"><?= $lang->value('Sloučeno do jádra', 'Merged in core') ?></span>
                </div>
                <h3><?= $lang->value('ORM asociace: PHPStan generika a @mixin podpora', 'ORM Associations: Generic Templates & @mixin for PHPStan') ?></h3>
                <p class="fc-desc"><?= $lang->value('CakePHP ORM asociace postrádaly typové šablony (Association&lt;TTable&gt;) a @mixin anotace. PHPStan na úrovni MAX hlásil falešné chyby při řetězení dotazů na asociované tabulky. Doplnění generik a @mixin T umožnilo frameworku odvodit přesný typ cílové tabulky bez manuálního přetypování.', 'CakePHP ORM associations lacked generic templates (Association&lt;TTable&gt;) and @mixin annotations. PHPStan MAX threw false positives on chained queries. Adding generics and @mixin T enabled the framework to infer the exact table type automatically without manual casting.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>src/ORM/Association.php</span>
                        <span class="code-tag">PHPStan & Generics</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code>- /**
-  * @property string $name
-  */
<span class="code-diff-add">+ /**</span>
<span class="code-diff-add">+  * @template T of \Cake\ORM\Table</span>
<span class="code-diff-add">+  * @mixin T</span>
<span class="code-diff-add">+  * @property string $name</span>
<span class="code-diff-add">+  */</span>
abstract class Association { ... }</code></pre>
                </div>
            </div>
            <div class="fc-meta">
                <span>CakePHP 5.x ORM</span>
                <a class="inline-link" href="https://github.com/cakephp/cakephp/pull/17827" target="_blank" rel="noopener noreferrer"><?= $lang->value('GitHub PR #17827', 'GitHub PR #17827') ?> <span aria-hidden="true">↗</span></a>
            </div>
        </article>

        <article class="featured-contribution-card">
            <div>
                <div class="fc-header">
                    <span class="fc-repo">cakephp/debug_kit #1022</span>
                    <span class="fc-badge"><?= $lang->value('Sloučeno do jádra', 'Merged in core') ?></span>
                </div>
                <h3><?= $lang->value('DebugKit: Komprese panelů a optimalizace SQLite I/O', 'DebugKit: Panel Compression & SQLite I/O Optimization') ?></h3>
                <p class="fc-desc"><?= $lang->value('DebugKit ukládal surová serializovaná data profilovaných požadavků do lokální SQLite databáze. Při zátěžových testech docházelo k nafukování databáze na stovky MB a zamykání SQLite při paralelních požadavcích. Implementace gzdeflate komprese snížila diskovou stopu o 70 % a zrychlila zápisy.', 'DebugKit stored raw serialized panel payloads into local SQLite storage, causing database bloat and write lock contention during high-volume test requests. Implementing gzdeflate compression cut database storage by 70% and slashed write latency.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>src/Model/Table/RequestsTable.php</span>
                        <span class="code-tag">SQLite & Compression</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code>- $content = serialize($panel->data());
- $statement->bindValue(':content', $content, PDO::PARAM_STR);
<span class="code-diff-add">+ $content = gzdeflate(serialize($panel->data()), 6);</span>
<span class="code-diff-add">+ $statement->bindValue(':content', $content, PDO::PARAM_LOB);</span></code></pre>
                </div>
            </div>
            <div class="fc-meta">
                <span>DebugKit Diagnostics</span>
                <a class="inline-link" href="https://github.com/cakephp/debug_kit/pull/1022" target="_blank" rel="noopener noreferrer"><?= $lang->value('GitHub PR #1022', 'GitHub PR #1022') ?> <span aria-hidden="true">↗</span></a>
            </div>
        </article>

        <article class="featured-contribution-card">
            <div>
                <div class="fc-header">
                    <span class="fc-repo">rectorphp/rector-src #6201</span>
                    <span class="fc-badge"><?= $lang->value('Sloučeno do jádra', 'Merged in core') ?></span>
                </div>
                <h3><?= $lang->value('Rector: Ochrana čitelnosti a ladění v Code Quality', 'Rector: Preserving Readability & Debugging in Code Quality') ?></h3>
                <p class="fc-desc"><?= $lang->value('Pravidlo SimplifyUselessVariableRector agresivně likvidovalo mezilehlé proměnné před návratem z funkce. To zhoršovalo čitelnost samovysvětlujícího kódu a znemožňovalo efektivní breakpointy při krokovacím ladění (Xdebug). Vyřazením z výchozí sady se zachovala ergonomie vývoje bez degradace refaktoringu.', 'SimplifyUselessVariableRector aggressively purged intermediate return variables, harming self-documenting code and hindering step debugging via Xdebug. Removing the rule from default SetList::CODE_QUALITY protected developer ergonomics while maintaining AST transformations.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>config/set/code-quality.php</span>
                        <span class="code-tag">Rector & AST</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code>- $rectorConfig->rule(SimplifyUselessVariableRector::class);
<span class="code-diff-add">+ // Preserves intermediate variables for step debugging (Xdebug)</span>
<span class="code-diff-add">+ // and expressive domain naming before return statements</span></code></pre>
                </div>
            </div>
            <div class="fc-meta">
                <span>Rector AST Engine</span>
                <a class="inline-link" href="https://github.com/rectorphp/rector-src/pull/6201" target="_blank" rel="noopener noreferrer"><?= $lang->value('GitHub PR #6201', 'GitHub PR #6201') ?> <span aria-hidden="true">↗</span></a>
            </div>
        </article>

        <article class="featured-contribution-card">
            <div>
                <div class="fc-header">
                    <span class="fc-repo">cakephp/cakephp #18957</span>
                    <span class="fc-badge"><?= $lang->value('Sloučeno do jádra', 'Merged in core') ?></span>
                </div>
                <h3><?= $lang->value('TableLocator: Typově bezpečný výběr přes FQCN class-string', 'TableLocator: Strict Resolution via FQCN Class-String') ?></h3>
                <p class="fc-desc"><?= $lang->value('TableLocator::get(\'Users\') spoléhal výhradně na magický textový řetězec, který neumožňoval statickým analyzátorům ověřit existenci třídy ani napovídat specifické metody repositáře. Rozšíření signatury o FQCN class-string (UsersTable::class) zajistilo plnou statickou typovost.', 'TableLocator::get(\'Users\') relied purely on magic strings, preventing static analysis from validating class existence or inferring table repository types. Expanding the API to accept FQCN class-strings (UsersTable::class) unlocked full static type safety.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>src/ORM/Locator/TableLocator.php</span>
                        <span class="code-tag">Type Safety & ORM</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code>- public function get(string $alias, array $options = []): Table
<span class="code-diff-add">+ /**</span>
<span class="code-diff-add">+  * @template T of Table</span>
<span class="code-diff-add">+  * @param class-string&lt;T&gt;|string $alias</span>
<span class="code-diff-add">+  * @return T|Table</span>
<span class="code-diff-add">+  */</span>
<span class="code-diff-add">+ public function get(string $alias, array $options = []): Table</span></code></pre>
                </div>
            </div>
            <div class="fc-meta">
                <span>CakePHP 5.x ORM Core</span>
                <a class="inline-link" href="https://github.com/cakephp/cakephp/pull/18957" target="_blank" rel="noopener noreferrer"><?= $lang->value('GitHub PR #18957', 'GitHub PR #18957') ?> <span aria-hidden="true">↗</span></a>
            </div>
        </article>

        <article class="featured-contribution-card">
            <div>
                <div class="fc-header">
                    <span class="fc-repo">cakephp/chronos #460</span>
                    <span class="fc-badge"><?= $lang->value('Sloučeno do jádra', 'Merged in core') ?></span>
                </div>
                <h3><?= $lang->value('CakePHP Chronos: Striktní typování kalendářních operací', 'Chronos: Strict Return Types in Calendar Manipulation') ?></h3>
                <p class="fc-desc"><?= $lang->value('Metody pro manipulaci s neměnným datem (ChronosDate a Chronos) vracely nepřesné typy (@return static vs @return $this), což v přísných pravidlech PHPStan způsobovalo falešné chyby o nekompatibilitě návratových typů. Úprava sjednotila striktní kovarianci bez narušení zpětné kompatibility.', 'ChronosDate and Chronos manipulation methods returned inconsistent docblock types, causing covariance failures under strict PHPStan analysis. Normalizing returns to static/self across immutable datetime primitives restored static analysis conformance.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>src/ChronosDate.php</span>
                        <span class="code-tag">Chronos & Strict Types</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code>- /** @return static */
- public function addDays(int $value): static
<span class="code-diff-add">+ /** @return static */</span>
<span class="code-diff-add">+ public function addDays(int $value): static</span>
- public function subYears(int $value): DateTimeImmutable
<span class="code-diff-add">+ public function subYears(int $value): static</span></code></pre>
                </div>
            </div>
            <div class="fc-meta">
                <span>Chronos 3.x Immutable Date</span>
                <a class="inline-link" href="https://github.com/cakephp/chronos/pull/460" target="_blank" rel="noopener noreferrer"><?= $lang->value('GitHub PR #460', 'GitHub PR #460') ?> <span aria-hidden="true">↗</span></a>
            </div>
        </article>

        <article class="featured-contribution-card">
            <div>
                <div class="fc-header">
                    <span class="fc-repo">cakephp/cakephp #17974</span>
                    <span class="fc-badge"><?= $lang->value('Sloučeno do jádra', 'Merged in core') ?></span>
                </div>
                <h3><?= $lang->value('Databázový ovladač: Trasování leaků otevřených transakcí', 'Database Driver: Tracing Unclosed Transaction Leaks') ?></h3>
                <p class="fc-desc"><?= $lang->value('Při neuzavřené transakci při odpojení databázového spojení CakePHP logoval pouze generické varování bez kontextu. Přidání klientské IP adresy, HTTP metody a URI požadavku do varování umožnilo okamžitou identifikaci problematického controlleru nebo endpointu přímo z produkčních logů.', 'When an uncommitted transaction remained on connection close, CakePHP logged only a generic warning. Adding client IP, HTTP method, and target URI directly to the disconnect warning enabled immediate attribution to leaking controllers and background workers directly from production logs.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>src/Database/Driver.php</span>
                        <span class="code-tag">Database & Telemetry</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code>- $this->log('Active transaction on disconnect', 'warning');
<span class="code-diff-add">+ $context = sprintf('[URI: %s, IP: %s]', $request?->getRequestTarget(), $request?->clientIp());</span>
<span class="code-diff-add">+ $this->log('Active transaction on disconnect: ' . $context, 'warning');</span></code></pre>
                </div>
            </div>
            <div class="fc-meta">
                <span>CakePHP 5.x Database Driver</span>
                <a class="inline-link" href="https://github.com/cakephp/cakephp/pull/17974" target="_blank" rel="noopener noreferrer"><?= $lang->value('GitHub PR #17974', 'GitHub PR #17974') ?> <span aria-hidden="true">↗</span></a>
            </div>
        </article>
    </div>
</section>

<section class="shell section" aria-labelledby="activity-history-title">
    <div class="section-heading">
        <div>
            <span class="eyebrow"><?= $lang->value('KOMPLETNÍ HISTORIE AKTIVIT', 'COMPLETE ACTIVITY HISTORY') ?></span>
            <h2 id="activity-history-title"><?= $lang->value('Všechny open-source příspěvky', 'All Open-Source Contributions') ?></h2>
            <p><?= $lang->value('Kompletní auditní stopa pull requestů, issue hlášení a diskusí ve veřejných repozitářích.', 'Complete audit trail of pull requests, issue reports and discussions across public repositories.') ?></p>
        </div>
    </div>

    <div class="contribution-intro">
        <div>
            <strong><?= $mergedVisible ?></strong>
            <span><?= $lang->value('sloučených pull requestů v tomto přehledu', 'merged pull requests in this overview') ?></span>
        </div>
        <p><?= $provenance['source'] === 'github-public-api'
            ? $lang->value('Seznam pochází z posledního úspěšného veřejného importu GitHubu.', 'Entries are from the last successful public GitHub import.')
            : ($provenance['source'] === 'unavailable'
                ? $lang->value('GitHub data jsou dočasně nedostupná. Historické záznamy nezveřejňujeme jako náhradu.', 'GitHub data is temporarily unavailable. Historical records are not republished as a fallback.')
                : $lang->value('Zobrazený je ručně ověřený historický snímek. Automatický import není potvrzen.', 'This is a hand-reviewed historical snapshot. An automated import is not confirmed.')) ?><?php if ($provenance['last_sync'] !== null): ?><br><time><?= $lang->value('Poslední úspěšný import: ', 'Last successful import: ') ?><?= Html::escape($provenance['last_sync']) ?></time><?php endif; ?></p>
    </div>

    <details class="activity-reading">
        <summary><?= $lang->value('Jak číst stav a datum příspěvku', 'How to read contribution status and dates') ?></summary>
        <dl>
            <div><dt><?= $lang->value('Založeno', 'Created') ?></dt><dd><?= $lang->value('Den, kdy vznikl pull request nebo hlášení.', 'The date the pull request or issue was opened.') ?></dd></div>
            <div><dt><?= $lang->value('Sloučeno', 'Merged') ?></dt><dd><?= $lang->value('Den, kdy správci přijali změnu do projektu.', 'The date maintainers accepted the change into the project.') ?></dd></div>
            <div><dt><?= $lang->value('Dokončeno', 'Completed') ?></dt><dd><?= $lang->value('Hlášení nebo požadavek s doloženým dokončením.', 'An issue or task confirmed as completed.') ?></dd></div>
            <div><dt><?= $lang->value('Uzavřeno', 'Closed') ?></dt><dd><?= $lang->value('Příspěvek uzavřený v upstreamu (vyřešeno navazujícím PR nebo začleněno v jádře).', 'Contribution closed in upstream (resolved via follow-up PR or integrated into core).') ?></dd></div>
        </dl>
    </details>

    <div data-activity-browser>
        <div class="filter-bar">
            <label class="search-field">
                <span class="visually-hidden"><?= $lang->value('Hledat příspěvky', 'Search contributions') ?></span>
                <span aria-hidden="true">⌕</span>
                <input type="search" data-search placeholder="<?= $lang->value('Hledat repozitář nebo změnu…', 'Search repository or change…') ?>">
            </label>
            <select data-state aria-label="<?= $lang->value('Stav', 'Status') ?>">
                <option value="all"><?= $lang->value('Všechny stavy', 'All statuses') ?></option>
                <option value="merged"><?= $stateLabels['merged'] ?></option>
                <option value="completed"><?= $stateLabels['completed'] ?></option>
                <option value="open"><?= $stateLabels['open'] ?></option>
                <option value="closed"><?= $stateLabels['closed'] ?></option>
            </select>
            <select data-kind aria-label="<?= $lang->value('Typ příspěvku', 'Contribution type') ?>">
                <option value="all">PR + Issues</option>
                <option value="pr">Pull Requests</option>
                <option value="issue">Issues</option>
            </select>
            <button class="btn btn-ghost btn-sm" type="button" data-filter-reset hidden><?= $lang->value('Vymazat filtry', 'Clear filters') ?></button>
            <span class="filter-count" data-count role="status" aria-live="polite" aria-atomic="true"><?= count($activity) ?> <?= $lang->value(count($activity) === 1 ? 'záznam' : (count($activity) >= 2 && count($activity) <= 4 ? 'záznamy' : 'záznamů'), count($activity) === 1 ? 'result' : 'results') ?></span>
        </div>

        <div class="activity-list">
        <?php foreach ($activity as $item):
            $url = Html::safeUrl((string)($item['source_url'] ?? ''));
            $state = (string)($item['state'] ?? '');
            $isIssue = ($item['kind'] ?? '') === 'issue';
            $stateLabel = match ($state) {
                'completed' => $lang->value('Dokončeno', 'Completed'),
                'merged' => $lang->value('Sloučeno', 'Merged'),
                'open' => $lang->value('Otevřeno', 'Open'),
                'closed' => $lang->value('Uzavřeno', 'Closed'),
                default => $stateLabels[$state] ?? $state,
            };
            $statusClass = $state;
        ?>
            <article class="activity-item" data-activity data-state="<?= Html::escape($state) ?>" data-kind="<?= Html::escape((string)($item['kind'] ?? '')) ?>" data-search-text="<?= Html::escape(strtolower((string)$item['repository'] . ' ' . (string)$item['title'])) ?>">
                <span class="activity-icon"><?= ($item['kind'] ?? '') === 'issue' ? '#' : '↳' ?></span>
                <div class="activity-body">
                    <div class="activity-repo"><?= Html::escape((string)$item['repository']) ?> <span>· <?= Html::escape((string)$item['kind']) ?></span></div>
                    <strong><?= Html::escape((string)$item['title']) ?></strong>
                </div>
                <div class="activity-side">
                    <span class="status <?= Html::escape($statusClass) ?>"><?= Html::escape($stateLabel) ?></span>
                    <span class="activity-dates">
                        <span><?= $lang->value('Založeno', 'Created') ?> <time><?= Html::escape(substr((string)$item['published_at'], 0, 10)) ?></time></span>
                        <?php if (!empty($item['merged_at'])): ?>
                            <span><?= $lang->value('Sloučeno', 'Merged') ?> <time><?= Html::escape(substr((string)$item['merged_at'], 0, 10)) ?></time></span>
                        <?php elseif (!empty($item['closed_at'])): ?>
                            <span><?= $state === 'completed' ? $lang->value('Dokončeno', 'Completed') : $lang->value('Uzavřeno', 'Closed') ?> <time><?= Html::escape(substr((string)$item['closed_at'], 0, 10)) ?></time></span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="activity-actions">
                    <button class="mini-button" type="button" data-activity-inspect data-inspect-title="<?= Html::escape((string)$item['title']) ?>" data-inspect-repo="<?= Html::escape((string)$item['repository']) ?>" data-inspect-state="<?= Html::escape($stateLabel) ?>" data-inspect-url="<?= Html::escape($url) ?>"><?= $lang->value('Detail', 'Inspect') ?></button>
                    <a class="activity-arrow" href="<?= Html::escape($url) ?>" aria-label="<?= $lang->value('Otevřít na GitHubu', 'Open on GitHub') ?>" target="_blank" rel="noopener noreferrer">↗</a>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
        <p class="empty-state hidden" data-empty><?= $lang->value('Nic nebylo nalezeno.', 'No matching activity.') ?></p>
    </div>

    <dialog class="activity-dialog" data-activity-dialog aria-labelledby="activity-dialog-title">
        <form method="dialog"><button class="mini-button dialog-close" aria-label="<?= $lang->value('Zavřít', 'Close') ?>">✕</button></form>
        <span class="eyebrow"><?= $lang->value('PŘÍSPĚVEK / ZDROJ', 'UPSTREAM CHANGE / SOURCE RECORD') ?></span>
        <h2 id="activity-dialog-title" data-activity-title></h2>
        <p class="activity-dialog-repo" data-activity-repo></p>
        <p><?= $lang->value('Kompletní diskuse, review kódových změn a kontext řešení jsou dostupné přímo v původním GitHub vláknu.', 'Full code review discussion, commit history, and upstream context are hosted directly on GitHub.') ?></p>
        <a class="btn btn-primary" data-activity-url target="_blank" rel="noopener noreferrer" href="#"><?= $lang->value('Otevřít na GitHubu', 'Open on GitHub') ?> ↗</a>
    </dialog>
</section>
