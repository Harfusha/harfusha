"""Prevent private editor field values from being copied into SQL error logs."""
from pathlib import Path
import unittest

ROOT = Path(__file__).resolve().parents[2]


class EditorialLogPrivacyTests(unittest.TestCase):
    def test_admin_never_logs_raw_database_exception_messages(self) -> None:
        controller = (ROOT / 'src/Controller/AdminController.php').read_text(encoding='utf-8')
        # CakePHP QueryException::getMessage() can include SQL bind values,
        # including private drafts and unpublished project descriptions.
        self.assertNotIn('$error->getMessage()', controller)
        self.assertGreaterEqual(controller.count('get_class($error)'), 6)


if __name__ == '__main__':
    unittest.main()
