# Follow-up verification — 14 November 2026

> **Historical run record.** Results apply only to the revision and environment recorded below; current status: [MASTER_PLAN.md](MASTER_PLAN.md).

## Actual checks run in this authoring environment

- `php -v`: PHP 8.4.24 CLI, **not** the required PHP 8.5.12+; `composer` absent, ext-intl, ext-mbstring, ext-pdo_mysql and ext-simplexml not installed.
- `php scripts/syntax-check.php`: PHP source parsed successfully; this is **not** CakePHP execution or PHPStan validation.
- `php tests/standalone.php`: **192 PASS, 0 FAIL**, including the new search/repository pagination integrity tests.
- `python3 -m unittest discover -s tests/Python`: **9 passed**, including missing/unreviewed/mutated public data, templates, missing confirmation and malformed receipt.
- `node --test tests/Client/*.test.cjs`: **15 passed**, browser-side deterministic core.
- `tsc --noEmit --allowJs --checkJs --strict ... webroot/assets/js/lab-core.js`: passed in local Node/TypeScript environment.
- `python3 scripts/audit-public.py`: passed curated privacy heuristics.
- `python3 scripts/audit-rendered.py`: **48** bilingual offline-rendered pages passed internal links, IDs, assets and simple HTML checks.
- `python3 tests/Browser/smoke.py --chromium /usr/bin/chromium`: **50** real local Chromium DOM/viewport checks passed. An initial invocation without a system-Chromium override failed because Playwright's own browser was missing; it succeeded on rerun with the installed system binary.
- Production content approval deliberately **not** performed. `python3 scripts/review-public-content.py --verify` returns a nonzero exit until the author manually reviews and approves the public data, templates and assets. This is an expected release block, not a passed review.

## Code changes in this iteration

1. Replaced permissive count-only GitHub pagination logic with deterministic, typed `GithubSearchPageCollector` and `GithubRepositoryPageCollector`. They reject duplicates, invalid payloads, contradictory counts, page-budget overruns and missing final pages before any database mutation.
2. Added a human-controlled publication gate. It hashes the 8 curated public JSON files, templates, bundled browser assets and provided screenshots; every changed byte invalidates the private local receipt. Automated CI executes verifier unit tests **without** self-approving content.
3. Updated source/build workflows, unit and isolated tests, operational docs, security model and `docs/MASTER_PLAN.md`.

## Not verified, release-blocking

- Real PHP 8.5.12+ server and CLI, Composer solving/lockfile, installed CakePHP 5.4 vendor tree.
- PHPStan max with CakeDC, ShipMonk, strict/deprecation rules, PHPCS, Rector, framework PHPUnit.
- MySQL migrations/transactions/rollback and actual HTTP security/session tests against CakePHP.
- Live public GitHub API and synchronization consistency under real rate limits.
- Author's real content approval and an actual staging deployment on the target shared hosting.

Result: **SOURCE CANDIDATE ONLY — NOT PRODUCTION VERIFIED OR APPROVED FOR PUBLIC DEPLOYMENT**.
