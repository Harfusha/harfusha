# Release gates — PHP 8.5 / CakePHP 5.4

## Non-negotiable acceptance

- An actual runtime of **PHP 8.5.12+** and required extensions (Web and CLI) is available. No Composer `--ignore-platform-reqs` or fake platform version.
- Use the already committed, reviewed **`composer.lock`** with `composer install` on PHP 8.5. Future dependency updates require review and a fresh `composer validate --strict` and CI run.
- The code passes **PHPStan level max + strict rules + ShipMonk + CakeDC** without ignores/baseline. Check that the installed extension configuration is valid.
- PHPUnit unit and CakePHP integration tests run against an isolated MySQL database after all three Migrations. Do not run CI against production.
- CakePHP CodeSniffer and privacy checks pass; 48 bilingual pages are rendered and their internal links, assets and IDs audited.
- Browser manual QA: keyboard navigation, focus, mobile, both languages, graph interaction, sync conflicts, STL upload on real browsers, reduced motion preference, CSP and console errors.
- Manual review approves publication of every case study derived from a **private** GitHub repository; public topology reveals no private addressing, hostnames, secrets or administrative links.
- Verify `composer install --no-dev` results and test the production artifact under an isolated HTTPS staging domain before DNS cutover. Run `python3 scripts/staging-smoke.py --base-url https://staging.example.com` from the source checkout and require zero findings; this is a read-only HTTP check, not a deployment substitute.
- Test MySQL backup **and restore**, error responses, cron behavior, failure fallback and rollback of the previous release.

## Automated production builder

With the committed `composer.lock`, dev packages installed, a MySQL test database explicitly confirmed and TypeScript 5.8.3 is installed in `node_modules` and Chromium/Playwright is configured, run:

```sh
npm install --no-save --ignore-scripts typescript@5.8.3
python3 scripts/build-release.py --output ../harfusha-lab-deploy.zip
```

On Windows, set `COMPOSER_PHAR` to the installed `composer.phar` path (for example `C:\php\composer.phar`). The builder invokes that PHAR through PHP directly instead of relying on a `.bat` launcher.

The builder checks PHP and required extensions and executes:

1. `composer validate --strict` and `composer audit --locked`
2. `php bin/cake.php migrations migrate` na **výslovně izolované** DB s názvem zakončeným `_test`, `_testing` nebo `_ci` a hodnotou `LAB_TEST_DB_CONFIRM` shodnou s `DB_NAME` (lokální host, neprivilegovaný účet); pak `composer quality` = PHP syntax, independent tests, PHPStan, PHPUnit and CakePHP CodeSniffer
3. public privacy and rendered HTML audits
4. JS parser, deterministic Node tests, offline Chromium DOM smoke and strict JSDoc typecheck
5. Kopie schváleného zdrojového stromu do izolovaného staging adresáře a Composer production install (`--no-dev`) **pouze v něm**
6. vendor-inclusive ZIP včetně `scripts/preflight.php` a SHA256 manifestu, excluding `.env`, local secrets, caches, test files and unneeded dev folders

**Latest confirmed CI evidence (15 Nov 2026):** [GitHub Actions run #43658555206](https://github.com/Harfusha/harfusha/actions/runs/43658555206) passed all three jobs on PHP 8.5 / MySQL 8.4, including locked Composer installation and audit, migrations, PHPStan max/strict/ShipMonk, PHPUnit, HTTP smoke, PHPCS, Chromium and an isolated `composer install --no-dev` plus platform requirements check. This **does not** prove that the production builder has completed its human-approved release process, a production ZIP has been deployed, or that shared-host staging, manual editorial review and database restore/rollback have passed.

## Apache/shared hosting

Set the only document root to `webroot/` or apply the sibling `public_html` layout. Set `APP_ENV=production`, `DEBUG=false`, canonical HTTPS `APP_URL`, random salts/password hash and DB config outside the public root. Use OPcache and daily GitHub CLI cron. Review `docs/DEPLOYMENT.md`.

Read `docs/MASTER_PLAN.md` for the full completion matrix, manual content requirements, staging and rollback gates.

**Doplnění:** Release build ověřuje skutečný migrační příkaz a chrání databázi podmínkou názvu. Nesmí mít dostupné produkční přihlašovací údaje. `composer.lock` je již verzovaný a ověřený v CI; build nesmí předstírat úspěch na cílovém hostingu. Veřejné API má samostatnou HTTP gate v `scripts/http-smoke.py` a typovaný kontrakt v `docs/API.md`.

## Mandatory private-derived content approval gate (14 Nov 2026)

Before the production build run `python3 scripts/review-public-content.py --review`, **actually inspect the content**, then interactively run `python3 scripts/review-public-content.py --approve --reviewer 'Your label'` and verify it. The release builder fails if the local receipt is missing or hashes differ; it never creates or ships this receipt. Independent review of images/templates and host configuration is still required. See `docs/CONTENT-APPROVAL.md`.

**Pozor:** Produktový ZIP neobsahuje `.env` ani `.env.example`. Před prvním spuštěním si z privátního zdrojového vzoru `.env.example` vytvořte `.env` mimo veřejný DocumentRoot. Podrobnosti: `docs/RELEASE-HARDENING-2026-11-14.md`.
