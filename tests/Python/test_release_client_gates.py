"""Regression tests for release-builder gate discovery without invoking a production build."""
from __future__ import annotations

import importlib.util
from pathlib import Path
import tempfile
import unittest
from unittest import mock
import sys

ROOT = Path(__file__).resolve().parents[2]
sys.path.insert(0, str(ROOT / 'scripts'))
SPEC = importlib.util.spec_from_file_location('harfusha_release_builder', ROOT / 'scripts/build-release.py')
assert SPEC is not None and SPEC.loader is not None
BUILDER = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(BUILDER)


class ReleaseClientGateTests(unittest.TestCase):
    def test_every_client_regression_file_is_included_in_sorted_order(self) -> None:
        with tempfile.TemporaryDirectory() as temp:
            root = Path(temp)
            tests = root / 'tests/Client'
            tests.mkdir(parents=True)
            (tests / 'z.test.cjs').write_text('', encoding='utf-8')
            (tests / 'a.test.cjs').write_text('', encoding='utf-8')
            (tests / 'helper.cjs').write_text('', encoding='utf-8')
            self.assertEqual(
                BUILDER.client_test_files(root),
                ['tests/Client/a.test.cjs', 'tests/Client/z.test.cjs'],
            )
            with mock.patch.object(BUILDER, 'require') as command:
                BUILDER.verify_client_tests(root)
            command.assert_called_once_with([
                'node', '--test', 'tests/Client/a.test.cjs', 'tests/Client/z.test.cjs',
            ])

    def test_production_builder_rejects_symlinked_regression_file(self) -> None:
        with tempfile.TemporaryDirectory() as temp:
            root = Path(temp)
            tests = root / 'tests/Client'
            tests.mkdir(parents=True)
            real = tests / 'safe.test.cjs'
            real.write_text('', encoding='utf-8')
            (tests / 'external.test.cjs').symlink_to(real)
            with self.assertRaisesRegex(RuntimeError, 'Unsafe browser-core test'):
                BUILDER.client_test_files(root)

    def test_missing_client_suite_fails_closed(self) -> None:
        with tempfile.TemporaryDirectory() as temp:
            with self.assertRaisesRegex(RuntimeError, 'No browser-core regression tests'):
                BUILDER.client_test_files(Path(temp))


if __name__ == '__main__':
    unittest.main()
