"""Production archive packaging must remain fail-closed without Composer installed."""
from __future__ import annotations

import hashlib
from pathlib import Path
import sys
import tempfile
import unittest
import zipfile

ROOT = Path(__file__).resolve().parents[2]
sys.path.insert(0, str(ROOT / 'scripts'))
from release_package import ReleasePackagingError, collect, stage_application, write_archive  # noqa: E402


class ReleasePackageTests(unittest.TestCase):
    def setUp(self) -> None:
        self.temp = tempfile.TemporaryDirectory()
        self.addCleanup(self.temp.cleanup)
        self.base = Path(self.temp.name)
        self.src = self.base / 'src'
        self.src.mkdir()
        for name, content in {
            'composer.json': '{}', 'composer.lock': '{}', 'webroot/index.php': '<?php',
            'webroot/assets/app.js': 'alert(1)', 'src/Application.php': '<?php',
            'scripts/preflight.php': '<?php', 'data/projects.json': '[]',
            'README.md': 'Hello', 'tests/secret.txt': 'not for production',
            '.env': 'DB_PASS=SECRET', 'config/app_local.php': '<?php // password',
            'webroot/.env.production': 'exposed?', 'logs/secret.log': 'private',
        }.items():
            file = self.src / name
            file.parent.mkdir(parents=True, exist_ok=True)
            file.write_text(content)

    def _stage(self) -> Path:
        stage = self.base / 'stage'
        stage_application(self.src, stage)
        return stage

    def test_staging_does_not_copy_private_files_or_tests(self) -> None:
        stage = self._stage()
        self.assertTrue((stage / 'scripts/preflight.php').exists())
        self.assertFalse((stage / '.env').exists())
        self.assertFalse((stage / 'tests/secret.txt').exists())
        self.assertFalse((stage / 'config/app_local.php').exists())
        self.assertFalse((stage / 'webroot/.env.production').exists())
        self.assertFalse((stage / 'logs/secret.log').exists())

    def test_zip_has_manifest_and_runtime_dependencies_without_secrets(self) -> None:
        stage = self._stage()
        vendor = stage / 'vendor'
        vendor.mkdir()
        (vendor / 'autoload.php').write_text('<?php // composer')
        (vendor / 'package.php').write_text('<?php // runtime')
        (vendor / 'bin').mkdir()
        (vendor / 'bin/phpunit').write_text('dev proxy, not shipped')
        out = self.base / 'result.zip'
        count, digest = write_archive(stage, out)
        self.assertGreater(count, 5)
        self.assertEqual(digest, hashlib.sha256(out.read_bytes()).hexdigest())
        with zipfile.ZipFile(out) as archive:
            self.assertIsNone(archive.testzip())
            names = set(archive.namelist())
            self.assertIn('SHA256SUMS', names)
            self.assertIn('vendor/autoload.php', names)
            self.assertIn('scripts/preflight.php', names)
            self.assertNotIn('vendor/bin/phpunit', names)
            self.assertNotIn('config/app_local.php', names)
            self.assertNotIn('.env', names)
            for row in archive.read('SHA256SUMS').decode().splitlines():
                digest, filename = row.split('  ', 1)
                self.assertEqual(digest, hashlib.sha256(archive.read(filename)).hexdigest())

    def test_missing_vendor_rejects_release(self) -> None:
        stage = self._stage()
        with self.assertRaisesRegex(ReleasePackagingError, 'autoload'):
            write_archive(stage, self.base / 'fail.zip')

    def test_symlink_inside_public_tree_rejected(self) -> None:
        try:
            (self.src / 'webroot/leak.txt').symlink_to(self.src / '.env')
        except OSError:
            self.skipTest('No symlink support')
        with self.assertRaisesRegex(ReleasePackagingError, 'Symlink'):
            collect(self.src, include_vendor=False)

    def test_staging_inside_source_rejected(self) -> None:
        with self.assertRaises(ReleasePackagingError):
            stage_application(self.src, self.src / 'inside')

    def test_output_inside_stage_rejected(self) -> None:
        stage = self._stage()
        (stage / 'vendor').mkdir()
        (stage / 'vendor/autoload.php').write_text('<?php')
        with self.assertRaises(ReleasePackagingError):
            write_archive(stage, stage / 'release.zip')


if __name__ == '__main__':
    unittest.main()
