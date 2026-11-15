#!/usr/bin/env python3
"""Create a reproducible, privacy-conservative, *source-only* archive.

A source archive is NOT the production deployment bundle. To build a deployable
zip with Composer vendor dependencies use build-release.py and all release gates.
"""
from __future__ import annotations

import argparse
import hashlib
from pathlib import Path
import subprocess
import sys
import zipfile

from client_gates import client_test_files
from verify_archive import verify_archive

ROOT = Path(__file__).resolve().parents[1]
EXCLUDED_DIRS = {
    '.git', 'vendor', 'node_modules', 'tmp', 'logs', '__pycache__', '.phpunit.cache',
    '.idea', '.vscode', '.serena', 'scratch', 'test-results', 'coverage',
}
EXCLUDED_FILES = {'sha256sums', '.ds_store', 'app_local.php', '.lab-content-approval.json',
                  'id_rsa', 'id_ed25519'}
EXCLUDED_SUFFIXES = {'.key', '.pem', '.p12', '.pfx', '.sqlite', '.db', '.log', '.pyc'}
ALLOW_ROOT_DOTFILES = {'.env.example', '.gitignore', '.htaccess'}
RUNTIME_PLACEHOLDERS = {
    'tmp/.gitkeep', 'tmp/cache/.gitkeep', 'tmp/cache/models/.gitkeep',
    'tmp/cache/persistent/.gitkeep', 'tmp/cache/views/.gitkeep', 'logs/.gitkeep',
}


def verify_client_tests(root: Path) -> None:
    """Run every client regression test, not just the initial lab-core file."""
    subprocess.run(
        ['node', '--test', *client_test_files(root)],
        cwd=root, check=True, stdout=subprocess.DEVNULL,
    )


def included_files() -> list[Path]:
    files: list[Path] = []
    for path in ROOT.rglob('*'):
        if not path.is_file():
            continue
        rel = path.relative_to(ROOT)
        folded = tuple(part.lower() for part in rel.parts)
        if any(part in EXCLUDED_DIRS for part in folded) and rel.as_posix() not in RUNTIME_PLACEHOLDERS:
            continue
        if path.name.lower() in EXCLUDED_FILES or path.suffix.lower() in EXCLUDED_SUFFIXES:
            continue
        if any(part.startswith('.env') for part in folded) and rel.as_posix() != '.env.example':
            continue
        if path.name.startswith('.') and len(rel.parts) == 1 and path.name not in ALLOW_ROOT_DOTFILES:
            continue
        if any(part in {'.ssh', '.config'} for part in folded):
            continue
        if path.is_symlink():
            raise RuntimeError(f'Symlink not included in source package: {rel}')
        files.append(path)
    return sorted(files)


def main() -> int:
    ap = argparse.ArgumentParser(description='Build source-only zip with integrity manifest')
    ap.add_argument('--output', type=Path, required=True)
    ap.add_argument('--skip-tests', action='store_true', help='Only for local archive inspection; normal packaging requires checks')
    args = ap.parse_args()
    out = args.output.resolve()
    if out.is_relative_to(ROOT):
        ap.error('Output must be outside the source tree')
    if not args.skip_tests:
        for command in (
            ['php', 'scripts/syntax-check.php'],
            ['php', 'tests/standalone.php'],
            ['python3', 'scripts/audit-public.py'],
            ['python3', '-m', 'unittest', 'discover', '-s', 'tests/Python'],
            ['python3', 'scripts/audit-rendered.py'],
            ['node', '--check', 'webroot/assets/js/app.js'],
            ['node', '--check', 'webroot/assets/js/lab-core.js'],
        ):
            subprocess.run(command, cwd=ROOT, check=True, stdout=subprocess.DEVNULL)
        verify_client_tests(ROOT)
    files = included_files()
    archive_manifest: list[str] = []
    out.parent.mkdir(parents=True, exist_ok=True)
    with zipfile.ZipFile(out, 'w', compression=zipfile.ZIP_DEFLATED, compresslevel=9) as z:
        for path in files:
            rel = path.relative_to(ROOT).as_posix()
            content = path.read_bytes()
            archive_manifest.append(f'{hashlib.sha256(content).hexdigest()}  {rel}')
            info = zipfile.ZipInfo(rel, date_time=(2026, 11, 14, 12, 0, 0))
            info.compress_type = zipfile.ZIP_DEFLATED
            info.external_attr = 0o100644 << 16
            z.writestr(info, content, compress_type=zipfile.ZIP_DEFLATED, compresslevel=9)
        info = zipfile.ZipInfo('SHA256SUMS', date_time=(2026, 11, 14, 12, 0, 0))
        info.compress_type = zipfile.ZIP_DEFLATED
        info.external_attr = 0o100644 << 16
        z.writestr(info, '\n'.join(archive_manifest) + '\n')
    with zipfile.ZipFile(out) as z:
        corrupt = z.testzip()
        if corrupt is not None:
            raise RuntimeError(f'Archive failed CRC test: {corrupt}')
    verified_files = verify_archive(out, variant='source')
    if verified_files != len(files):
        raise RuntimeError('Source manifest does not match package file count')
    digest = hashlib.sha256(out.read_bytes()).hexdigest()
    print(f'SOURCE archive: {out}\nFiles: {len(files) + 1}, bytes: {out.stat().st_size}\nSHA256: {digest}')
    print('This source archive does not contain installed Composer vendor/ packages; NOT a deployable release.')
    return 0


if __name__ == '__main__':
    sys.exit(main())
