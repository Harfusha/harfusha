# Project experience redesign — 2026-11-16

## Goal

Turn the portfolio from a set of similarly shaped summaries into a set of source-backed, distinct project stories. Visitors should understand what problem each project addresses, how it actually works, what is unfinished, and why the work is worth exploring.

The existing home visual is polished, but abstract. The nine project pages repeat the same “why / how / steps / diagram” structure, mostly without screenshots or proof. Playground has several separate toy-like panels without one clear reason to use it. Other sections currently read as standalone headings or generic lists rather than useful visitor journeys.

## Baseline scores

These are a first-pass critique of the current Czech rendered pages, not claims about content quality outside the visible site.

| Page / section | Score | Main gap |
| --- | ---: | --- |
| Home | 5/10 | Strong composition, but abstract promise and weak reason to continue or return. |
| Project index and categories | 3/10 | Search/filter works; categories are labels, not useful ways to understand the work. |
| Project detail pages | 2/10 | Same hierarchy and SVG diagram on all nine; no project-specific interaction; most have no image. |
| Playground | 3/10 | Many controls, but disconnected panels feel like unrelated prototypes and do not teach one coherent idea. |
| HomeLab | 4/10 | Useful service map and role list, but lacks a guided service/dependency story and evidence depth. |
| Connections | 4/10 | A graph exists, but the page has no strong narrative for why a visitor should explore an edge. |
| Journal | 3/10 | Long chronological archive; hard to see cause, decision, result, and affected project together. |
| Decisions | 3/10 | The title promises reasoning, but the page needs legible alternatives and consequences. |
| Now | 3/10 | Updates lack a clear status/provenance model and a path into deeper project context. |
| Open Source | 2/10 | The current headline is ambiguous and contribution stories are not easy to inspect. |
| About | 3/10 | Generic self-description, with little personal story or evidence. |
| Maker Lab | 2/10 | Correctly avoids unapproved products, but reads as an accidental empty page. |
| Not found / error states | 2/10 | Need deliberate recovery paths and clear next steps. |

Observed rendered structure: all nine project detail pages have zero input/button/canvas controls; each uses the shared SVG diagram. Only MutaUI has application screenshots, Tiskov and Woodbound have one graphic each. The home page has one image and no interactive control. The Playground has 34 controls, but its six demos are separate panels without a common visitor journey.

## Design direction

Use one shared editorial system for navigation, type, spacing, language, and accessibility; give each project its own exhibit, visual metaphor, interaction, and narrative. Avoid a repeated four-card process diagram. Reuse only the site shell and small accessible primitives.

Every claim about origin, architecture, status, performance, or results must cite committed project evidence or a primary source. Mark inference and unknowns. If the actual start motive is not documented, describe the evidenced problem and say the original motive is unknown. Screenshots must come from the real project or be labeled as an offline/synthetic demo; diagrams must say whether they show documented design or verified runtime. Never expose private paths, credentials, internal addresses, or unapproved products.

Each case study must answer the harder question: **why build this when alternatives exist?** Compare the closest current tool or workflow using primary sources and a dated check. State where an alternative is already adequate, where it differs from the documented project, and whether the author’s original reason is recorded. Do not manufacture a competitive advantage or personal motivation; label a reason as an inference when project records do not say it.

## Project exhibits to validate against research

| Project | Distinct experience direction |
| --- | --- |
| Local AI Hub | Repository intake and preprocessing timeline; compare exact indexed lookup with broader context, then show provenance and host-side verification. |
| MutaUI | Two-endpoint synchronization and forwarding console with explicit engine/daemon boundary; synthetic paths and disabled actions only. |
| Home Infrastructure | Service and dependency map: DNS, media, documentation, print, backup, orchestration, network and test host, with documented role details; never live monitoring. |
| Woodbound | Layered world-building canvas tied to real pipeline stages, spatial checks and a source-backed region; never imply an accepted playable build. |
| Scan2CAD Studio | Scan-to-CAD bench showing scale, cleanup, camera/photo alignment, uncertainty and human review; never imply certified measurements. |
| Tiskov | Document the proposed request-to-worker queue and human review without presenting it as implemented; distinguish a synthetic interaction from a real job, price or live order. |
| Shpotify | Shared-room queue and recommendation flow; expose the playback/agent boundary and what is not end-to-end proven. |
| Software Inc co-op | Collaboration state across domain core, persistence and game adapter; show only verified protocol/game behavior and state when in-game acceptance is absent. |
| Discord bot | Schematic command-to-maintenance path with unknown permission rules and the prior catalog-reported syntax issue; do not claim a live bot or current source validation. |

## Sections

