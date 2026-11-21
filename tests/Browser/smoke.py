#!/usr/bin/env python3
"""Chromium checks for the current offline-rendered portfolio UI.

This checks responsive rendering and browser interactions without pretending to
verify CakePHP routing, database state or production telemetry.
"""
from __future__ import annotations

import argparse
import base64
import mimetypes
import os
import re
import subprocess
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
HTML = Path(os.environ.get('HAFRUSHA_PREVIEW_DIR', str(ROOT / 'tmp' / 'public-preview')))
APP = (ROOT / 'webroot/assets/js/app.js').read_text(encoding='utf-8')
CORE = (ROOT / 'webroot/assets/js/lab-core.js').read_text(encoding='utf-8')
CSS = "\n".join((ROOT / 'webroot/assets/css' / name).read_text(encoding='utf-8')
                for name in ('app.css', 'portfolio.css'))
SCRIPTS = re.compile(r'<script\b[^>]+src="[^"]+"[^>]*></script>', re.IGNORECASE)
STYLES = re.compile(r'<link\b[^>]+rel="stylesheet"[^>]*>', re.IGNORECASE)
SOURCES = re.compile(r'<source\b[^>]*>', re.IGNORECASE)
IMAGE_SRC = re.compile(r'(?P<prefix>\bsrc=")/assets/(?P<path>[^"]+)"', re.IGNORECASE)


