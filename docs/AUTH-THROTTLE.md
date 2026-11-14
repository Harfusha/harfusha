# Authentication throttling: bounded shared-host storage

The public `/admin/login` action reserves an attempt **before** checking the
submitted password. This prevents the old check-then-record race where many
concurrent requests could all pass a five-attempt check. Successful logins clear
the caller's counter after `password_verify` succeeds and **before** the
application creates the admin session. A failed reset returns 503, never an
unthrottled successful session. Invalid credentials count toward the same limit.

## Storage and limits

- **5 attempts per client address per sliding 15 minutes** (success resets).
- **32 fixed shard files**, `tmp/auth/login-0.json` through `login-31.json`.
- **512 simultaneous active client digests per shard**, maximum 16,384 total;
  256 KiB upper bound per shard (8 MiB total upper bound, normally far less).
- Records contain *SHA-256 digests* of addresses and integer timestamps. This
  is **pseudonymization**, not anonymity: some addresses are guessable. Keep
  `tmp/` outside the public document root and restrict filesystem access.
- A file lock covers read → prune expired records → check → reserve → persist.
  File writes are flushed before the lock is released.
- Exhausted shards, invalid client addresses, corrupt JSON, symlinks,
  non-writable directories, unexpectedly future timestamps or failed I/O
  **deny authentication** instead of evicting existing active lockouts.
- If the limiter is full, unrelated users mapping to that shard also receive
  HTTP 429. This is an explicit security/availability trade-off.

## Reverse proxies and incidents

`clientIp(false)` intentionally uses the transport peer address and does not
trust arbitrary `X-Forwarded-For` headers. Behind a reverse proxy, all visitors
may appear to share that proxy IP; configure the application's trusted proxy
handling and firewall before switching to forwarded addresses. Do not simply
trust a user-controlled request header. A distributed password-spraying attack
can still use many distinct source addresses; this is not a replacement for
edge/WAF protection, MFA or incident monitoring.

A production deploy upgrading from the old per-IP design may leave **legacy**
`tmp/auth/auth-<64-hex-character>.json` files. These are no longer read. Once
no old workers remain, remove only that legacy pattern after inspecting the
path and keeping `tmp/auth/login-*.json` intact. Never delete the whole auth
folder while traffic is active.

## Test evidence

- Deterministic standalone checks: isolation, expiration, reset, corruption,
  rollback of system clock and shard saturation.
- Real cross-process regression via `pcntl_fork` (when `pcntl` is available):
  16 simultaneous attempts from one address yield **exactly five successes**.
- PHPUnit unit tests cover the same behaviors plus a larger collection of
  addresses to assert the fixed file count. These tests have been authored but
  **not executed under PHPUnit** in the isolated authoring environment.
- Full CakePHP HTTP/session/CSRF integration, host-specific permissions and
  PHPStan max are release gates that must run on patched PHP 8.5 with Composer.
