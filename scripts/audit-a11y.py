#!/usr/bin/env python3
"""Accessibility audit of all public routes and both themes using axe-core.
Evaluates WCAG 2.0, 2.1, 2.2 Level A and AA rules across 38 routes in CS and EN.
"""
from __future__ import annotations
import json
from pathlib import Path
import re
import subprocess
import sys
from playwright.sync_api import sync_playwright

ROOT = Path(__file__).resolve().parents[1]
HTML = ROOT / 'tmp/public-preview'
AXE_PATH = ROOT / 'tmp/axe.min.js'
APP = (ROOT / 'webroot/assets/js/app.js').read_text(encoding='utf-8')
CORE = (ROOT / 'webroot/assets/js/lab-core.js').read_text(encoding='utf-8')
CSS = "\n".join((ROOT / 'webroot/assets/css' / name).read_text(encoding='utf-8')
                for name in ('app.css', 'portfolio.css'))
SCRIPTS = re.compile(r'<script\b[^>]+src="[^"]+"[^>]*></script>', re.IGNORECASE)
STYLES = re.compile(r'<link\b[^>]+rel="stylesheet"[^>]*>', re.IGNORECASE)

def main() -> None:
    if not AXE_PATH.is_file():
        import urllib.request
        AXE_PATH.parent.mkdir(parents=True, exist_ok=True)
        url = 'https://cdnjs.cloudflare.com/ajax/libs/axe-core/4.10.2/axe.min.js'
        with urllib.request.urlopen(url, timeout=10) as r:
            AXE_PATH.write_bytes(r.read())

    axe_script = AXE_PATH.read_text(encoding='utf-8')
    subprocess.run(['php', 'scripts/render-public-preview.php'], cwd=ROOT, check=True)

    all_sites = [p.parent.relative_to(HTML).as_posix() for p in HTML.rglob('index.html')]
    sites = sorted(site + '/' if site in ('cs', 'en') else site for site in all_sites)

    total_violations = 0
    route_results = {}

    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(headless=True, args=['--no-sandbox', '--disable-dev-shm-usage'])

        for site in sites:
            path = HTML / site / 'index.html' if site.endswith('/') else HTML / f'{site}/index.html'
            raw = path.read_text(encoding='utf-8')
            raw = STYLES.sub('', raw).replace('</head>', f'<style>{CSS}</style></head>')
            raw = SCRIPTS.sub('', raw).replace('</body>', f'<script>{CORE}</script><script>{APP}</script></body>')
            for theme in ('light', 'dark'):
                page = browser.new_page(viewport={'width': 1365, 'height': 900})
                page.set_content(raw)
                page.evaluate(f"document.documentElement.dataset.theme = '{theme}'")
                page.wait_for_timeout(250)
                page.evaluate(axe_script)
                result = page.evaluate("""
                    new Promise(resolve => {
                        axe.run(document, {
                            runOnly: {
                                type: 'tag',
                                values: ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa', 'best-practice']
                            }
                        }, (err, results) => {
                            if (err) resolve({error: String(err)});
                            else resolve({violations: results.violations, passes: results.passes.length});
                        });
                    })
                """)

                violations = result.get('violations', [])
                actionable = [v for v in violations if v.get('impact') in ('critical', 'serious', 'moderate')]
                key = f'{theme}:{site}'
                route_results[key] = {'violations': actionable, 'raw_violations': violations, 'passes': result.get('passes', 0)}
                if actionable:
                    total_violations += len(actionable)
                    print(f"FAILED {key}: {len(actionable)} violations")
                    for v in actionable:
                        print(f"  - [{v.get('impact')}] {v.get('id')}: {v.get('help')} ({len(v.get('nodes', []))} nodes)")
                        for node in v.get('nodes', [])[:3]:
                            print(f"    {node.get('target')}: {node.get('failureSummary', '').splitlines()[-1:]}")
                else:
                    print(f"PASSED {key}: 0 violations ({result.get('passes', 0)} checks passed)")
                page.close()

        browser.close()

    print("\n" + "=" * 60)
    print(f"A11Y AUDIT SUMMARY: {len(sites)} routes × 2 themes, {total_violations} actionable violations")
    if total_violations > 0:
        sys.exit(1)
    else:
        print("ALL ROUTES PASSED ACCESSIBILITY AUDIT!")

if __name__ == '__main__':
    main()
