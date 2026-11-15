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

ROOT=Path(__file__).resolve().parents[1]
PAGES=ROOT/'tmp/public-preview'

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
        if tag=='img' and not a.get('alt'): self.errors.append('image missing alternative text')
        if any(k.lower().startswith('on') for k in a): self.errors.append('inline event handler')

subprocess.run(['php',str(ROOT/'scripts/render-public-preview.php')],cwd=ROOT,check=True)
htmls=sorted(PAGES.glob('cs/**/*.html'))+sorted(PAGES.glob('en/**/*.html'))
errors=[]
if len(htmls)<40: errors.append('Too few generated pages')
for html in htmls:
    parser=Parse()
    content=html.read_text(encoding='utf-8')
    parser.feed(content)
    rel=html.relative_to(PAGES)
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

if errors:
    print('Rendered HTML audit failed:',*errors,sep='\n - ')
    sys.exit(1)
print(f'Rendered HTML audit passed: {len(htmls)} pages, internal links, assets and IDs')
