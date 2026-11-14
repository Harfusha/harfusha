"""Offline tests for the HTTP test runner's auth assertions.

These mock the transport, not CakePHP. They ensure the real-runtime smoke
script actually requires the expected CSRF/authentication/rotation boundaries.
"""
from __future__ import annotations
from contextlib import redirect_stderr
import importlib.util
import io
import os
from pathlib import Path
from unittest import TestCase, mock

ROOT = Path(__file__).resolve().parents[2]
SPEC = importlib.util.spec_from_file_location('harfusha_http_smoke', ROOT / 'scripts/http-smoke.py')
assert SPEC is not None and SPEC.loader is not None
SMOKE = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(SMOKE)


class HttpSmokeTests(TestCase):
    def test_extracts_real_form_token(self) -> None:
        html = b'<form><input name="unrelated" value="q"><input type="hidden" name="_csrfToken" value="ab&amp;cd"></form>'
        self.assertEqual(SMOKE.csrf_token(html), 'ab&cd')

    def test_missing_token_fails(self) -> None:
        with self.assertRaisesRegex(RuntimeError, 'CSRF token'):
            SMOKE.csrf_token(b'<form><input name="password"></form>')

    def test_real_auth_smoke_is_mandatory_not_silently_skipped(self) -> None:
        with mock.patch.dict(os.environ, {'APP_ENV': 'development', 'LAB_SMOKE_ADMIN_PASSWORD': 'x'*30}):
            with redirect_stderr(io.StringIO()):
                self.assertFalse(SMOKE.test_admin_roundtrip())

    def test_auth_roundtrip_contract(self) -> None:
        password = 'local-smoke-password-generated-for-test'
        login = b'<h1>Admin Login</h1><input name="_csrfToken" value="login-token">'
        admin = b'<h1>Workshop control.</h1><input name="_csrfToken" value="admin-token">'
        expected = [
            ('/admin', 'GET', None, 200, login, {'Cache-Control': 'no-store'}),
            ('/admin/login', 'POST', {'password': password}, 403, b'', {}),
            ('/admin/login', 'POST', {'password': password, '_csrfToken': 'invalid'}, 403, b'', {}),
            ('/admin/login', 'POST', {'password': 'not-the-correct-password', '_csrfToken': 'login-token'},
             303, b'', {'Location': '/admin?error=1'}),
            ('/admin/login', 'POST', {'password': password, '_csrfToken': 'login-token'},
             303, b'', {'Location': '/admin'}),
            ('/admin', 'GET', None, 200, admin, {'Cache-Control': 'no-store'}),
            ('/admin/journal', 'POST', {'_csrfToken': 'admin-token', 'category': '<invalid>',
                                     'title_cs': 'A', 'title_en': 'B', 'body_cs': 'C', 'body_en': 'D'},
             422, b'', {}),
            ('/admin/logout', 'POST', {'_csrfToken': 'admin-token'}, 303, b'', {'Location': '/admin'}),
            ('/admin', 'GET', None, 200, login, {}),
            ('/admin/project', 'POST', {'_csrfToken': 'login-token'}, 403, b'', {}),
        ]
        calls = []

        def simulated_fetch(path: str, *, method: str = 'GET', data: dict[str, str] | None = None):
            index = len(calls)
            self.assertLess(index, len(expected))
            want_path, want_method, want_data, status, body, headers = expected[index]
            self.assertEqual((path, method, data), (want_path, want_method, want_data))
            calls.append(index)
            return status, body, headers

        with mock.patch.dict(os.environ, {'APP_ENV': 'testing', 'LAB_SMOKE_ADMIN_PASSWORD': password}):
            with mock.patch.object(SMOKE, 'fetch', side_effect=simulated_fetch):
                self.assertTrue(SMOKE.test_admin_roundtrip())
        self.assertEqual(len(calls), len(expected))

    def test_unexpected_login_success_is_a_failure(self) -> None:
        login = b'<h1>Admin Login</h1><input name="_csrfToken" value="login-token">'
        with mock.patch.dict(os.environ, {'APP_ENV': 'testing', 'LAB_SMOKE_ADMIN_PASSWORD': 'x' * 40}):
            with mock.patch.object(SMOKE, 'fetch', side_effect=[
                (200, login, {'Cache-Control': 'no-store'}), (303, b'', {}),
            ]):
                with redirect_stderr(io.StringIO()):
                    self.assertFalse(SMOKE.test_admin_roundtrip())


if __name__ == '__main__':
    import unittest
    unittest.main()
