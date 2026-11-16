#!/usr/bin/env python3
"""Independently verify a Harfusha Lab ZIP before distribution or upload.

No Composer, PHP runtime, network or MySQL is needed. This validates the archive
as a *whole*, not just the ZIP CRC: safe paths, exhaustive SHA-256 manifest,
file kind, prohibited secret paths and variant-specific required files.
"""
from __future__ import annotations

import argparse
from hashlib import sha256
from pathlib import Path, PurePosixPath
import re
import stat
import zipfile

ALLOWED_VARIANTS = ('source', 'production')
MINIMUM = {'composer.json', 'webroot/index.php', 'src/Application.php', 'README.md'}
PROHIBITED_PARTS = {'.git', '.ssh', '__pycache__', '.phpunit.cache', 'node_modules', 'tmp-secret'}
PROHIBITED_NAMES = {'app_local.php', '.lab-content-approval.json', 'id_rsa', 'id_ed25519'}
PROHIBITED_SUFFIXES = {'.pem', '.key', '.p12', '.pfx', '.sqlite', '.db', '.pyc'}
# Composer ca-bundle 1.6.1 at the source reference pinned in composer.lock.
CA_BUNDLE = 'vendor/composer/ca-bundle/res/cacert.pem'
CA_BUNDLE_SHA256 = '796f1389bfea13c0ed78f663b765baaff54a508e5483786bd6ece73c5e3e3cbe'
MAX_ENTRIES = 20000
MAX_UNCOMPRESSED = 512 * 1024 * 1024


class ArchiveVerificationError(ValueError):
    pass


def _safe_name(name: str) -> bool:
    parts = PurePosixPath(name).parts
    if not parts or name.startswith('/') or '\\' in name or '\x00' in name or re.match(r'^[A-Za-z]:', name):
        return False
    folded = tuple(part.lower() for part in parts)
    if any(part in ('', '.', '..') for part in name.split('/')):
        return False
    if any(part in PROHIBITED_PARTS or part in PROHIBITED_NAMES for part in folded):
        return False
    if any(part.startswith('.env') for part in folded) and name != '.env.example':
        return False
    return name == CA_BUNDLE or PurePosixPath(name).suffix.lower() not in PROHIBITED_SUFFIXES


def verify_archive(path: Path, *, variant: str) -> int:
    """Raise on any failed assertion; return the count of hashed application files."""
    if variant not in ALLOWED_VARIANTS:
        raise ArchiveVerificationError('Unknown package variant')
    with zipfile.ZipFile(path, 'r') as archive:
        entries = archive.infolist()
        if not entries or len(entries) > MAX_ENTRIES:
            raise ArchiveVerificationError('Invalid archive entry count')
        names = [info.filename for info in entries]
        if len(names) != len(set(names)):
            raise ArchiveVerificationError('Duplicate archive paths')
        if 'SHA256SUMS' not in names:
            raise ArchiveVerificationError('Missing SHA256SUMS')
        total = 0
        for info in entries:
            if info.is_dir() or not _safe_name(info.filename):
                raise ArchiveVerificationError(f'Unsafe archive member: {info.filename!r}')
            if info.flag_bits & 1:
                raise ArchiveVerificationError('Encrypted archive entries are forbidden')
            file_mode = info.external_attr >> 16
            if stat.S_IFMT(file_mode) not in (0, stat.S_IFREG):
                raise ArchiveVerificationError(f'Non-regular archive member: {info.filename!r}')
            if info.file_size > 128 * 1024 * 1024:
                raise ArchiveVerificationError('Oversized individual archive member')
            total += info.file_size
            if total > MAX_UNCOMPRESSED:
                raise ArchiveVerificationError('Uncompressed archive too large')
        if archive.testzip() is not None:
            raise ArchiveVerificationError('Invalid ZIP checksum')
        actual = set(names) - {'SHA256SUMS'}
        if not MINIMUM.issubset(actual):
            raise ArchiveVerificationError('Missing application entrypoint or metadata')
        if variant == 'production':
            if not {'composer.lock', 'vendor/autoload.php', 'scripts/preflight.php'}.issubset(actual):
                raise ArchiveVerificationError('Production bundle has no locked Composer runtime')
            if 'vendor/composer/ca-bundle/src/CaBundle.php' in actual and CA_BUNDLE not in actual:
                raise ArchiveVerificationError('Composer public CA bundle missing')
            if CA_BUNDLE in actual and sha256(archive.read(CA_BUNDLE)).hexdigest() != CA_BUNDLE_SHA256:
                raise ArchiveVerificationError('Composer public CA bundle hash mismatch')
            if any(name.lower().startswith(('tests/', '.github/')) or name.lower().endswith('.env.example') for name in actual):
                raise ArchiveVerificationError('Development files in production bundle')
        elif any(name.startswith('vendor/') for name in actual):
            raise ArchiveVerificationError('Source package must not ship vendor dependencies')
        manifest = archive.read('SHA256SUMS')
        if len(manifest) > 2 * 1024 * 1024:
            raise ArchiveVerificationError('SHA256 manifest too large')
        try:
            lines = manifest.decode('utf-8').splitlines()
        except UnicodeError as exc:
            raise ArchiveVerificationError('Invalid SHA256 manifest encoding') from exc
        seen: set[str] = set()
        for row in lines:
            if not re.match(r'^[0-9a-f]{64}  ', row):
                raise ArchiveVerificationError('Malformed SHA256 line')
            checksum, name = row.split('  ', 1)
            if name not in actual or name in seen:
                raise ArchiveVerificationError('Unknown or duplicated SHA256 manifest entry')
            seen.add(name)
            if sha256(archive.read(name)).hexdigest() != checksum:
                raise ArchiveVerificationError(f'SHA256 mismatch for {name}')
        if seen != actual:
            raise ArchiveVerificationError('SHA256 manifest does not cover every file')
        return len(actual)


def main() -> None:
    parser = argparse.ArgumentParser(description='Verify an entire source or production ZIP')
    parser.add_argument('archive', type=Path)
    parser.add_argument('--variant', required=True, choices=ALLOWED_VARIANTS)
    args = parser.parse_args()
    count = verify_archive(args.archive, variant=args.variant)
    print(f'Verified {count} files, {args.variant} package, SHA256 manifest and safe paths: {args.archive}')


if __name__ == '__main__':
    main()