- **Home:** one clear personal proposition, one evidence-backed featured story, and purposeful routes into projects, systems, and journal. The hero must explain why to explore, not only announce a portfolio.
- **Projects and categories:** searchable catalog organized by visitor questions/outcomes, with category descriptions that explain the kind of work. Filters remain usable by keyboard and on mobile.
- **Project pages:** distinct opening question, evidenced origin/problem, real architecture, concrete walkthrough, project-specific interactive exhibit, actual images/diagram, status/limits, and source trail. Shared boilerplate is not an acceptable substitute.
- **Playground:** one coherent “change a constraint, inspect the consequence” workbench. Each scenario must teach a real system idea, respond visibly to input, disclose simulation limits, and remain useful without network or model access.
- **HomeLab:** interactive service-role explorer with dependencies and detailed service descriptions, clearly presented as documented configuration rather than live telemetry.
- **Connections:** explorable relationship map with a reason and source for every edge; selectable projects reveal the connection's concrete meaning.
- **Journal / Decisions / Now:** connect work to decisions, outcomes, dates, and evidence freshness; let visitors move from an entry to the affected project.
- **Open Source / About:** explain contribution scope and personal working principles with verifiable examples, not generic claims.
- **Maker Lab:** stay product-free until there is approved evidence; make the empty state purposeful and honest.

## Acceptance criteria

1. Research chats return a source dossier for all nine projects; each dossier separates verified facts, inference, unknowns, and current working-tree changes.
2. Each public project page has a different narrative and a project-specific interactive exhibit. No two pages reuse the same generic diagram as their main visual.
3. All public sections and category journeys receive a documented audit and targeted redesign; no section is left as an accidental placeholder.
4. Each project page includes at least one real source image or a clearly labeled, evidence-grounded diagram; source and synthetic/demo status are visible.
5. Text is human, specific, bilingual, and accurate; all previous naming/privacy constraints remain enforced.
6. Playground has a clear teaching purpose, keyboard support, reduced-motion behavior, and explicit deterministic simulation boundaries.
7. Updated README images are captured from the final site, visually inspected, and match current UI.
8. PHP, JavaScript, content, rendered pages, browser/responsive, accessibility-relevant keyboard paths, archive, and live HTTP checks pass before release.
9. Each project has its own sourced alternatives comparison and an honest answer on why a custom project may or may not be justified. State the author's decision rule plainly: use an adequate existing option; build custom only for a concrete remaining gap. Keep that present-day rule separate from the original motive when sources do not record it.

## Work order

1. Finish the nine read-only source research chats; reconcile source facts and current alternatives into the project research record.
2. Score and map every public page/category against the acceptance criteria; finalize per-page design, alternatives and copy.
3. Implement the shared primitives and distinct project exhibits, then the category and site-section experiences.
4. Update bilingual content, diagrams, evidence captions, screenshots, and README.
5. Run focused and full CI checks; build and verify the production archive; deploy only the reviewed candidate and repeat live checks.

## Steering: author voice and visitor value

The current site still feels machine-written and underdeveloped. Keep the author's first-person voice grounded in what he has actually said: prefer an adequate existing tool; build a custom project only for a concrete remaining gap. Never invent the original motive when the source does not preserve it. Explain the actual problem and the current decision separately.

- Replace the current project map if its edges cannot be explained by real evidence. A visitor should understand the route through projects at a glance; no decorative relationship graph.
- Remove any demo control whose only effect is replacing a static paragraph. Every retained interaction must change a visible model, data structure, diagram, decision state, or authentic artifact, and explain what just changed.
- Expand Playground into a coherent multi-project workbench that shows the range of the author's work. Each station must use project-specific evidence and a real client-side state transition; no fake upload, live service, CAD conversion, game runtime, or audio playback.
- Turn HomeLab into a useful map of hosts, services, dependencies, and data flows. Keep configuration evidence separate from live health.
- Now must combine current public repository activity with a dated, sourced history. Do not label old events as current.
- Show closed contributions as completed when closure means the work was finished; distinguish merged PRs, completed issues, open work, failures, and unknown closure reasons.
- Enrich the development journal with a source-backed timeline. Give every entry its date, project, what changed, why the choice mattered, outcome or limitation, and a primary source.
- Rebuild technical decisions as a readable chain: problem → real alternatives → evidence → choice → trade-off → later result. Separate current decision rule from undocumented original motivation.
- Project studies must have genuinely different narrative structure and diagrams. Specifically: Local AI Hub preprocessing; MutaUI's existing CLI/daemon and operation preview; HomeLab service dependencies; Woodbound's route integrity and unaccepted runtime; Scan2CAD's scale/camera gates; Tiskov as an unverified proposal; Shpotify's implemented queue versus unverified playback; Software Inc's exact game-evidence boundary; Discord bot's catalog-only evidence boundary.
- Preserve bilingual parity, keyboard use, reduced motion, privacy redaction, source links, current README screenshots, and the exact-content release gate. No cron or scheduler.
