"""Shared fail-closed discovery of every offline Node regression test.

The source and production release builders must run identical client suites.
"""
from __future__ import annotations

from pathlib import Path


def client_test_files(root: Path) -> list[str]:
    """Return all checked-in *.test.cjs files in stable order, or fail."""
    tests = sorted((root / 'tests/Client').glob('*.test.cjs'))
    if not tests:
        raise RuntimeError('No browser-core regression tests found')
    for path in tests:
        if path.is_symlink() or not path.is_file():
            raise RuntimeError(f'Unsafe browser-core test file: {path.name}')
    return [path.relative_to(root).as_posix() for path in tests]
