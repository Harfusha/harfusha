# Harfusha Lab — technical documentation

**PHP 8.5.12+ · CakePHP 5.4 · MySQL · PHPStan max + CakeDC + strict rules + ShipMonk**

This site is an interactive engineering workshop, not just a self-promotion page. It shows real projects and upstream contributions, a privacy-reviewed model of a home network, and **clearly labelled simulations** visitors can explore themselves. Its own source demonstrates CakePHP MVC/ORM, typed domain objects, migrations, security controls, careful browser code and reproducible validation scripts.

> **Verification status, 14 Nov 2026:** The full source project is delivered, but **not a production-verified deployable**. Authoring environment: PHP 8.4, no Composer, no vendor packages, no PHP 8.5 runtime, MySQL driver or server. We ran standalone PHP, JavaScript/TypeScript and offline SSR/privacy checks, but **could not run CakePHP itself, PHPUnit integrations, real MySQL migrations, Composer dependency solving or PHPStan**. CI is configured to run them on PHP 8.5 + MySQL. Do not publicly deploy until that pipeline passes; never interpret a configured test as a passed test. See `docs/RELEASE.md` and `RELEASE-NOTES.md`.

## Explore the site

| Public URL (both `/cs` and `/en`) | Behavior |
|---|---|
| `/` | Project-first homepage with interactive areas and recent *public* repository snapshot |
| `/projects` and `/projects/{slug}` | Filterable catalogue and 13 curated case studies, including private project *descriptions* only |
| `/connections` | Interactive relationship graph of projects, tools and home infrastructure |
| `/decisions` | Seven explainable architectural choices with context, trade-offs and documented limitations |
| `/homelab` | Network topology, node inspection, failure simulation and four bilingual conceptual data paths |
| `/maker` | Physical prototypes, design notes, FDM experiments and links to related software |
| `/playground#ai` | Illustrated, step-by-step local AI agent workflow; never runs agents or code |
| `/playground#sync` | Deterministic synchronization / conflict simulation; **not real Mutagen** |
| `/playground#world` | Seeded 2D terrain generator; **not the Unity game engine** |
| `/playground#cad` | Client-only STL inspection with file/coordinate limits; **not CAD repair** |
| `/open-source` | Real external PR/issues, filterable and linked to upstream sources |
| `/journal` | Engineering log, newest first; unlisted notes can be drafts in MySQL |
| `/about` | Engineering profile and values |

Additional endpoints: `/healthz` (simple public liveness, not DB health), `/sitemap.xml`, `/admin` (editor), `/cron/github` (authenticated POST; alternative to CLI cron). No publicly routable CRUD fallback actions.

## Architecture

- **CakePHP MVC and Cake ORM** with typed `Project` values, status enum, type-annotated Entities, typed TableLocator FQCNs and curated-content boundary validator. Domain and validation logic can run independently of the web framework.
- **MySQL** stores editorial overrides, public journal entries/drafts, cached GitHub activity, sync-run receipts and append-only editorial fingerprints. Static reviewed JSON remains readable with no DB.
- **No Node service, background daemon, WebSocket or third-party browser CDN** on hosting. SSR templates use vanilla JS/CSS; heavy demos run locally in the browser and import **zero** secret configuration.
- **Local deterministic JS core** (`lab-core.js`) provides network outage propagation, curated route validation/tracing, synchronization resolution, terrain noise and bounded STL parsing. The same functions are exercised by Node tests and JSDoc/TypeScript strict checking.
- **Bilingual** route scopes, accessible navigation and interactive SVG elements with keyboard activation.
- **No live homelab access:** the graph is a manually approved abstraction, not a scan of machines or credentials. Any real telemetry would require a separate redaction and consent design.

## Start locally (PHP 8.5)

Requirements: patched PHP **8.5.12+** (8.5 branch), `intl`, `mbstring`, `simplexml`, `pdo_mysql`, `openssl`, Composer 2, MySQL 8.4+ or compatible MariaDB, and a proper Apache rewrite/virtual host for production.

```sh
cp .env.example .env
# Development: APP_ENV=development DEBUG=true APP_URL=http://localhost:8765
# Fill database, SECURITY_SALT and other local variables in private .env
composer install --no-interaction --prefer-dist
php bin/cake.php migrations migrate
php bin/cake.php server -p 8765
```

Open `http://localhost:8765/cs/` or `/en/`. The repository now contains a Composer lockfile resolved on a real PHP 8.5 GitHub Actions runner. Always use `composer install` against that reviewed snapshot; the source archive still does not ship `vendor/`.

### Docker development (optional)

The repo includes `docker/Dockerfile` and `compose.dev.yml` with a PHP 8.5 CLI container and disposable MySQL. Start with `docker compose -f compose.dev.yml up --build`. This is a development convenience, **not** a substitute for testing the target shared host. Example passwords are local-only and must never be deployed. Docker was not available to test this configuration in the authoring environment.

## Content & editorial workflow

