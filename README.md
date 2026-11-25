# Adam Halfar

**Backend engineer · PHP / CakePHP · developer tooling · local-first AI**

I am a backend engineer focused on PHP and CakePHP. I build developer tools and desktop utilities, work with local AI and home infrastructure, and explore 3D reconstruction. I value code that remains understandable months later, so I pay attention to types, clear boundaries, tests and documentation.

- **Web / Engineering Portfolio:** [adam-halfar.cz](https://adam-halfar.cz) · [English Edition](https://adam-halfar.cz/en/)
- **Contact:** [adam.halfar@gmail.com](mailto:adam.halfar@gmail.com) · [GitHub Profile](https://github.com/Harfusha)
- **Based in:** Czechia (Central Europe)

## Selected projects

| Project | What it does | Status and boundary |
| --- | --- | --- |
| [Harfusha Lab](https://github.com/Harfusha/harfusha) | Server-side rendered engineering portfolio and interactive systems lab in CakePHP 5, PHP 8.5, and MariaDB. | Release v5.2.0; 0 errors under PHPStan max; interactive Request Lifecycle, Database Concurrency, and Infrastructure Failure labs. |
| [Local AI Hub](https://github.com/Harfusha/local-ai-hub) | Registers repositories, preprocesses and indexes them, then gives coding agents deterministic search, context, coordination and optional local models. | Version 6.1.0 source is a release candidate; model and optional backend availability depend on runtime configuration. |
| [MutaUI](https://github.com/Harfusha/MutaUI) | Desktop control surface for Mutagen sync and forwarding sessions. | Uses separately installed Mutagen CLI and its daemon; not its own sync engine. |
| Home Infrastructure | Git-backed Ansible automation for Pi-hole/Unbound DNS, Jellyfin, BookStack, CUPS printing, NAS backups, routing and on-demand Proxmox tests. | Private infrastructure, described through a redacted role model rather than live telemetry. |
| Woodbound | Unity prototype for layered world generation, routes and spatial validation. | A generated candidate is not yet an accepted playable release. |
| Scan2CAD Studio | Reconstructs CAD candidates from 3D scans and reviewed photo references. | Beta; dimensions need physical review, and a STEP export may not be an editable solid. |
| Tiskov | A proposal for a local 3D-print request and processing flow. | No source checkout, slicer run or live service is available to verify. |
| Shpotify | Private music rooms, shared queues and explainable recommendations. | Prototype; end-to-end playback is still being integrated. |
| Software Inc co-op mod | Separates collaboration rules from the game adapter. | In development; no playable multiplayer release has been accepted. |
| Discord bot | A catalog record mentions application commands and a separate maintenance path. | The source checkout is unavailable; command behavior, permissions and live operation are unverified. |

The portfolio explains each project’s purpose, current stage and evidence. Private infrastructure is described by role, without exposing addresses, credentials or live state.

## Harfusha Lab

Harfusha Lab is the CakePHP 5 application behind this portfolio ([adam-halfar.cz](https://adam-halfar.cz)). It documents ten projects with their current stages, technical trade-offs, and verifiable engineering evidence. It features interactive engineering demonstrations:
- **PHP Request Lifecycle Lab:** Trace CakePHP 5 requests from reverse proxy and security headers through strict routing to domain DTOs and conditional RFC 9110 ETag responses.
- **Database Concurrency Lab:** Step-by-step simulator explaining lost updates, optimistic version locking (`revision_token`), InnoDB atomic row locks, and request idempotency.
- **Infrastructure Failure Lab:** Dependency graph and multi-node failure simulator modeling home infrastructure topology and service redundancy.
- **Type Safety Lab:** Demonstrates compile-time generics and conditional return types from upstream core PR `cakephp/cakephp#18957`.

## How I work

- **Traceable behavior:** types and explicit validation at system boundaries.
- **Evidence before claims:** distinguish a source feature, a passing test, a running service and an accepted release.
- **Usable engineering:** readable interfaces, keyboard access, useful failure messages and current docs.

Research notes: [source reconciliation](docs/PROJECT-EVIDENCE-RECONCILIATION-2026-11-16.md) · [dated alternatives review](docs/PROJECT-ALTERNATIVES-RESEARCH-2026-11-16.md).

**Tools:** PHP, CakePHP, MySQL, Python, C#, Unity, Linux, Ansible, Docker and locally hosted AI models.

## Open source

Selected upstream contributions:

- [CakePHP #17827](https://github.com/cakephp/cakephp/pull/17827) — association typing and PHPStan improvements.
- [CakePHP #18957](https://github.com/cakephp/cakephp/pull/18957) — typed table locator usage.
- [DebugKit #1022](https://github.com/cakephp/debug_kit/pull/1022) — panel data compression and performance.
- [More public pull requests](https://github.com/pulls?q=is%3Apr+author%3AHarfusha).

## This repository

This checkout contains both my GitHub profile README and the Harfusha Lab application. Start with the [technical guide](docs/PROJECT-README.md), [architecture](docs/ARCHITECTURE.md), [release process](docs/RELEASE.md) or [Czech quick start](START-CZ.md).

CI evidence belongs to an exact commit. See the [GitHub Actions history](https://github.com/Harfusha/harfusha/actions) and the release notes for the revision you are reviewing; a green run alone does not prove a production deployment.

---

*I like systems where the useful path is clear, the failure path is visible, and the limits are written down.*
