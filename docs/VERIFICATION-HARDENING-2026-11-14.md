# Harfusha Lab — verified iteration on 14 November 2026

This record is limited to commands that actually ran in the current isolated container, and distinguishes a source archive from a production deployment.

## Results observed

| Check | Outcome |
| --- | --- |
| PHP source parser (`php scripts/syntax-check.php`) | **PASS**, 99 PHP files; runtime PHP 8.4.24 (not 8.5) |
| Independent domain, validation and security tests (`php tests/standalone.php`) | **PASS**, 198 assertions |
| Node tests (`node --test tests/Client/lab-core.test.cjs`) | **PASS**, 15 tests |
| JS syntax (`node --check` both source files) | **PASS** |
| TypeScript strict JSDoc check (`tsc --noEmit ...`) | **PASS** with installed `tsc` (version determined by environment, not a project lock) |
| Python suite (`python3 -m unittest discover -s tests/Python -v`) | **PASS**, 15 tests (9 approval tests, 6 staging/release tests) |
| Privacy scan (`python3 scripts/audit-public.py`) | **PASS**, heuristic only |
| Offline HTML links and IDs (`python3 scripts/audit-rendered.py`) | **PASS**, 48 pages |
| Chromium offline browser QA (`python3 tests/Browser/smoke.py --chromium /usr/bin/chromium`) | **PASS**, 50 real browser cases |
| PHPUnit unsafe bootstrap (`php tests/bootstrap.php` without test DB config) | **PASS rejection** before autoload |
| Safe test DB configuration, but missing vendor | Test DB guard accepted safe configuration; execution subsequently stopped at missing vendor as expected |
| Production builder without `composer.lock` | **PASS rejection**, no ZIP generated |

## Changes in this iteration

- Mandatory test database guard before PHPUnit bootstrap (`src/Security/TestDatabaseGuard.php`) and matching CI configuration.
- Production-only Composer install in disposable stage, without destructive modifications to development `vendor` (`scripts/release_package.py` + `scripts/build-release.py`).
- Production ZIP contains `scripts/preflight.php`, no `.env`, no approval receipt, no tests or development tools, and has a deterministic `SHA256SUMS` manifest.
- Dedicated package exclusion, symlink and integrity tests (`tests/Python/test_release_package.py`).
- Documentation updated (`docs/RELEASE-HARDENING-2026-11-14.md`, `docs/MASTER_PLAN.md`, release/deployment/testing guides).

## Blocking unknowns / release criteria

**Not executed and NOT passed:** PHP 8.5.12+ interpreter and extensions, Composer dependency solver, `composer.lock`, CakePHP web runtime, PHPStan max + strict extensions, CakePHP-backed PHPUnit, MySQL 8.4 migrations / rollback and query behavior, actual hosting deployment, manual editorial approval and production backup/restore. The current environment cannot retrieve Composer packages from the network. No success is inferred from configuration files.

The downloadable ZIP is a **source-only** archive. It is **not** an approved or deployable production build.
