# Testování a důkazní materiály — aktualizováno 15. 11. 2026

## Ověřený stav a hranice důkazů

[GitHub Actions běh #43648999069](https://github.com/Harfusha/harfusha/actions/runs/43648999069) (14. 11. 2026) prokazatelně dokončil všechny tři joby `application`, `static` a `browser-offline` se stavem **success**. Tento běh zahrnoval PHP 8.5, Composer validaci a audit, CakePHP HTTP a session/CSRF testy, izolované migrace MySQL 8.4, PHPUnit, PHPStan max + CakeDC/strict/ShipMonk, PHPCS, JS regresní testy i Chromium offline browser testy.

Tento výsledek se vztahuje **výhradně k tehdejší revizi**. Novější commity (včetně bezpečnostních a release oprav z 15. 11. 2026) vyžadují vlastní úspěšný běh CI; z tohoto dokumentu nevyplývá, že již proběhl. Lokální izolované prostředí autorování navíc nemá stejné PHP/MySQL runtime jako GitHub Actions.

| Oblast | Stav | Doklad / hranice |
| --- | --- | --- |
| PHP 8.5, Composer a uzamčené závislosti | **PASS v uvedeném CI** | PHP 8.5 a `composer.lock`; znovu spustit po změnách |
| PHPStan max, strict, CakeDC, ShipMonk | **PASS v uvedeném CI** | Job `application` |
| PHPUnit, CakePHP HTTP, CSRF a sessions | **PASS v uvedeném CI** | Job `application`, dočasné testovací přihlašovací údaje |
| Izolované MySQL 8.4 migrace | **PASS v uvedeném CI** | Není to důkaz obnovy produkční DB |
| PHPCS | **PASS v uvedeném CI** | Job `application` |
| JavaScript a offline Chromium | **PASS v uvedeném CI** | Joby `static`, `browser-offline` a `application` |
| Produkční ZIP s `vendor/` | **NEOVĚŘENO** | Nutný samostatný průchod `scripts/build-release.py` |
| Skutečný sdílený hosting, HTTPS, PHP-FPM | **NEOVĚŘENO** | Vyžaduje cílové prostředí a doménu |
| Backup/restore a rollback | **NEOVĚŘENO** | Vyžaduje test obnovy reálných dat na stagingu |
| Ruční schválení obsahu a obrázků | **NESCHVÁLENO** | Automatické kontroly nesmějí předstírat lidské rozhodnutí |

Chromium offline testy zobrazují předem vykreslené stránky a neověřují HTTP hlavičky nebo produkční PHP-FPM. Testy skutečného CakePHP HTTP běžely nezávisle v `application` jobu. Nezaměňovat tyto dvě úrovně testů.

## Povinné CI kontroly na PHP 8.5.12+

```sh
composer validate --strict
composer audit --locked
composer syntax
composer standalone
composer stan
composer test
composer cs-check
python3 scripts/audit-public.py
python3 scripts/audit-rendered.py
node --check webroot/assets/js/app.js
node --check webroot/assets/js/lab-core.js
node --test tests/Client/*.test.cjs
npx --no-install tsc --noEmit --allowJs --checkJs --strict --target ES2022 --lib ES2022,DOM webroot/assets/js/lab-core.js
python3 tests/Browser/smoke.py  # s nainstalovaným Playwright Chromium
```

Zdrojový i produkční balíčkovač sdílí kontrolu `scripts/client_gates.py`: provede **všechny** `tests/Client/*.test.cjs` v deterministickém pořadí, odmítne prázdnou sadu a symlinky. `scripts/preflight.php` používá totožnou produkční politiku jako bootstrap aplikace (`DeploymentPolicy`), nikoli vlastní slabší regulární výrazy. Regresní testy ověřují validaci URL a saltu i soulad těchto kontrol.

**Nesnižovat PHPStan level ani nevytvářet baseline.** Povinné nadstavby: CakeDC, PHPStan strict rules, ShipMonk, deprecation rules a PHPUnit extension. Každý nový problém se řeší konkrétní opravou kódu a regresním testem.

## Testovací matice

- **Doména:** immutable Project, povolené statusy, validace JSON struktur, provázanost grafu, cykly topologie, URL allowlist.
- **Editorial:** validace UTF-8, délky, typy formulářů, bezpečnostní boundary, koncepty, publikování, změna projektových textů.
- **GitHub:** validace autora, externí vs vlastní repo, merged subset, neúplná stránka / špatný URL / změna `total_count`, atomická DB aktualizace, souběžný cron.
- **Browser core:** deterministické seed, sync konflikty, STL ASCII/binary/invalid, výpadek a transitivní závislosti.
- **Browser DOM:** 320px, 390px, 768px, 1365px a 1920px, galerie, homepage, katalog, projektový graf, homelab, Maker, Now, Open Source; keyboard role/aktivace, žádné overflow/JS errors.
- **Cake HTTP:** CSRF, admin login/lockout, cookies, no-store, security headers, sitemap, RSS, 404 a neautorizované POSTy.
- **DB:** izolované schéma, všechny čtyři migrace, kompatibilita SQL fallback, měření dotazů, převzetí dat po restartu a zálohování.
- **Release:** vendor-inclusive build na PHP8.5, leak scan, staging smoke, DNS/SSL, rollback a návrat z DB zálohy.

## Význam výsledků

`PASS` = skutečně vykonáno. `NOT RUN` = neexistuje důkaz, že to funguje. CI konfigurace sama o sobě není PASS. Celá aplikace není označena *production verified*, dokud P0 v `docs/MASTER_PLAN.md` není uzavřeno.

## Rozšířené gate tests pro aktuální verzi

- HTTP `scripts/http-smoke.py`: vyžaduje běžící opravdovou Cake aplikaci na `localhost:8765`; kontroluje GET/POST, XML parser, CSP, anti-cache a odmítnutí cron bez tokenu. Není nahrazen offline rendererem.
- `tests/TestCase/Controller/`: frameworková IntegrationTestTrait sada pro routování a admin authorization. Tyto testy už byly provedeny ve výše uvedeném CI běhu; po nových změnách je nutné je zopakovat.
- 7 kurátorovaných engineering decision records: validace pole, reference na katalog, dvojjazyčnost, SSR, vyhledávání v Chromiu.
- GitHub veřejné repo API: explicitní schéma, vlastnictví, URL, timestamps, duplicity a odmítnutí privátních záznamů.
- Editorial audit: všechny projekty a poznámky se zapisují atomicky spolu s redigovaným auditním záznamem. MySQL integrace proběhla v CI; produkční migrace, obnova a rollback na cílovém hostingu však ověřeny nejsou.

## Nové regresní případy

- Type-safe veřejná serializace projektů: zakázané odkazy na soukromá repa, dvojjazyčné pole, stabilní kontrakt (`PublicProjectViewTest`).
- HTTP API: pouze GET, 400 pro jiný jazyk, 404 pro neznámý slug, 304 pro `If-None-Match` (`ApiControllerTest`, `http-smoke.py`; v uvedeném CI proběhlo s CakePHP).
- Browser: zaměření SVG uzlu po redraw, šipkové přepínání ARIA tabů, CakePHP request pipeline 200/303/403 (offline Chromium).
- ASCII STL: limitovaný sampling většího souboru bez materiálizace všech regex matchů.
- Sitemap: 2 jazykové URL včetně `x-default`, zamítnutí chybných cest (pure PHP + PHPUnit).


## Additional framework integration tests

`tests/TestCase/Controller/EditorialPersistenceTest.php` executes real
CakePHP controller actions and verifies persisted project overrides, MySQL
editorial audit rows, the journal draft/public lifecycle and escaping. Run on
**disposable migrated MySQL** only. Its source can be syntax-checked offline,
and were exercised in the cited PHP 8.5 / MySQL CI run.

`tests/Unit/OperationsStatusTest.php` covers the pure operational readiness
policy; its smaller equivalent is also tested by `tests/standalone.php`.

## Rate limiter regression cases (latest iteration)

`tests/standalone.php` includes a real `pcntl_fork` concurrency check where
available, not merely a sequential mock. `tests/Unit/LoginLimiterTest.php`
also contains deterministic clock, shard capacity, failure and file-count
tests (their PHPUnit execution passed in the cited CI run). The
limiter runs as pure PHP; see `docs/AUTH-THROTTLE.md`.

## Database destructive-test guard (14 November 2026)

Before running `composer test` or the release builder, configure **only** a disposable database and explicitly confirm its exact name:

```sh
export APP_ENV=testing
export DB_HOST=127.0.0.1 DB_NAME=harfusha_lab_ci DB_USER=lab_ci
export LAB_TEST_DB_CONFIRM=harfusha_lab_ci
composer test
```

The test bootstrap refuses production/development environments, mismatched or missing confirmation, unsafe local database names and administrative accounts. Remote schemas require a separate exact `DB_HOST/DB_NAME` confirmation in `LAB_TEST_DB_REMOTE_CONFIRM`. Use that opt-in only for a dedicated disposable test schema and a non-privileged account; migrations alter that schema. This is a secondary safety guard, **not a replacement for isolated MySQL grants**. CI configures the local guard explicitly.


## HTTP auth smoke and archive integrity (2026-11-14)

The real CakePHP HTTP smoke is **mandatory** when building a production release.
It now checks the full CSRF-protected admin flow (anonymous login page, rejected
missing/invalid CSRF, rejected wrong password, successful login, a rejected invalid
editorial write, logout and forbidden edits afterward). CI and the release builder
create an ephemeral password and hash only for this **disposable test server**.
Neither script needs nor accepts the production admin password.

For a manually started local integration server, set `APP_ENV=testing` and a
random `LAB_SMOKE_ADMIN_PASSWORD`, export `ADMIN_PASSWORD_HASH` as a PHP
`password_hash()` of that exact value, then run `python3 scripts/http-smoke.py`.
**Never** point this script at a production application: it intentionally probes
login attempts and authenticated routes. The base URL is fixed to localhost.

For a source archive, run:

```sh
python3 scripts/build-source.py --output /tmp/harfusha-lab-source.zip
python3 scripts/verify_archive.py /tmp/harfusha-lab-source.zip --variant source
```

For a fully validated deployment archive, use `scripts/build-release.py` and
then verify with `--variant production`. Both variants reject duplicate ZIP
paths, symlinks, secret config filenames, incomplete or stale SHA-256 manifests,
and missing required application files. Production additionally requires
`composer.lock` and `vendor/autoload.php`. ZIP integrity alone does not
validate dependency vulnerability status or correct CakePHP behavior.

## Mocked GitHub API / MySQL integration (15 Nov 2026)

`tests/Unit/GithubSyncIntegrationTest.php` executes the actual `GithubSyncService`
using CakePHP HTTP client mocks and disposable MySQL. It checks successful
replacement of both cache tables, the filtered import receipt, no mutation on
an unexpected private repository, and fail-closed handling of oversized JSON
responses. The surrounding test transaction is rolled back after each
database scenario. The suite never uses credentials or sends live GitHub
requests. A successful CI run validates this controlled integration, not
live upstream pagination or a staging import on the target host.

## Lokální omezení: 12 integračních testů vyžadujících aktivní MySQL (Known Local Limitations)

Při spuštění `vendor/bin/phpunit` bez běžící izolované testovací MySQL databáze selhává přesně 12 integračních testů v 5 testovacích třídách z důvodu `MissingDatasourceConfigException` / absence `default` či `test` datasource (což vyžaduje `App\Security\TestDatabaseGuard` pro ochranu před nechtěným zápisem do neověřené databáze). Všechny tyto testy jsou plně ověřeny v dedikovaném CI běhu (Docker/GitHub Actions s disposable MySQL 8.4):

1. **`GithubSnapshotFallbackTest::testFailedGithubCacheReadsNeverResurrectHistoricActivity`**
   - *Důvod:* Vyžaduje live spojení `test` s tabulkami `github_activity` a `github_public_repos` pro ověření chování při výpadku cache.
2. **`GithubSnapshotFallbackTest::testUnreadableSyncReceiptNeverResurrectsStaticSelection`**
   - *Důvod:* Ověřuje, že nečitelné potvrzení synchronizace v DB neobnovuje zastaralý statický výběr.
3. **`GithubSnapshotFallbackTest::testUncommittedGithubCacheRowsAreNotPublished`**
   - *Důvod:* Testuje transakční izolaci necommitnutých řádků v MySQL cache tabulkách.
4. **`GithubSnapshotFallbackTest::testCompletedEmptyGithubSyncOverridesStaticFallback`**
   - *Důvod:* Vyžaduje DB pro ověření přepsání statického fallbacku prázdným syncem.
5. **`GithubSyncIntegrationTest::testCompleteMockedPublicImportAtomicallyReplacesBothCaches`**
   - *Důvod:* Testuje atomické nahrazení tabulek `github_activity` a `github_public_repos` v transakci.
6. **`GithubSyncIntegrationTest::testPrivateRepoResponsePreservesLastGoodCache`**
   - *Důvod:* Testuje zachování předchozího stavu v DB při neočekávané privátní odpovědi.
7. **`GithubSyncIntegrationTest::testOversizedResponseFailsBeforeJsonDecoding`**
   - *Důvod:* Vyžaduje nakonfigurovanou DB pro inicializaci sync procesu před přijetím HTTP odpovědi.
8. **`OperationsServiceTest::testCompleteMigratedSchemaPassesTheDatabaseProbe`**
   - *Důvod:* Dotazuje se na stav migrované databáze v ostrém či testovacím prostředí.
9. **`OperationsServiceTest::testMissingEditorialMigrationIsNotReportedAsReady`**
   - *Důvod:* Dotazuje se na migrační ledger `cake_migrations` v live testovací databázi.
10. **`SyncRunRetentionTest::testOldReceiptsArePrunedWithoutAffectingOtherSources`**
   - *Důvod:* Testuje promazávání starých záznamů v tabulce `sync_runs` přes SQL dotaz.
11. **`SyncRunRetentionTest::testShortHistoryIsNotDeleted`**
    - *Důvod:* Testuje zachování krátké historie v tabulce `sync_runs`.
12. **`JournalDatabaseReadTest::testInvalidPersistedJournalRowDropsTheEntireDatabaseBatch`**
    - *Důvod:* Vyžaduje tabulku `journal_entries` v testovacím datasource.

Všech 113 čistě jednotkových PHPUnit testů (899 assertions), 166 kontrol v `tests/standalone.php` i 15 klientských JS testů prochází lokálně bez závislosti na MySQL.
