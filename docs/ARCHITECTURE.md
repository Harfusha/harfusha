# Architecture and design decisions

## Goals

Harfusha Lab is a *digital workshop*, not a marketing microsite. It should work on ordinary Apache/PHP/MySQL shared hosting, with fast SSR page loads, real project provenance, meaningful browser-only demos and no access from the public site to private infrastructure.

## Request lifecycle

1. Apache serves versioned self-hosted CSS/JS/SVG files directly from `webroot/assets`.
2. Unmatched paths are rewritten to `webroot/index.php`, which initializes CakePHP `Application`.
3. Host guard validates the incoming Host header against `App.fullBaseUrl` to limit host-header abuse.
4. `CsrfProtectionMiddleware` protects mutating admin routes; public webhook-like `/cron/github` uses a separate secret header and is the only exception.
5. Explicit route table dispatches to `PagesController`, `AdminController`, `SystemController`. There is no fallback controller/action exposure.
6. Pages use a typed `ProjectCatalog` (curated bilingual JSON) enriched by `ContentRepository` (CakePHP ORM overrides and cached GitHub data), and Cake templates.
7. `templates/layout/default.php` sets canonical/alternate URLs; content is HTML-escaped and unsafe external URLs are denied.

## Domain, application and infrastructure

- `src/Domain/Project.php`: readonly value object and strict slug validation.
- `src/Domain/ProjectConnections.php`: validated, curated relationship graph; no private network data.
- `src/Domain/ProjectCatalog.php`: validates curated JSON list and protects private repository URL boundaries.
- `src/Domain/GithubRecord.php`: typed PR/issue entry with strict URL and classification validation.
- `src/Domain/GithubSnapshot.php`: fail-closed upstream PR/merge/issue correlation, author consistency and duplicate detection.
- `src/Domain/EditorialInput.php`: request-independent typed validation of editorial form data.
- `src/Domain/JournalFeed.php`: safely escaped RSS 2.0 output for published notes.
- `src/Service/ContentRepository.php`: Cake ORM reads with a read-only static fallback when DB is unconfigured.
- `src/Service/GithubSyncService.php`: bounded complete public GitHub fetching with a nonblocking import lock + atomic refresh and stale-cache pruning via prepared MySQL `INSERT ... ON DUPLICATE KEY UPDATE` upserts. The ORM handles editorial CRUD; batched import uses prepared native Cake DB statements for efficiency.
- `src/Model/Table/*`: CakePHP tables for editor overrides, journal, GitHub activity and import receipts.
- `src/Middleware/SecurityHeadersMiddleware.php`: strong CSP and other browser security headers.
- `src/Controller/*`: HTTP entry points only, with input bounds, authenticated mutations and response ownership.
- `src/Command/GithubSyncCommand.php`: scheduled CLI import.
- `config/Migrations/*`: CakePHP migrations; `db/schema.sql` allows phpMyAdmin initial provisioning on restricted hosting.

## Data flow

```text
Curated JSON (reviewed) ---> ProjectCatalog ------> ContentRepository --> PagesController --> CakePHP SSR templates
                                                        ^
                                                        |
MySQL project overrides / journal / GitHub public cache (Cake ORM + prepared SQL)
                                                        ^
                                                        |
Scheduled PHP CLI ---> GithubSyncService ---> PUBLIC GitHub API (unauthenticated)

Browser --> self-hosted JS --> SVG home lab + Canvas world/3D + AI/sync demos (purely local)
```

## Why PHP+CakePHP instead of SPA/server-side JS

- CakePHP 5.4 demonstrates real production-oriented PHP skill: ORM, commands, migrations, controllers, middleware and security.
- Rendered content does not depend on JavaScript and avoids hydration overhead.
- Browser simulations are lazy triggered by their own page DOM and require no server requests.
- MySQL only stores editable metadata and cached public activity, not remote private data.
- No daemons and no server-side frontend build process. All assets are committed in their deployable form.

## Performance principles

