# Adam Halfar

**PHP Backend Developer · Open Source Contributor · Homelab & Maker**

Jsem backendový vývojář z Česka. Stavím aplikace, které mají být **typově bezpečné, rychlé, udržovatelné a dobře otestované**. Baví mě rozumět věcem do hloubky — od návrhu API a statické analýzy přes infrastrukturu až po fyzický výrobek vytištěný na 3D tiskárně.

*I'm a Czech backend developer focused on reliable PHP software, open-source contributions, local AI, self-hosted infrastructure and practical maker projects.*

[Projekty](#co-stavím--what-im-building) · [Open source](#open-source) · [Domácí laboratoř](#mimo-editor--beyond-the-editor) · [Tento repozitář](#o-tomto-repozitáři--about-this-repository)

---

## Co stavím / What I'm building

| Projekt | Co řeší | Odkaz |
| --- | --- | --- |
| **Local AI Hub** | Lokální AI nástroje, kontext repozitářů a integrace s vývojářskými agenty. Důraz na soukromí a kontrolu nad tím, kdy je potřeba cloud. | [Zdrojový kód](https://github.com/Harfusha/local-ai-hub) |
| **MutaUI** | Desktopové grafické rozhraní k Mutagenu: přehled a správa synchronizačních relací i forwardingu. | [Zdrojový kód](https://github.com/Harfusha/MutaUI) |
| **Harfusha Lab** | Interaktivní osobní technická dílna: projekty, bezpečná simulace domácí sítě, architektonická rozhodnutí, 3D modely a příspěvky do open source. Backend v CakePHP 5 / PHP 8.5. | [Tento repozitář](https://github.com/Harfusha/harfusha) |
| **Scan2CAD Studio** | Experimenty s rekonstrukcí 3D skenů a převodem mesh geometrie do CAD workflow. | *Soukromý vývojový projekt* |
| **Home Infrastructure** | Automatizace a správa domácích Linux služeb, sítě, záloh a virtuálních strojů pomocí Infrastructure as Code. | *Soukromá infrastruktura* |

Experimentuji také s dalšími aplikacemi a herními prototypy. Ne každý projekt je veřejný nebo hotový; raději rozlišuji **nápad, prototyp a funkční výsledek**.

## Jak přemýšlím o vývoji / Engineering principles

- **Type safety first:** moderní PHP, CakePHP, PHPStan na maximální úrovni, generika a přesně vymezené datové kontrakty.
- **Kvalita je součást návrhu:** PHPUnit, regresní testy, statické kontroly, CI a čitelný kód.
- **Bezpečnost a výkon:** validace na hranicích systému, minimální oprávnění, rozumné využívání prostředků a měření místo odhadů.
- **Pragmatická architektura:** žádná zbytečná komplexita; nástroje vybírám podle problému, ne podle trendu.
- **Dobrá použitelnost:** i technicky složité nástroje by měly být srozumitelné a dobře zdokumentované.

**Technologie, se kterými pracuji:** PHP 8+, CakePHP 5, MySQL, PHPStan, PHPUnit, GitHub Actions, Python, Linux, Ansible, Docker, Raspberry Pi a lokální AI modely.

## Open source

Nejen vytvářím vlastní repozitáře, ale také přispívám do projektů, které používám. Zajímají mě zejména typové informace, statická analýza, diagnostika a efektivita.

Vybrané upstream změny:

- [CakePHP #17827](https://github.com/cakephp/cakephp/pull/17827) — typové anotace asociací a podpora PHPStan.
- [CakePHP #18957](https://github.com/cakephp/cakephp/pull/18957) — práce s typovaným TableLocatorem.
- [DebugKit #1022](https://github.com/cakephp/debug_kit/pull/1022) — komprese panelových dat a optimalizace.
- [Další veřejné pull requesty](https://github.com/pulls?q=is%3Apr+author%3AHarfusha) — včetně PHP ekosystému a vývojářských nástrojů.

## Mimo editor / Beyond the editor

Velkou část věcí zkouším na vlastní infrastruktuře. Provozuji domácí služby, experimentuji s **Linuxem, Raspberry Pi, NASem, virtualizací a síťovými technologiemi** a zkoumám, jak nasazovat lokální AI smysluplně i na běžném hardwaru.

K tomu patří **3D tisk a CAD**: od drobných praktických součástí přes návrh mechanických dílů až po experimenty se skenováním a rekonstrukcí geometrie.

Právě propojení softwaru, hardwaru a reálných problémů mě baví nejvíc.

## O tomto repozitáři / About this repository

Tento repozitář má dvě role:

1. **Tento README je můj GitHub profil** — představení toho, co dělám a co mě zajímá.
2. **Zdrojový kód Harfusha Labu** — rozpracované interaktivní portfolio postavené na CakePHP 5 a PHP 8.5, určené pro PHP/MySQL hosting.

<img src="docs/screenshots/desktop-home.webp" width="800" alt="Ukázka rozpracovaného interaktivního portfolia Harfusha Lab">

*Ukázka aplikace ve vývoji; nejde o odkaz na již nasazený web.*

**Technická dokumentace aplikace:** [PROJECT-README.md](docs/PROJECT-README.md) · [Český start](START-CZ.md) · [Architektura](docs/ARCHITECTURE.md) · [Plán a kritéria dokončení](docs/MASTER_PLAN.md).

> **Stav:** Zdrojový projekt je dostupný, ale produkční nasazení zatím není ověřené. Pro úspěšné vydání musí projít instalace Composer závislostí, PHPStan, integrační testy CakePHP a MySQL i testy nasazení. [Podrobnosti](docs/PROJECT-README.md#quality-gates).

---

### In English

I'm a backend engineer from Czechia. My primary stack is **PHP and CakePHP**, with a strong interest in strict static analysis, testing, security and efficient architecture. I contribute upstream to projects such as CakePHP and DebugKit, build developer tools like [Local AI Hub](https://github.com/Harfusha/local-ai-hub) and [MutaUI](https://github.com/Harfusha/MutaUI), and run my own homelab. Outside software, I enjoy 3D printing, CAD and practical engineering experiments.

**Explore my work through the repositories and technical case studies, not just a list of badges.**
