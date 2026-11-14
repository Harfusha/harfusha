# Conceptual Data Paths — architecture and privacy contract

The `/cs/homelab` and `/en/homelab` page has a **Data Paths / Cesty dat** view.
It demonstrates four human-curated educational scenarios: media playback, VPN
access, network printing and backup transport. **There is no monitoring, scanning,
packet capture, contact with private hosts, or direct action on home machines.**

## Data contract

- `data/topology.json`: privacy-reviewed nodes and directed service dependencies.
- `data/flows.json`: curated examples; each has `id`, localized titles and descriptions,
  a `path` of 2–16 node IDs, and `steps_cs`/`steps_en` with exactly one label per hop.
- Consecutive path nodes must be adjacent through a dependency edge, traversable in
  either direction. It is valid to revisit the router when modeling a second leg.
- `PublicContentValidator` rejects missing fields, duplicates and inconsistent step
  counts. `NetworkFlow::fromArray` rejects nonexistent nodes or invalid links **before
  rendering**. Data errors are not treated as an empty flow.
- `ProjectCatalog::flows()` returns readonly typed domain values. Templates export
  allowlisted, JSON_HEX-escaped arrays, never raw code or configuration.
- `lab-core.js` uses the same publicly modeled dependencies to propagate a chosen
  simulated failure. `traceFlow()` returns the first unavailable hop and an availability
  flag for each step; UI displays a localized explanation and highlighted SVG edges.

## Threat model

No actual IP, DNS name, VLAN, routing policy, interface or credential is allowed.
Before editing a scenario, manually review each phrase for privacy leaks. Do not
assume that a diagram edge corresponds to a real physical cable or security rule.
`/data` must remain outside `DocumentRoot`; the data is published only in curated
HTML. `scripts/audit-public.py` scans data files heuristically for selected secret
patterns, but **manual review remains mandatory**.

## Examples and testing

1. Open `/cs/homelab`, choose **Cesty dat** and **Zálohování služeb**.
2. The browser shows the conceptual sequence `pi → router → nas`.
3. Enable outage simulation and mark the NAS as failed. The path is interrupted.
4. Reset. The illustrative path is available again.

Run `php tests/standalone.php`, `node --test tests/Client/lab-core.test.cjs`,
`tsc --noEmit --allowJs --checkJs --strict --target ES2022 --lib ES2022,DOM webroot/assets/js/lab-core.js`,
`python3 tests/Browser/smoke.py --chromium /usr/bin/chromium`, and
`python3 scripts/audit-public.py` on the appropriate build machine.
These tests do **not** validate the real CakePHP runtime on PHP 8.5 or live network
availability. Those require the distinct release gates in `docs/MASTER_PLAN.md`.
