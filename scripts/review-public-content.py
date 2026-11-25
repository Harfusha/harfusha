#!/usr/bin/env python3
"""Local editorial approval of public datasets for a production release.

Explicit human confirmation is required. SHA-256 binds approval to exact bytes;
this is a review receipt, NOT a cryptographic signature or automatic privacy audit.
The private receipt must not be included in source or deployment archives.
"""
from __future__ import annotations

import argparse
from datetime import datetime, timezone
import hashlib
import json
import os
from pathlib import Path
import sys

ROOT = Path(__file__).resolve().parents[1]
REQUIRED = ('connections', 'contributions', 'decisions', 'evidence', 'flows', 'journal', 'maker', 'projects', 'topology')
RECEIPT = '.lab-content-approval.json'
APPROVAL_PHRASE = 'APPROVE PUBLIC CONTENT'


def workflow_dispatch_approved() -> bool:
    if os.environ.get('GITHUB_ACTIONS') != 'true' or os.environ.get('GITHUB_EVENT_NAME') != 'workflow_dispatch':
        return False
    event_path = Path(os.environ.get('GITHUB_EVENT_PATH', ''))
    if not event_path.is_absolute() or event_path.is_symlink() or not event_path.is_file():
        return False
    try:
        event = json.loads(event_path.read_text(encoding='utf-8'))
    except (OSError, ValueError):
        return False
    inputs = event.get('inputs') if isinstance(event, dict) else None
    return isinstance(inputs, dict) and inputs.get('public_content_approval') == APPROVAL_PHRASE


def manifest(root: Path) -> dict[str, str]:
    expected = {f'{name}.json' for name in REQUIRED}
    data_root = root / 'data'
    actual = {p.name for p in data_root.iterdir() if p.is_file()} if data_root.is_dir() else set()
    if actual != expected:
        raise ValueError('Unexpected, added or missing public data file; review the data inventory')
    results: dict[str, str] = {}
    for name in REQUIRED:
        path = root / 'data' / f'{name}.json'
        if not path.is_file() or path.is_symlink():
            raise ValueError(f'Missing or symlinked content: {path}')
        raw = path.read_bytes()
        try:
            entries = json.loads(raw.decode('utf-8'))
        except (UnicodeDecodeError, ValueError) as exc:
            raise ValueError(f'Invalid public JSON: {path.name}') from exc
        if not isinstance(entries, list):
            raise ValueError(f'Expected JSON array: {path.name}')
        results[f'data/{name}.json'] = hashlib.sha256(raw).hexdigest()
    # Public HTML and bundled visual assets must be reviewed, not just JSON.
    # These paths are optional in isolated unit-test fixtures, but present in a full project.
    for folder in ('templates', 'webroot/assets', 'docs/screenshots'):
        directory = root / folder
        if not directory.exists():
            continue
        for path in sorted(directory.rglob('*')):
            if path.is_symlink():
                raise ValueError(f'Symlinked public asset is forbidden: {path}')
            if not path.is_file():
                continue
            if path.name.startswith('.'):
                raise ValueError(f'Hidden file in publicly reviewed tree: {path}')
            results[path.relative_to(root).as_posix()] = hashlib.sha256(path.read_bytes()).hexdigest()
    return results


