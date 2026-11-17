# Osobitější Harfusha Implementation Plan

> **For agentic workers:** Use superpowers:executing-plans to implement and verify each task.

**Goal:** Proměnit portfolio v osobitý, názorný a důvěryhodný web.

**Architecture:** Zachovat CakePHP a CZ/EN. Zachovat jen interakce, které mění skutečný model nebo data. Přestavět cestu návštěvníka, projektové případové studie i historické sekce podle ověřených podkladů.

**Tech Stack:** PHP, CakePHP, CSS, JavaScript.

**Spec:** `docs/DESIGN-OSOBITEJSI-WEB.md` (schváleno uživatelem 2026-11-16).

## Global Constraints

- Zachovat stávající nesouvisející úpravy, soukromí, přesné stavy projektů a označení simulací.
- Čeština a angličtina, mobilní zobrazení, klávesnice a omezení pohybu.
- Před publikací dodržet projektovou release bránu pro veřejný obsah.

## Review Focus

- Dlouhé české i anglické texty se musí vejít na malé obrazovky.
- Odkazy a interaktivní prvky zůstanou ovladatelné klávesnicí.
- Demo, návrh a skutečný provoz musí být rozlišitelné.
- Soukromá data a neověřené výsledky se nesmí zveřejnit.
- Přidané vizuály nesmí skrývat obsah ani způsobovat výrazný posun stránky.

## Tasks

1. **Důkazní obsah a autorský hlas:** integrovat devět dokončených projektových auditů do `data/projects.json`, `templates/element/project_deepdive.php`, `templates/element/project_experience.php` a zdrojových poznámek. Opravit nepřesnosti (motivace, Tiskov jako návrh, Shpotify bez ověřeného přehrávání, Woodbound bez přijaté hry, Software Inc multiplayer a kandidát, Discord bez zdrojového checkoutu). Zachovat fair alternatives. Přidat kontroly, že chráněná jména ani nepodložená runtime tvrzení neuniknou.
2. **Kategorie a projektová mapa:** nejdřív testem definovat navigační skupiny a vztahy z ověřených metadat. Nahradit nesmyslné hrany srozumitelným průvodcem podle skutečné povahy práce; pokud vztahy nejdou doložit, zobrazit přímou mapu témat bez hran. Každá skupina musí mít jasný důvod, příklady a odkazy.
3. **Project exhibits:** vytvořit devět odlišných kompozic a interakcí v existujících PHP elementech. Neopakovat stejné čtyři kroky/diagram. Každý demo ovladač musí měnit diagram, stav či data a odpovídat implementaci; jinak ho odstranit. Test nejprve pro konkrétní stavovou změnu.
4. **Playground:** rozšířit `templates/Pages/playground.php` na více samostatných workbenchů (Hub intake/preprocessing, service dependencies, scale/camera gate, route integrity a queue concurrency). Přidat funkční SVG/DOM výsledky a ověřit JS testem i klávesnicí. Syntetické vstupy, bez síťových nebo systémových akcí.
5. **HomeLab:** doplnit ověřený katalog hostů, služeb a závislostí z `data/topology.json` a konfigurace Home; vytvořit navigovatelný graf toku (host → role → služba → závislost) a zdrojový panel. Testy ověří úplnost veřejných položek i redakci citlivých adres.
6. **Historie a rozhodnutí:** nejdřív opravit test stavu contributions na `Dokončeno` pro dokončené uzavřené issues. Rozšířit `now.php` o datovanou historii z ověřených veřejných událostí. Deník doplnit pouze primárními chronologickými zdroji, rozhodnutí přestrukturovat na problém/možnosti/důkazy/volbu/důsledky. Přidat unit testy časové osy a stavů.
7. **Vizuální důkazy a README:** po úpravách znovu pořídit skutečné screenshoty z finální stránky, projít každou velikost i jazyk, aktualizovat README odkazy a popisky. Nikdy nevydávat diagram za screenshot aplikace.
8. **Vydání:** cílený PHP/JS/data/link/accessibility test, kompletní relevantní sada, produkční archivní audit, přesný veřejný náhled, release CI, nasazení a živé HTTP/screenshot ověření. Dodržet veřejnou obsahovou bránu a neinstalovat cron.
