#!/usr/bin/env python3
"""Secure, deterministic production bundle packaging.

No network or runtime dependencies: these primitives are independently testable.
The caller must first run the complete framework quality gates and install a
production-only Composer vendor tree *inside a disposable staging directory*.
"""
from __future__ import annotations

from hashlib import sha256
from pathlib import Path
import shutil
import stat
import zipfile

from verify_archive import verify_archive

ROOT_FILES = ('composer.json', 'composer.lock', '.htaccess', 'LICENSE', 'README.md', 'START-CZ.md')
SOURCE_DIRS = ('bin', 'config', 'data', 'db', 'src', 'templates', 'webroot', 'plugins', 'resources')
SHIPPING_DIRS = (*SOURCE_DIRS, 'vendor')
RUNTIME_FILES = ('tmp/.gitkeep', 'tmp/cache/.gitkeep', 'tmp/cache/models/.gitkeep',
                 'tmp/cache/persistent/.gitkeep', 'tmp/cache/views/.gitkeep', 'logs/.gitkeep')
OPERATIONS_FILES = ('scripts/preflight.php', 'docs/PROJECT-README.md')
EXCLUDE_PARTS = {'tests', 'test', '__tests__', '__pycache__', '.git', '.github',
                 'node_modules', '.idea', '.vscode', 'examples', '.phpunit.cache'}
EXCLUDE_NAMES = {'.DS_Store', 'app_local.php', '.env', '.env.local', '.env.production',
                 'id_rsa', 'id_ed25519', '.lab-content-approval.json'}
EXCLUDE_SUFFIXES = {'.key', '.pem', '.p12', '.pfx', '.sqlite', '.db', '.log', '.bak', '.swp'}
FIXED_DATE = (2026, 11, 14, 12, 0, 0)


class ReleasePackagingError(RuntimeError):
    """A security or completeness failure blocks packaging."""


def _allowed(relative: Path) -> bool:
    if any(part in EXCLUDE_PARTS for part in relative.parts):
        return False
    if any(part.startswith('.env') or part in EXCLUDE_NAMES for part in relative.parts):
        return False
    if relative.suffix.lower() in EXCLUDE_SUFFIXES:
        return False
    if relative.parts[:2] == ('vendor', 'bin'):
        return False  # Composer bin proxies are unnecessary for the deployed app.
    return True


def collect(root: Path, *, include_vendor: bool) -> list[Path]:
    """Allowlisted files only, never follow symlinks or include private env files."""
    root = root.resolve()
    result: list[Path] = []
    for name in (*ROOT_FILES, *OPERATIONS_FILES, *RUNTIME_FILES):
        path = root / name
        if path.is_symlink():
            raise ReleasePackagingError(f'Symlink in release input: {name}')
        if path.is_file():
            result.append(path)
    for folder in (SHIPPING_DIRS if include_vendor else SOURCE_DIRS):
        directory = root / folder
        if not directory.exists():
            continue
        if directory.is_symlink() or not directory.is_dir():
            raise ReleasePackagingError(f'Invalid release directory: {folder}')
        for path in directory.rglob('*'):
            rel = path.relative_to(root)
            if path.is_symlink():
                if _allowed(rel):
                    raise ReleasePackagingError(f'Symlink in release input: {rel}')
                continue
            if not _allowed(rel) or not path.is_file():
                continue
            if not stat.S_ISREG(path.stat().st_mode):
                raise ReleasePackagingError(f'Non-regular release file: {rel}')
            result.append(path)
    return sorted(set(result), key=lambda p: p.relative_to(root).as_posix())


def stage_application(source: Path, destination: Path) -> int:
    """Copy only reviewed source assets (not vendor, caches, secrets or tests)."""
    source = source.resolve()
    destination = destination.resolve()
    if destination == source or source in destination.parents:
        raise ReleasePackagingError('Staging must be outside the source tree')
    files = collect(source, include_vendor=False)
    for file in files:
        rel = file.relative_to(source)
        target = destination / rel
        target.parent.mkdir(parents=True, exist_ok=True)
        shutil.copyfile(file, target)
    for relative in ('tmp', 'tmp/cache', 'tmp/cache/models', 'tmp/cache/persistent',
                     'tmp/cache/views', 'logs'):
        (destination / relative).mkdir(parents=True, exist_ok=True)
    for required in ('composer.json', 'composer.lock', 'webroot/index.php', 'src/Application.php'):
        if not (destination / required).is_file():
            raise ReleasePackagingError(f'Release input missing: {required}')
    return len(files)


def write_archive(stage: Path, output: Path) -> tuple[int, str]:
    """Create ZIP with verified SHA-256 per file, without exposing local secrets."""
    stage = stage.resolve()
    output = output.resolve()
    if stage == output or stage in output.parents:
        raise ReleasePackagingError('Release output must be outside staging')
    if not (stage / 'vendor/autoload.php').is_file():
        raise ReleasePackagingError('Production vendor/autoload.php is missing')
    if not (stage / 'composer.lock').is_file():
        raise ReleasePackagingError('Reproducible composer.lock is missing')
    files = collect(stage, include_vendor=True)
    output.parent.mkdir(parents=True, exist_ok=True)
    manifest: list[str] = []
    with zipfile.ZipFile(output, 'w', compression=zipfile.ZIP_DEFLATED, compresslevel=9) as bundle:
        for file in files:
            rel = file.relative_to(stage).as_posix()
            payload = file.read_bytes()
            manifest.append(f'{sha256(payload).hexdigest()}  {rel}')
            info = zipfile.ZipInfo(rel, date_time=FIXED_DATE)
            info.compress_type = zipfile.ZIP_DEFLATED
            info.external_attr = 0o100644 << 16
            bundle.writestr(info, payload, compress_type=zipfile.ZIP_DEFLATED, compresslevel=9)
        info = zipfile.ZipInfo('SHA256SUMS', date_time=FIXED_DATE)
        info.compress_type = zipfile.ZIP_DEFLATED
        info.external_attr = 0o100644 << 16
        bundle.writestr(info, '\n'.join(manifest) + '\n')
    with zipfile.ZipFile(output) as bundle:
        failure = bundle.testzip()
        if failure is not None:
            raise ReleasePackagingError(f'ZIP checksum mismatch: {failure}')
    if verify_archive(output, variant='production') != len(files):
        raise ReleasePackagingError('Production manifest file count mismatch')
    return len(files) + 1, sha256(output.read_bytes()).hexdigest()
