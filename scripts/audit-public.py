#!/usr/bin/env python3
"""Fail a build if public curated files contain internal targets, secrets or invalid refs.
This heuristic is a second layer after manual privacy approval, not a full secret scanner.
"""
import json
from pathlib import Path
import re
import sys

ROOT = Path(__file__).resolve().parents[1]
PUBLIC = [ROOT / 'webroot', ROOT / 'data']
PATTERNS = {
    'RFC1918-192': re.compile(r'(?<!\d)192\.168\.\d{1,3}\.\d{1,3}'),
    'RFC1918-10': re.compile(r'(?<!\d)10\.\d{1,3}\.\d{1,3}'),
    'RFC1918-172': re.compile(r'(?<!\d)172\.(?:1[6-9]|2\d|3[01])\.\d{1,3}\.\d{1,3}'),
    'common-secrets': re.compile(r'(?:ADMIN_PASSWORD_HASH|CRON_SECRET|DB_PASS|BEGIN (?:RSA|OPENSSH|EC) PRIVATE KEY)'),
}
failures=[]
for folder in PUBLIC:
    for f in folder.rglob('*'):
        if not f.is_file() or f.suffix.lower() not in {'.json','.css','.js','.svg','.html','.txt'}:
            continue
        content=f.read_text(encoding='utf-8')
        for label,pat in PATTERNS.items():
            if pat.search(content): failures.append(f'{f.relative_to(ROOT)}: {label}')
for name in ('projects','topology','connections','contributions','journal','maker','decisions','flows'):
    p=ROOT/'data'/f'{name}.json'
    try:
        data=json.loads(p.read_text(encoding='utf-8'))
        if not isinstance(data,list): failures.append(f'{p.name}: expected list')
    except (OSError,ValueError) as exc:
        failures.append(f'{p.name}: {exc}')
if failures:
    print('Public content audit failed:')
    print('\n'.join(' - '+v for v in failures))
    sys.exit(1)
print('Public content audit passed')
