# Verification record — bounded login limiter, 2026-11-13

## Scope

This is an **incremental source update** on top of the previous Harfusha Lab
source archive. The prior full CakePHP/MySQL release blockers remain open; this
record does not silently mark them complete.

### Changes inspected

- `src/Security/LoginLimiter.php`: removed per-address files and race-prone
  `allow()/fail()`; added fixed-shard atomic `consume()/clear()` and fail-closed
  bounds, corruption handling, exact sliding window and clock rollback handling.
- `src/Controller/AdminController.php`: attempts are reserved before password
  verification; valid credentials clear the reservation before session issuance;
  429 carries a `Retry-After: 900` header.
- `tests/Unit/LoginLimiterTest.php`: deterministic 15-minute expiry, 5-attempt
  limit, isolation, same-shard saturation, corrupt/oversized state, symlink,
  clock rollback, file-count cap. **Written, not run under PHPUnit.**
- `tests/standalone.php`: same core invariants, 16-fork concurrent test on an
  interpreter with `pcntl` available. Suppressed benign mkdir EEXIST races are
  handled correctly by the strict test error handler.
- `.github/workflows/resolve-lockfile.yml`: manual PHP 8.5 Composer resolver
  that uploads an artifact for human review and commit. **Not executed.**

## Observed successful tests in the current container

| Executed command | Observed result |
|---|---|
| `php scripts/syntax-check.php` | **93** PHP files, 0 syntax errors (PHP **8.4.24**) |
| `php tests/standalone.php` | **165 PASS, 0 FAIL**; includes real 16-process lock contention |
| `php tests/standalone.php` repeated five times | **All five runs 165 PASS, 0 FAIL** |
| `node --check webroot/assets/js/app.js` | PASS |
| `node --check webroot/assets/js/lab-core.js` | PASS |
| `node --test tests/Client/lab-core.test.cjs` | **13 PASS** |
| `tsc --noEmit --allowJs --checkJs --strict --target ES2022 --lib ES2022,DOM webroot/assets/js/lab-core.js` | PASS (TypeScript 5.8.3) |
| `python3 scripts/audit-public.py` | PASS |
| `python3 scripts/audit-rendered.py` | **48 pages**; links, assets and IDs PASS |
| `python3 tests/Browser/smoke.py --chromium /usr/bin/chromium` | **50 real Chromium viewport/page checks** PASS |
| Source ZIP CRC + SHA256SUMS verification | Performed during source archive creation; independently verify on extraction |

## Not executed and must remain release blockers

- PHP 8.5.12+ interpreter, PHP-FPM and required extensions.
- Composer dependency resolution, `composer.lock` generation and security audit.
- CakePHP bootstrap/routing/middleware, PHPUnit integration and ORM persistence.
- **PHPStan level max** including CakeDC, ShipMonk and strict-rules extensions.
- MySQL migrations and rollback, staging HTTPS/cookie/CSRF checks, real cron.
- Real target-host permissions and file-lock behavior on its filesystem.
- Human review of private project descriptions and network abstraction.

The archive is a **source candidate**, not a vendor-inclusive production build.
`docs/MASTER_PLAN.md` remains the canonical acceptance and release checklist.
