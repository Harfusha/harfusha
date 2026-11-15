# Deployment — CakePHP 5 on PHP/MySQL shared hosting

## Prerequisites

- PHP **8.5.12+** with `intl`, `mbstring`, `pdo_mysql`, `simplexml`, `openssl`, `json` (CakePHP 5 requires `mbstring` and `intl`).
- MySQL 8+/MariaDB 10.6+ recommended; CakePHP supports older versions, but this project is checked against the declared schema only.
- HTTPS, `.htaccess` rewrite, Apache or equivalent web server.
- Sufficient file permissions for `tmp/`, `tmp/cache/`, `logs/`.
- Composer **on build machine or CI** even if shared hosting does not provide it.

## Standard layout — host can set document root

```text
/home/account/harfusha-lab/
  .env                          # private, not in webroot
  src/ config/ data/ templates/
  vendor/ tmp/ logs/
  webroot/                      # **domain document root**
    index.php
    .htaccess
    assets/
```

Run on local/CI machine (the source ZIP does **not** contain resolved Composer vendor packages):

```sh
composer install --no-interaction
composer syntax
composer standalone
composer stan
# Explicitní potvrzení disposable MySQL je povinné pro PHPUnit:
# APP_ENV=testing DB_HOST=127.0.0.1 DB_NAME=harfusha_lab_ci DB_USER=lab_ci LAB_TEST_DB_CONFIRM=harfusha_lab_ci
composer test
composer cs-check
node --check webroot/assets/js/app.js
```

**Do not deploy if any check fails.** On the configured production host, run `php scripts/preflight.php` from the private application tree before enabling traffic. This uses the **same** `DeploymentPolicy` as CakePHP bootstrap; an origin with a path, query, fragment, credentials, or a repeated/default salt must fail even if basic PHP and filesystem prerequisites are present. The production packager, source packager and independent ZIP verifier reject sensitive configuration files and editorial approval receipts regardless of filename case (for example `.env` and `.ENV.production`). To prepare a production build after the gates are green **and human public-content approval has been recorded**:

```sh
# Na build stroji s úspěšnými testy a schváleným veřejným obsahem:
python3 scripts/build-release.py --output ../harfusha-lab-deploy.zip
```

Skript sestaví produkční `vendor/` mimo pracovní strom ve vlastním dočasném adresáři a ověří integrity SHA-256. Není bezpečné nahrazovat jej příkazem `composer install --no-dev` nad ověřovaným zdrojovým adresářem.

Upload files including `vendor/`, except tests/dev-only tooling, `.git/`, `.env.example`, and generated caches. Do not upload secrets in plaintext outside the private `.env` file.

## Fixed `public_html` host

If your hosting cannot change the document root, use this sibling layout instead of putting the app itself inside `public_html`:

```text
/home/account/
  src/ config/ data/ templates/ vendor/ tmp/ logs/ .env
  public_html/                # contents of the provided webroot/
    index.php
    assets/ .htaccess
```

Update **both** `config/paths.php` and `config/app.php` for that layout:

- `WWW_ROOT` in `config/paths.php` must point to `ROOT . DS . 'public_html' . DS`.
- `App.webroot` in `config/app.php` must be `public_html` (not `webroot`).

The provided `webroot/index.php` uses `dirname(__DIR__)` to resolve the framework root; that is correct with this *sibling* layout. Check it before deploying to ensure public_html is directly adjacent to the application directories. If host uses a different directory tree, edit both paths accordingly.

## Database initialization

Create a separate SQL user. Choose **one** initial schema method:

- Preferred: `php bin/cake.php migrations migrate` using CakePHP Migrations.
- **Only the CakePHP migration history is authoritative.** Do **not** import `db/schema.sql` and subsequently invoke migrations on the same database: the SQL reference does not register Phinx migration versions. `db/schema.sql` is provided for comparison and emergency review, **not as an automated installation path**.
- If the shared host has no terminal access, run `php bin/cake.php migrations migrate` via a *private, temporary* scheduled PHP CLI task where supported. If no PHP CLI tasks are available, arrange a secure migration on a staging-capable environment or obtain host support; do not expose an unauthenticated web installer.

Migrations and the standalone SQL define equivalent tables but are independent mechanisms. If you import SQL directly, do not immediately run migration generation on the same DB without reconciling Phinx's migration history.

## Secrets

Use `.env.example` **from the source checkout** to create a private `.env` file and set (neither file is shipped in the production ZIP):

- `APP_ENV=production`, `DEBUG=false`.
- `APP_URL=https://your-real-host` matching the Host header of the domain.
- `SECURITY_SALT` at least 64 random hex characters. Generate using `php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'`.
- MySQL host/port/db/user/password.
- `ADMIN_PASSWORD_HASH` (password_hash output in single quotes).
- `CRON_SECRET` (random 32+ chars) for the optional HTTP cron endpoint.