def inline_local_images(markup: str) -> str:
    markup = SOURCES.sub('', markup)

    def inline(match: re.Match[str]) -> str:
        asset = ROOT / 'webroot' / 'assets' / match.group('path')
        if not asset.is_file():
            return match.group(0)
        mime = mimetypes.guess_type(asset.name)[0] or 'application/octet-stream'
        encoded = base64.b64encode(asset.read_bytes()).decode('ascii')
        return f'{match.group("prefix")}data:{mime};base64,{encoded}"'

    return IMAGE_SRC.sub(inline, markup)


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--screenshots', action='store_true')
    parser.add_argument('--screenshot-dir', default=str(ROOT / 'tmp' / 'browser-qa'))
    parser.add_argument('--chromium', default=None)
    args = parser.parse_args()
    try:
        from playwright.sync_api import sync_playwright
    except ImportError as exc:
        raise SystemExit('Playwright is required: pip install playwright && python -m playwright install chromium') from exc

    subprocess.run(['php', 'scripts/render-public-preview.php'], cwd=ROOT, check=True)
    pages = sorted(HTML.glob('cs/**/*.html')) + sorted(HTML.glob('en/**/*.html'))
    all_sites = [page.parent.relative_to(HTML).as_posix() for page in pages]
    core_sites = ['cs/', 'en/', 'cs/connections', 'en/connections', 'cs/homelab', 'en/homelab',
                  'cs/projects', 'cs/maker', 'cs/projects/local-ai-hub']
    screenshots = Path(args.screenshot_dir)
    if args.screenshots:
        screenshots.mkdir(parents=True, exist_ok=True)

    checked = 0
    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(headless=True, executable_path=args.chromium,
                                             args=['--no-sandbox', '--disable-dev-shm-usage'])
        try:
            for width, height in ((320, 780), (390, 844), (768, 1024), (1365, 900)):
                sites = all_sites if width in (390, 1365) else core_sites
                for site in sites:
                    path = HTML / site / 'index.html'
                    raw = inline_local_images(STYLES.sub('', SCRIPTS.sub('', path.read_text(encoding='utf-8'))))
                    page = browser.new_page(viewport={'width': width, 'height': height}, device_scale_factor=1)
                    errors: list[str] = []
                    page.on('pageerror', lambda error: errors.append(str(error)))
                    page.set_content(raw, wait_until='domcontentloaded')
                    page.add_style_tag(content=CSS)
                    page.add_script_tag(content=CORE)
                    page.add_script_tag(content=APP)
                    images = page.locator('img')
                    images.evaluate_all('items => items.forEach(image => image.loading = "eager")')
                    assert images.evaluate_all('items => items.every(image => image.complete && image.naturalWidth > 0)'), (site, 'an image did not load')
                    assert page.locator('h1').count() == 1, (width, site, 'expected one H1')
                    if site.startswith('en/'):
                        text = page.locator('body').inner_text()
                        assert not re.search(r'[áčďéěíňóřšťúůýžÁČĎÉĚÍŇÓŘŠŤÚŮÝŽ]', text), (site, 'Czech text leaked into the English page')
                    if site in ('cs', 'en'):
                        hero_image = page.locator('.portfolio-home-hero img').get_attribute('src')
                        featured_image = page.locator('#selected-work .showcase-desktop img').get_attribute('src')
                        assert hero_image != featured_image, (site, 'the MutaUI screenshot is duplicated on the homepage')
                    dimensions = page.evaluate('({width: innerWidth, scroll: document.documentElement.scrollWidth})')
                    assert dimensions['scroll'] <= dimensions['width'], (width, site, 'horizontal overflow', dimensions)
                    assert page.locator('img:not([alt])').count() == 0, (width, site, 'image without alt text')
                    assert '/playground' not in raw, (site, 'retired demo link remains')

                    if site.endswith('/connections'):
                        cards = page.locator('[data-map-project]')
                        assert cards.count() == 9, (site, 'expected nine project records')
                        assert {card.get_attribute('data-theme') for card in cards.all()} == {'operate', 'candidate', 'coordinate'}
                        detail = page.locator('.project-map-reason').first
                        detail.locator('summary').click()
                        assert detail.locator('.project-map-problem').is_visible()
                        assert detail.locator('.project-map-solution').is_visible()
                        page.locator('[data-map-search]').fill('tiskov')
                        assert page.locator('[data-map-project]:visible').count() == 1
                        page.locator('[data-map-search]').fill('')

                    if site.startswith(('cs/projects/', 'en/projects/')):
                        assert page.locator('.case-evidence').count() == 1, (site, 'missing evidence and limits')
                        assert page.locator('.case-followup').count() == 0, (site, 'obsolete generic follow-up remains')

                    if site.endswith('/homelab'):
                        nodes = page.locator('[data-lab-node-id]')
                        assert nodes.count() == 7
                        assert page.locator('.lab-topology-edges line').count() == 6
                        if width >= 1365:
                            unavailable = 'Unavailable' if site.startswith('en/') else 'Nedostupné'
                            available = 'Available' if site.startswith('en/') else 'Dostupné'
                            page.locator('[data-lab-view="dependencies"]').click()
                            page.locator('[data-lab-node-id="nas"]').click()
                            nas_label = 'QNAP NAS' if site.startswith('en/') else 'NAS QNAP'
                            assert page.locator('[data-lab-name]').inner_text() == nas_label
                            page.locator('[data-failure-mode]').check()
                            page.locator('[data-lab-node-id="nas"]').click()
                            assert unavailable in page.locator('[data-lab-node-id="nas"]').inner_text()
                            page.locator('[data-lab-reset]').click()
                            assert available in page.locator('[data-lab-node-id="nas"]').inner_text()
                            page.locator('[data-lab-view="flows"]').click()
                            page.locator('[data-flow-select]').select_option('media')
                            page.locator('[data-lab-scenario="media"]').click()
                            expected_failure = f'Path interrupted: {nas_label}' if site.startswith('en/') else f'Cesta přerušena: {nas_label}'
                            assert expected_failure in page.locator('[data-flow-result]').inner_text()
                        if width <= 390:
                            boxes = [node.bounding_box() for node in nodes.all()]
                            for i, left in enumerate(boxes):
                                for right in boxes[i + 1:]:
                                    if left and right:
                                        overlaps = (min(left['x'] + left['width'], right['x'] + right['width']) > max(left['x'], right['x'])
                                                    and min(left['y'] + left['height'], right['y'] + right['height']) > max(left['y'], right['y']))
                                        assert not overlaps, (width, site, 'overlapping map nodes')

                    if args.screenshots and site == 'cs' and width == 1365:
                        page.screenshot(path=str(screenshots / 'home-desktop.png'))
                    if args.screenshots and site == 'cs' and width == 390:
                        page.screenshot(path=str(screenshots / 'home-mobile.png'))
                    if args.screenshots and width == 1365 and site == 'cs/homelab':
                        page.locator('.site-header').evaluate("element => element.style.display = 'none'")
                        page.locator('.lab-shell').screenshot(path=str(screenshots / 'homelab-map.png'))
                    assert not errors, (width, site, errors)
                    checked += 1
                    page.close()
        finally:
            browser.close()
    print(f'Portfolio browser smoke passed: {checked} page/viewport checks, project filters and network interactions.')


if __name__ == '__main__':
    main()
