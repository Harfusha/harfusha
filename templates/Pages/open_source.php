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
    <span class="eyebrow"><?= $lang->value('PŘÍSPĚVKY DO OPEN-SOURCE PROJEKTŮ', 'OPEN-SOURCE CONTRIBUTIONS') ?></span>
    <h1><?= $lang->value('Open‑source práce', 'Open‑source contributions') ?></h1>
    <p><?= $lang->value('Konkrétní změny v CakePHP, DebugKitu, Chronosu a Rectoru. U každé ukázky je odkaz na sloučený pull request.', 'Specific changes to CakePHP, DebugKit, Chronos and Rector, each linked to its merged pull request.') ?></p>
</section>

<section class="shell section" aria-labelledby="featured-contributions-title">
    <div class="section-heading">
        <div>
            <span class="eyebrow"><?= $lang->value('VÝBĚR ZMĚN', 'SELECTED CHANGES') ?></span>
            <h2 id="featured-contributions-title"><?= $lang->value('Vybrané příspěvky', 'Selected contributions') ?></h2>
            <p><?= $lang->value('Vybrané změny a jejich konkrétní dopad na kód.', 'Selected changes and their specific effect on the code.') ?></p>
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
                <p class="fc-desc"><?= $lang->value('Do tříd BelongsTo a BelongsToMany přibyly šablona T a anotace @mixin T. Statické nástroje tak mohou při práci s asociací vidět API cílové tabulky.', 'The BelongsTo and BelongsToMany classes gained a T template and an @mixin T annotation. Static analysis tools can then see the target table API through the association.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>src/ORM/Association/BelongsTo.php</span>
                        <span class="code-tag">PHPStan & Generics</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code><span class="code-diff-add">+  * @template T of \Cake\ORM\Table</span>
<span class="code-diff-add">+  * @mixin T</span>
class BelongsTo extends Association</code></pre>
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
                <h3><?= $lang->value('DebugKit: komprese dat panelů', 'DebugKit: Compressing panel data') ?></h3>
                <p class="fc-desc"><?= $lang->value('Optimalizace ukládání stavu panelů DebugKitu do SQLite. Pull request zavádí kompresi serializovaných dat pomocí gzdeflate před zápisem, což výrazně šetří diskové I/O u objemných stavů požadavků, a konfiguruje automatizovanou údržbu přes INCREMENTAL VACUUM.', 'Optimizing DebugKit panel state persistence in SQLite. The pull request introduces gzdeflate compression on serialized panel payloads before write, minimizing disk I/O overhead on large request state dumps, and configures incremental SQLite autovacuum routines.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>src/Model/Table/RequestsTable.php</span>
                        <span class="code-tag">SQLite & Compression</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code><span class="code-diff-del">- $content = serialize($panel->data());</span>
<span class="code-diff-del">- $statement->bindValue(':content', $content, PDO::PARAM_STR);</span>
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
                <h3><?= $lang->value('Rector: odebrání pravidla z výchozí sady', 'Rector: Removing a rule from the default set') ?></h3>
                <p class="fc-desc"><?= $lang->value('Pull request odebral SimplifyUselessVariableRector z výchozí sady Code Quality. Zachovává tak při jejím použití mezilehlé proměnné; samostatně lze pravidlo dál zapnout.', 'The pull request removed SimplifyUselessVariableRector from the default Code Quality set. Intermediate variables therefore remain when using that set; the rule can still be enabled separately.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>src/Config/Level/CodeQualityLevel.php</span>
                        <span class="code-tag">Rector & AST</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code><span class="code-diff-del">- use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;</span>
<span class="code-diff-del">- SimplifyUselessVariableRector::class,</span></code></pre>
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
                <h3><?= $lang->value('CakePHP ORM: typ tabulky podle FQCN', 'CakePHP ORM: Table type from an FQCN') ?></h3>
                <p class="fc-desc"><?= $lang->value('Příspěvek zpřesnil PHPDoc metody fetchTable(): pro class-string vrací odpovídající typ tabulky, pro běžný alias obecný Table.', 'The contribution refined fetchTable() PHPDoc: a class-string resolves to its table type, while a regular alias returns the general Table type.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>src/ORM/Locator/LocatorAwareTrait.php</span>
                        <span class="code-tag">Type Safety & ORM</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code><span class="code-diff-add">+ @template T of \Cake\ORM\Table</span>
<span class="code-diff-add">+ @param class-string&lt;T&gt;|string|null $alias</span>
<span class="code-diff-add">+ @return ($alias is class-string&lt;T&gt; ? T : \Cake\ORM\Table)</span></code></pre>
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
                <h3><?= $lang->value('Chronos: přesné návratové typy metod pro hledání data', 'Chronos: Correct return types for date-finding methods') ?></h3>
                <p class="fc-desc"><?= $lang->value('Metody jako next(), previous() a firstOfMonth() deklarovaly mixed. Pull request zpřesnil jejich PHPDoc na static a u nthOfMonth() na static|false.', 'Methods such as next(), previous() and firstOfMonth() declared mixed. The pull request refined their PHPDoc to static and nthOfMonth() to static|false.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>src/ChronosInterface.php</span>
                        <span class="code-tag">Chronos & PHPDoc</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code><span class="code-diff-del">-      * @return mixed</span>
<span class="code-diff-add">+      * @return static</span>
     public function next(?int $dayOfWeek = null);</code></pre>
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
                <h3><?= $lang->value('CakePHP: kontext požadavku při odpojení s transakcí', 'CakePHP: Request context on disconnect with a transaction') ?></h3>
                <p class="fc-desc"><?= $lang->value('Při neočekávaném ukončení běhu s otevřenou databázovou transakcí bylo v produkčních záznamech obtížné dohledat původce. Pull request rozšiřuje varování driveru o cílovou URI požadavku a klientskou IP adresu pro přesné trasování chybujícího kontroleru.', 'When a worker terminates abruptly with an uncommitted database transaction, diagnosing the originating request from production logs was difficult. The pull request augments the driver warning with the request URI and client IP for immediate trace-back to the failing controller.') ?></p>
                <div class="code-block">
                    <div class="code-header">
                        <span>src/Database/Connection.php</span>
                        <span class="code-tag">Database & Logging</span>
                    </div>
                    <pre class="code-content" tabindex="0" aria-label="<?= $lang->value('Rozdíl v kódu', 'Code diff') ?>"><code><span class="code-diff-add">+ $requestUrl = $_SERVER['REQUEST_URI'] ?? '';</span>
<span class="code-diff-add">+ if ($requestUrl) { $message .= "\nRequest URL: " . $requestUrl; }</span>
<span class="code-diff-add">+ $clientIp = $_SERVER['REMOTE_ADDR'] ?? '';</span>
<span class="code-diff-add">+ if ($clientIp) { $message .= "\nClient IP: " . $clientIp; }</span></code></pre>
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
            <span class="eyebrow"><?= $lang->value('PŘEHLED PŘÍSPĚVKŮ', 'CONTRIBUTION SNAPSHOT') ?></span>
            <h2 id="activity-history-title"><?= $lang->value('Přehled open-source aktivit', 'Open-source activity snapshot') ?></h2>
            <p><?= $lang->value('Přehled veřejných pull requestů a issue. Původní záznam obsahuje diskusi, komentáře i úplný kontext.', 'An index of public pull requests and issues. The upstream record contains the discussion, comments and full context.') ?></p>
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
                : $lang->value('Jde o výběrový přehled veřejných aktivit, ne o úplnou historii GitHubu.', 'This is a selected view of public activity, not a complete GitHub history.')) ?><?php if ($provenance['last_sync'] !== null): ?><br><time><?= $lang->value('Poslední úspěšný import: ', 'Last successful import: ') ?><?= Html::escape($provenance['last_sync']) ?></time><?php endif; ?></p>
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
                    <a class="inline-link" href="<?= Html::escape($url) ?>" target="_blank" rel="noopener noreferrer"><?= $lang->value('Otevřít záznam', 'Open upstream') ?></a>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
        <p class="empty-state hidden" data-empty><?= $lang->value('Nic nebylo nalezeno.', 'No matching activity.') ?></p>
    </div>
</section>
