"""Guard against divergent Apache and CakePHP security header policies."""
from pathlib import Path
import re
import unittest

ROOT = Path(__file__).resolve().parents[2]


class SecurityHeaderConsistencyTests(unittest.TestCase):
    def test_apache_and_php_csp_have_identical_directives(self) -> None:
        apache = (ROOT / 'webroot/.htaccess').read_text(encoding='utf-8')
        php = (ROOT / 'src/Middleware/SecurityHeadersMiddleware.php').read_text(encoding='utf-8')
        match_apache = re.search(r'Header always set Content-Security-Policy "([^"]+)"', apache)
        match_php = re.search(r"Content-Security-Policy', implode\('; ', \[(.*?)\]\)\)", php, re.S)
        self.assertIsNotNone(match_apache)
        self.assertIsNotNone(match_php)
        assert match_apache is not None and match_php is not None
        directives = re.findall(r'"([^"]+)"', match_php.group(1))
        self.assertTrue(directives)

        def by_name(items: list[str]) -> dict[str, str]:
            parsed = {}
            for directive in items:
                name = directive.split(' ', 1)[0]
                self.assertNotIn(name, parsed)
                parsed[name] = directive
            return parsed

        self.assertEqual(
            by_name(match_apache.group(1).split('; ')),
            by_name(directives),
        )

    def test_apache_and_php_permissions_policy_match(self) -> None:
        apache = (ROOT / 'webroot/.htaccess').read_text(encoding='utf-8')
        php = (ROOT / 'src/Middleware/SecurityHeadersMiddleware.php').read_text(encoding='utf-8')
        match = re.search(r'Header always set Permissions-Policy "([^"]+)"', apache)
        self.assertIsNotNone(match)
        assert match is not None
        self.assertIn("'Permissions-Policy', '" + match.group(1) + "'", php)


if __name__ == '__main__':
    unittest.main()
