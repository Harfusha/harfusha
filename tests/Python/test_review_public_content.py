from __future__ import annotations

from importlib.machinery import SourceFileLoader
import json
import io
from pathlib import Path
import tempfile
import unittest
from unittest.mock import patch

SCRIPT = Path(__file__).resolve().parents[2] / 'scripts' / 'review-public-content.py'
module = SourceFileLoader('content_review', str(SCRIPT)).load_module()


class EditorialReviewTest(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory()
        self.addCleanup(self.tmp.cleanup)
        self.root = Path(self.tmp.name)
        (self.root / 'data').mkdir()
        for name in module.REQUIRED:
            (self.root / 'data' / f'{name}.json').write_text('[]\n', encoding='utf-8')

    def test_missing_approval_blocks_release(self):
        with self.assertRaisesRegex(ValueError, 'missing'):
            module.check(self.root)

    def test_explicit_approval_required(self):
        with self.assertRaisesRegex(ValueError, 'confirmation'):
            module.create(self.root, 'Reviewer', confirmed=False)

    def test_noninteractive_approval_requires_exact_manual_workflow_input(self):
        event = self.root / 'event.json'
        event.write_text(json.dumps({'inputs': {'public_content_approval': 'APPROVE PUBLIC CONTENT'}}))
        environment = {
            'GITHUB_ACTIONS': 'true',
            'GITHUB_EVENT_NAME': 'workflow_dispatch',
            'GITHUB_EVENT_PATH': str(event),
        }
        with patch.dict('os.environ', environment, clear=True):
            self.assertTrue(module.workflow_dispatch_approved())
            event.write_text(json.dumps({'inputs': {'public_content_approval': 'APPROVE OLD CONTENT'}}))
            self.assertFalse(module.workflow_dispatch_approved())
            event.write_text(json.dumps({'inputs': {'public_content_approval': 'APPROVE PUBLIC CONTENT'}}))
            with patch.dict('os.environ', {'GITHUB_EVENT_NAME': 'push'}):
                self.assertFalse(module.workflow_dispatch_approved())
        with patch.object(module, 'ROOT', self.root), patch.object(
            module.sys, 'argv', [str(SCRIPT), '--approve', '--reviewer', 'CI reviewer']
        ), patch.object(module.sys, 'stdin', io.StringIO('')), patch.dict(
            'os.environ', environment, clear=True
        ):
            self.assertEqual(module.main(), 0)
            self.assertTrue((self.root / module.RECEIPT).is_file())

    def test_unchanged_public_data_can_be_verified(self):
        module.create(self.root, 'Reviewer', confirmed=True)
        module.check(self.root)
        receipt = json.loads((self.root / module.RECEIPT).read_text())
        self.assertEqual(len(receipt['files']), len(module.REQUIRED))

    def test_mutating_public_data_invalidates_approval(self):
        module.create(self.root, 'Reviewer', confirmed=True)
        (self.root / 'data/projects.json').write_text('[{"private":true}]\n', encoding='utf-8')
        with self.assertRaisesRegex(ValueError, 'changed'):
            module.check(self.root)

    def test_missing_public_file_invalidates_approval(self):
        module.create(self.root, 'Reviewer', confirmed=True)
        (self.root / 'data/topology.json').unlink()
        with self.assertRaisesRegex(ValueError, 'missing'):
            module.check(self.root)

    def test_unreviewed_additional_data_file_is_rejected(self):
        module.create(self.root, 'Reviewer', confirmed=True)
        (self.root / 'data/new.json').write_text('[]\n', encoding='utf-8')
        with self.assertRaisesRegex(ValueError, 'inventory'):
            module.check(self.root)

    def test_changed_public_template_invalidates_approval(self):
        (self.root / 'templates').mkdir()
        (self.root / 'templates/public.php').write_text('<p>Approved</p>', encoding='utf-8')
        module.create(self.root, 'Reviewer', confirmed=True)
        (self.root / 'templates/public.php').write_text('<p>Changed</p>', encoding='utf-8')
        with self.assertRaisesRegex(ValueError, 'changed'):
            module.check(self.root)

    def test_corrupt_receipt_rejected(self):
        (self.root / module.RECEIPT).write_text('null')
        with self.assertRaisesRegex(ValueError, 'schema'):
            module.check(self.root)

    def test_future_receipt_rejected(self):
        module.create(self.root, 'Reviewer', confirmed=True)
        path = self.root / module.RECEIPT
        receipt = json.loads(path.read_text())
        receipt['reviewed_at'] = '2999-01-01T00:00:00+00:00'
        path.write_text(json.dumps(receipt))
        with self.assertRaisesRegex(ValueError, 'future'):
            module.check(self.root)


if __name__ == '__main__':
    unittest.main()
