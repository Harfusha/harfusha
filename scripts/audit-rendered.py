#!/usr/bin/env python3
"""Inspect all built SSR pages for broken site links, duplicate IDs and unsafe HTML.
This complements, but does not replace, full browser or CakePHP runtime testing.
"""
from __future__ import annotations
from html.parser import HTMLParser
from pathlib import Path
from urllib.parse import urlparse, unquote
from xml.etree import ElementTree
import subprocess
import sys
import os
import re
import json

ROOT=Path(__file__).resolve().parents[1]
PAGES=Path(os.environ.get('HAFRUSHA_PREVIEW_DIR', str(ROOT/'tmp/public-preview')))

class Parse(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.links=[]
        self.ids=[]
        self.h1=0
        self.errors=[]
    def handle_starttag(self, tag, attrs):
        a=dict(attrs)
        if 'id' in a: self.ids.append(a['id'])
        if tag=='h1': self.h1+=1
        if tag in ('a','link','script','img'):
            value=a.get('href' if tag in ('a','link') else 'src')
            if value: self.links.append(value)
        if tag=='img':
            if not a.get('alt'): self.errors.append('image missing alternative text')
            if not a.get('width') or not a.get('height'): self.errors.append(f"image {a.get('src')} missing width or height")
        if any(k.lower().startswith('on') for k in a): self.errors.append('inline event handler')

subprocess.run(['php',str(ROOT/'scripts/render-public-preview.php')],cwd=ROOT,check=True)
htmls=sorted(PAGES.glob('cs/**/*.html'))+sorted(PAGES.glob('en/**/*.html'))
errors=[]
rendered_pages=[]
system_controller=(ROOT/'src/Controller/SystemController.php').read_text(encoding='utf-8')
for stale_sitemap_image in ('/assets/img/scale-studio.webp', '/assets/img/tiskov-bambu-lab-a1.webp'):
    if stale_sitemap_image in system_controller:
        errors.append(f'sitemap still advertises a non-evidence image: {stale_sitemap_image}')
if re.search(r"\$lastmods\[['\"][^'\"]+['\"]\]\s*=\s*['\"]\d{4}-\d{2}-\d{2}['\"]", system_controller):
    errors.append('sitemap contains a hard-coded last-modified date')
if len(htmls)<38: errors.append('Too few generated pages')
for html in htmls:
    parser=Parse()
    content=html.read_text(encoding='utf-8')
    parser.feed(content)
    rendered_pages.append((html,parser))
    rel=html.relative_to(PAGES)
    if rel in (Path('cs/projects/discord-bot/index.html'), Path('en/projects/discord-bot/index.html')):
        stale_copy = (
            'Témata v dostupném popisu', 'Topics in the available description',
            'Command names and permission rules are unavailable',
            'Názvy příkazů a pravidla oprávnění nejsou dostupné',
            'Katalogový záznam o Node.js botovi', 'A catalog record for a Node.js Discord bot',
            'Co uvádí katalog', 'What the catalog records',
            'Katalog uvádí aplikační příkazy', 'Catalog lists application commands',
        )
        for phrase in stale_copy:
            if phrase in content:
                errors.append(f'{rel}: stale source-unavailable filler remains: {phrase}')
        required_copy = (
            ('Ansible spravuje samostatný checkout', 'vyhrazeným neprivilegovaným účtem')
            if rel.parts[0] == 'cs' else
            ('Ansible manages a separate checkout', 'unprivileged account')
        )
        for phrase in required_copy:
            if phrase not in content:
                errors.append(f'{rel}: missing verified operations detail: {phrase}')
    if rel in (Path('cs/connections/index.html'), Path('en/connections/index.html'), Path('cs/projects/index.html'), Path('en/projects/index.html')):
        stale_status_labels = ('Záznam bez zdrojového kódu', 'Record without source code', 'záznam bez kódu', 'record without source')
        for phrase in stale_status_labels:
            if phrase in content:
                errors.append(f'{rel}: stale record filler remains: {phrase}')
    if rel in (Path('cs/projects/scan2cad/index.html'), Path('en/projects/scan2cad/index.html')):
        repeated_disclaimers = (
            'not a physical measurement or certification',
            'fyzické měření ani certifikaci',
            'validation has not run in this capture',
            'validace v tomto snímku neproběhla',
        )
        for phrase in repeated_disclaimers:
            if phrase in content:
                errors.append(f'{rel}: repeated screenshot disclaimer remains: {phrase}')
    if rel in (Path('cs/homelab/index.html'), Path('en/homelab/index.html')):
        match=re.search(r'<script[^>]+id="topology-data"[^>]*>(.*?)</script>',content,re.DOTALL)
        if not match:
            errors.append(f'{rel}: missing localized topology data')
        else:
            try:
                nodes=json.loads(match.group(1))
            except json.JSONDecodeError as exc:
                errors.append(f'{rel}: invalid topology data: {exc}')
                nodes=[]
            if rel.parts[0]=='cs':
                labels={node['name'] for node in nodes}
                labels.update(label for node in nodes for label in node['tech']+node['services'])
                required={'MikroTik router','NAS QNAP','Testovací server Proxmox','Tiskový uzel Pi Zero','Lokální zařízení','VPN brána','Směrování','Sdílené soubory','Zálohy','Snímky','Síťový tisk','Vývojářská stanice'}
                untranslated={'VPN gateway','Routing','File storage','Backups','Test VMs','Network printing','Dev workstation','Snapshots','Proxmox Lab','Pi Zero Print','Local Clients'}
                if not required <= labels:
                    errors.append(f'{rel}: missing Czech topology labels {sorted(required-labels)}')
                if untranslated & labels:
                    errors.append(f'{rel}: untranslated topology labels {sorted(untranslated & labels)}')
            else:
                labels={node['name'] for node in nodes}
                labels.update(label for node in nodes for label in node['tech']+node['services'])
                required={'MikroTik Router','QNAP NAS','Proxmox Lab','Pi Zero Print','Local Clients','VPN gateway','Routing','File storage','Backups','Test VMs','Network printing','Dev workstation','Snapshots'}
                if not required <= labels:
                    errors.append(f'{rel}: missing English topology labels {sorted(required-labels)}')
    actual_title = re.search(r'<title>(.*?)</title>', content, re.IGNORECASE | re.DOTALL)
    if actual_title is None or actual_title.group(1).strip() == 'Harfusha Lab':
        errors.append(f'{rel}: missing specific page title')
    meta_description = re.search(r'<meta name="description" content="([^"]*)"', content, re.IGNORECASE)
    if meta_description is None or meta_description.group(1).strip() == 'Offline view smoke preview':
        errors.append(f'{rel}: missing page-specific search description')
    elif len(rel.parts) == 4 and rel.parts[1] == 'projects':
        project_name = re.search(r'<h1[^>]*>(.*?)</h1>', content, re.IGNORECASE | re.DOTALL)
        expected_title = f'{project_name.group(1).strip()} | Harfusha Lab' if project_name else ''
        if actual_title.group(1).strip() != expected_title:
            errors.append(f'{rel}: expected project title {expected_title!r}')
    if rel in (Path('cs/index.html'), Path('en/index.html')):
        if 'home-network-story' in content:
            errors.append(f'{rel}: redundant home-network callout remains on the homepage')
        stale_home_headings = ('Programuju backendy v PHP a nástroje pro vývojáře', 'I build PHP backends and tools for developers')
        for phrase in stale_home_headings:
            if phrase in content:
                errors.append(f'{rel}: stale generic homepage headline remains: {phrase}')
        expected_home_heading = 'Navrhuju backendové systémy, nástroje pro vývojáře a automatizaci.' if rel.parts[0] == 'cs' else 'I build backend systems, developer tools and automation.'
        if expected_home_heading not in content:
            errors.append(f'{rel}: missing specific homepage headline: {expected_home_heading}')
    if rel in (Path('cs/projects/scan2cad/index.html'), Path('en/projects/scan2cad/index.html')) and 'data-project-source-image' in content:
        errors.append(f'{rel}: unverified Scan2CAD interface mockup remains presented as project evidence')
    if rel in (Path('cs/projects/tiskov/index.html'), Path('en/projects/tiskov/index.html')):
        if 'data-project-source-image' in content or 'tiskov-bambu-lab-a1' in content:
            errors.append(f'{rel}: concept graphic is presented as product evidence')
        if 'property="og:image"' in content or 'name="twitter:image"' in content:
            errors.append(f'{rel}: unverified proposal graphic is exposed as a social preview')
    if rel.parts[1:3] == ('projects', 'scan2cad') and ('class="feature-list"' in content or 'ARCHITECTURE &amp; TRADE-OFFS' in content or 'ARCHITEKTURA A KOMPROMISY' in content):
        errors.append(f'{rel}: repeated Scan2CAD workflow detail remains on the case-study page')
    if rel.parts[1:3] == ('projects', 'tiskov') and ('class="feature-list"' in content or 'ARCHITECTURE &amp; TRADE-OFFS' in content or 'ARCHITEKTURA A KOMPROMISY' in content):
        errors.append(f'{rel}: repeated Tiskov proposal detail remains on the case-study page')
    if rel in (Path('cs/maker/index.html'), Path('en/maker/index.html')):
        if '<ol' in content or 'maker-project-grid' not in content:
            errors.append(f'{rel}: separate 3D projects are still presented as a sequential workflow')
        expected_title = '3D skeny a příprava tisku | Harfusha' if rel.parts[0] == 'cs' else '3D Scanning and Print Preparation | Harfusha'
        if actual_title is None or actual_title.group(1).strip() != expected_title:
            errors.append(f'{rel}: expected maker page title {expected_title!r}')
    if parser.h1!=1: errors.append(f'{rel}: expected one H1, got {parser.h1}')
    if len(set(parser.ids))!=len(parser.ids): errors.append(f'{rel}: duplicated HTML IDs')
    if '<script>alert' in content: errors.append(f'{rel}: unescaped HTML')
    errors.extend(f'{rel}: {error}' for error in parser.errors)
    for href in parser.links:
        parsed=urlparse(href)
        if parsed.scheme or parsed.netloc or href.startswith(('mailto:','tel:','#','data:')): continue
        if not href.startswith('/'): continue
        # Public CakePHP JSON routes are verified by HTTP smoke, not by offline HTML rendering.
        if parsed.path in ('/api/v1/projects',): continue
        target=unquote(parsed.path)
        if '/..' in target or '\\' in target: errors.append(f'{rel}: unsafe internal URL {href}');continue
        f=PAGES/target.lstrip('/')
        if not (f.is_file() or (f/'index.html').is_file()):
            errors.append(f'{rel}: broken link or missing asset {href}')
# Check fragments only after every locale page and its IDs are known.
routes={}
for html, parser in rendered_pages:
    route='/'+html.relative_to(PAGES).as_posix()
    routes[route.removesuffix('/index.html').rstrip('/')]=parser
for html, parser in rendered_pages:
    current='/'+html.relative_to(PAGES).as_posix()
    current=current.removesuffix('/index.html').rstrip('/')
    for href in parser.links:
        parsed=urlparse(href)
        if not parsed.fragment or parsed.scheme or parsed.netloc: continue
        route=unquote(parsed.path).rstrip('/') if parsed.path else current
        target=routes.get(route)
        if target and unquote(parsed.fragment) not in target.ids:
            errors.append(f'{html.relative_to(PAGES)}: broken fragment {href}')
# Validate that RSS item permalinks actually resolve to visible journal IDs.
# Static previews are generated for both locales, so no external HTTP is needed.
for locale in ('cs', 'en'):
    journal = PAGES / locale / 'journal' / 'index.html'
    feed_path = PAGES / locale / 'feed.xml'
    page = Parse()
    page.feed(journal.read_text(encoding='utf-8'))
    anchors = set(page.ids)
    try:
        entries = ElementTree.parse(feed_path).findall('./channel/item')
    except (OSError, ElementTree.ParseError) as exc:
        errors.append(f'{locale}: invalid or missing RSS document: {exc}')
        continue
    if not entries:
        errors.append(f'{locale}: no RSS journal entries')
    for entry in entries:
        link = entry.findtext('link') or ''
        guid = entry.find('guid')
        url = urlparse(link)
        if url.path != f'/{locale}/journal' or not url.fragment or url.fragment not in anchors:
            errors.append(f'{locale}: RSS permalink lacks a matching journal anchor')
        if guid is None or guid.get('isPermaLink') != 'true' or guid.text != link:
            errors.append(f'{locale}: RSS GUID does not match its permanent article URL')

# Validate that every journal entry has date, project and primary source link
class JournalEntryChecker(HTMLParser):
    def __init__(self, loc):
        super().__init__()
        self.loc = loc
        self.in_entry = False
        self.current = {}
        self.entries = []
    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if tag == 'article' and 'data-journal-entry' in a:
            self.in_entry = True
            self.current = {'id': a.get('id'), 'project': a.get('data-project'), 'has_time': False, 'has_source': False}
        elif self.in_entry:
            if tag == 'time' and a.get('datetime'):
                self.current['has_time'] = True
            if tag == 'a' and ('target' in a or a.get('href', '').startswith(f'/{self.loc}/projects/')):
                self.current['has_source'] = True
    def handle_endtag(self, tag):
        if tag == 'article' and self.in_entry:
            self.entries.append(self.current)
            self.in_entry = False

for locale in ('cs', 'en'):
    checker = JournalEntryChecker(locale)
    checker.feed((PAGES / locale / 'journal' / 'index.html').read_text(encoding='utf-8'))
    if len(checker.entries) < 40:
        errors.append(f'{locale}: too few journal entries ({len(checker.entries)})')
    for e in checker.entries:
        if not e['project']:
            errors.append(f"{locale} journal entry {e['id']}: missing project")
        if not e['has_time']:
            errors.append(f"{locale} journal entry {e['id']}: missing date")
        if not e['has_source']:
            errors.append(f"{locale} journal entry {e['id']}: missing primary source link")

if errors:
    print('Rendered HTML audit failed:',*errors,sep='\n - ')
    sys.exit(1)
print(f'Rendered HTML audit passed: {len(htmls)} pages, internal links, assets and IDs')
