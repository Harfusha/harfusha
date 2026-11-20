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
import hashlib
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


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--screenshots', action='store_true')
    parser.add_argument('--screenshot-dir', default=str(ROOT / 'tmp' / 'browser-qa'), help='Output directory for isolated browser screenshots')
    parser.add_argument('--chromium', default=None, help='Path to system Chromium; otherwise use Playwright bundled browser')
    args = parser.parse_args()
    try:
        from playwright.sync_api import sync_playwright
    except ImportError as exc:
        raise SystemExit('Playwright is required: pip install playwright && python -m playwright install chromium') from exc
    subprocess.run(['php', 'scripts/render-public-preview.php'], cwd=ROOT, check=True)
    screenshots = Path(args.screenshot_dir)
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
                                  'cs/now', 'cs/playground', 'cs/projects/local-ai-hub', 'cs/maker', 'cs/open-source', 'cs/decisions')
                    if width in (320, 1365):
                        all_sites = [p.parent.relative_to(HTML).as_posix() for p in HTML.rglob('index.html')]
                        sites = sorted(site + '/' if site in ('cs', 'en') else site for site in all_sites)
                    else:
                        sites = core_sites
                    for site in sites:
                        path = HTML / site / 'index.html'
                        html = STYLES.sub('', SCRIPTS.sub('', path.read_text(encoding='utf-8')))
                        page.goto(path.resolve().as_uri(), wait_until='domcontentloaded')
                        page.set_content(html, wait_until='domcontentloaded')
                        page.add_style_tag(content=CSS)
                        page.add_script_tag(content=CORE)
                        page.add_script_tag(content=APP)
                        if page.evaluate('document.documentElement.scrollWidth > window.innerWidth'):
                            overflow = page.evaluate(r'''() => {
                                const root = document.documentElement;
                                const width = window.innerWidth;
                                const graph = document.querySelector('[data-connections-svg]');
                                const connectionChain = [];
                                for (let element = graph; element && element !== root; element = element.parentElement) {
                                    const style = getComputedStyle(element);
                                    const rect = element.getBoundingClientRect();
                                    connectionChain.push({
                                        tag: element.tagName.toLowerCase(),
                                        className: typeof element.className === 'string' ? element.className : '',
                                        left: Math.round(rect.left),
                                        width: Math.round(rect.width),
                                        scrollWidth: element.scrollWidth,
                                        clientWidth: element.clientWidth,
                                        overflowX: style.overflowX,
                                        minWidth: style.minWidth,
                                    });
                                }
                                return {
                                    document: {scrollWidth: root.scrollWidth, clientWidth: root.clientWidth, innerWidth: width},
                                    connectionChain,
                                    connectionChildren: [...(document.querySelector('.connection-layout')?.children ?? [])].map((element) => {
                                        const style = getComputedStyle(element);
                                        const rect = element.getBoundingClientRect();
                                        return {className: element.className, left: Math.round(rect.left), right: Math.round(rect.right), width: Math.round(rect.width), scrollWidth: element.scrollWidth, clientWidth: element.clientWidth, minWidth: style.minWidth, gridTemplateColumns: style.gridTemplateColumns, overflowX: style.overflowX};
                                    }),
                                    inspectorChildren: [...(document.querySelector('.connection-inspector')?.querySelectorAll('*') ?? [])].map((element) => {
                                        const style = getComputedStyle(element);
                                        const rect = element.getBoundingClientRect();
                                        return {tag: element.tagName.toLowerCase(), className: element.className, left: Math.round(rect.left), right: Math.round(rect.right), width: Math.round(rect.width), scrollWidth: element.scrollWidth, clientWidth: element.clientWidth, whiteSpace: style.whiteSpace, text: (element.innerText || '').trim().replace(/\s+/g, ' ').slice(0, 45)};
                                    }),
                                    offenders: [...document.querySelectorAll('body *')]
                                    .map((element) => {
                                        const rect = element.getBoundingClientRect();
                                        let clipped = false;
                                        for (let parent = element.parentElement; parent && parent !== root; parent = parent.parentElement) {
                                            if (['auto', 'scroll', 'hidden', 'clip'].includes(getComputedStyle(parent).overflowX)) {
                                                clipped = true;
                                                break;
                                            }
                                        }
                                        return {
                                            tag: element.tagName.toLowerCase(),
                                            id: element.id,
                                            className: typeof element.className === 'string' ? element.className : '',
                                            left: Math.round(rect.left),
                                            right: Math.round(rect.right),
                                            width: Math.round(rect.width),
                                            scrollWidth: element.scrollWidth,
                                            clipped,
                                            text: (element.innerText || '').trim().replace(/\\s+/g, ' ').slice(0, 48),
                                        };
                                    })
                                    .filter((element) => !element.clipped && (element.left < -1 || element.right > width + 1))
                                    .sort((a, b) => b.right - a.right)
                                    .slice(0, 12),
                                };
                            }''')
                            raise AssertionError((width, site, 'horizontal overflow', overflow))
                        assert page.locator('h1').count() == 1, (width, site, 'missing/extra H1')
                        if '/connections' in site:
                            assert page.locator('[data-map-project]').count() == 9, (site, 'missing project index cards')
                            themes = {card.get_attribute('data-theme') for card in page.locator('[data-map-project]').all()}
                            assert themes == {'operate', 'candidate', 'coordinate'}, (site, 'map must use the three thematic groups')
                            assert page.locator('[data-map-domain]').count() == 3, (site, 'the map must not group by technical repository category')
                            theme_counts = {theme: sum(card.get_attribute('data-theme') == theme for card in page.locator('[data-map-project]').all()) for theme in themes}
                            assert theme_counts == {'operate': 3, 'candidate': 3, 'coordinate': 3}, (site, 'each thematic group must show its three assigned projects')
                            input_label = 'Input / problem' if site.startswith('en/') else 'Vstup / problém'
                            result_label = 'Result / limit' if site.startswith('en/') else 'Výsledek / limit'
                            assert all(input_label in (card.text_content() or '') and result_label in card.inner_text() for card in page.locator('[data-map-project]').all()), (site, 'each project must retain its input and show its result')
                            first_reason = page.locator('.project-map-reason').first
                            assert first_reason.get_attribute('open') is None
                            first_reason.locator('summary').click()
                            assert first_reason.locator('.project-map-problem').is_visible()
                            assert all(card.is_visible() for card in page.locator('[data-map-project]').all()), (site, 'project map must show its grouped catalogue without an extra reveal step')
                            assert page.locator('.project-map-thread').count() == 0, (site, 'remove speculative cross-project comparison tracks')
                            assert page.locator('.project-map-route').count() == 0, (site, 'project index implies unsupported relationships')
                        if site == 'cs/playground':
                            assert page.locator('[data-playground]').is_visible()
                            assert page.locator('.playground-station-link').count() == 8
                            assert page.locator('[data-playground-station]').count() == 8
                            assert all(page.locator('[data-playground-station]').nth(i).is_visible() for i in range(8))
                            assert page.locator('.playground-project-index').count() == 0
                            assert page.locator('main a[href="#demo-stations"]').is_visible()
                            page.locator('[data-preprocess-path]').fill('src/worker.py')
                            page.locator('[data-preprocess-content]').fill('def answer(): return 42')
                            page.locator('[data-preprocess-run]').click()
                            assert 'změn' in page.locator('[data-preprocess-title]').inner_text().lower(), page.locator('[data-preprocess-title]').inner_text()
                            fingerprint = page.locator('[data-preprocess-hash]').inner_text()
                            assert fingerprint == hashlib.sha256(b'def answer(): return 42').hexdigest(), 'preprocessing must calculate the real sample SHA-256'
                            assert page.locator('[data-preprocess-steps] li').count() >= 2
                            page.locator('[data-preprocess-path]').fill('.git/config')
                            page.locator('[data-preprocess-run]').click()
                            assert 'vylouč' in page.locator('[data-preprocess-title]').inner_text().lower()
                            assert page.locator('[data-preprocess-hash]').inner_text() == ''
                            page.locator('[data-preprocess-reset]').click()
                            assert 'beze změny' in page.locator('[data-preprocess-title]').inner_text().lower()
                            page.locator('[data-sync-local]').fill('config.yml|v2\nREADME.md|v1')
                            page.locator('[data-sync-remote]').fill('config.yml|v3\nREADME.md|v1')
                            page.locator('[data-sync-preview]').click()
                            assert 'konflikt' in page.locator('[data-sync-title]').inner_text().lower()
                            assert page.locator('[data-sync-conflicts] li').count() == 1
                            page.locator('[data-scale-mm]').fill('100')
                            page.locator('[data-scale-pixels]').fill('500')
                            page.locator('[data-scale-probe]').fill('25')
                            assert '5.00 px/mm' in page.locator('[data-scale-result]').inner_text()
                            assert '5.00 mm' in page.locator('[data-scale-probe-result]').inner_text()
                            route_cell = page.locator('[data-route-cell="8"]')
                            route_cell.click()
                            assert route_cell.get_attribute('aria-pressed') == 'true', 'route editor must change the modeled obstacle grid'
                            assert 'Trasa existuje' in page.locator('[data-route-status]').inner_text()
                            page.locator('[data-queue-add="a"]').click()
                            assert page.locator('[data-queue-version]').inner_text() == '13'
                            page.locator('[data-queue-add="b"]').click()
                            assert 'odmítnuta' in page.locator('[data-queue-result]').inner_text().lower(), 'stale Shpotify update must be rejected'
                            page.locator('[data-outage-host]').select_option('print-pi')
                            assert 'tisk' in page.locator('[data-outage-title]').inner_text().lower()
                            page.locator('[data-authority-roster]').uncheck()
                            assert 'zamítnout' in page.locator('[data-authority-title]').inner_text().lower()
                            assert 'popsaného návrhu' in page.locator('#station-tiskov-queue').inner_text().lower()
                            assert '120 min' in page.locator('[data-tiskov-summary]').inner_text()
                            page.locator('[data-tiskov-workers]').evaluate("el => { el.value = '2'; el.dispatchEvent(new Event('input', {bubbles: true})); }")
                            assert '60 min' in page.locator('[data-tiskov-summary]').inner_text()
                            assert page.locator('[data-tiskov-queue] [data-job-state="running"]').count() == 2
                            assert page.locator('[data-tiskov-queue] [data-job-state="waiting"]').count() == 2
                        if '/projects/' in site and site.split('/')[-1] in ('local-ai-hub', 'mutaui', 'home-infrastructure', 'woodbound', 'scan2cad', 'tiskov', 'shpotify', 'softwareinc-multiplayer', 'discord-bot'):
                            slug = site.split('/')[-1]
                            assert page.locator('[data-project-example]').count() == 1, (site, 'missing example')
                            assert page.locator('[data-project-deepdive] p').count() >= 3, (site, 'missing project detail')
                            assert page.locator('[data-project-deepdive] .case-walkthrough-steps li').count() == 4, (site, 'missing concrete walkthrough')
                            assert page.locator('#walkthrough-title').inner_text().casefold() != 'co se stane krok za krokem'
                            assert page.locator('[data-project-architecture] li').count() >= 5, (site, 'missing detailed architecture')
                            diagram = page.locator('[data-project-architecture]')
                            assert diagram.count() == 1, (site, 'missing component guide')
                            assert diagram.locator('[data-architecture-node]').count() >= 5, (site, 'missing components')
                            edges = diagram.locator('[data-architecture-edge]').all()
                            assert len(edges) >= 4, (site, 'missing project relationships')
                            assert all(page.locator(edge.get_attribute('href')).count() == 1 for edge in edges)
                            slug = site.split('/')[-1]
                            assert page.locator('[data-project-exhibit]').count() == 0, (site, 'fake project-level interaction must stay removed')
                            assert page.locator('[data-project-story]').count() == 1, (site, 'missing project-specific experience')
                            alternatives = page.locator('[data-project-story] .project-alternatives a')
                            assert alternatives.count() >= 1 and page.locator('.alternative-date').count() == 1, (site, 'missing dated alternative research')
                            assert alternatives.first.get_attribute('href').startswith('https://'), (site, 'alternative needs an external source')
                            fit_headings = {
                                'local-ai-hub': ('KDY HUB DÁVÁ SMYSL', 'WHEN THE HUB EARNS ITS PLACE'),
                                'mutaui': ('KDY STAČÍ MUTAGEN CLI', 'WHEN MUTAGEN CLI IS ENOUGH'),
                                'home-infrastructure': ('KDY STAČÍ ANSIBLE', 'WHEN ANSIBLE IS ENOUGH'),
                                'woodbound': ('KDY STAČÍ HOTOVÁ HRA', 'WHEN AN EXISTING GAME IS ENOUGH'),
                                'scan2cad': ('KDY STAČÍ BĚŽNÝ CAD', 'WHEN STANDARD CAD IS ENOUGH'),
                                'tiskov': ('KDY STAČÍ TISKOVÁ SLUŽBA', 'WHEN A PRINT SERVICE IS ENOUGH'),
                                'shpotify': ('KDY STAČÍ SPOTIFY JAM', 'WHEN SPOTIFY JAM IS ENOUGH'),
                                'softwareinc-multiplayer': ('KDY STAČÍ HRA SAMA', 'WHEN THE GAME ITSELF IS ENOUGH'),
                                'discord-bot': ('KDY STAČÍ SPRÁVA SERVERU', 'WHEN SERVER MANAGEMENT IS ENOUGH'),
                            }
                            why_headings = {
                                'local-ai-hub': ('NEJDŘÍV ROZSAH, PAK ZDROJE', 'SCOPE FIRST, SOURCES SECOND'),
                                'mutaui': ('JEDNA ZMĚNA, TŘI STAVY', 'ONE CHANGE, THREE STATES'),
                                'home-infrastructure': ('KTERÁ SLUŽBA ZÁVISÍ NA HOSTU?', 'WHICH SERVICE DEPENDS ON THIS HOST?'),
                                'woodbound': ('SLEDUJ TOK MATERIÁLU', 'FOLLOW THE MATERIAL FLOW'),
                                'scan2cad': ('KDE KONČÍ JISTOTA MĚŘENÍ', 'WHERE MEASUREMENT CONFIDENCE ENDS'),
                                'tiskov': ('NÁVRH PRŮCHODU ZAKÁZKY', 'PROPOSED JOB FLOW'),
                                'shpotify': ('JAK SE MĚNÍ SPOLEČNÝ STAV', 'HOW SHARED STATE CHANGES'),
                                'softwareinc-multiplayer': ('KDO SMÍ POTVRDIT ZMĚNU?', 'WHO MAY CONFIRM A CHANGE?'),
                                'discord-bot': ('CO JE DOLOŽENO A CO CHYBÍ', 'WHAT IS DOCUMENTED AND WHAT IS MISSING'),
                            }
                            language_index = 1 if site.startswith('en/') else 0
                            assert page.locator('[data-project-story] .project-fit').inner_text().startswith(fit_headings[slug][language_index]), (site, 'project-specific alternative heading is missing')
                            assert page.locator('[data-project-story] .project-why-heading .eyebrow').inner_text() == why_headings[slug][language_index], (site, 'project-specific lead-in is missing')
                            story = page.locator('[data-project-story]')
                            assert story.get_attribute('data-project-story') == slug, (site, 'story identity mismatch')
                            story_layouts = {
                                'local-ai-hub': 'pipeline', 'mutaui': 'operator-console',
                                'home-infrastructure': 'topology', 'woodbound': 'world-route',
                                'scan2cad': 'measurement', 'tiskov': 'proposal',
                                'shpotify': 'shared-room', 'softwareinc-multiplayer': 'agreement',
                                'discord-bot': 'evidence-ledger',
                            }
                            assert story.locator('.project-why-grid').get_attribute('data-story-layout') == story_layouts[slug], (site, 'missing project-specific story layout')
                            if width > 800:
                                areas = story.locator('.project-why-grid').evaluate('(element) => getComputedStyle(element).gridTemplateAreas')
                                assert areas != 'none', (site, 'project story layout is not applied')
                            model_routes = {
                                'local-ai-hub': '#station-preprocessing', 'mutaui': '#station-sync-preview',
                                'home-infrastructure': '/homelab#network-model', 'woodbound': '#station-route-finder',
                                'scan2cad': '#station-scale-check', 'shpotify': '#station-queue-conflict',
                                'softwareinc-multiplayer': '#station-mod-authority', 'tiskov': '#station-tiskov-queue',
                            }
                            model_link = story.locator('.project-lab-link a')
                            if slug in model_routes:
                                assert model_link.count() == 1 and model_link.get_attribute('href').endswith(model_routes[slug]), (site, 'missing working model link')
                                if slug == 'tiskov':
                                    assert 'proposal simulation' in story.locator('.project-lab-link').inner_text().lower() if site.startswith('en/') else 'návrhová simulace' in story.locator('.project-lab-link').inner_text().lower()
                            else:
                                assert model_link.count() == 0, (site, 'unverified project must not imply a working model')
                            project_markers = {
                                'local-ai-hub': ('Code index', 'Code index'), 'mutaui': ('MutaUI', 'MutaUI'),
                                'home-infrastructure': ('Ansible', 'Ansible'), 'woodbound': ('biomy', 'biomes'),
                                'scan2cad': ('STEP', 'STEP'), 'tiskov': ('Slicer', 'Slicer'),
                                'shpotify': ('Místnost', 'Room'), 'softwareinc-multiplayer': ('C# doménové jádro', 'C# domain core'),
                                'discord-bot': ('Údržbová větev', 'Maintenance path'),
                            }
                            expected = project_markers[slug][1 if site.startswith('en/') else 0]
                            assert expected in diagram.inner_text(), (site, 'diagram does not match project')
                            body_text = page.locator('body').inner_text().casefold()
                            if slug == 'local-ai-hub':
                                retrieval_order = 'cheaper indexed routes' if site.startswith('en/') else 'levnějších indexovaných cestách'
                                assert 'adaptive context' in body_text and retrieval_order in body_text, (site, 'Local AI Hub retrieval order is inaccurate')
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
                            contrast = diagram.locator('[data-architecture-node] p').first.evaluate("""el => {
                                const luminance = color => color.match(/\\d+/g).slice(0, 3).map(Number).map(v => {
                                    v /= 255; return v <= .04045 ? v / 12.92 : ((v + .055) / 1.055) ** 2.4;
                                }).reduce((sum, v, i) => sum + v * [ .2126, .7152, .0722 ][i], 0);
                                const a = luminance(getComputedStyle(el).color), b = luminance(getComputedStyle(el.parentElement).backgroundColor);
                                return (Math.max(a, b) + .05) / (Math.min(a, b) + .05);
                            }""")
                            assert contrast >= 4.5, (site, f'diagram legend contrast {contrast:.2f}:1')
                        if site in ('cs/', 'en/'):
                            assert page.locator('body').evaluate('(el) => getComputedStyle(el).colorScheme') == 'dark'
                            headline = page.locator('.workbench-intro h1')
                            assert headline.evaluate('(el) => getComputedStyle(el).fontFamily').startswith('Inter,')
                            assert headline.evaluate('(el) => getComputedStyle(el).letterSpacing') == 'normal'
                            assert page.locator('.workbench-cover').get_attribute('href') == '#scale-quick'
                            assert (ROOT / 'webroot/assets/img/scale-studio.webp').is_file()
                            assert page.locator('.experiment-grid a').count() == 3
                            assert page.locator('.showcase-card').count() == 3
                            quick = page.locator('[data-quick-scale]')
                            assert '60.00 mm' in quick.locator('[data-scale-probe-result]').inner_text()
                            quick.locator('[data-scale-mm]').fill('45')
                            assert '90.00 mm' in quick.locator('[data-scale-probe-result]').inner_text()
                            quick.locator('[data-scale-mm]').fill('0')
                            assert not quick.locator('[data-scale-probe-result]').inner_text()
                        if site == 'cs/' and width <= 390:
                            menu = page.locator('details.mobile-menu')
                            menu.locator('summary').click()
                            assert menu.get_attribute('open') is not None
                            assert menu.locator('nav a').count() >= 6
                        if site == 'cs/connections':
                            catalogue = page.locator('.project-map-catalog')
                            assert catalogue.is_visible()
                            assert page.locator('.project-map-thread').count() == 0
                            assert page.locator('.project-map-story').count() == 0
                            cards = page.locator('[data-map-project]')
                            assert cards.count() == 9, 'project index must show all nine independent projects'
                            assert all(cards.nth(i).is_visible() for i in range(cards.count())), 'the catalogue must not hide projects behind a separate action'
                            assert page.locator('[data-map-dependency]').count() == 0, 'the map must not imply unsupported technical dependencies'
                            columns = page.locator('.project-map-list').first.evaluate('(element) => getComputedStyle(element).gridTemplateColumns.split(" ").length')
                            expected_columns = 1 if width <= 740 else 3
                            assert columns == expected_columns, (site, f'unbalanced map card columns at {width}px: {columns}')
                            search = page.locator('[data-map-search]')
                            search.fill('mutaui')
                            assert page.locator('[data-map-project]:visible').count() == 1
                            assert 'MutaUI' in page.locator('[data-map-project]:visible').inner_text()
                            search.fill('no matching project')
                            assert page.locator('[data-map-empty]').is_visible()
                            search.fill('')
                            page.locator('[data-map-category]').select_option('candidate')
                            assert page.locator('[data-map-project]:visible').count() == 3, 'thematic filter'
                            page.locator('[data-map-category]').select_option('all')
                            page.locator('[data-map-state]').select_option('prototype')
                            assert page.locator('[data-map-project]:visible').count() == 5
                            assert '5' in page.locator('[data-map-status]').inner_text()
                        if site == 'cs/homelab':
                            assert page.locator('[data-lab-view="services"]').get_attribute('aria-pressed') == 'true'
                            services = page.locator('[data-service-catalog]').inner_text()
                            for name in ('Pi-hole', 'Unbound', 'Jellyfin', 'BookStack', 'Semaphore', 'CUPS', 'Nginx', 'Discord bot'):
                                assert name in services
                            nodes = page.locator('button[data-lab-node-id]')
                            assert nodes.count() >= 7
                            assert '8 služeb' in nodes.filter(has_text='Raspberry Pi 5').get_attribute('aria-label')
                            assert '1 služba' in nodes.filter(has_text='Pi Zero Print').get_attribute('aria-label')
                            page.locator('[data-failure-mode]').check()
                            nodes.filter(has_text='MikroTik').first.click()
                            assert 'Simulace:' in page.locator('[data-lab-note]').inner_text()
                            page.locator('button[data-lab-node-id]').first.focus()
                            page.keyboard.press('Enter')
                            assert page.locator('button[data-lab-node-id]').first.evaluate('(element) => document.activeElement === element'), 'homelab focus lost on redraw'
                            page.locator('[data-lab-reset]').click()
                            assert 'Všechny' in page.locator('[data-lab-note]').inner_text()
                            page.locator('[data-lab-view="flows"]').click()
                            assert page.locator('[data-flow-toolbar]').is_visible()
                            assert page.locator('[data-flow-steps] li').count() >= 3
                            page.locator('[data-flow-select]').select_option('backup')
                            assert 'Zálohování' in page.locator('[data-flow-title]').inner_text()
                            assert 'dostupná' in page.locator('[data-flow-result]').inner_text()
                            page.locator('[data-failure-mode]').check()
                            page.locator('button[data-lab-node-id]').filter(has_text='QNAP').click()
                            assert 'přerušena' in page.locator('[data-flow-result]').inner_text()
                            page.locator('[data-lab-reset]').click()
                            assert 'dostupná' in page.locator('[data-flow-result]').inner_text()
                            for scenario in ('media', 'printing'):
                                page.locator(f'[data-lab-scenario="{scenario}"]').click()
                                assert page.locator('[data-flow-select]').input_value() == scenario
                                assert 'přerušena' in page.locator('[data-flow-result]').inner_text()
                                assert page.locator('[data-failure-mode]').is_checked()
                                page.locator('[data-lab-reset]').click()
                                assert 'dostupná' in page.locator('[data-flow-result]').inner_text()
                        if site == 'cs/maker':
                            assert page.locator('[data-maker]').count() == 0
                            assert page.locator('main a[href="/cs/projects/scan2cad"]').is_visible()
                            assert page.locator('main a[href="/cs/projects/tiskov"]').is_visible()
                        if site == 'cs/open-source':
                            assert page.locator('[data-activity]').count() >= 10
                            assert page.locator('.activity-dates').first.inner_text().startswith('Založeno')
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
                            assert page.locator('.activity-dates').first.inner_text().startswith('Created')
                            assert page.locator('select[data-state] option[value=merged]').inner_text() == 'Merged'
                            page.locator('[data-activity-inspect]').first.click()
                            assert 'Merged' in page.locator('[data-activity-repo]').inner_text()
                            assert 'GitHub' in page.locator('[data-activity-url]').inner_text()
                            page.locator('.dialog-close').click()
                        if site == 'cs/now':
                            assert page.locator('section[aria-labelledby="now-repositories-title"] .activity-body strong').count() == 0, 'empty repository descriptions must not duplicate the name'
                            history = page.locator('[data-now-activity] [data-activity]')
                            labels = history.locator('.activity-dates > span').all_inner_texts()
                            assert len(labels) == history.count() and all(label.startswith(('Založeno', 'Sloučeno', 'Uzavřeno')) for label in labels)
                            for pull in page.locator('[data-now-activity] [data-activity][data-kind="pr"][data-state="closed"]').all():
                                assert 'uzavřeno' in pull.locator('.status').inner_text().casefold()
                            notes = page.locator('[data-now-notes] .now-build-entry')
                            assert notes.count() >= 33, 'the timeline should include the full, broader curated history'
                            assert notes.first.locator('time').inner_text() == '2026-11-17', 'newest dated work should lead the timeline'
                            visible_notes = page.locator('[data-now-notes] .now-build-entry:visible')
                            assert visible_notes.count() == 4
                            assert visible_notes.first.locator('h3 a').get_attribute('href').startswith('/cs/journal#')
                            archive = page.locator('.now-note-archive')
                            assert archive.get_attribute('open') is None
                            archive.locator('summary').click()
                            assert visible_notes.count() == notes.count()
                            note_dates = {value.strip() for value in notes.locator('time').all_inner_texts()}
                            assert {'2024-08-22', '2024-08-29'} <= note_dates, 'older upstream milestones should remain visible'
                            assert notes.locator('a[href*="/pull/17827"]').count() == 1
                            assert notes.locator('a[href*="/pull/1022"]').count() == 1
                            year_filter = page.locator('[data-activity-browser] select[data-year]')
                            dated_rows = history.locator('time').evaluate_all('(times) => times.map(el => el.dateTime)')
                            assert dated_rows == sorted(dated_rows, reverse=True)
                            for option in year_filter.locator('option:not([value="all"])').all():
                                year = option.get_attribute('value')
                                assert int(option.inner_text().split('·')[-1]) == history.and_(page.locator(f'[data-year="{year}"]')).count()
                            if year_filter.locator('option').count() > 1:
                                year = year_filter.locator('option').nth(1).get_attribute('value')
                                expected = page.locator(f'[data-activity][data-year="{year}"]').count()
                                year_filter.select_option(year)
                                assert page.locator('[data-activity]:not(.hidden)').count() == expected
                                year_filter.select_option('all')
                            for issue in page.locator('[data-activity][data-kind="issue"][data-state="closed"]').all():
                                assert 'closed' in (issue.locator('.status').get_attribute('class') or '')
                                assert 'uzavřeno' in issue.locator('.status').inner_text().casefold()
                            for issue in page.locator('[data-activity][data-kind="issue"][data-state="completed"]').all():
                                assert 'completed' in (issue.locator('.status').get_attribute('class') or '')
                                assert issue.locator('.status').inner_text().casefold() == 'dokončeno'
                                assert issue.locator('.status').evaluate('(element) => getComputedStyle(element).color') == 'rgb(144, 231, 189)'
                        if site == 'en/now':
                            history = page.locator('[data-now-activity] [data-activity]')
                            labels = history.locator('.activity-dates > span').all_inner_texts()
                            assert len(labels) == history.count() and all(label.startswith(('Created', 'Merged', 'Closed')) for label in labels)
                            for pull in page.locator('[data-now-activity] [data-activity][data-kind="pr"][data-state="closed"]').all():
                                assert 'closed' in pull.locator('.status').inner_text().casefold()
                        if site == 'cs/journal':
                            assert page.locator('[data-journal-entry]').count() == 44
                            assert page.get_by_text('Jak se rodil Local AI Hub').is_visible()
                        if site.endswith('/journal'):
                            assert page.locator('[data-journal-year]').count() == 4
                            page.locator('[data-journal-year="2025"]').click()
                            assert page.locator('[data-journal-entry]:not([hidden])').count() == 2
                            assert page.locator('[data-journal-count]').inner_text().startswith('2 ')
                            page.locator('[data-journal-year="2024"]').click()
                            assert page.locator('[data-journal-entry]:not([hidden])').count() == 9
                            page.locator('[data-journal-year="all"]').click()
                            assert page.locator('[data-journal-entry]:not([hidden])').count() == 44
                            journal_search = page.locator('[data-journal-search]')
                            journal_search.fill('MutaUI F3')
                            assert 0 < page.locator('[data-journal-entry]:not([hidden])').count() < 44
                            page.locator('[data-journal-year="2024"]').click()
                            assert page.locator('[data-journal-empty]').is_visible()
                            journal_search.fill('')
                            assert page.locator('[data-journal-entry]:not([hidden])').count() == 9
                        if site == 'cs/projects':
                            project_text = page.locator('[data-project-browser]').inner_text().casefold()
                            for hidden in ('wishflix', 'code reviews', 'tools extended', 'woodtycoon', 'vítkovské', 'vytiskov'):
                                assert hidden not in project_text
                            for visible in ('woodbound', 'tiskov', 'discord bot'):
                                assert visible in project_text
                            assert page.locator('[data-project] .status').first.inner_text().casefold() == 'aktivní'
                            assert 'Infrastruktura' in page.locator('[data-filter]').inner_text()
                            assert page.locator('[data-category-shortcut]').count() == 0, 'duplicated category shortcuts obscure the project map'
                            page.locator('[data-filter]').select_option('3d & engineering')
                            assert page.locator('[data-project]:not(.hidden)').count() == 1
                            page.locator('[data-filter]').select_option('all')
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
                            page.locator('[data-filter-reset]').click()
                            assert page.locator('[data-search]').input_value() == ''
                            assert page.locator('[data-filter]').input_value() == 'all'
                            assert page.locator('[data-project]:not(.hidden)').count() == 9
                        if site.startswith(('cs/projects/', 'en/projects/')):
                            assert page.locator('.case-evidence-limit').is_visible()
                            assert page.locator('.case-followup a').count() == 1
                            locale = site.split('/', 1)[0]
                            slug = site.rsplit('/', 1)[-1]
                            model_paths = {
                                'local-ai-hub': '/playground#station-preprocessing',
                                'mutaui': '/playground#station-sync-preview',
                                'home-infrastructure': '/homelab#network-model',
                                'woodbound': '/playground#station-route-finder',
                                'scan2cad': '/playground#station-scale-check',
                                'tiskov': '/playground#station-tiskov-queue',
                                'shpotify': '/playground#station-queue-conflict',
                                'softwareinc-multiplayer': '/playground#station-mod-authority',
                            }
                            primary_link = page.locator('.project-detail-hero .detail-links .btn-primary')
                            assert primary_link.count() == 1
                            if slug in model_paths:
                                assert primary_link.get_attribute('href') == f'/{locale}{model_paths[slug]}', f'{slug} must open its real browser model'
                            else:
                                assert primary_link.get_attribute('href') == '#project-deepdive-title'
                        if site == 'cs/decisions':
                            cards = page.locator('[data-decision]')
                            assert cards.count() >= 7
                            chapters = page.locator('.decision-chapter')
                            assert chapters.count() == 2, 'the page should separate documented choices from unresolved prototypes'
                            assert page.locator('.decision-principle').count() == 1
                            assert 'hotov' in page.locator('.decision-principle').inner_text().lower()
                            cards.first.locator('summary').click()
                            assert cards.first.get_attribute('open') is not None
                            assert cards.first.locator('.decision-context').count() == 1, 'expanded card must not repeat its problem statement'
                            page.locator('[data-decision-status]').select_option('prototype')
                            assert page.locator('[data-decision]:not(.hidden)').count() >= 1
                            assert page.locator('.decision-chapter:visible').count() == 1
                            assert page.locator('.decision-chapter:visible').get_attribute('data-filter-group') == 'prototype'
                            page.locator('[data-search]').fill('no-such-architecture-decision')
                            assert page.locator('[data-empty]').is_visible()
                            assert page.locator('.decision-chapter:visible').count() == 0
                        if site == 'cs/playground':
                            assert page.locator('.playground-station-link').count() == 8
                            assert page.locator('[data-playground-station]').count() == 8
                            page.locator('[data-sync-preview]').click()
                            assert 'konflikt' in page.locator('[data-sync-title]').inner_text().casefold()
                            assert page.locator('[data-sync-conflicts] li').count() == 1
                            page.locator('[data-sync-local]').fill('config.yml|v2\nREADME.md|v1\ntheme.css|v1\nnotes.md|v1')
                            page.locator('[data-sync-remote]').fill('config.yml|v2\nREADME.md|v1\ntheme.css|v2')
                            page.locator('[data-sync-preview]').click()
                            assert 'konflikt' not in page.locator('[data-sync-title]').inner_text().casefold()
                            assert page.locator('[data-sync-operations] li').count() >= 1
                            page.locator('[data-queue-add="a"]').click()
                            page.locator('[data-queue-add="b"]').click()
                            assert page.locator('[data-queue-version]').inner_text() == '13'
                            assert 'odmítnuta' in page.locator('[data-queue-result]').inner_text().casefold()
                        if args.screenshots and width == 1365 and site == 'cs/':
                            page.screenshot(path=str(screenshots / 'readme-home.png'), full_page=False)
                        if args.screenshots and site == 'cs/projects/local-ai-hub' and width in (390, 1365):
                            page.add_style_tag(content='.site-header{display:none!important}')
                            name = 'readme-local-ai-hub-mobile.png' if width == 390 else 'readme-local-ai-hub.png'
                            target = '[data-project-deepdive]'
                            page.locator(target).screenshot(path=str(screenshots / name))
                        if args.screenshots and site.startswith('cs/projects/') and width == 1365 and page.locator('[data-project-deepdive]').count():
                            slug = site.split('/')[-1]
                            page.add_style_tag(content='.site-header{display:none!important}')
                            page.locator('[data-project-story]').screenshot(path=str(screenshots / f'project-story-{slug}.png'))
                        if args.screenshots and site == 'cs/playground' and width == 1365:
                            page.locator('[data-preprocess-reset]').click()
                            page.locator('[data-preprocess-run]').click()
                            page.locator('[data-sync-base]').fill('config.yml|v1\nREADME.md|v1\ntheme.css|v1')
                            page.locator('[data-sync-local]').fill('config.yml|v2\nREADME.md|v1\ntheme.css|v1\nnotes.md|v1')
                            page.locator('[data-sync-remote]').fill('config.yml|v3\nREADME.md|v1\ntheme.css|v2')
                            page.locator('[data-sync-preview]').click()
                            page.locator('[data-queue-reset]').click()
                            page.locator('[data-outage-host]').select_option('main-pi')
                            page.locator('[data-authority-roster]').check()
                            page.add_style_tag(content='.site-header{display:none!important}')
                            page.screenshot(path=str(screenshots / 'readme-playground.png'), full_page=True)
                        if args.screenshots and site == 'cs/connections' and width == 1365:
                            page.locator('.project-map-catalog').evaluate('(element) => { element.open = true; }')
                            page.locator('[data-map-search]').fill('')
                            page.locator('[data-map-category]').select_option('all')
                            page.locator('[data-map-state]').select_option('all')
                            page.locator('[data-map-search]').evaluate('(element) => element.blur()')
                            page.add_style_tag(content='.site-header{display:none!important}')
                            page.screenshot(path=str(screenshots / 'readme-project-map.png'), full_page=True)
                        if args.screenshots and site == 'cs/homelab' and width == 1365:
                            page.locator('[data-lab-reset]').click()
                            page.locator('[data-lab-view="services"]').click()
                            page.locator('button[data-lab-node-id]').filter(has_text='Raspberry Pi 5').click()
                            page.add_style_tag(content='.site-header{display:none!important}')
                            page.locator('.lab-shell').screenshot(path=str(screenshots / 'readme-homelab.png'))
                            page.locator('[data-service-catalog]').screenshot(path=str(screenshots / 'readme-homelab-services.png'))
                        if args.screenshots and site == 'cs/decisions' and width == 1365:
                            page.locator('[data-search]').fill('')
                            page.locator('[data-search]').evaluate('(element) => element.blur()')
                            page.locator('[data-decision-status]').select_option('all')
                            page.locator('.decision-card').first.evaluate('(element) => element.open = true')
                            page.add_style_tag(content='.site-header{display:none!important}')
                            page.screenshot(path=str(screenshots / 'readme-decisions.png'), full_page=True)
                        assert not errors, (width, site, errors)
                        checked += 1
                finally:
                    page.close()
        finally:
            browser.close()
    print(f'Browser DOM smoke passed: {checked} real Chromium page/viewport checks, JS runtime, responsive and interactions.')


if __name__ == '__main__':
    main()
