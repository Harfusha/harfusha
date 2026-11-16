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
                    core_sites = ('cs/', 'en/', 'cs/connections', 'cs/homelab', 'cs/projects',
                                  'cs/now', 'cs/playground', 'cs/maker', 'cs/open-source', 'cs/decisions')
                    if width in (320, 1365):
                        all_sites = [p.parent.relative_to(HTML).as_posix() for p in HTML.rglob('index.html')]
                        sites = sorted(site + '/' if site in ('cs', 'en') else site for site in all_sites)
                    else:
                        sites = core_sites
                    for site in sites:
                        path = HTML / site / 'index.html'
                        html = STYLES.sub('', SCRIPTS.sub('', path.read_text(encoding='utf-8')))
                        page.set_content(html, wait_until='domcontentloaded')
                        page.add_style_tag(content=CSS)
                        page.add_script_tag(content=CORE)
                        page.add_script_tag(content=APP)
                        assert not page.evaluate('document.documentElement.scrollWidth > document.documentElement.clientWidth'), (width, site, 'horizontal overflow')
                        assert page.locator('h1').count() == 1, (width, site, 'missing/extra H1')
                        if '/connections' in site:
                            assert page.locator('.connection-node.selected').count() == 1, (site, 'missing default selection')
                            assert page.locator('[data-connections-name]').inner_text() not in ('—', 'Mapa projektů', 'Project map'), (site, 'empty project inspector')
                            assert 'hidden' not in (page.locator('[data-connections-open]').get_attribute('class') or ''), (site, 'missing selected project link')
                        if site == 'cs/playground':
                            assert page.locator('[data-demo-tab="sync"]').get_attribute('aria-selected') == 'true'
                            assert page.locator('[data-demo-panel="sync"]').is_visible()
                        if '/projects/' in site and site.split('/')[-1] in ('local-ai-hub', 'mutaui', 'home-infrastructure', 'woodbound', 'scan2cad', 'tiskov', 'shpotify', 'softwareinc-multiplayer', 'discord-bot'):
                            assert page.locator('[data-project-example]').count() == 1, (site, 'missing example')
                            assert page.locator('[data-project-deepdive] p').count() >= 2, (site, 'missing project detail')
                            assert page.locator('[data-project-architecture] li').count() >= 5, (site, 'missing detailed architecture')
                            assert page.locator('[data-project-architecture] svg').count() == 1, (site, 'missing visual')
                            diagram = page.locator('[data-project-architecture]')
                            assert diagram.locator('[data-architecture-node]').count() >= 5, (site, 'missing architecture nodes')
                            assert diagram.locator('[data-architecture-edge]').count() >= 4, (site, 'missing project relationships')
                            assert diagram.locator('[data-architecture-canvas] svg').get_attribute('aria-label'), (site, 'missing diagram description')
                            slug = site.split('/')[-1]
                            project_markers = {
                                'local-ai-hub': ('MCP', 'MCP'), 'mutaui': ('MutaUI', 'MutaUI'),
                                'home-infrastructure': ('Ansible', 'Ansible'), 'woodbound': ('Biomy', 'Biomes'),
                                'scan2cad': ('STEP', 'STEP'), 'tiskov': ('PrusaSlicer', 'PrusaSlicer'),
                                'shpotify': ('Místnost', 'Room'), 'softwareinc-multiplayer': ('C# jádro', 'C# core'),
                                'discord-bot': ('Údržba', 'Maintenance'),
                            }
                            expected = project_markers[slug][1 if site.startswith('en/') else 0]
                            assert expected in diagram.inner_text(), (site, 'diagram does not match project')
                            body_text = page.locator('body').inner_text().casefold()
                            for retired_name in ('wishflix', 'code reviews', 'tools extended', 'woodtycoon', 'vitkovske npc', 'filamentapp'):
                                assert retired_name not in body_text, (site, f'retired name leaked: {retired_name}')
                            source_image_slugs = ('tiskov', 'woodbound')
                            if slug in source_image_slugs:
                                figure = page.locator('[data-project-source-image]')
                                assert figure.count() == 1, (site, 'missing source image')
                                image = figure.locator('img')
                                src = image.get_attribute('src')
                                assert src.startswith('/assets/img/'), (site, 'source image must be a local asset')
                                assert image.get_attribute('alt'), (site, 'source image has no alt text')
                                caption = figure.locator('figcaption').inner_text()
                                provenance = {'tiskov': ('Projektová grafika', 'Project graphic'), 'woodbound': ('Vývojový záběr', 'Development capture')}
                                expected_provenance = provenance[slug][1 if site.startswith('en/') else 0]
                                assert expected_provenance in caption, (site, 'source image provenance is missing')
                                asset = ROOT / 'webroot' / src.lstrip('/')
                                assert asset.is_file() and asset.read_bytes().startswith(b'\x89PNG\r\n\x1a\n'), (site, 'source image is missing or not PNG')
                            contrast = diagram.locator('li span').first.evaluate("""el => {
                                const luminance = color => color.match(/\\d+/g).slice(0, 3).map(Number).map(v => {
                                    v /= 255; return v <= .04045 ? v / 12.92 : ((v + .055) / 1.055) ** 2.4;
                                }).reduce((sum, v, i) => sum + v * [ .2126, .7152, .0722 ][i], 0);
                                const a = luminance(getComputedStyle(el).color), b = luminance(getComputedStyle(el.parentElement).backgroundColor);
                                return (Math.max(a, b) + .05) / (Math.min(a, b) + .05);
                            }""")
                            assert contrast >= 4.5, (site, f'diagram legend contrast {contrast:.2f}:1')
                        if site == 'cs/' and width <= 390:
                            menu = page.locator('details.mobile-menu')
                            menu.locator('summary').click()
                            assert menu.get_attribute('open') is not None
                            assert menu.locator('nav a').count() >= 6
                        if site == 'cs/connections':
                            nodes = page.locator('g.connection-node[role="button"]')
                            assert nodes.count() == 9
                            if width <= 390:
                                canvas = page.locator('.connection-canvas-wrap')
                                assert canvas.evaluate('(e) => e.scrollWidth > e.clientWidth')
                                canvas.focus()
                                page.keyboard.press('ArrowRight')
                                page.wait_for_function("document.querySelector('.connection-canvas-wrap').scrollLeft > 0")
                            boxes = nodes.locator('rect').evaluate_all("rects => rects.map(r => ({x: +r.getAttribute('x'), y: +r.getAttribute('y'), w: +r.getAttribute('width'), h: +r.getAttribute('height')}))")
                            for i, a in enumerate(boxes):
                                for b in boxes[i + 1:]:
                                    assert a['x'] + a['w'] <= b['x'] or b['x'] + b['w'] <= a['x'] or a['y'] + a['h'] <= b['y'] or b['y'] + b['h'] <= a['y'], 'overlapping project nodes'
                            nodes.first.focus()
                            page.keyboard.press('Enter')
                            assert page.locator('[data-connections-name]').inner_text() == 'Local AI Hub'
                            assert 'podpora vývoje' in page.locator('[data-connections-links]').inner_text()
                            assert nodes.first.evaluate('(element) => document.activeElement === element'), 'connection focus lost on redraw'
                            page.locator('[data-connection-search]').fill('mutaui')
                            assert page.locator('g.connection-node.muted').count() > 0
                        if site == 'cs/homelab':
                            services = page.locator('[data-service-catalog]').inner_text()
                            for name in ('Pi-hole', 'Unbound', 'Jellyfin', 'BookStack', 'Semaphore', 'CUPS', 'Nginx'):
                                assert name in services
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
                            assert page.locator('[data-maker]').count() == 0
                            assert page.get_by_text('Projekty z dílny postupně doplním').is_visible()
                        if site == 'cs/open-source':
                            assert page.locator('[data-activity]').count() >= 10
                            assert page.locator('select[data-state] option[value=merged]').inner_text() == 'Sloučeno'
                            page.locator('[data-activity-inspect]').first.click()
                            assert 'Sloučeno' in page.locator('[data-activity-repo]').inner_text()
                            assert 'GitHubu' in page.locator('[data-activity-url]').inner_text()
                            page.locator('.dialog-close').click()
                            page.locator('select[data-kind]').select_option('issue')
                            assert page.locator('[data-activity]:not(.hidden)').count() >= 1
                            page.locator('[data-search]').fill('none-such-repo-zz')
                            assert page.locator('[data-empty]').is_visible()
                        if site == 'en/open-source':
                            assert page.locator('select[data-state] option[value=merged]').inner_text() == 'Merged'
                            page.locator('[data-activity-inspect]').first.click()
                            assert 'Merged' in page.locator('[data-activity-repo]').inner_text()
                            assert 'GitHub' in page.locator('[data-activity-url]').inner_text()
                            page.locator('.dialog-close').click()
                        if site == 'cs/now':
                            assert page.locator('.activity-body strong').count() == 0, 'empty repository descriptions must not duplicate the name'
                        if site == 'cs/journal':
                            assert page.locator('.timeline-item').count() >= 8
                            assert page.get_by_text('Jak se rodil Local AI Hub').is_visible()
                        if site == 'cs/projects':
                            project_text = page.locator('[data-project-browser]').inner_text().casefold()
                            for hidden in ('wishflix', 'code reviews', 'tools extended', 'woodtycoon', 'vítkovské', 'vytiskov'):
                                assert hidden not in project_text
                            for visible in ('woodbound', 'tiskov', 'discord bot'):
                                assert visible in project_text
                            assert page.locator('[data-project] .status').first.inner_text().casefold() == 'aktivní'
                            assert 'Infrastruktura' in page.locator('[data-filter]').inner_text()
                            page.locator('[data-search]').fill('hry a simulace')
                            assert page.locator('[data-project]:not(.hidden)').count() == 2, 'localized category search'
                            assert page.locator('[data-count]').inner_text() == '2 záznamy'
                            page.locator('[data-search]').fill('discord bot')
                            assert page.locator('[data-project]:not(.hidden)').count() == 1, 'accent-free Czech search'
                            assert page.locator('[data-count]').inner_text() == '1 záznam'
                            assert 'Discord bot' in page.locator('[data-project]:not(.hidden)').inner_text()
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
                            page.locator('[data-diff-before]').fill('one\ntwo\nthree')
                            page.locator('[data-diff-after]').fill('zero\none\nthree')
                            page.locator('[data-diff-run]').click()
                            diff = page.locator('[data-diff-result]').inner_text()
                            assert '+ zero' in diff and '- two' in diff
                            assert '+ one' not in diff and '- one' not in diff
                            page.locator('[data-diff-before]').fill('')
                            page.locator('[data-diff-after]').fill('<script>alert(1)</script>')
                            page.locator('[data-diff-run]').click()
                            assert '<script>' in page.locator('[data-diff-result]').inner_text()
                            assert page.locator('[data-diff-result] script').count() == 0
                            page.locator('[data-diff-after]').fill('\n'.join(['x'] * 201))
                            page.locator('[data-diff-run]').click()
                            assert '200 řádků' in page.locator('[data-diff-result]').inner_text()
                            for demo in ('ai', 'sync', 'world', 'cad', 'request'):
                                tab = page.locator(f'[data-demo-tab="{demo}"]')
                                panel = page.locator(f'[data-demo-panel="{demo}"]')
                                assert tab.get_attribute('aria-controls') == demo
                                assert panel.get_attribute('aria-labelledby') == f'tab-{demo}'
                                assert panel.get_attribute('tabindex') == '0'
                            page.locator('[data-demo-tab="ai"]').click()
                            before = page.locator('[data-ai-index]').inner_text()
                            page.locator('[data-ai-next]').click()
                            assert page.locator('[data-ai-index]').inner_text() != before
                            page.locator('[data-demo-tab="ai"]').focus()
                            page.keyboard.press('ArrowRight')
                            assert page.locator('[data-demo-tab="sync"]').get_attribute('aria-selected') == 'true', 'ArrowRight tab navigation'
                            page.keyboard.press('End')
                            assert page.locator('[data-demo-tab="request"]').get_attribute('aria-selected') == 'true', 'End tab navigation'
                            assert page.locator('[data-demo-tab="ai"]').get_attribute('tabindex') == '-1'
                            assert page.locator('[data-demo-tab="request"]').get_attribute('tabindex') == '0'
                            page.locator('[data-demo-tab="request"]').click()
                            assert page.locator('[data-request-response]').inner_text().startswith('HTTP 200')
                            assert 'Doména přijata' in page.locator('[data-request-steps]').inner_text()
                            page.locator('[data-request-route]').select_option('/admin/project')
                            page.locator('[data-request-method]').select_option('POST')
                            page.locator('[data-request-csrf]').uncheck()
                            assert page.locator('[data-request-response]').inner_text().startswith('HTTP 403')
                            page.locator('[data-request-csrf]').check()
                            assert page.locator('[data-request-response]').inner_text().startswith('HTTP 303')
                            # Exercise real browser File/ArrayBuffer STL parsing and reset.
                            page.locator('[data-demo-tab="cad"]').click()
                            canvas = page.locator('[data-cad-canvas]')
                            assert canvas.get_attribute('tabindex') == '0'
                            original = canvas.evaluate('(element) => element.toDataURL()')
                            canvas.focus()
                            page.keyboard.press('ArrowRight')
                            page.wait_for_function('(original) => document.querySelector("[data-cad-canvas]").toDataURL() !== original', arg=original)
                            assert canvas.evaluate('(element) => getComputedStyle(element).outlineOffset') == '-4px', 'canvas focus ring must remain inside clipped container'
                            ascii_stl = (
                                b'solid sample\nfacet normal 0 0 1\nouter loop\n'
                                b'vertex 0 0 0\nvertex 1 0 0\nvertex 0 1 0\n'
                                b'endloop\nendfacet\nendsolid sample\n'
                            )
                            page.locator('[data-cad-file]').set_input_files({
                                'name': 'triangle.stl', 'mimeType': 'model/stl', 'buffer': ascii_stl,
                            })
                            page.wait_for_function(
                                "() => document.querySelector('[data-cad-info]').textContent.includes('local STL sample')"
                            )
                            assert '1 displayed triangles' in page.locator('[data-cad-info]').inner_text()
                            page.locator('[data-cad-wire]').check()
                            page.locator('[data-cad-reset]').click()
                            assert 'sample geometry' in page.locator('[data-cad-info]').inner_text()
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
