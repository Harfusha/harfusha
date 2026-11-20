#!/usr/bin/env python3
"""Read-only HTTPS staging smoke; never send credentials or mutate a real site.

Run AFTER deploying the reviewed release to an isolated staging hostname.
This does not replace database restore, editorial approval, or manual browser QA.
"""
from __future__ import annotations

import argparse
import json
import ssl
from collections.abc import Callable
from urllib.error import HTTPError
from urllib.parse import urlsplit
from urllib.request import HTTPSHandler, HTTPRedirectHandler, Request, build_opener
from xml.etree import ElementTree

MAX_BODY = 2 * 1024 * 1024
SENSITIVE_PATHS = (
    '/.env', '/.htaccess', '/composer.lock', '/vendor/autoload.php',
    '/config/app.php', '/data/projects.json', '/src/Application.php',
    '/tests/standalone.php', '/.git/HEAD', '/logs/error.log',
    '/db/schema.sql', '/scripts/preflight.php',
)


class NoRedirect(HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None


def fetch(base: str, path: str) -> tuple[int, bytes, dict[str, str]]:
    opener = build_opener(HTTPSHandler(context=ssl.create_default_context()), NoRedirect())
    request = Request(base + path, headers={'User-Agent': 'HarfushaLabStagingSmoke/1'})
    try:
        with opener.open(request, timeout=10) as response:
            return (
                response.status,
                response.read(MAX_BODY + 1),
                {key.lower(): val for key, val in response.headers.items()},
            )
    except HTTPError as error:
        return (
            error.code,
            error.read(MAX_BODY + 1),
            {key.lower(): val for key, val in error.headers.items()},
        )


def validate_origin(origin: str) -> str:
    parsed = urlsplit(origin)
    if (
        parsed.scheme != 'https' or not parsed.hostname or parsed.username is not None
        or parsed.password is not None or parsed.path not in ('', '/')
        or parsed.query or parsed.fragment
    ):
        raise ValueError('Supply a bare HTTPS staging origin (no path, query, or credentials)')
    try:
        port = parsed.port
    except ValueError as error:
        raise ValueError('Invalid HTTPS port') from error
    if port is not None and not 1 <= port <= 65535:
        raise ValueError('Invalid HTTPS port')
    return origin.rstrip('/')


def verify(
    origin: str,
    get: Callable[[str, str], tuple[int, bytes, dict[str, str]]] = fetch,
) -> list[str]:
    """Return failures without leaking response bodies, tokens or internal paths."""
    base = validate_origin(origin)
    errors: list[str] = []
    pages = {
        '/healthz': b'ok',
        '/cs/': b'Harfusha',
        '/en/': b'Harfusha',
        '/cs/projects': b'Local AI Hub',
        '/en/projects': b'Local AI Hub',
        '/api/v1/projects?lang=en': b'"projects"',
        '/sitemap.xml': b'<urlset',
        '/cs/feed.xml': b'<rss',
        '/en/feed.xml': b'<rss',
        '/cs/journal': b'id="entry-',
        '/en/journal': b'id="entry-',
        '/admin': b'Admin Login',
    }
    successful_bodies: dict[str, bytes] = {}
    for path, expected in pages.items():
        status, body, headers = get(base, path)
        if status != 200 or expected not in body or len(body) > MAX_BODY:
            errors.append(f'{path}: unexpected HTTP status, page content, or response size')
            continue
        successful_bodies[path] = body
        if headers.get('x-content-type-options') != 'nosniff':
            errors.append(f'{path}: missing nosniff')
        if 'frame-ancestors' not in headers.get('content-security-policy', ''):
            errors.append(f'{path}: missing CSP frame-ancestors')
        if 'max-age=' not in headers.get('strict-transport-security', ''):
            errors.append(f'{path}: missing HSTS')
        if path == '/admin':
            if 'no-store' not in headers.get('cache-control', '') or 'noindex' not in headers.get('x-robots-tag', ''):
                errors.append('Admin must be non-cacheable and non-indexable')
        if path == '/healthz' and 'no-store' not in headers.get('cache-control', ''):
            errors.append('Liveness endpoint must not be cached')
        if path in (
            '/cs/', '/en/', '/cs/projects', '/en/projects',
            '/cs/journal', '/en/journal', '/cs/feed.xml', '/en/feed.xml',
            '/api/v1/projects?lang=en',
        ) and 'no-store' not in headers.get('cache-control', ''):
            errors.append(f'{path}: mutable public content must not be cached')
        if path == '/sitemap.xml':
            try:
                ElementTree.fromstring(body)
            except ElementTree.ParseError:
                errors.append('Sitemap is not well-formed XML')
        if path.startswith('/api/'):
            if 'application/json' not in headers.get('content-type', '') or not headers.get('etag'):
                errors.append('API missing JSON Content-Type or ETag')
            try:
                payload = json.loads(body)
                records = payload['projects']
                if not isinstance(records, list) or not records or payload['count'] != len(records):
                    errors.append('API project catalog count or shape is invalid')
                elif any(not isinstance(item, dict) or 'private' in item for item in records):
                    errors.append('API includes unreviewed project field')
            except (ValueError, KeyError, TypeError):
                errors.append('API JSON is invalid')

    for locale in ('cs', 'en'):
        feed = successful_bodies.get(f'/{locale}/feed.xml')
        journal = successful_bodies.get(f'/{locale}/journal')
        if feed is None or journal is None:
            continue
        try:
            root = ElementTree.fromstring(feed)
        except ElementTree.ParseError:
            errors.append(f'{locale}: RSS is not well-formed XML')
            continue
        entries = root.findall('./channel/item')
        if not entries:
            errors.append(f'{locale}: RSS feed has no entries')
        for entry in entries:
            link = entry.findtext('link')
            guid = entry.find('guid')
            expected_prefix = base + f'/{locale}/journal#'
            if not isinstance(link, str) or not link.startswith(expected_prefix):
                errors.append(f'{locale}: RSS article link is not a same-origin permalink')
                continue
            fragment = link[len(expected_prefix):]
            if (not fragment or not fragment.startswith('entry-')
                    or ('id="' + fragment + '"').encode('utf-8') not in journal):
                errors.append(f'{locale}: RSS permalink has no visible article anchor')
            if guid is None or guid.get('isPermaLink') != 'true' or guid.text != link:
                errors.append(f'{locale}: RSS GUID does not match the article permalink')

    for path in SENSITIVE_PATHS:
        status, _, _ = get(base, path)
        if status not in (403, 404):
            errors.append(f'{path}: sensitive path did not return 403/404')

    return errors


def main() -> int:
    parser = argparse.ArgumentParser(description='Non-mutating shared-host staging smoke')
    parser.add_argument('--base-url', required=True, help='Bare HTTPS staging origin')
    args = parser.parse_args()
    try:
        failures = verify(args.base_url)
    except (ValueError, OSError) as error:
        parser.exit(2, f'Staging check could not start: {error}\n')
    if failures:
        print('Staging smoke failed:')
        for failure in failures:
            print(' - ' + failure)
        return 1
    print('Staging HTTP checks passed (non-mutating). Database restore and content approval remain separate.')
    return 0


if __name__ == '__main__':
    raise SystemExit(main())