- `data/projects.json` — 13 curated software/infrastructure project stories.
- `data/maker.json` — physical designs with explicit prototype/planned/validated status.
- `data/connections.json` — reviewed project relationships.
- `data/decisions.json` — 7 architecture decision records, including counterarguments and verification limits.
- `data/topology.json` — anonymized, dependency-checked infrastructure diagram.
- `data/flows.json` — four human-reviewed paths (media, remote access, printing, backup). Each path has bilingual step explanations and is validated against the topology edges.
- `data/journal.json` — static historical engineering log.
- `data/contributions.json` — curated external GitHub PR/issues until first successful import.
- `/admin` — authenticated project summary/problem/solution editor; create, edit, publish and unpublish MySQL journal entries. Drafts never appear in the public feed. Changes are stored with an atomic, redacted audit receipt. All saved output is rendered as **plain escaped text**, not arbitrary HTML.

Public static content is bounded and validated at load time. The privacy audit also scans selected public assets, but **manual approval is still mandatory**, especially for private projects and network topology.

### GitHub import

Prefer a **daily PHP CLI cron**, `php bin/cake.php github_sync`. For hosts offering only HTTP cron, POST `/cron/github` with a high-entropy token in the `X-Cron-Token` header (never URL/query). The importer uses GitHub's **unauthenticated public API**, rejects incomplete pages, explicitly validates public repository ownership and visibility, uses a local `flock` lock, commits only after all remote reads succeed and replaces stale database snapshots transactionally. It will fail on remote rate limits rather than silently present a partial import. Historical/project summaries must still be reviewed manually.

## Quality gates

```sh
composer validate --strict
composer syntax              # PHP parser
composer standalone          # independent domain/content/template checks
composer stan                # PHPStan level max + strict + CakeDC + ShipMonk
composer test                # PHPUnit + CakePHP integration tests (explicit disposable DB only)
composer cs-check            # CakePHP coding standard
python3 scripts/audit-public.py
python3 scripts/audit-rendered.py   # renders 48 pages and checks internal links/IDs/assets
node --check webroot/assets/js/app.js
node --check webroot/assets/js/lab-core.js
node --test tests/Client/*.test.cjs
# with local typescript 5.8.3
npx --no-install tsc --noEmit --allowJs --checkJs --strict --target ES2022 --lib ES2022,DOM webroot/assets/js/lab-core.js
```

The GitHub Actions workflow provisions PHP 8.5 and MySQL 8.4, **requires an already committed Composer lockfile**, runs all three migrations and checks both real CakePHP HTTP routes and standalone/static/JS tests. Static and browser CI jobs have passed; full PHP/MySQL integration and PHPStan verification are still in progress.

## Deploying on shared hosting

**Only `webroot/` should be exposed to HTTP.** Keep `.env`, `src/`, `config/`, `vendor/`, `logs/`, `tmp/`, `data/` outside the document root. Set `APP_ENV=production`; the application enforces patched PHP 8.5, TLS origin, disabled DEBUG and a random hex salt. Configure `APP_URL`, strong independent secrets, database credentials, writable runtime paths and OPcache. For a fixed `public_html` sibling layout see `docs/DEPLOYMENT.md`.

To make a *production* ZIP (with Composer vendor and a real lockfile), once a disposable MySQL test DB and all build tooling are ready:

```sh
npm install --no-save --ignore-scripts typescript@5.8.3
APP_ENV=testing APP_URL=http://localhost:8765 DB_HOST=127.0.0.1 DB_NAME=harfusha_lab_ci DB_USER=lab_ci LAB_TEST_DB_CONFIRM=harfusha_lab_ci python3 scripts/build-release.py --output ../harfusha-lab-deploy.zip
```

Unlike the source ZIP, the production builder **runs** Composer quality, static privacy/SSR tests, browser-core tests and strict client type checking; it aborts on failure before packaging a `vendor/`-inclusive artifact. Production Composer dependencies are installed into a temporary **clean staging directory**, never over the audited development `vendor/`. It requires the reviewed `composer.lock`, installed dev dependencies, PHP 8.5.12+, an explicitly confirmed disposable MySQL database (`LAB_TEST_DB_CONFIRM=DB_NAME`) configured for integration tests and a working local HTTP smoke server. Set `CHROMIUM_PATH=/usr/bin/chromium` if using system Chromium.

## Documentation

- [Český rychlý start](../START-CZ.md)
- [End-to-end master plan with acceptance criteria](MASTER_PLAN.md)
- [Architecture](ARCHITECTURE.md)
- [Security](SECURITY.md)
- [Deployment](DEPLOYMENT.md)
- [Testing](TESTING.md)
- [Release gates](RELEASE.md)
- [Latest executed verification (14 Nov 2026 — release hardening)](VERIFICATION-HARDENING-2026-11-14.md)
- [Earlier verification](VERIFICATION-2026-11-13.md)
- [Public network request paths](DATA-PATHS.md) — safety model, validation and usage
- [Veřejné typované API](API.md) — `/api/v1/projects` (CS/EN, ETag, privacy allowlist)
- [Changes & verification status](../RELEASE-NOTES.md)

MIT license. Not affiliated with CakePHP, GitHub, Mutagen, Spotify or other upstream projects.

### Create an auditable source-only ZIP

