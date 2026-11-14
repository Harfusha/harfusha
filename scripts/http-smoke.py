#!/usr/bin/env python3
"""HTTP integration smoke against a REAL running CakePHP runtime, never the offline renderer."""
from __future__ import annotations

import os
import sys
import time
from http.cookiejar import CookieJar
from html.parser import HTMLParser
from urllib.error import HTTPError, URLError
from urllib.parse import urlencode
from urllib.request import HTTPCookieProcessor, HTTPRedirectHandler, Request, build_opener
from xml.etree import ElementTree

BASE = 'http://localhost:8765'


class NoRedirect(HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None


# CookieJar is essential for exercising actual CakePHP sessions and CSRF cookies.
OPENER = build_opener(HTTPCookieProcessor(CookieJar()), NoRedirect())


class CsrfExtractor(HTMLParser):
    def __init__(self) -> None:
        super().__init__()
        self.token: str | None = None

    def handle_starttag(self, tag: str, attrs: list[tuple[str, str | None]]) -> None:
        if tag == 'input':
            fields = dict(attrs)
            if fields.get('name') == '_csrfToken':
                self.token = fields.get('value')


def csrf_token(html: bytes) -> str:
    parser = CsrfExtractor()
    parser.feed(html.decode('utf-8'))
    if not parser.token:
        raise RuntimeError('Login page missing CSRF token')
    return parser.token


def fetch(path: str, *, method: str = 'GET', data: dict[str, str] | None = None) -> tuple[int, bytes, dict[str, str]]:
    req = Request(BASE + path, data=urlencode(data).encode('utf-8') if data is not None else None, method=method)
    try:
        with OPENER.open(req, timeout=5) as response:
            return response.status, response.read(), dict(response.headers)
    except HTTPError as error:
        return error.code, error.read(), dict(error.headers)


def test_admin_roundtrip() -> bool:
    """Exercise real cookies and Cake CSRF, never against production credentials."""
    password = os.environ.get('LAB_SMOKE_ADMIN_PASSWORD', '')
    if len(password) < 20 or os.environ.get('APP_ENV') != 'testing':
        print('FAIL admin smoke requires APP_ENV=testing and ephemeral LAB_SMOKE_ADMIN_PASSWORD', file=sys.stderr)
        return False
    code, html, headers = fetch('/admin')
    if code != 200 or b'Admin Login' not in html or 'no-store' not in headers.get('Cache-Control', ''):
        print('FAIL anonymous admin page', file=sys.stderr)
        return False
    token = csrf_token(html)
    if fetch('/admin/login', method='POST', data={'password': password})[0] not in (400, 403):
        print('FAIL missing CSRF token accepted', file=sys.stderr)
        return False
    if fetch('/admin/login', method='POST', data={'password': password, '_csrfToken': 'invalid'})[0] not in (400, 403):
        print('FAIL invalid CSRF token accepted', file=sys.stderr)
        return False
    code, _, headers = fetch('/admin/login', method='POST', data={
        'password': 'not-the-correct-password', '_csrfToken': token,
    })
    if code != 303 or not headers.get('Location', '').endswith('/admin?error=1'):
        print('FAIL incorrect password response', file=sys.stderr)
        return False
    code, _, headers = fetch('/admin/login', method='POST', data={
        'password': password, '_csrfToken': token,
    })
    if code != 303 or not headers.get('Location', '').endswith('/admin'):
        print('FAIL correct password/session renewal', file=sys.stderr)
        return False
    code, admin_html, headers = fetch('/admin')
    if code != 200 or b'Workshop control.' not in admin_html or 'no-store' not in headers.get('Cache-Control', ''):
        print('FAIL authenticated admin session', file=sys.stderr)
        return False
    admin_token = csrf_token(admin_html)
    code, _, _ = fetch('/admin/journal', method='POST', data={
        '_csrfToken': admin_token, 'category': '<invalid>', 'title_cs': 'A',
        'title_en': 'B', 'body_cs': 'C', 'body_en': 'D',
    })
    if code != 422:
        print('FAIL authenticated invalid editor input', file=sys.stderr)
        return False
    code, _, headers = fetch('/admin/logout', method='POST', data={'_csrfToken': admin_token})
    if code != 303 or not headers.get('Location', '').endswith('/admin'):
        print('FAIL logout response', file=sys.stderr)
        return False
    code, html, _ = fetch('/admin')
    if code != 200 or b'Admin Login' not in html or b'Workshop control.' in html:
        print('FAIL logout did not revoke session', file=sys.stderr)
        return False
    code, _, _ = fetch('/admin/project', method='POST', data={'_csrfToken': csrf_token(html)})
    if code != 403:
        print('FAIL anonymous editor after logout', file=sys.stderr)
        return False
    print('Actual CakePHP admin smoke passed: CSRF, cookies, password, session renewal, validation, logout.')
    return True


def main() -> int:
    for attempt in range(35):
        try:
            code, body, headers = fetch('/healthz')
            if code == 200 and body == b'ok':
                break
        except URLError:
            pass
        time.sleep(0.2)
    else:
        print('CakePHP HTTP server unavailable or unhealthy', file=sys.stderr)
        return 1

    cases = [
        ('/', 302, None), ('/cs/', 200, b'HARFUSHA'),
        ('/en/', 200, b'HARFUSHA'), ('/cs/projects', 200, b'Local AI Hub'),
        ('/cs/decisions', 200, 'Rozhodnutí za kódem'.encode('utf-8')),
        ('/en/homelab', 200, b'Inside the network'),
        ('/cs/projects/missing-project', 404, None),
        ('/sitemap.xml', 200, b'<urlset'),
        ('/cs/feed.xml', 200, b'<rss'),
        ('/admin', 200, b'HARFUSHA'),
        ('/api/v1/projects?lang=en', 200, b'"projects"'),
    ]
    for path, expected, contains in cases:
        status, body, headers = fetch(path)
        if path == '/':
            if not headers.get('Location', '').endswith('/cs/'):
                print('FAIL root redirect target', file=sys.stderr)
                return 1
        if status != expected or (contains is not None and contains not in body):
            print(f'FAIL {path}: expected {expected} {contains!r}, got {status}', file=sys.stderr)
            return 1
        if headers.get('X-Content-Type-Options') != 'nosniff' or 'frame-ancestors' not in headers.get('Content-Security-Policy', ''):
            print(f'FAIL {path}: security headers missing', file=sys.stderr)
            return 1
        if path == '/admin' and 'no-store' not in headers.get('Cache-Control',''):
            print('FAIL admin cache policy', file=sys.stderr)
            return 1
        if path.endswith('.xml'):
            ElementTree.fromstring(body)

    status, api_body, api_headers = fetch('/api/v1/projects?lang=en')
    if status != 200 or 'application/json' not in api_headers.get('Content-Type',''):
        print('FAIL API response type',file=sys.stderr)
        return 1
    import json
    payload=json.loads(api_body)
    if payload.get('count', 0) < 13 or any('private' in item for item in payload['projects']):
        print('FAIL API data or redaction',file=sys.stderr)
        return 1
    etag=api_headers.get('ETag','')
    if not etag:
        print('FAIL missing API ETag',file=sys.stderr)
        return 1
    req=Request(BASE+'/api/v1/projects?lang=en',headers={'If-None-Match':etag})
    try:
        with OPENER.open(req,timeout=5) as response:
            code=response.status
    except HTTPError as error:
        code=error.code
    if code!=304:
        print('FAIL API conditional 304',file=sys.stderr)
        return 1
    if fetch('/api/v1/projects?lang=fr')[0] != 400 or fetch('/api/v1/projects/does-not-exist')[0] != 404:
        print('FAIL API input bounds',file=sys.stderr)
        return 1

    # PHP's development server does not enforce Apache FilesMatch directives.
    dotfile_status, dotfile_body, _ = fetch('/.htaccess')
    if dotfile_status not in (403, 404) or b'RewriteEngine On' in dotfile_body:
        print(f'FAIL development server exposed webroot/.htaccess: {dotfile_status}', file=sys.stderr)
        return 1

    # An attacker-controlled Host must never become an accepted canonical origin.
    hostile_host = Request(BASE + '/healthz', headers={'Host': 'attacker.invalid'})
    try:
        with OPENER.open(hostile_host, timeout=5) as response:
            hostile_status = response.status
    except HTTPError as error:
        hostile_status = error.code
    if hostile_status != 400:
        print(f'FAIL untrusted Host was not rejected: {hostile_status}', file=sys.stderr)
        return 1

    status, _, headers = fetch('/cron/github', method='POST')
    if status != 403 or 'no-store' not in headers.get('Cache-Control', ''):
        print(f'FAIL cron authorization/cache: {status}', file=sys.stderr)
        return 1
    if not test_admin_roundtrip():
        return 1
    print(f'Actual CakePHP HTTP smoke passed: {len(cases)} pages, XML, headers and denied cron.')
    return 0


if __name__ == '__main__':
    raise SystemExit(main())