Keep separate salts/passwords for each environment. Never use a real admin password in example files or repository commits.

## Scheduled GitHub import

Prefer PHP CLI daily cron:

```sh
/usr/bin/php /home/account/harfusha-lab/bin/cake.php github_sync
```

Adapt path to your host's PHP CLI binary. Import is **public-only**, and the portfolio shows cached results. No token is needed. An unauthenticated GitHub Search API response can be rate limited; retry a subsequent day rather than hammering the API. The importer atomically refreshes the public cache only after every requested page has completed, and removes stale records; it cannot guarantee an immediate privacy revocation between scheduled runs.

If the host only supports URL cron, configure `CRON_SECRET` and send an HTTPS POST with header `X-Cron-Token`, not a query param. Ensure the endpoint is excluded from caching and not indexed.

## Release verification checklist

- Both language homepages, projects and details load.
- Homelab SVG interaction and outage simulation work on mobile and desktop.
- AI/sync/world/CAD demos work without external network requests.
- Admin login shows anti-CSRF field, wrong password is rejected and successful login renews session.
- Project edit persists and another request reads the MySQL override.
- Journal entry is escaped on public page (test `<script>` in a draft before production).
- GitHub import preserves upstream URL and never imports private data.
- Set `APP_ENV=production`, `DEBUG=false`, HTTPS and verify no debug pages.
- View page source / Network panel: no internal addresses, passwords, `.env`, vendor directories or unexpected third-party calls.
- Test backup and restore of MySQL database, config and content.
- Confirm PHP-FPM and Apache use the **same patched PHP branch** as CLI; verify needed extensions and permissions under the real hosting account.
- Download the freshly built archive and run `python3 scripts/verify_archive.py <path-to-zip> --variant production` before upload.
- Verify development-server-specific dotfile protection is not used as a substitute for Apache access controls; request `/.env`, `/.htaccess`, `/composer.lock`, and `/vendor/autoload.php` from staging and ensure none disclose file contents.
- Confirm the deployment has the exact same source revision as the reviewed ZIP, and record rollback instructions before DNS cutover.

## Read-only staging smoke

After uploading the reviewed production archive to a **separate HTTPS staging hostname**, run from the **source checkout** (the production ZIP intentionally does not contain development smoke scripts):

```sh
python3 scripts/staging-smoke.py --base-url https://staging.example.com
```

The command uses verified TLS and GET requests only; it checks public CS/EN routes, API JSON/ETag, sitemap, both language RSS feeds and their corresponding visible journal anchors, security headers, admin no-store/noindex, and denial of sensitive paths such as `/.env`, `/config/app.php`, `/data/projects.json`, `/src/Application.php`, `/.git/HEAD`, `/composer.lock` and `/vendor/autoload.php`. It does not send credentials, write records, exercise cron or alter the database. **A pass does not replace** manual visual/mobile QA, content approval, actual PHP-FPM/version verification or backup-and-restore drills.

## Rollback

Keep the previous directory intact, replace only application release files atomically if hosting permits; migrations need compatible backward rollback separately. Backup MySQL before schema changes.


## Revised production packaging and upgrade

1. Use the committed, reviewed `composer.lock` via `composer install` on PHP 8.5.12+; require a fresh green PHPStan/PHPCS/PHPUnit/MySQL CI run for the release commit.
2. If upgrading from the initial source candidate, run **all three** migrations. `20261112000200_AddEditorialFields.php` adds nullable project narrative fields and `20261113000100_AddEditorialAudit.php` adds audit receipts; do not use `db/schema.sql` for migration on an already populated database.
3. On a suitable local CI build machine, install TypeScript 5.8.3 for the client-core strict type check and run `python3 scripts/build-release.py --output ../harfusha-lab-deploy.zip`. This script runs all configured quality gates and then installs production-only packages.
4. Test the deployable ZIP on **staging**, compare behavior under target PHP-FPM and MySQL, test cron and backups and only then deploy. Do not upload `compose.dev.yml` or its test credentials.

## Ochrana testovací databáze a bezpečné balení

PHPUnit `tests/bootstrap.php` před načtením CakePHP vyžaduje `APP_ENV=testing`, jednoznačný název disposable DB, `LAB_TEST_DB_CONFIRM` shodný s `DB_NAME`, lokální/izolovaný `DB_HOST` a neprivilegovaný `DB_USER`. Produkční builder opakuje stejné kontroly, před migracemi; nedostatek dokladů zablokuje release. Závislosti `--no-dev` se instalují jen do soukromého stagingu. Viz `docs/RELEASE-HARDENING-2026-11-14.md`.
