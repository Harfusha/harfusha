"""The release preflight must never weaken its runtime production checks."""
from pathlib import Path
import unittest

ROOT = Path(__file__).resolve().parents[2]


class ProductionPreflightConsistencyTests(unittest.TestCase):
    def test_preflight_delegates_to_runtime_security_policy(self) -> None:
        source = (ROOT / 'scripts/preflight.php').read_text(encoding='utf-8')
        self.assertIn(r'\App\Security\DeploymentPolicy::assertEnvironment(', source)
        self.assertIn('class_exists(', source)
        # No second origin/salt regex that could diverge from runtime startup.
        self.assertNotIn('parse_url(', source)
        self.assertNotIn("preg_match('/^[a-fA-F0-9]{64,128}$/D'", source)

    def test_runtime_policy_rejects_url_fragments_and_repeated_salts(self) -> None:
        source = (ROOT / 'src/Security/DeploymentPolicy.php').read_text(encoding='utf-8')
        self.assertIn("isset($parts['fragment'])", source)
        self.assertIn("isset($parts['pass'])", source)
        self.assertIn("filter_var($origin, FILTER_VALIDATE_URL)", source)
        self.assertIn("preg_match('/^(.)\\\\1+$/D', $salt)", source)


if __name__ == '__main__':
    unittest.main()
