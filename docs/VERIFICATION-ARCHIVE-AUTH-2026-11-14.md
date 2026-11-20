# Verification record — archive integrity and admin roundtrip (2026-11-14)

> **Historical run record.** Results apply only to the revision and environment recorded below; current status: [MASTER_PLAN.md](MASTER_PLAN.md).

## Environment

- Source: release-hardening ZIP from 2026-11-14, updated with archive integrity verifier and admin HTTP test.
- Available CLI: PHP 8.4.24, Node.js 22, TypeScript 5.8.3, Python 3.
- Not available: PHP 8.5 runtime, Composer, vendor dependencies, ext-mbstring/intl/pdo_mysql, MySQL.
- Internet resolution for Composer packages was unavailable.

## Performed locally

- `php scripts/syntax-check.php`: to be recorded after final regression run.
- `php tests/standalone.php`: to be recorded after final regression run.
- `python3 -m unittest discover -s tests/Python -q`: 27 passing unit tests, including five HTTP harness tests and seven archive verifier tests.
- `scripts/build-source.py` uses the independent archive verifier by default.
- `tsc --noEmit --allowJs --checkJs --strict --target ES2022 --lib ES2022,DOM webroot/assets/js/lab-core.js`: passed.
- All archive checks are reproducible using `python3 scripts/verify_archive.py ZIP --variant source`.

## Not verified; do not claim production readiness

- Full CakePHP server runtime, actual admin HTTP roundtrip, password/session renewal, PHPStan max, PHPUnit, database migrations and shared hosting deployment.
- Production composer install and deployment ZIP with `vendor/`.
- Human editorial permission to publish private-derived content.

The new Python HTTP tests exercise the smoke **harness logic with a simulated transport**;
they do not prove any CakePHP response. The build scripts reject missing production prerequisites.

## Final regression results (executed 2026-11-14)

| Check | Result |
| --- | --- |
| PHP syntax | 99 files, zero failures |
| PHP standalone | 198 PASS, 0 FAIL |
| Python tests | 27 PASS (including archive verifier and mocked auth harness) |
| Node JavaScript tests | 15 PASS |
| JS strict `tsc` | PASS |
| JS syntax | PASS |
| Public-data safety heuristic | PASS |
| Offline rendered pages | 48 pages, links, assets, IDs PASS |
| Chromium offline DOM | 50 viewport/page combinations PASS |
| CI YAML parsing | PASS (3 jobs) |
| Source ZIP independent SHA-256 verification | See final release log |

**No evidence gained** for PHPStan/CakePHP PHPUnit/MySQL/real HTTP server. The
HTTP harness only ran against an offline mock in this iteration. Local PHP is 8.4.
