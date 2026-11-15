# GitHub import integrity: fail closed on pagination and inconsistent data

The portfolio does not expose a personal GitHub token to the web or to browser JavaScript. The importer calls **public, unauthenticated** GitHub API endpoints only.

## Before any database mutation

1. `GithubSearchPageCollector` checks `total_count`, `incomplete_results`, valid canonical GitHub issue/PR URLs, page order, unique records and the GitHub 1,000-result ceiling. A shortened page is only allowed if the accumulated count exactly equals `total_count`.
2. `GithubRepositoryPageCollector` requires ordered pages of at most 100 objects, rejects duplicate `full_name` across pages and requires a final page with fewer than 100 records. Unlike Search API there is no declared total count.
3. `GithubSnapshot` validates author, PR kind and merge-state consistency, and strips activity authored against owned repositories.
4. `PublicRepository` validates public visibility, owner, canonical URL, UTF-8 description and timestamp. It rejects an unexpected private repository and an entirely empty list of original public repositories.
5. Only after all steps succeed does the code replace both caches and record a successful sync **in one MySQL transaction**. In that transaction, it also retains only the latest **64** successful GitHub sync receipts in `sync_runs` (other sources are untouched). A nonblocking `flock()` prevents overlapping CLI/HTTP imports. On any failure the last valid database snapshot remains unchanged.

## Guarantees and limitations

- Duplicate or changing Search API pages cannot silently replace good data.
- The `imported`, `merged`, and `issues` counts in the synchronization receipt are calculated from accepted **external** entries only. Raw GitHub Search totals can also include authored work in owned repositories and must not be presented as published import counts.
- GitHub titles and repository descriptions must be valid UTF-8 with XML-safe public text. Impossible upstream UTC timestamps and malformed contribution URLs fail validation before database mutation.
- GitHub API is eventually consistent and the separate merged-PR query is not an atomic upstream transaction. If the searches disagree, synchronization **fails** and should be retried at a later time.
- The GitHub Search API caps access to 1,000 items per query. This code refuses truncated records; accounts with larger histories need year-scoped search and deterministic merging before expansion.
- A public GitHub repository could be made private after the most recent sync. Cached snapshots are **not instantaneous revocation**. The owner must periodically audit visibility or disable the importer if the privacy model changes.
- The importer is not run in this isolated package build. PHP unit/pure tests cover its deterministic page collectors, but a real public API and MySQL transactional integration check remains required for production acceptance.
