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
    def test_source_builder_runs_every_browser_regression_file(self) -> None:
        with TemporaryDirectory() as tmp:
            root = Path(tmp)
            tests = root / 'tests/Client'
            tests.mkdir(parents=True)
            (tests / 'b.test.cjs').write_text('', encoding='utf-8')
            (tests / 'a.test.cjs').write_text('', encoding='utf-8')
            (tests / 'fixture.cjs').write_text('', encoding='utf-8')
            with patch.object(BUILDER.subprocess, 'run') as runner:
                BUILDER.verify_client_tests(root)
            runner.assert_called_once_with(
                ['node', '--test', 'tests/Client/a.test.cjs', 'tests/Client/b.test.cjs'],
                cwd=root, check=True, stdout=BUILDER.subprocess.DEVNULL,
            )

    def test_source_builder_fails_closed_without_browser_tests(self) -> None:
        with TemporaryDirectory() as tmp:
            with self.assertRaisesRegex(RuntimeError, 'No browser-core regression tests'):
                BUILDER.verify_client_tests(Path(tmp))

    def test_source_builder_rejects_symlinked_browser_test(self) -> None:
        with TemporaryDirectory() as tmp:
            root = Path(tmp)
            tests = root / 'tests/Client'
            tests.mkdir(parents=True)
            target = tests / 'real.test.cjs'
            target.write_text('', encoding='utf-8')
            try:
                (tests / 'linked.test.cjs').symlink_to(target)
            except OSError as error:
                if getattr(error, 'winerror', None) == 1314:
                    self.skipTest('Windows symlink privilege is required')
                raise
            with self.assertRaisesRegex(RuntimeError, 'Unsafe browser-core test'):
                BUILDER.verify_client_tests(root)

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
