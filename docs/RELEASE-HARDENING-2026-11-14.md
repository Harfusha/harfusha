# Release hardening — 14 November 2026

## What's actually changed

1. **Destructive database guard** (`src/Security/TestDatabaseGuard.php`). The PHPUnit bootstrap now refuses to initialize CakePHP when `APP_ENV` is not exactly `testing`; the DB name isn't a safe disposable suffix (`_test`, `_testing`, `_ci`); `LAB_TEST_DB_CONFIRM` isn't the exact `DB_NAME`; the DB host is remote; or the DB username is an administrative account. The guard runs *before* the Composer autoloader and framework test fixtures. This prevents common accidental test runs against real data. **It is a secondary safeguard, not a guarantee**: use separate credentials and a disposable MySQL instance.
2. **Clean staging release builder** (`scripts/build-release.py` and `scripts/release_package.py`). The builder runs all original gates on the source, then copies only allowlisted deployable files into a temporary directory. It performs `composer install --no-dev` **inside staging**, verifies platform requirements, and assembles the output ZIP with `SHA256SUMS`. This no longer destroys the development `vendor/` tree or copies arbitrary local files into the release.
3. **Private data exclusions.** `.env`, `config/app_local.php`, `.lab-content-approval.json`, logs, local DBs, tests, `vendor/bin` tools and symlinks are excluded from production output. `scripts/preflight.php` is included so operators can check runtime configuration after deployment.
4. **Regressions.** A PHPUnit test class checks the DB guard (requires real PHPUnit). The PHP standalone suite exercises six scenarios. Six Python tests verify staging, archive integrity, exclusions, symlink rejection and missing-vendor rejection. CI and documentation were updated to require the confirmation variable.

## How to reproduce on a **safe build machine**

```bash
# PHP 8.5.12+ / Composer 2 / Node 22 / MySQL 8 / Python 3 / Chromium are prerequisites.
composer install --no-interaction
# Every command below uses a genuinely disposable, dedicated local database.
export APP_ENV=testing DEBUG=false APP_URL=http://localhost:8765
export DB_HOST=127.0.0.1 DB_NAME=harfusha_lab_ci DB_USER=lab_ci
export DB_PASS='<test-only-password>'
export LAB_TEST_DB_CONFIRM=harfusha_lab_ci
php bin/cake.php migrations migrate
composer quality
python3 -m unittest discover -s tests/Python -v
python3 scripts/review-public-content.py --review
# Manually inspect the contents; then interactive approval (never automate):
python3 scripts/review-public-content.py --approve --reviewer '<reviewer>'
python3 scripts/build-release.py --output ../harfusha-lab-deploy.zip
```

The release builder additionally expects the TypeScript checker and browser QA prerequisites described in `docs/RELEASE.md`. **Never substitute a live database**, even one suffixed `_test`.

## Executed vs blocked

**Executed here:** PHP parser, independent PHP suite, Python release builder tests, JS suite, rendered HTML/privacy checks and ZIP SHA-256 verification.

**Not executable here:** Composer dependency resolution and `composer.lock`, PHP 8.5 runtime with required extensions, true CakePHP HTTP execution, PHPStan, PHPUnit, database migrations and real MySQL. Do not present the source ZIP as a production distribution. Before public launch, run every gate, approve the actual content, verify on staging and rehearse recovery.
