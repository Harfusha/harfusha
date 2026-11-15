# Security, privacy and source provenance

## Public vs private data

The public catalogue contains 13 **curated** case studies based on reviewed GitHub metadata and project descriptions. All private project source URLs are empty; their generic summary descriptions are still PUBLIC text. Review and edit every entry before publishing the site. `data/topology.json` is deliberately an abstract graph, not a full device inventory. Never publish internal IP addresses, SSH endpoints, real router configuration, device serials, access tokens, private family records, filenames or network management ports.

## GitHub data boundary

`GithubSyncService` deliberately uses GitHub's **unauthenticated public API**; do not add a broad OAuth token. The act of authenticating can expose private issues or PRs through Search and make them appear in the public cache. It imports only public activity found from `author:<login>`, and original public repos. An already-cached record may remain after an upstream visibility change until a complete new import; require editorial review before displaying detailed historic activity if privacy requirements change. Once the database is configured, failed cache/provenance queries fail closed rather than resurrecting the historical static selection. Cache rows are never published before a completed GitHub synchronization receipt exists; until the first successful import, only the reviewed static selection is displayed. Dynamic journal, repository activity, edited project HTML and RSS are sent with `Cache-Control: no-store` so conforming shared caches do not prolong publication after withdrawal.

## Admin and cron

- Admin password is configured as `password_hash()` output in private `.env`. `password_verify()` is used, no plaintext DB password field.
- Session regenerated after login and logout; cookie is HttpOnly, same-site Strict and HTTPS Secure in production.
- CakePHP `CsrfProtectionMiddleware` protects state-changing admin POST forms, with `_csrfToken` field.
- Login limiter **atomically reserves every login attempt** (successful attempts are reset) by pseudonymized address digest. It uses 32 bounded, locked shard files, fails closed on storage corruption/capacity and does not trust arbitrary forwarding headers; see `docs/AUTH-THROTTLE.md`.
- HTTP GitHub cron requires 32+ character secret sent in `X-Cron-Token`; it is POST-only and the only CSRF exemption.
- Host header middleware only accepts the domain from `APP_URL`.
- No public write API, no anonymous file upload to PHP and no dynamic template interpretation of user content.

## Rendering and frontend

- `App\Support\Html::escape` escapes public text; JSON is encoded with JSON_HEX_TAG/AMP/APOS/QUOT.
- External project links pass a strict GitHub HTTPS allowlist.
- SVG model draws only curated nodes; no actual network connection is opened.
- Local STL import happens entirely inside the browser and is bounded by client-side limits; browser-side JS must still treat files as hostile input.
- `.htaccess` serves files only under webroot and sets CSP, frame, referrer and MIME-sniffing guards.
- Production error detail is disabled; PHP error logs live in private `logs/`.

## Additional pre-release testing

- Verify CSP in real browsers (including SVG/Canvas/JSON read paths); fix blocked interactions.
- Attack-test URL/slug injection, CSRF, login throttling, oversized form fields, invalid unicode and polluted JSON.
- Exercise HTTPS reverse proxy path with Host validation and Secure cookies.
- Run composer audit and dependency updates.
- Check cached GitHub records for renamed/privatized repos; consider deletion/tombstone jobs.
- Check Apache document root manually; a wrong document root would expose private config.


## Additional editorial and data safeguards

- `PublicContentValidator` rejects malformed entries and private repository links at the public content boundary. The privacy scanner is heuristic; every private-derived description still needs author approval.
- CakePHP Entity classes restrict mass assignment to explicit editorial fields. Public GitHub cache Entities are not mass assignable.
- Admin journals start as drafts unless explicitly selected as public and can be unlisted again; updates and visibility changes require POST + CSRF + session. Public presentation escapes all submitted text.
- Caught administrative, public content-read and GitHub import failures log only an exception **class**, never the raw driver error message. CakePHP query error messages can include bound SQL values, such as unpublished journal text; these values must not be duplicated in operation logs.
- Database journal rendering appends published rows only after the complete bounded batch is read and validated; malformed rows cannot expose a partially loaded batch.
- The public JavaScript core has no network/storage access and rejects malformed or excessive STL uploads; browsing a model still carries ordinary browser resource risks, so do not offer untrusted public file hosting.

## 13 November hardening

- `EditorialInput` rejects nested arrays, malformed UTF-8, excessive text, invalid IDs and unapproved statuses before ORM writes.
- `GithubSnapshot` cross-checks authored PRs with merged PRs, confirms public-result authorship and excludes own repositories. GitHub Search pagination now requires exact `total_count` agreement; incomplete or changing results retain the last valid DB snapshot.
- RSS output is derived only from `ContentRepository::journal()` (published entries), escaped with XML-safe encoding and contains no authentication state or hidden drafts.
- Browser smoke runs in an isolated offline DOM to avoid access to private hostnames; it does not validate real HTTP security headers. Separate staging HTTP/CSP tests remain release blockers.
- The source and production ZIP builders reject/omit symlink escape, secrets, credentials, runtime caches and private env files; `SHA256SUMS` allows independent source verification.

## 13 November hardening

- `DeploymentPolicy` vynucuje explicitní APP_ENV; produkční konfigurace vyžaduje PHP 8.5.12+, HTTPS origin bez subcesty, vypnutý debug a náhodný hex salt.
- `PublicRepository::fromGithub()` odmítne neočekávanou soukromou viditelnost, cizí vlastníka, falešné URL a duplicitní/bezobsažné odpovědi. Poslední dobrý DB snapshot se přepisuje až po validaci všech stránek.
- Editor používá transakční audit receipt bez plného textového obsahu; SHA-256 fingerprint není autentizační mechanismus ani neměnný WORM log.
- CI vyžaduje přítomný `composer.lock` a nedovoluje automatický update závislostí při každém buildu.

## Bounded authentication state (current iteration)

The previous one-file-per-address throttle permitted unbounded filesystem
growth and a race between `allow()` and `fail()`. It has been replaced by an
atomic `consume()` API with fixed sharding and strict limits. The HTTP endpoint
uses `Retry-After: 900` on lockout. Full operational details and edge cases are
in `docs/AUTH-THROTTLE.md`.

## Additional publication guard (14 Nov 2026)

The automated source privacy scanner cannot authorize publication of case studies based on private repositories. Production builds now require a local human-review hash receipt (`docs/CONTENT-APPROVAL.md`) that fails if any public JSON file changed. This is not an authenticated cryptographic signature and does not include manual review of image/template assets; both remain separate release requirements. The GitHub importer now checks duplicate and inconsistent pages before altering cached public data (`docs/GITHUB-IMPORT-INTEGRITY.md`).
