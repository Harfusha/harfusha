"""Pure transport-mocked tests for the non-mutating staging gate."""
from __future__ import annotations

import importlib.util
from pathlib import Path
import unittest

ROOT = Path(__file__).resolve().parents[2]
SPEC = importlib.util.spec_from_file_location('harfusha_staging_smoke', ROOT / 'scripts/staging-smoke.py')
assert SPEC is not None and SPEC.loader is not None
STAGING = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(STAGING)

COMMON = {
    'x-content-type-options': 'nosniff',
    'content-security-policy': "default-src 'self'; frame-ancestors 'none'",
    'strict-transport-security': 'max-age=31536000',
}


def good_response(base: str, path: str):
    assert base == 'https://stage.example.test'
    if path in STAGING.SENSITIVE_PATHS:
        return 404, b'Not found', COMMON
    if path == '/healthz':
        return 200, b'ok', {**COMMON, 'cache-control': 'no-store'}
    if path == '/admin':
        return 200, b'Admin Login', {
            **COMMON, 'cache-control': 'no-store, private',
            'x-robots-tag': 'noindex, nofollow',
        }
    if path == '/sitemap.xml':
        return 200, b'<?xml version="1.0"?><urlset></urlset>', COMMON
    if path == '/api/v1/projects?lang=en':
        return 200, b'{"projects":[{"slug":"local-ai-hub"}],"count":1}', {
            **COMMON, 'content-type': 'application/json', 'etag': '"etag"',
        }
    if path in ('/cs/journal', '/en/journal'):
        return 200, b'<article id="entry-sample123">Workshop note</article>', COMMON
    if path in ('/cs/feed.xml', '/en/feed.xml'):
        lang = path.split('/')[1]
        link = f'https://stage.example.test/{lang}/journal#entry-sample123'
        xml = ('<rss><channel><item><link>' + link + '</link>'
               '<guid isPermaLink="true">' + link + '</guid></item></channel></rss>')
        return 200, xml.encode(), COMMON
    if path in ('/cs/', '/en/'):
        return 200, b'HARFUSHA', COMMON
    if path in ('/cs/projects', '/en/projects'):
        return 200, b'Local AI Hub', COMMON
    raise AssertionError('Unexpected request ' + path)


class StagingSmokeTests(unittest.TestCase):
    def test_only_plain_https_origins_are_allowed(self) -> None:
        for url in ('http://stage.example.test', 'https://user:secret@stage.example.test',
                    'https://stage.example.test/admin', 'https://stage.example.test/?token=x',
                    'https://stage.example.test:99999'):
            with self.subTest(url=url), self.assertRaises(ValueError):
                STAGING.validate_origin(url)
        self.assertEqual('https://stage.example.test', STAGING.validate_origin('https://stage.example.test/'))

    def test_non_mutating_checks_pass_with_appropriate_responses(self) -> None:
        self.assertEqual([], STAGING.verify('https://stage.example.test', good_response))

    def test_live_rss_links_must_target_visible_same_origin_entries(self) -> None:
        def broken(base: str, path: str):
            if path == '/cs/feed.xml':
                return (200, b'<rss><channel><item><link>https://evil.example/feed</link>'
                        b'<guid isPermaLink="true">https://evil.example/feed</guid>'
                        b'</item></channel></rss>', COMMON)
            if path == '/en/journal':
                return 200, b'<article id="entry-unrelated">Workshop</article>', COMMON
            return good_response(base, path)

        failures = STAGING.verify('https://stage.example.test', broken)
        self.assertTrue(any('same-origin' in message for message in failures))
        self.assertTrue(any('article anchor' in message for message in failures))

    def test_incorrect_document_root_exposes_application_files(self) -> None:
        def exposed(base: str, path: str):
            if path == '/config/app.php':
                return 200, b'<?php private config', COMMON
            return good_response(base, path)

        self.assertTrue(any('/config/app.php' in message
                            for message in STAGING.verify('https://stage.example.test', exposed)))

    def test_exposed_private_file_is_a_hard_failure(self) -> None:
        def exposed(base: str, path: str):
            if path == '/.env':
                return 200, b'should-never-be-public', COMMON
            return good_response(base, path)

        self.assertTrue(any('/.env' in error for error in STAGING.verify('https://stage.example.test', exposed)))

    def test_missing_security_headers_and_api_shape_are_detected(self) -> None:
        def invalid(base: str, path: str):
            status, body, headers = good_response(base, path)
            if path == '/api/v1/projects?lang=en':
                return status, b'{"projects":[],"count":1}', {**headers, 'strict-transport-security': ''}
            return status, body, headers

        failures = STAGING.verify('https://stage.example.test', invalid)
        self.assertTrue(any('HSTS' in error for error in failures))
        self.assertTrue(any('project catalog' in error for error in failures))


if __name__ == '__main__':
    unittest.main()
