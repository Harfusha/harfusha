# Public-content approval: no automatic publication of private-derived content

Some case studies describe **private repositories**. They have no repository links in public JSON, but even descriptions, screenshots, timelines and architecture may be sensitive. Passing a regex secret scanner or schema validator **does not** constitute editorial authorization.

## Mandatory manual release workflow

1. Before approving a release, review **all** eight JSON files, templates and public assets in `data/` and every related screenshot/asset/template for personal information, internal hostnames, service details, licenses, false status claims and things not intended for public disclosure. The script hashes JSON, templates and bundled images/assets, but **actual manual review** is still required.
2. Run `python3 scripts/review-public-content.py --review` to see the file hashes and a shortlist of private-derived case studies.
3. After a real human review, approve on a terminal with `python3 scripts/review-public-content.py --approve --reviewer 'Your reviewer label'` and type `APPROVE PUBLIC CONTENT`, or start the GitHub Actions `workflow_dispatch` release manually with that exact input. Noninteractive approval is accepted only from that manual workflow event.
4. Approval creates a **private receipt** `.lab-content-approval.json` binding the eight public JSON files, templates and public visual assets to exact SHA-256 values. Local receipts stay on the release machine; the manual CI build creates its receipt only on the disposable runner. Receipts are gitignored and excluded from source and production ZIPs.
5. `python3 scripts/review-public-content.py --verify` checks that no data, template or public asset changed after approval. The production builder now **requires** this check before and after all quality gates, and refuses unapproved changes.
6. Any change to `data/`, templates or public assets invalidates the receipt and requires a fresh review. Unknown added data files are rejected too.

This is an **auditability control, not a digital signature**: someone with full write access to the build machine could fabricate a receipt. Protect the release environment, restrict filesystem access and consider independent review for private material. Ordinary CI never approves content; the deployable archive is built only after a human manually starts `workflow_dispatch` with the exact phrase. This workflow builds an artifact; it does not deploy or schedule deployments.

**Current artifact status:** this source ZIP does not contain a manual approval receipt and is **not authorized for automatic public production deployment**. The application can be reviewed and exercised locally without this approval.