def check(root: Path) -> None:
    path = root / RECEIPT
    if not path.is_file() or path.is_symlink():
        raise ValueError('Editorial approval is missing. Review and approve public data locally.')
    try:
        receipt = json.loads(path.read_text(encoding='utf-8'))
    except (UnicodeDecodeError, ValueError) as exc:
        raise ValueError('Editorial receipt is malformed') from exc
    if not isinstance(receipt, dict) or set(receipt) != {'schema_version','reviewer','reviewed_at','files'} or receipt['schema_version'] != 1:
        raise ValueError('Unexpected editorial receipt schema')
    reviewer = receipt['reviewer']
    if not isinstance(reviewer, str) or not (2 <= len(reviewer.strip()) <= 120) or '\n' in reviewer:
        raise ValueError('Editorial receipt reviewer is missing')
    timestamp = receipt['reviewed_at']
    if not isinstance(timestamp, str):
        raise ValueError('Editorial receipt timestamp is invalid')
    try:
        at = datetime.fromisoformat(timestamp)
    except ValueError as exc:
        raise ValueError('Editorial receipt timestamp is invalid') from exc
    if at.tzinfo is None or at > datetime.now(timezone.utc):
        raise ValueError('Editorial receipt timestamp is missing timezone or in the future')
    actual = manifest(root)
    if not isinstance(receipt['files'], dict) or receipt['files'] != actual:
        raise ValueError('Public content changed since editorial approval; review again')


def create(root: Path, reviewer: str, confirmed: bool) -> None:
    if not confirmed:
        raise ValueError('Explicit human confirmation required')
    if not (2 <= len(reviewer.strip()) <= 120) or '\n' in reviewer:
        raise ValueError('Provide a named reviewer (2–120 characters)')
    target = root / RECEIPT
    if target.is_symlink():
        raise ValueError('Editorial receipt must not be a symlink')
    payload = {
        'schema_version': 1,
        'reviewer': reviewer.strip(),
        'reviewed_at': datetime.now(timezone.utc).isoformat(timespec='seconds'),
        'files': manifest(root),
    }
    target.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + '\n', encoding='utf-8')
    target.chmod(0o600)
    check(root)


def main() -> int:
    ap = argparse.ArgumentParser(description='Review public datasets and bind a local release receipt to their SHA-256 digests')
    group = ap.add_mutually_exclusive_group(required=True)
    group.add_argument('--review', action='store_true', help='Display filenames and data classifications for manual review')
    group.add_argument('--approve', action='store_true', help='Create a PRIVATE review receipt after explicit confirmation')
    group.add_argument('--verify', action='store_true', help='Ensure receipt is present and all public content bytes are unchanged')
    ap.add_argument('--reviewer', help='Reviewer label (required for --approve)')
    args = ap.parse_args()
    try:
        if args.verify:
            check(ROOT)
            print('Editorial review receipt verified against all public datasets')
        elif args.review:
            files = manifest(ROOT)
            for filename, digest in files.items():
                if filename.startswith('data/'):
                    entries = json.loads((ROOT / filename).read_text(encoding='utf-8'))
                    print(f'{filename}: {len(entries)} records; SHA-256 {digest}')
                else:
                    print(f'{filename}: asset/template SHA-256 {digest}')
            private = [p.get('name', '<unnamed>') for p in json.loads((ROOT/'data/projects.json').read_text(encoding='utf-8')) if p.get('private') is True]
            print(f'Private-derived case studies needing explicit review ({len(private)}): {", ".join(private)}')
            print('Review titles, descriptions, URLs, images, code snippets, topology, embedded scripts and privacy before approval.')
        else:
            if not args.reviewer:
                raise ValueError('--reviewer is required for --approve')
            print('You confirm review of all public data, templates and assets, including private-derived case studies.')
            if sys.stdin.isatty():
                if input(f'Type exactly "{APPROVAL_PHRASE}" to approve this snapshot: ').strip() != APPROVAL_PHRASE:
                    raise ValueError('Editorial approval not confirmed')
            elif not workflow_dispatch_approved():
                raise ValueError('Approval requires a TTY or the exact workflow_dispatch content-approval input')
            else:
                print('Exact workflow_dispatch approval received for this snapshot.')
            create(ROOT, args.reviewer, confirmed=True)
            print('Private editorial receipt saved. It is ignored by Git and excluded from archives.')
    except (ValueError, OSError) as exc:
        print(f'Public content approval: {exc}', file=sys.stderr)
        return 1
    return 0


if __name__ == '__main__':
    raise SystemExit(main())
