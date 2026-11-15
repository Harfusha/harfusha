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
        '/cs/': b'HARFUSHA',
        '/en/': b'HARFUSHA',
        '/cs/projects': b'Local AI Hub',
        '/en/projects': b'Local AI Hub',
        '/api/v1/projects?lang=en': b'"projects"',
        '/sitemap.xml': b'<urlset',
        '/admin': b'Admin Login',
    }
    for path, expected in pages.items():
        status, body, headers = get(base, path)
        if status != 200 or expected not in body or len(body) > MAX_BODY:
            errors.append(f'{path}: unexpected HTTP status, page content, or response size')
            continue
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

    for path in ('/.env', '/.htaccess', '/composer.lock', '/vendor/autoload.php'):
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