```sh
python3 scripts/build-source.py --output ../Harfusha-Lab-full-source.zip
```

The source builder runs the locally executable checks, excludes private `.env`, caches, transient preview files and dependencies, and embeds a `SHA256SUMS` integrity manifest. The source ZIP cannot be uploaded directly as a functioning CakePHP installation because it lacks `vendor/`.

## Vývojový milestone 13. 11. 2026 — Backend Lab a veřejné API

- Přibyl pátý prohlížečový **CakePHP Request Lab**: deterministická simulace HostGuard, routování, CSRF, autorizace, vstupní typové validace a transakční odpovědi. Jde o výukový model, nikoli živý HTTP request.
- Nové veřejné API **`GET /api/v1/projects[/{slug}]?lang=cs|en`** s DTO allowlistem, zamítnutím soukromých URL, `ETag` a podmíněným `304`; specifikace v `docs/API.md`.
- Zlepšené ovládání SVG grafů klávesnicí (obnovení focusu po překreslení), šipkové ovládání ARIA záložek, úspornější ASCII STL parser a vícejazyčný sitemap.
- Snímky skutečně implementovaného request-labu: `docs/screenshots/desktop-cakephp-request.webp` a `docs/screenshots/mobile-cakephp-request.webp`.
- Zdrojový ZIP stále **není release s Composer vendor/**; všechny P0 v `docs/MASTER_PLAN.md` potřebují skutečné PHP 8.5, MySQL, Composer a staging.


## Operational readiness (private)

After MySQL migrations and the first successful public GitHub sync, run
`php bin/cake.php lab_health`. A nonzero exit means the database, runtime
write permissions or 48-hour GitHub cache freshness requirements are not met.
The same coarse health states are visible only to an authenticated editor.
Public `/healthz` is **liveness only**. Details: `docs/OPERATIONS.md`.

### Current verification supplement

See `docs/VERIFICATION-CONTINUED-2026-11-13.md` for the current test evidence,
including explicit differentiation between run tests and framework tests that
require the PHP 8.5/Composer/MySQL runtime.

### Authentication-storage hardening (additional iteration)

`LoginLimiter` now atomically reserves login attempts under a fixed 32-shard
file lock, caps entries/storage size, rejects corruption and I/O failures, and
clears state only after a verified password. This replaces unbounded per-IP
files and closes the old concurrent check-then-record race. See
`docs/AUTH-THROTTLE.md` for threat model, false-positive capacity trade-offs
and legacy cleanup. This change has isolated tests but still needs real CakePHP
HTTP, PHPStan and hosting verification.

For future controlled updates to `composer.lock`, GitHub Actions provides the **manual** `Resolve Composer lockfile on PHP 8.5` workflow. Review the exported artifact and its dependency advisories before updating the tracked lockfile. The main CI intentionally runs `composer install`, not an uncontrolled dependency update. Neither workflow has been run in this delivery.

## New source-candidate hardening (14 Nov 2026)

- GitHub sync has two independently tested, typed pagination collectors. Duplicates, inconsistent Search API totals, truncated pages and unexpected repository pages fail before destructive MySQL refresh. See `docs/GITHUB-IMPORT-INTEGRITY.md`.
- A production ZIP cannot be built without explicit manual review of public content: run `python3 scripts/review-public-content.py --review` then, **after actual human review**, `--approve --reviewer '...'` interactively. The private hash receipt is excluded from every ZIP; edits invalidate it. See `docs/CONTENT-APPROVAL.md`.
- Python review-gate tests run in CI and source ZIP building; this is not evidence that a human has approved the content.
- The real PHP 8.5 runner resolved and committed `composer.lock` on 14 Nov 2026. Source archives remain **vendor-free**; production approval still requires successful migrations, PHPStan, PHPUnit and deployment checks.

### Release hardening (14 Nov 2026)

The test bootstrap refuses to run integration tests unless `APP_ENV=testing`, `DB_NAME` ends in `_test`, `_testing` or `_ci`, `LAB_TEST_DB_CONFIRM` exactly equals `DB_NAME`, the database host is local/container-only and the account is not administrative. The release builder uses staging and includes `scripts/preflight.php` in the production archive. Details: [Release hardening notes](RELEASE-HARDENING-2026-11-14.md).


### Latest quality controls (2026-11-14)

- `scripts/http-smoke.py` covers **real** CakePHP CSRF, cookie-backed admin login,
  session rotation, invalid writes and logout in the disposable CI environment.
  It requires an ephemeral test-only password and cannot silently skip auth checks.
- `scripts/verify_archive.py --variant source|production` independently validates
  every distributed file against SHA-256, rejects unsafe ZIP entries and secrets,
  and requires Composer vendor/lock files in production packages.
- Local isolated and browser-core checks do **not** replace `composer quality`,
  MySQL migration/rollback, actual HTTP smoke, or a successful shared-host staging test.
  See `docs/VERIFICATION-ARCHIVE-AUTH-2026-11-14.md`.


> This technical guide was formerly the repository-root `README.md`. The root README now serves the GitHub profile. Links above are relative to this `docs/` directory.
