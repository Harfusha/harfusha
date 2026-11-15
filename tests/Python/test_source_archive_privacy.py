"""Source-only packaging never copies secrets even when filenames change case."""
from __future__ import annotations

import importlib.util
from pathlib import Path
import sys
from tempfile import TemporaryDirectory
import unittest
from unittest.mock import patch

ROOT = Path(__file__).resolve().parents[2]
sys.path.insert(0, str(ROOT / 'scripts'))
SPEC = importlib.util.spec_from_file_location('harfusha_source_builder', ROOT / 'scripts/build-source.py')
assert SPEC is not None and SPEC.loader is not None
BUILDER = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(BUILDER)


class SourceArchivePrivacyTests(unittest.TestCase):
    def test_case_insensitive_secret_exclusions(self) -> None:
        with TemporaryDirectory() as tmp:
            root = Path(tmp)
            cases = {
                '.env.example': 'a harmless template',
                'src/Example.php': '<?php',
                '.ENV': 'SECRET',
                'config/.Env.production': 'SECRET',
                'webroot/.ENV.local': 'SECRET',
                'data/.LAB-CONTENT-APPROVAL.JSON': 'SECRET',
                'config/APP_LOCAL.PHP': 'SECRET',
                'scripts/worker.PEM': 'SECRET',
            }
            for name, text in cases.items():
                path = root / name
                path.parent.mkdir(parents=True, exist_ok=True)
                path.write_text(text)
            with patch.object(BUILDER, 'ROOT', root):
                included = {path.relative_to(root).as_posix() for path in BUILDER.included_files()}
            self.assertEqual({'.env.example', 'src/Example.php'}, included)


if __name__ == '__main__':
    unittest.main()
