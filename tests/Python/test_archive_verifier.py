"""Archive verification must detect tampering, path escapes and secret leaks."""
from __future__ import annotations
from hashlib import sha256
from pathlib import Path
import stat
import sys
import tempfile
import unittest
import warnings
import zipfile

ROOT = Path(__file__).resolve().parents[2]
sys.path.insert(0, str(ROOT / 'scripts'))
from verify_archive import ArchiveVerificationError, verify_archive  # noqa: E402


class ArchiveVerifierTests(unittest.TestCase):
    def setUp(self) -> None:
        temporary = tempfile.TemporaryDirectory()
        self.addCleanup(temporary.cleanup)
        self.root = Path(temporary.name)
        self.archive = self.root / 'sample.zip'
        self.files = {
            'composer.json': b'{}', 'README.md': b'# Example',
            'src/Application.php': b'<?php', 'webroot/index.php': b'<?php',
        }

    def make(self, *, files: dict[str, bytes] | None = None, manifest_overrides: dict[str, str] | None = None,
             duplicate: bool = False, symlink: bool = False) -> None:
        content = files if files is not None else self.files
        manifest = [f'{sha256(data).hexdigest()}  {name}' for name, data in content.items()]
        if manifest_overrides:
            manifest.extend(f'{digest}  {name}' for name, digest in manifest_overrides.items())
        with zipfile.ZipFile(self.archive, 'w', compression=zipfile.ZIP_DEFLATED) as zipf:
            for name, data in content.items():
                info = zipfile.ZipInfo(name)
                info.external_attr = (stat.S_IFLNK if symlink and name == 'README.md' else stat.S_IFREG) << 16
                zipf.writestr(info, data)
            if duplicate:
                with warnings.catch_warnings():
                    warnings.simplefilter('ignore', UserWarning)
                    zipf.writestr('README.md', b'duplicate')
            zipf.writestr('SHA256SUMS', '\n'.join(manifest) + '\n')

    def test_valid_source_archive(self) -> None:
        self.make()
        self.assertEqual(verify_archive(self.archive, variant='source'), 4)

    def test_unchanged_crc_but_modified_payload_is_detected(self) -> None:
        # A ZIP with internally valid CRCs is still rejected for wrong SHA256.
        self.make(files={**self.files, 'README.md': b'modified'},
                  manifest_overrides={'README.md': sha256(b'original').hexdigest()})
        with self.assertRaises(ArchiveVerificationError):
            verify_archive(self.archive, variant='source')

    def test_rejects_unsafe_members(self) -> None:
        for name in ('../escape.txt', 'webroot/.env', 'src/../../escape.php', 'config/app_local.php',
                     'webroot\\hack.php', 'C:/malware.php', '/root/leak.php', 'webroot/.env.example', 'webroot/.ENV.production', 'config/APP_LOCAL.PHP',
                     'data/.LAB-CONTENT-APPROVAL.JSON', 'vendor/.Env.Local'):
            with self.subTest(name=name):
                self.make(files={**self.files, name: b'secret'})
                with self.assertRaises(ArchiveVerificationError):
                    verify_archive(self.archive, variant='source')

    def test_rejects_missing_manifest_entry(self) -> None:
        self.make(files={**self.files, 'src/Extra.php': b'<?php'})
        with zipfile.ZipFile(self.archive, 'w') as z:
            for name, data in self.files.items():
                z.writestr(name, data)
            z.writestr('src/Extra.php', '<?php')
            z.writestr('SHA256SUMS', '\n'.join(f'{sha256(x).hexdigest()}  {n}' for n, x in self.files.items()))
        with self.assertRaises(ArchiveVerificationError):
            verify_archive(self.archive, variant='source')

    def test_rejects_duplicate_entries(self) -> None:
        self.make(duplicate=True)
        with self.assertRaisesRegex(ArchiveVerificationError, 'Duplicate'):
            verify_archive(self.archive, variant='source')

    def test_rejects_symlink(self) -> None:
        self.make(symlink=True)
        with self.assertRaisesRegex(ArchiveVerificationError, 'Non-regular'):
            verify_archive(self.archive, variant='source')

    def test_production_requires_vendor_and_lock(self) -> None:
        self.make()
        with self.assertRaisesRegex(ArchiveVerificationError, 'Composer'):
            verify_archive(self.archive, variant='production')
        self.make(files={**self.files, 'vendor/autoload.php': b'<?php', 'composer.lock': b'{}',
                         'scripts/preflight.php': b'<?php'})
        self.assertEqual(verify_archive(self.archive, variant='production'), 7)
        with self.assertRaisesRegex(ArchiveVerificationError, 'vendor'):
            verify_archive(self.archive, variant='source')


if __name__ == '__main__':
    unittest.main()
