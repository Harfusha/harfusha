# Harfusha Lab — verification evidence (14 November 2026)

## Scope

Continuation of the 13 November complete source candidate. Added the
privacy-reviewed Data Paths Explorer, typed `NetworkFlow`, topology validation,
request trace model, UI controls and targeted tests. This is a **source candidate**.
The original PHPUnit/CakePHP/Composer/MySQL release blockers remain **open**.

## Locally executed

| Command | Status |
|---|---|
| `php scripts/syntax-check.php` | 94 PHP files, 0 failures |
| `php tests/standalone.php` | 178 PASS, 0 FAIL |
| `node --test tests/Client/lab-core.test.cjs` | 15 PASS |
| `tsc --noEmit --allowJs --checkJs --strict ... lab-core.js` | PASS |
| `python3 scripts/audit-public.py` | PASS |
| `python3 scripts/audit-rendered.py` | 48 pages audited, PASS |
| `python3 tests/Browser/smoke.py --chromium /usr/bin/chromium` | 50 page/viewport checks, PASS; includes selecting a route and failing its NAS hop |
| `node --check` on both browser JS files | PASS |
| Source archive integrity | CRC and SHA256SUMS manifest independently verified after packaging |

## Not executed (blocking production approval)

- PHP 8.5.12+ interpreter/extension compliance (authoring PHP is 8.4.24).
- Composer dependency resolution, security audit and lockfile creation.
- PHPStan max, CakeDC, ShipMonk, PHPUnit and real CakePHP HTTP/ORM integration.
- Real MySQL migration/rollback and credential rotation.
- Staging deploy/rollback on the user’s shared host and manual public-data approval.

The authoring environment is offline and lacks Composer, the MySQL driver,
PHP-FPM and required PHP extensions. These cannot be reported as passing.
