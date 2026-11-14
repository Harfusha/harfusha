# Release gates — PHP 8.5 / CakePHP 5.4

## Non-negotiable acceptance

- An actual runtime of **PHP 8.5.12+** and required extensions (Web and CLI) is available. No Composer `--ignore-platform-reqs` or fake platform version.
- Resolve and **commit `composer.lock`** on PHP 8.5 after reviewing dependencies. `composer validate --strict` must pass.
- The code passes **PHPStan level max + strict rules + ShipMonk + CakeDC** without ignores/baseline. Check that the installed extension configuration is valid.
- PHPUnit unit and CakePHP integration tests run against an isolated MySQL database after all three Migrations. Do not run CI against production.
- CakePHP CodeSniffer and privacy checks pass; 48 bilingual pages are rendered and their internal links, assets and IDs audited.
- Browser manual QA: keyboard navigation, focus, mobile, both languages, graph interaction, sync conflicts, STL upload on real browsers, reduced motion preference, CSP and console errors.
- Manual review approves publication of every case study derived from a **private** GitHub repository; public topology reveals no private addressing, hostnames, secrets or administrative links.
- Verify `composer install --no-dev` results and test production artifact under an isolated staging domain before DNS cutover.
- Test MySQL backup **and restore**, error responses, cron behavior, failure fallback and rollback of the previous release.

## Automated production builder

When `composer.lock` is present, dev packages are installed, MySQL test database is explicitly confirmed and TypeScript 5.8.3 is installed in `node_modules` and Chromium/Playwright is configured, run:

```sh
npm install --no-save --ignore-scripts typescript@5.8.3
python3 scripts/build-release.py --output ../harfusha-lab-deploy.zip
```

The builder checks PHP and required extensions and executes:

1. `composer validate --strict` and `composer audit --locked`
2. `php bin/cake.php migrations migrate --no-interaction` na **výslovně izolované** DB s názvem zakončeným `_test`, `_testing` nebo `_ci` a hodnotou `LAB_TEST_DB_CONFIRM` shodnou s `DB_NAME` (lokální host, neprivilegovaný účet); pak `composer quality` = PHP syntax, independent tests, PHPStan, PHPUnit and CakePHP CodeSniffer
3. public privacy and rendered HTML audits
4. JS parser, deterministic Node tests, offline Chromium DOM smoke and strict JSDoc typecheck
5. Kopie schváleného zdrojového stromu do izolovaného staging adresáře a Composer production install (`--no-dev`) **pouze v něm**
6. vendor-inclusive ZIP včetně `scripts/preflight.php` a SHA256 manifestu, excluding `.env`, local secrets, caches, test files and unneeded dev folders

**Do not treat manual approval, passing syntax or a configured CI workflow as proof of a successful release.** The delivered source ZIP has not run this builder, because PHP 8.5/Composer/MySQL are unavailable in the authoring runtime.

## Apache/shared hosting

Set the only document root to `webroot/` or apply the sibling `public_html` layout. Set `APP_ENV=production`, `DEBUG=false`, canonical HTTPS `APP_URL`, random salts/password hash and DB config outside the public root. Use OPcache and daily GitHub CLI cron. Review `docs/DEPLOYMENT.md`.

Read `docs/MASTER_PLAN.md` for the full completion matrix, manual content requirements, staging and rollback gates.

**Doplnění:** Release build ověřuje skutečný migrační příkaz a chrání databázi podmínkou názvu. Nesmí mít dostupné produkční přihlašovací údaje. `composer.lock` chybí do prvního opravdového řešení závislostí na PHP 8.5; build nesmí předstírat úspěch. Veřejné API má samostatnou HTTP gate v `scripts/http-smoke.py` a typovaný kontrakt v `docs/API.md`.

## Mandatory private-derived content approval gate (14 Nov 2026)

Before the production build run `python3 scripts/review-public-content.py --review`, **actually inspect the content**, then interactively run `python3 scripts/review-public-content.py --approve --reviewer 'Your label'` and verify it. The release builder fails if the local receipt is missing or hashes differ; it never creates or ships this receipt. Independent review of images/templates and host configuration is still required. See `docs/CONTENT-APPROVAL.md`.

**Pozor:** Produktový ZIP neobsahuje `.env` ani `.env.example`. Před prvním spuštěním si z privátního zdrojového vzoru `.env.example` vytvořte `.env` mimo veřejný DocumentRoot. Podrobnosti: `docs/RELEASE-HARDENING-2026-11-14.md`.