- Static content precurated in JSON rather than fetched from GitHub on each request.
- ORM queries only for optional database-enriched views; independent catalog fallback.
- Cached metadata, OPcache, `utf8mb4` indexes, bounded read/return sizes.
- GitHub import rate limited by daily cron and bounded pages.
- Native browser Canvas/SVG rather than rendering libraries delivered to every visitor.
- No external CDN, web fonts, analytics, tracking or heavy npm runtime.

## What is intentionally *not* implemented

- No public network management or SSH access.
- No untrusted remote STL upload: imported 3D geometry stays entirely in the user's browser.
- No real AI inference from visitor inputs.
- No automated publishing of private repo README/source.
- No fabricated real-time service status or token-savings benchmarks.
- No automatic login registration or open write endpoints.

## External verification still required before public production

- Run Composer/CI in a complete PHP environment and resolve every PHPStan/ShipMonk error without ignores.
- Add MySQL integration tests and migration rollback tests.
- Optional explicit cache invalidation and a signed, reviewed homelab telemetry snapshot pipeline **only if** desired.
- Build project-by-project proof assets: real screenshots, sanctioned benchmark traces, architecture diffs and annotated PR code snippets.


## Additional domain design

- Six curated JSON datasets are validated by `PublicContentValidator` at the trust boundary. `data/maker.json` contains physical projects with planned/prototype/in-use status and a cross-link to a software case study.
- CakePHP `ProjectOverrides` now supports optional problem/solution overrides without destroying checked-in baseline content; `JournalEntries` supports reversible visibility changes. The second migration adds nullable columns for backward compatibility.
- `lab-core.js` is a pure browser and Node compatible simulation module. The DOM/UI layer in `app.js` delegates outage/sync/world/STL calculations to tested pure functions.
- SSR is statically smoke-rendered for every locale and project, and the 46-page offline output is audited for broken internal paths. This complements real CakePHP integration tests, but never replaces them.

## Engineering rationale dataset

`data/decisions.json` je samostatný reviewed dataset. `PublicContentValidator::decision()` hlídá strukturu, `ProjectCatalog::decisions()` vazbu na existující veřejně schválený projekt, `EngineeringDecision` je neměnný DTO. HTML `<details>` zaručuje čitelnost bez klientského JS a generický filtr používá `data-status` bez síťových volání. Neobsahuje neověřené benchmarky.

## Transactional editorial audit

Třetí migrace zavádí `editorial_audit` s časem, typem, identifikátorem a SHA-256 otiskem normalizovaného vstupu; neukládá plný text ani přihlašovací údaje. Editace projektů a poznámek běží v jediné DB transakci s auditním zápisem. Pokud audit insert selže, rollback chrání jednotnost změny.

## Public API v1 and request-lifecycle teaching model (2026-11-13)

- `src/Controller/ApiController.php` provides a **read-only** CakePHP GET endpoint at `/api/v1/projects[/{slug}]`. It delegates to the existing `ContentRepository`, creates typed `PublicProjectView` DTOs and serializes only allowlisted fields. Links to private repositories are not serialized. Validated language selection, Content-Type, body-hashed ETag and conditional 304 improve predictability and cache performance. See `docs/API.md`.
- The fifth Playground tab, **CakePHP Request Lab**, simulates request routing, HostGuard, CSRF, session authorization, `EditorialInput` typed validation and a transactional write as an *illustrative diagram*. It never makes HTTP calls, modifies admin content or pretends that a failed browser-only check protects the real backend. Pure `requestTrace` logic is regression-tested alongside the existing four demos.
- Accessibility: the SVG graph explorers re-focus the selected recreated node after redraw; ARIA tabs accept ArrowLeft/ArrowRight/Home/End. These specific interactions are covered in Chromium browser smoke.
- The STL ASCII parser now uses two bounded scan passes rather than materializing an unbounded `matchAll` result array. It retains the 8 MB input cap and 30,000-triangle render cap. The demonstrator is not a metrology or mesh-repair tool.
- `PublicSitemap` is a typed, standalone-testable multilingual sitemap builder with `xhtml:link` for Czech, English and x-default. A route injection or duplicated path fails closed.
