# Testování a důkazní materiály — 13. 11. 2026

## Co bylo skutečně ověřeno v prostředí sestavení

| Kontrola | Výsledek | Příkaz / omezení |
|---|---|---|
| PHP syntaxe všech zdrojů | PASS, aktuální počet určí skript | `php scripts/syntax-check.php` — lokální PHP 8.4, **není to PHP 8.5 runtime** |
| Pure PHP doména a šablony | PASS | `php tests/standalone.php` — skutečné počty v aktuálním výpisu |
| Veřejná data a heuristická privacy kontrola | PASS | `python3 scripts/audit-public.py` |
| 48 dvojjazyčných HTML stránek + interní odkazy, assets, ID | PASS | `python3 scripts/audit-rendered.py` |
| 13 JS regresních testů | PASS | `node --test tests/Client/*.test.cjs` |
| Syntaxe JS | PASS | `node --check webroot/assets/js/*.js` (spouštět každý soubor zvlášť) |
| Striktní JSDoc/TS jádro simulací | Dříve provedeno; znovu ověřit v CI | `npx --no-install tsc --noEmit --allowJs --checkJs --strict ...` |
| Skutečný Chromium offline-DOM, klávesnice, mobil | PASS: 50 kombinací stránka/viewport | `python3 tests/Browser/smoke.py --chromium=/usr/bin/chromium` |
| CakePHP bootstrap, ORM a HTTP na PHP 8.5 | **NOT RUN** | Composer a vendor v tomto prostředí chybí |
| MySQL/migrace/rollback | **NOT RUN** | MySQL a `pdo_mysql` chybí |
| PHPUnit/CakePHP integrace | **NOT RUN** | Frameworkové závislosti chybí |
| PHPStan max/strict/ShipMonk/CakeDC | **NOT RUN** | Závislosti nejsou nainstalované |
| PHPCS | **NOT RUN** | Composer není k dispozici |
| HTTP/HTTPS CSP ověření v prohlížeči | **NOT RUN** | Sandbox blokuje navigaci do lokálního HTTP i file URL |
| Staging/hosting/backup-restore | **NOT RUN** | Nutné infrastrukturní přístupy a nasazení |

Test v Chromium je skutečný browser test, ale záměrně vkládá skutečné vygenerované HTML/CSS/JS přímo do dokumentu. **Neprokazuje** HTTP hlavičky, cookies, Cake routování ani PHP-FPM. Screenshoty v `docs/screenshots` jsou výstupem tohoto testu, nikoliv fotografie jiných projektů.

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

**Nesnižovat PHPStan level ani nevytvářet baseline.** Povinné nadstavby: CakeDC, PHPStan strict rules, ShipMonk, deprecation rules a PHPUnit extension. Každý nový problém se řeší konkrétní opravou kódu a regresním testem.

## Testovací matice

- **Doména:** immutable Project, povolené statusy, validace JSON struktur, provázanost grafu, cykly topologie, URL allowlist.
- **Editorial:** validace UTF-8, délky, typy formulářů, bezpečnostní boundary, koncepty, publikování, změna projektových textů.
- **GitHub:** validace autora, externí vs vlastní repo, merged subset, neúplná stránka / špatný URL / změna `total_count`, atomická DB aktualizace, souběžný cron.
- **Browser core:** deterministické seed, sync konflikty, STL ASCII/binary/invalid, výpadek a transitivní závislosti.
- **Browser DOM:** 320px, 390px, 768px, 1365px a 1920px, galerie, homepage, katalog, projektový graf, homelab, Maker, Now, Open Source; keyboard role/aktivace, žádné overflow/JS errors.
- **Cake HTTP:** CSRF, admin login/lockout, cookies, no-store, security headers, sitemap, RSS, 404 a neautorizované POSTy.
- **DB:** izolované schéma, všechny tři migrace, kompatibilita SQL fallback, měření dotazů, převzetí dat po restartu a zálohování.
- **Release:** vendor-inclusive build na PHP8.5, leak scan, staging smoke, DNS/SSL, rollback a návrat z DB zálohy.

## Význam výsledků

`PASS` = skutečně vykonáno. `NOT RUN` = neexistuje důkaz, že to funguje. CI konfigurace sama o sobě není PASS. Celá aplikace není označena *production verified*, dokud P0 v `docs/MASTER_PLAN.md` není uzavřeno.

## Rozšířené gate tests pro aktuální verzi

- HTTP `scripts/http-smoke.py`: vyžaduje běžící opravdovou Cake aplikaci na `localhost:8765`; kontroluje GET/POST, XML parser, CSP, anti-cache a odmítnutí cron bez tokenu. Není nahrazen offline rendererem.
- `tests/TestCase/Controller/`: frameworková IntegrationTestTrait sada pro routování a admin authorization. Bude provedena až na PHP 8.5 s CakePHP.
- 7 kurátorovaných engineering decision records: validace pole, reference na katalog, dvojjazyčnost, SSR, vyhledávání v Chromiu.
- GitHub veřejné repo API: explicitní schéma, vlastnictví, URL, timestamps, duplicity a odmítnutí privátních záznamů.
- Editorial audit: všechny projekty a poznámky se zapisují atomicky spolu s redigovaným auditním záznamem. MySQL transakce **zatím nemají ověřený end-to-end běh**.

## Nové regresní případy

- Type-safe veřejná serializace projektů: zakázané odkazy na soukromá repa, dvojjazyčné pole, stabilní kontrakt (`PublicProjectViewTest`).
- HTTP API: pouze GET, 400 pro jiný jazyk, 404 pro neznámý slug, 304 pro `If-None-Match` (`ApiControllerTest`, `http-smoke.py`; **neprovedeno bez CakePHP**).
- Browser: zaměření SVG uzlu po redraw, šipkové přepínání ARIA tabů, CakePHP request pipeline 200/303/403 (offline Chromium).
- ASCII STL: limitovaný sampling většího souboru bez materiálizace všech regex matchů.
- Sitemap: 2 jazykové URL včetně `x-default`, zamítnutí chybných cest (pure PHP + PHPUnit).


## Additional framework integration tests

`tests/TestCase/Controller/EditorialPersistenceTest.php` executes real
CakePHP controller actions and verifies persisted project overrides, MySQL
editorial audit rows, the journal draft/public lifecycle and escaping. Run on
**disposable migrated MySQL** only. Its source can be syntax-checked offline,
but a PASS requires real PHP 8.5 + Composer + Cake ORM + MySQL.

`tests/Unit/OperationsStatusTest.php` covers the pure operational readiness
policy; its smaller equivalent is also tested by `tests/standalone.php`.

## Rate limiter regression cases (latest iteration)

`tests/standalone.php` includes a real `pcntl_fork` concurrency check where
available, not merely a sequential mock. `tests/Unit/LoginLimiterTest.php`
also contains deterministic clock, shard capacity, failure and file-count
tests (their PHPUnit execution is **not verified** without Composer). The
limiter runs as pure PHP; see `docs/AUTH-THROTTLE.md`.

## Database destructive-test guard (14 November 2026)

Before running `composer test` or the release builder, configure **only** a disposable database and explicitly confirm its exact name:

```sh
export APP_ENV=testing
export DB_HOST=127.0.0.1 DB_NAME=harfusha_lab_ci DB_USER=lab_ci
export LAB_TEST_DB_CONFIRM=harfusha_lab_ci
composer test
```

The test bootstrap refuses production/development environments, mismatched or missing confirmation, non-test database names, remote DB hosts and administrative accounts. This is a secondary safety guard, **not a replacement for isolated MySQL grants and a dedicated test database**. CI configures it explicitly.


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
