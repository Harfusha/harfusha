# Operational checks — Harfusha Lab

The public `/healthz` endpoint deliberately tests **liveness only**. It returns
`ok` with `Cache-Control: no-store` and does not query MySQL or expose internal
services. Monitoring must not interpret its 200 status as deployment readiness.

## Private readiness

After deployment, run `php scripts/preflight.php` to check secrets, Composer
lockfile, extensions and filesystem permissions. For production, it also checks
the admin password hash and optional HTTP cron token length. Then, after the
first GitHub import, run:

```sh
php bin/cake.php lab_health
```

The command returns exit code **0** only when all of these are true:

1. MySQL connection works **and** the `sync_runs` migration exists.
2. `tmp/`, `tmp/cache/` and `logs/` are writable.
3. A completed public GitHub snapshot exists and is no more than 48 hours old.

Otherwise the exit code is nonzero. It prints only generic component states:
`ok`, `unreachable`, `not-configured`, `unwritable`, `not-synced`,
`invalid-timestamp`, `stale` or `unknown`. It never prints credentials, internal
network addresses, database names, SQL exceptions or topology. This output also
appears under the **authenticated** `/admin` page.

This check does not replace `php scripts/preflight.php`, PHPStan, PHPUnit,
security review, HTTP smoke, or a restore drill. Before the first GitHub sync it
is expected to fail. Repeated failures can indicate rate-limiting by GitHub's
unauthenticated API; do not add a privileged token just to turn the light green,
as a token could change public-only visibility guarantees.

## Incident response

- **Database unreachable**: confirm database host, migration history, user
  privileges and credential storage; do not enable `DEBUG` in production.
- **Storage unwritable**: fix directory ownership outside the public docroot;
  never recursively make the project world-writable.
- **GitHub stale / not synced**: inspect private CLI cron logs, connectivity and
  rate-limit behavior; the importer preserves the previous snapshot if it fails.
- **Unexpected timestamps**: check database UTC clock and import data; a future
  record is intentionally not considered fresh.

Before release: verify the command against the *target* shared hosting CLI PHP
binary (same 8.5.12+ branch as PHP-FPM), and confirm that the source of every
private case study has been reviewed for publication.

## Login limiter maintenance

Inspect `docs/AUTH-THROTTLE.md` before modifying `tmp/auth`. The new limiter
uses **at most 32** active shard files; old per-IP `auth-*.json` files are no
longer consulted but may remain after an upgrade. Inspect and clean them only
after all old PHP workers have stopped. Persistent HTTP 429 can mean a genuine
lockout, exhausted shard or storage problem; investigate private logs and
filesystem capacity without weakening fail-closed handling.
