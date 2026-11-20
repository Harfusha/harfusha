# Redesign Harfusha — Inženýrská hloubka a profesionální dev styl (Směr A)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Cíl:** Zásadně pozvednout portfolio z mechanického, defenzivního a „levně“ působícího webu na úroveň sebevědomého, technicky hlubokého a vizuálně prvotřídního inženýrského portfolia seniorního backend/systémového vývojáře.

**Vize:** Web ve stylu špičkových dev portfolií (Linear, Stripe docs, Vercel) – uhlíková/grafitová paleta, čistá typografie, kódové ukázky a diffy, systémová architektonická schémata a skutečné technické případové studie bez byrokratických omluv.

---

## 1. Analýza současných nedostatků a cílový stav

| Oblast | Současný stav (problém) | Cílový stav (řešení) |
| :--- | :--- | :--- |
| **Titulka (Hero)** | Suchý slogan *„Změň vstup. Sleduj výsledek.“* + triviální přepočet pixelů na milimetry u posuvného měřítka. Působí jako školní kvíz. | **Adam Halfar — Backend & Systems Engineer**. Sebevědomý claim o stavbě spolehlivých backendů, lokálního AI toolingu a správě systémů. Hned pod tím 3 nosné vlajkové projekty s reálnou architekturou. |
| **Open Source** | Suchý seznam 14 titulků bez kontextu. Tlačítko „Detail“ otevře dialog, který jen zopakuje název a odkáže na GitHub. | **Vitrína klíčových upstream zásahů**: 4–5 velkých PRs (CakePHP ORM PHPStan mixin, DebugKit komprese paměti, Rector AST) s popisem problému, řešením v kódu a kódovým diffem. |
| **Textace a tón** | Ustrašené právní disclaimery na každém rohu (*„nepředstírá spuštění“*, *„výpočet stojí“*, *„není to důkaz“*). | Přímý, technicky přesný inženýrský jazyk. Omezení jsou popsaná věcně jako architektonické parametry a trade-offy, ne jako omluvy. |
| **Projekty** | Šablonovitý text rozdělený na totožné odrážky bez technické hloubky a bez reálného kódu. | Hloubkové technické studie klíčových systémů: diagramy vrstev, protokoly, sockety, reálné úryvky kódu a řešení souběhu/konfliktů. |
| **Diagramy** | Abstraktní SVG s kroužky a tenkými čarami, které působí jako nucená školní grafika. | Profesionální systémová schémata (vrstvy: UI, Daemon, Storage, OS; uvedené protokoly a porty jako IPC, SQLite WAL, WireGuard UDP). |
| **Playground** | Vstupní textarey s pipe-oddělovači (`config.yml|v1`) a amatérskými formuláři. | Systémový workbench: předpřipravené inženýrské scénáře na kliknutí, vizuální diffy a terminálové výstupy. |
| **Vizuál & Téma** | Lacině modrý podklad (`#0d1019`, `#18223b`) s těžkými šedými rámy. | Moderní grafitová a uhelná paleta (`#090a0f`, `#12131a`, jemné linky `rgba(255,255,255,0.08)`, monospace akcenty). |

---

## 2. Plán úkolů (Tasks)

### Task 1: Nový designový systém (Grafitová/uhelná paleta, typografie a terminálový styl)
- [ ] Upravit barevnou paletu v `webroot/assets/css/portfolio.css`:
  - Pozadí: hluboká černá `#090a0f`, povrchy `#11131a` a `#171924`.
  - Hranice: jemné `rgba(255,255,255,0.08)` a `rgba(255,255,255,0.14)`.
  - Akcenty: technologická modrá/fialová (`#38bdf8`, `#818cf8`) a smaragdová zelená (`#10b981`).
- [ ] Typografie: Zpřesnit hierarchii, přidat monospace (`SFMono-Regular, Consolas, monospace`) pro kód, porty, příkazy a technické štítky.
- [ ] Vytvořit komponenty pro kódové bloky (`.code-snippet`, `.code-diff`) a systémové badge.
- [ ] Ověření: `python scripts/audit-a11y.py` (WCAG 2.1 AA kontrast ve všech stavech).

### Task 2: Redesign Titulní stránky (`templates/Pages/home.php`)
- [ ] Odstranit sekci se školním posuvným měřítkem a kalkulačkou pixelů z titulky.
- [ ] Vytvořit nový, autoritativní Hero:
  - Jméno, role (**Backend & Systems Developer**) a přesný profesní profil.
  - Rychlé metriky: 60+ sloučených PRs v core frameworcích, 120+ open-source aktivit, zero-downtime deployment, homelab infrastruktura.
- [ ] Vytvořit sekci **Vlajkové projekty (Flagship Engineering Projects)**:
  - **Local AI Hub**: Architektura MCP serveru pro kódovací agenty a lokální indexování.
  - **MutaUI**: Desktopový správce synchronizace a port-forwardingu nad Mutagen CLI.
  - **Home Infrastructure as Code**: GitOps homelab s automatizací přes Ansible a WireGuard.
