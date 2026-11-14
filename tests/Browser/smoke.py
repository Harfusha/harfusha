#!/usr/bin/env python3
"""Real Chromium DOM/UI checks of offline-rendered pages (not CakePHP HTTP E2E).

The browser environment may block loopback and file URLs. To avoid giving these
restrictions false pass/fail significance, this runner injects *the actual built*
HTML, CSS and JS into a clean page. It validates responsive layout, runtime JS,
keyboard activation, filters and deterministic network simulations. HTTP, CSP,
Cake ORM and authentication still require the separate integration/staging gates.
"""
from __future__ import annotations

import argparse
import re
import subprocess
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
HTML = ROOT / 'tmp' / 'public-preview'
APP = (ROOT / 'webroot/assets/js/app.js').read_text(encoding='utf-8')
CORE = (ROOT / 'webroot/assets/js/lab-core.js').read_text(encoding='utf-8')
CSS = (ROOT / 'webroot/assets/css/app.css').read_text(encoding='utf-8')
SCRIPTS = re.compile(r'<script\b[^>]+src="[^"]+"[^>]*></script>', re.IGNORECASE)
STYLES = re.compile(r'<link\b[^>]+rel="stylesheet"[^>]*>', re.IGNORECASE)


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--screenshots', action='store_true')
    parser.add_argument('--chromium', default=None, help='Path to system Chromium; otherwise use Playwright bundled browser')
    args = parser.parse_args()
    try:
        from playwright.sync_api import sync_playwright
    except ImportError as exc:
        raise SystemExit('Playwright is required: pip install playwright && python -m playwright install chromium') from exc
    subprocess.run(['php', 'scripts/render-public-preview.php'], cwd=ROOT, check=True)
    screenshots = ROOT / 'tmp/browser-qa'
    if args.screenshots:
        screenshots.mkdir(parents=True, exist_ok=True)
    checked = 0
    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(headless=True, executable_path=args.chromium,
                                             args=['--no-sandbox', '--disable-dev-shm-usage'])
        try:
            for width, height in ((320, 780), (390, 844), (768, 1024), (1365, 900), (1920, 1080)):
                page = browser.new_page(viewport={'width': width, 'height': height}, device_scale_factor=1)
                errors: list[str] = []
                page.on('pageerror', lambda error: errors.append(str(error)))
                try:
                    for site in ('cs/', 'en/', 'cs/connections', 'cs/homelab', 'cs/projects',
                                 'cs/now', 'cs/playground', 'cs/maker', 'cs/open-source', 'cs/decisions'):
                        path = HTML / site / 'index.html'
                        html = STYLES.sub('', SCRIPTS.sub('', path.read_text(encoding='utf-8')))
                        page.set_content(html, wait_until='domcontentloaded')
                        page.add_style_tag(content=CSS)
                        page.add_script_tag(content=CORE)
                        page.add_script_tag(content=APP)
                        assert not page.evaluate('document.documentElement.scrollWidth > document.documentElement.clientWidth'), (width, site, 'horizontal overflow')
                        assert page.locator('h1').count() == 1, (width, site, 'missing/extra H1')
                        if site == 'cs/' and width <= 390:
                            menu = page.locator('details.mobile-menu')
                            menu.locator('summary').click()
                            assert menu.get_attribute('open') is not None
                            assert menu.locator('nav a').count() >= 6
                        if site == 'cs/connections':
                            nodes = page.locator('g.connection-node[role="button"]')
                            assert nodes.count() >= 13
                            nodes.first.focus()
                            page.keyboard.press('Enter')
                            assert page.locator('[data-connections-name]').inner_text() == 'Local AI Hub'
                            assert nodes.first.evaluate('(element) => document.activeElement === element'), 'connection focus lost on redraw'
                            page.locator('[data-connection-search]').fill('mutaui')
                            assert page.locator('g.connection-node.muted').count() > 0
                        if site == 'cs/homelab':
                            nodes = page.locator('g.lab-node[role="button"]')
                            assert nodes.count() >= 7
                            page.locator('[data-failure-mode]').check()
                            nodes.filter(has_text='MikroTik').first.click()
                            assert 'Simulace:' in page.locator('[data-lab-note]').inner_text()
                            page.locator('g.lab-node[role="button"]').first.focus()
                            page.keyboard.press('Enter')
                            assert page.locator('g.lab-node[role="button"]').first.evaluate('(element) => document.activeElement === element'), 'homelab focus lost on redraw'
                            page.locator('[data-lab-reset]').click()
                            assert 'Všechny' in page.locator('[data-lab-note]').inner_text()
                            page.locator('[data-lab-view="flows"]').click()
                            assert page.locator('[data-flow-toolbar]').is_visible()
                            assert page.locator('[data-flow-steps] li').count() >= 3
                            page.locator('[data-flow-select]').select_option('backup')
                            assert 'Zálohování' in page.locator('[data-flow-title]').inner_text()
                            assert 'dostupná' in page.locator('[data-flow-result]').inner_text()
                            page.locator('[data-failure-mode]').check()
                            page.locator('g.lab-node[role="button"]').filter(has_text='QNAP').click()
                            assert 'přerušena' in page.locator('[data-flow-result]').inner_text()
                            page.locator('[data-lab-reset]').click()
                            assert 'dostupná' in page.locator('[data-flow-result]').inner_text()
                        if site == 'cs/maker':
                            assert page.locator('[data-maker]').count() >= 5
                            page.locator('[data-maker-status]').select_option('prototype')
                            assert page.locator('[data-maker]:not(.hidden)').count() >= 1
                            page.locator('[data-search]').fill('this-product-does-not-exist')
                            assert page.locator('[data-empty]').is_visible()
                        if site == 'cs/open-source':
                            assert page.locator('[data-activity]').count() >= 10
                            page.locator('select[data-kind]').select_option('issue')
                            assert page.locator('[data-activity]:not(.hidden)').count() >= 1
                            page.locator('[data-search]').fill('none-such-repo-zz')
                            assert page.locator('[data-empty]').is_visible()
                        if site == 'cs/projects':
                            page.locator('[data-search]').fill('nonexistent-zzzzz')
                            assert page.locator('[data-project]:not(.hidden)').count() == 0
                            assert page.locator('[data-empty]').is_visible()
                        if site == 'cs/decisions':
                            cards = page.locator('[data-decision]')
                            assert cards.count() >= 7
                            cards.first.locator('summary').click()
                            assert cards.first.get_attribute('open') is not None
                            page.locator('[data-decision-status]').select_option('prototype')
                            assert page.locator('[data-decision]:not(.hidden)').count() >= 1
                            page.locator('[data-search]').fill('no-such-architecture-decision')
                            assert page.locator('[data-empty]').is_visible()
                        if site == 'cs/playground':
                            page.locator('[data-demo-tab="ai"]').click()
                            before = page.locator('[data-ai-index]').inner_text()
                            page.locator('[data-ai-next]').click()
                            assert page.locator('[data-ai-index]').inner_text() != before
                            page.locator('[data-demo-tab="ai"]').focus()
                            page.keyboard.press('ArrowRight')
                            assert page.locator('[data-demo-tab="sync"]').get_attribute('aria-selected') == 'true', 'ArrowRight tab navigation'
                            page.keyboard.press('End')
                            assert page.locator('[data-demo-tab="request"]').get_attribute('aria-selected') == 'true', 'End tab navigation'
                            page.locator('[data-demo-tab="request"]').click()
                            assert page.locator('[data-request-response]').inner_text().startswith('HTTP 200')
                            page.locator('[data-request-route]').select_option('/admin/project')
                            page.locator('[data-request-method]').select_option('POST')
                            page.locator('[data-request-csrf]').uncheck()
                            assert page.locator('[data-request-response]').inner_text().startswith('HTTP 403')
                            page.locator('[data-request-csrf]').check()
                            assert page.locator('[data-request-response]').inner_text().startswith('HTTP 303')
                        if args.screenshots and site in ('cs/', 'cs/homelab', 'cs/connections'):
                            image = screenshots / f"{width}_{site.strip('/').replace('/', '_') or 'home'}.png"
                            page.screenshot(path=str(image), full_page=False)
                        assert not errors, (width, site, errors)
                        checked += 1
                finally:
                    page.close()
        finally:
            browser.close()
    print(f'Browser DOM smoke passed: {checked} real Chromium page/viewport checks, JS runtime, responsive and interactions.')


if __name__ == '__main__':
    main()
