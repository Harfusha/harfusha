# Harfusha Lab — source candidate 2026-11-13

## Verified CI milestone — 14 Nov 2026

All three GitHub Actions jobs passed on PHP 8.5 and MySQL 8.4 in [run #43648999069](https://github.com/Harfusha/harfusha/actions/runs/43648999069): application, static and browser-offline. The reproducible `composer.lock` is committed; PHPStan max + strict/CakeDC/ShipMonk, PHPUnit, CakePHP HTTP checks, real MySQL migrations, PHPCS and Chromium regressions passed. This is **CI verification only**, not authorization to deploy to shared hosting. Target-host staging, rollback/restore tests and human review of any private-project descriptions/screenshots remain release blockers.

## New in final follow-up (2026-11-13)

- **Type-safe read-only public JSON API**: `/api/v1/projects` and `/api/v1/projects/{slug}`, CS/EN, reviewed DTO fields, no private source URLs, conditional SHA-256 ETag/304, no auth or permissive CORS. Added unit tests and real HTTP smoke gate code (`docs/API.md`).
- **Fifth interactive demo — CakePHP Request Lab**: deterministic educational backend-request walkthrough with host, method, CSRF, authentication, validation and response paths. 3 new deterministic JS regression tests (13 total), tested in Chromium.
- SVG selected-node keyboard focus preserved after DOM redraw, role/button/ARIA state for homelab; ARIA tab arrow-key navigation.
- ASCII STL parsing memory reduced through two bounded scans; maintains file/triangle limits.
- New reviewed `PublicSitemap` builder with cs/en/x-default alternates and path validation, plus unit/standalone tests.
- Release builder now requires disposable MySQL DB name and **actually runs migrations** before PHPUnit and PHPStan gates. Staging/production deployment still unverified.
- Real browser screenshots of the new module under `docs/screenshots/`.

## New in this iteration

- PHP 8.5 production configuration hardened by `DeploymentPolicy`: explicit environment, patched PHP, valid HTTPS canonical origin, debug off and random hex salt.
- **Engineering Decisions:** seven bilingual, evidence-conscious technical design notes and trade-offs with a native `<details>` explorer, filter and project links.
- **Editorial audit:** third Phinx/Cake migration; admin mutations write atomic SHA-256 receipts without full text. Drafts receive their public timestamp on first publication.
- **GitHub import hardening:** validates all public repository records, canonical ownership/URLs, visibility and timestamps *before* deleting the previous snapshot. Bad/empty/duplicate listings abort.
- **Tests:** 141 standalone PHP assertions, 13 deterministic JS tests, PHP syntax pass on 86 files, 48 offline rendered pages, and 50 actual Chromium checks across 320/390/768/1365/1920px.
- **Actual runtime gates prepared:** PHPUnit CakePHP HTTP integration tests, `scripts/http-smoke.py`, and a lockfile-only CI. These cannot be executed here without Composer/CakePHP/PHP 8.5/MySQL.
- Fixed a real mobile layout bug in the Open Source page at 320px.
- Added real desktop/mobile screenshots of the Engineering Decisions page, updated architecture and release documentation, retained the detailed `docs/MASTER_PLAN.md`.

## Executed successfully in the authoring container

`php scripts/syntax-check.php`; `php tests/standalone.php`; `node --test tests/Client/lab-core.test.cjs`; `node --check`; TypeScript 5.8.3 `--checkJs --strict` of the browser core; `python3 scripts/audit-public.py`; `python3 scripts/audit-rendered.py`; `python3 tests/Browser/smoke.py --chromium /usr/bin/chromium`.

## Not verified — mandatory before public deployment

- Real PHP 8.5 runtime; locally available PHP is 8.4 without the required extensions.
- Composer resolution / `composer.lock` / installed CakePHP dependencies.
- PHPStan max, CakeDC, ShipMonk, PHPUnit, Cake ORM/MySQL migrations, transactional save and audit; configured but **not run**.
- Real CakePHP HTTP runtime (including auth, CSRF, cron, cookies, XML and security headers). The new smoke test is **code, not a PASS**.
- Staging on the actual shared host, backup/restore and rollback, authenticated admin operation and real GitHub cron.
- Manual privacy review of descriptions of private projects, source images, proof-backed benchmark metrics and project-level visual assets.

**Classification: FULL SOURCE CANDIDATE; NOT PRODUCTION-VERIFIED.** The ZIP is not a runnable vendor-inclusive build. `docs/MASTER_PLAN.md` lists the outstanding release blockers and exact acceptance criteria.

## Additional iteration — private operational readiness and persistence coverage

- Implemented safe private operational status (`OperationsStatus`, `OperationsService`),
  a gated CLI health check (`bin/cake.php lab_health`), and an authenticated admin
  status panel. The public `/healthz` is explicitly **liveness only**, now
  `Cache-Control: no-store`.
- Added deterministic standalone and PHPUnit tests for fresh, stale, missing,
  future and inaccessible GitHub sync signals.
- Added actual MySQL/CakePHP integration test source for project overrides,
  transactional audit receipts, journal draft → publish → unpublish and escaped
  untrusted HTML. These integration tests are **not verified** in this environment.
- See `docs/OPERATIONS.md` for deployment checks and incident guidance.

- Fixed a real login boundary bug: passwords retain significant leading/trailing whitespace. A dedicated typed credential verifier rejects invalid form types and excessive input sizes without normalization.

- Hardened `scripts/preflight.php`: flags absent lockfile, invalid admin password hashes, weak configured cron token, production debug mode and world-accessible `.env` permissions. These checks require target runtime verification.

## Continuation — bounded concurrent login security (2026-11-13)

- Replaced one-file-per-IP `LoginLimiter::allow()/fail()` with atomic
  `consume()/clear()` under 32 fixed, locked shards; maximum 512 active
  identities/shard and 256 KiB/shard; 15-minute five-attempt sliding window.
- Rejects corrupted/full/unwritable state and clock rollback rather than
  silently allowing authentication. Admin 429 includes `Retry-After: 900`.
- Added deterministic unit/standalone cases and a real process concurrency
  regression (`pcntl_fork` when available).
- Added `docs/AUTH-THROTTLE.md` and optional manual lockfile resolution workflow
  to help address the existing Composer blocker without fabricating a lockfile.
- Full CakePHP/Composer/PHPStan/MySQL gates remain UNVERIFIED in this container.

## Additional iteration — Data Paths Explorer (2026-11-14)

- Added four safe, bilingual, explicitly **conceptual** scenarios to the Home Lab topology: media playback, remote access, network printing and service backup. They are not packet captures or statements of actual network addressing.
- Typed `App\Domain\NetworkFlow` validates step counts, topology adjacency, identifiers and bilingual descriptions. The curated boundary now accepts `flows.json` with strict schema checks.
- `lab-core.js` exposes `traceFlow()` (deterministic, dependency-aware, no remote access), driving the graphical highlighted links, trace explanations and outage state. The simulation does not probe machines.
- Added pure PHPUnit tests, standalone regression assertions, Node tests and Chromium interactive tests; CSS reduces animation on user preference.
- Added `docs/DATA-PATHS.md`, this release record, and desktop/mobile screenshots.
- PHP 8.5 + CakePHP/Composer/PHPStan/MySQL gates **remain unverified**; do not call this a production build.

## Iteration 2026-11-14 — public data integrity and publication approval

- Refactored `GithubSyncService` to use `GithubSearchPageCollector` and `GithubRepositoryPageCollector`. Search pagination now checks duplicates, total count drift, missing/malformed URLs, incomplete page bodies and the 1,000-result API ceiling. Repository pagination detects duplicates across pages, malformed pages and incomplete end of listing. The transactional DB update remains unchanged and only begins after successful validation.
- Added `tests/Unit/GithubPaginationTest.php`, independent regression assertions and documentation `docs/GITHUB-IMPORT-INTEGRITY.md`.
- Added `scripts/review-public-content.py` and tested manual publication gate. `scripts/build-release.py` now requires a matching private, non-packaged, human-approved SHA-256 receipt. This avoids treating a code audit as permission to publish private-derived case studies.
- **Limits:** no Composer lockfile, vendor dependencies or PHP 8.5 runtime were resolved in this environment, so PHPStan/real CakePHP/MySQL checks remain blocked. No private content has been approved on the user's behalf.

## Iteration — 14 November 2026, clean release & test DB hardening

- `TestDatabaseGuard` prevents CakePHP PHPUnit from touching a non-disposable MySQL database unless the environment, schema name, database host, unprivileged user and exact confirmation all pass. Guard runs before any framework/fixture initialization; CI exports `LAB_TEST_DB_CONFIRM`.
- `build-release.py` now installs production-only Composer dependencies in isolated temporary staging rather than altering the source's development `vendor/` tree. Dedicated `release_package.py` creates a deterministic SHA-256-verified production ZIP, excludes private files/symlinks and includes `scripts/preflight.php`.
- Added six Python archive/staging tests, PHPUnit guard test with data provider and six standalone guard assertions. Updated deployment and release documentation.
- Executed checks: 99 PHP syntax, 198 standalone assertions, 15 Python unit tests, 15 Node client tests, strict JS JSDoc/type checks, 48 HTML pages and 50 real Chromium offline checks. **Composer, PHPStan, MySQL, CakePHP/PHPUnit and production hosting still unverified.**

Detailed execution record: `docs/VERIFICATION-HARDENING-2026-11-14.md`. This is a source candidate, not a validated production build.


## 2026-11-14 — HTTP auth integration assertions and independent package checks

- Extended the actual CakePHP HTTP smoke to exercise login, CSRF, HTTP cookies,
  session renewal, editor validation, logout and access revocation. CI generates
  an ephemeral hash rather than testing with any real administrator credentials.
- Added independent `verify_archive.py` coverage for SHA-256 manifests, ZIP
  path traversal, duplicate entries, symbolic links, missing dependencies and
  private config filenames. Both archive builders now verify their own results.
- Added 12 Python regression cases for the archive validator and smoke harness,
  plus an automated source ZIP integrity check in CI.
- PHP 8.5 / Composer / MySQL remain unavailable in this container. These changes
  must still pass the actual framework and static analysis gates on PHP 8.5.12+.
