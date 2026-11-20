# Verification supplement — 2026-11-13 (follow-up)

> **Historical run record.** Results apply only to the revision and environment recorded below; current status: [MASTER_PLAN.md](MASTER_PLAN.md).

This is a follow-up to `VERIFICATION-2026-11-13.md`. It reports tests actually
executed in the current authoring container. It does **not** supersede the
unverified release blockers in `MASTER_PLAN.md`.

## Confirmed passes

| Gate | Observed result | How to reproduce |
|---|---|---|
| PHP syntax | 93 PHP source/test files; 0 errors | `php scripts/syntax-check.php` |
| Isolated PHP checks | 150 PASS, 0 FAIL | `php tests/standalone.php` |
| Browser JS syntax | Both application JS files pass | `node --check webroot/assets/js/{app,lab-core}.js` |
| Strict JS core typing | TypeScript 5.8.3 PASS | `tsc --noEmit --allowJs --checkJs --strict --target ES2022 --lib ES2022,DOM webroot/assets/js/lab-core.js` |
| Deterministic JS tests | 13 PASS | `node --test tests/Client/lab-core.test.cjs` |
| Curated public data audit | PASS | `python3 scripts/audit-public.py` |
| Offline SSR HTML audit | 48 pages PASS | `python3 scripts/audit-rendered.py` |
| Chromium UI smoke | 50 page/viewport combinations PASS | `python3 tests/Browser/smoke.py --chromium /usr/bin/chromium` |
| ZIP integrity | Full CRC test + included `SHA256SUMS` manifest PASS | `python3 scripts/build-source.py --output /path/out.zip` |

## Newly authored but **not run**

- `tests/Unit/OperationsStatusTest.php`, `tests/Unit/AdminCredentialsTest.php`:
  these are PHPUnit tests requiring Composer/PHPUnit. Selected policy cases were
  separately covered by `tests/standalone.php`.
- `tests/TestCase/Controller/EditorialPersistenceTest.php`: full CakePHP HTTP
  requests, ORM writes, audit receipts, journal draft/publish/unpublish and HTML
  escaping against migrated MySQL. Syntax only, **not integration tested**.
- `php bin/cake.php lab_health`: needs a migrated live MySQL connection and a
  successful GitHub sync; its pure classification policy has isolated tests.
- Enhanced `scripts/preflight.php`: on the authoring runtime (PHP 8.4 without
  intl, mbstring, pdo_mysql and simplexml) it fails closed as intended.

## Open release blockers

There is no `composer.lock` or `vendor/` in this **source** bundle. The
sandbox has PHP 8.4 rather than the mandated PHP 8.5.12+, no Composer and no
MySQL driver/server. Consequently PHPStan max, CakePHP PHPUnit, Migrations,
PDO transaction behavior, exact HTTP middleware behavior and host staging
remain **unverified**. Do not claim `production-ready` on the strength of the
above passes alone. Consult `docs/MASTER_PLAN.md` for the acceptance list.