- [ ] Integrovat přehled nejnovějších technických zápisků a open-source příspěvků.
- [ ] Ověření: `python scripts/audit-rendered.py` a Chromium smoke test.

### Task 3: Open Source jako skutečné technické trofeje (`open_source.php` & `data/contributions.json`)
- [ ] Doplnit do `data/contributions.json` strukturované detaily pro 4–5 klíčových upstream PR:
  - `cakephp/cakephp #17827` (PHPStan generika a mixin pro ORM asociace).
  - `cakephp/debug_kit #1022` (Optimalizace SQLite paměti a komprese panelů).
  - `rectorphp/rector-src #6201` (Optimalizace AST pravidel v Code Quality).
  - `cakephp/chronos #460` (Typová invariance v neměnném datovém balíku).
- [ ] Přepracovat `templates/Pages/open_source.php`:
  - Horní sekce: **Vlajkové příspěvky (Featured Contributions)** – kódové karty s popisem problému, řešením a vizuálním diffem kódu před/po.
  - Spodní sekce: Čistý, strukturovaný přehled všech 120+ příspěvků s filtry (Frameworky, Nástroje, Issues) a přímými odkazy na GitHub.
  - Odstranit prázdný dialog, který neměl žádný obsah.
- [ ] Ověření: Test zobrazení diffů, přístupnost a odkazová validace.

### Task 4: Hloubkové inženýrské případové studie (`project.php` & `data/projects.json`)
- [ ] Přepsat texty v `data/projects.json` a `templates/element/portfolio_case_reading.php`:
  - Odstranit defenzivní disclaimery a nahradit je architektonickými fakty.
  - **MutaUI**: Popsat integraci s Mutagen daemonem, Qt/PySide6 architekturu, řešení obousměrných konfliktů.
  - **Local AI Hub**: Popsat MCP protokol, asynchronní token economizer, SQLite transakční integritu.
  - **Home Infrastructure**: Popsat Ansible playbooky, WireGuard mesh a orchestraci služeb.
  - R&D koncepty (Woodbound, Scan2CAD, Shpotify, Tiskov): Formulovat jako algoritmické a matematické výzvy (A* pathfinding, fotogrammetrie, optimistické zamykání).
- [ ] Přidat do šablony projektu bloky pro **Architekturu, Kódové ukázky a Systémové diagramy**.
- [ ] Ověření: `python scripts/audit-rendered.py` pro všech 9 detailů projektů v CS i EN.

### Task 5: Profesionální systémové diagramy
- [ ] Překreslit diagram v `templates/Pages/homelab.php`:
  - Místo kroužků a tenkých čar vytvořit přehledné schéma síťových vrstev: Brána (RouterOS) -> Klastr služeb (Raspberry Pi, systemd) -> Virtualizace (Proxmox) -> Úložiště a zálohy (QNAP SMB3, Restic).
  - Zobrazit reálné protokoly a porty (DNS UDP/TCP 53, WireGuard UDP 51820, HTTPS 443, SSH).
- [ ] Zpřesnit architektonické schéma Local AI Hubu a MutaUI.
- [ ] Ověření: Responzivita SVG na mobilu (320 px) i desktopu (1365 px).

### Task 6: Modernizace Playgroundu (`playground.php` & `lab-core.js`)
- [ ] Odstranit surové pipe-oddělovače v textarey u synchronizace.
- [ ] Přidat klikací presety (např. *„Souběžná editace bez konfliktu“*, *„Skutečný konflikt v configu“*, *„Jednosměrné přepsání“*).
- [ ] Výstupy stylizovat jako terminálové logy a JSON struktury místo holého textu.
- [ ] STL viewer ponechat jako čistý 3D prvek pro 3D tisk a modely.
- [ ] Ověření: `node tests/Client/lab-core.test.cjs` (15/15 PASS).

### Task 7: Finální testovací sada, Git commit, nový produkční ZIP a nasazení
- [ ] Spustit celou testovací sadu:
  - `composer validate --strict` & `composer audit --locked`
  - `php tests/standalone.php`
  - `node tests/Client/lab-core.test.cjs`
  - Docker PHP 8.5: `phpcs`, `phpstan max`, `phpunit`
  - `python scripts/audit-rendered.py` & `python scripts/audit-a11y.py`
  - `python tests/Browser/smoke.py --screenshots`
  - `python scripts/review-public-content.py --approve --reviewer "Adam Halfar"` & `--verify`
- [ ] Git commit s popisem redesignu a `git push origin master`.
- [ ] Sestavit nový produkční ZIP (`python scripts/build-candidate.py`).
- [ ] Nasadit na ostrou produkci `adam-halfar.cz` přes ověřený FTPS skript se zálohou a ověřit živý web.
