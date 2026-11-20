# Harfusha Lab — handoff zbývající práce

> **NAHRAZENO — historický handoff k 17. 11. 2026.** Jeho stav a další kroky už neplatí; aktuální plán je [MASTER_PLAN.md](MASTER_PLAN.md). Záznam ponechán kvůli historii tehdejší candidate větve.

Stav k 17. 11. 2026. Uživatel požádal práci zastavit, zbývající práci zapsat a goal pozastavit. Goal není dokončený.

## Cíl

Dokončit osobní, důkazně podložený portfolio web: každý projekt musí mít vlastní srozumitelný příběh, důvody a alternativy, konkrétní ukázky a odpovídající CZ/EN obsah. Všechny sekce mají být promyšlené a užitečně interaktivní. README a screenshoty musí odpovídat kandidátovi. Po ověření nasadit a potvrdit běh.

Potvrzená motivace Local AI Hubu: autorovi vadilo, že se přes AI řeší i práce, kterou lze udělat programově a deterministicky. Chtěl projekt zaregistrovat, nechat předem doběhnout náročný preprocessing a při vlastní práci zbytečně neopakovat hledání ani spotřebu zdrojů.

Názvy zachovat: Woodbound, Tiskov, Discord bot. Nezmiňovat Wishflix, Code Reviews, Tools Extended ani WoodTycoon.

## Aktuální checkout

- Root: `C:\Ostatní\harfusha`
- Branch: `codex/harfusha-release-candidate-2026-11-16`
- HEAD: `029ddbedf61866c67e7bda9ec0ca07a747eaf6a0`
- Poslední Local AI Hub status: 41 změněných souborů, 21 untracked cest, 0 staged; žádné konflikty. Jde o sdílený dirty checkout. Nic nevracet, nestashovat ani hromadně nepřepisovat.

## Změny v posledním pracovním úseku

- `templates/element/project_experience.php`: přeformulováno 18 odstavců (proč a alternativy/fit pro devět projektů) do přirozenější první osoby. U Tiskovu a Discord botu zůstávají neznámé motivy i implementace výslovně neznámé.
- `templates/element/project_experience.php` a `webroot/assets/css/app.css`: devět odlišných desktopových kompozic podle typu práce; mobilní rozvržení se skládá bez horizontálního přetečení.
- `tests/Browser/smoke.py`: kontrola identity a aplikování každého projektového rozvržení; volitelný `--screenshot-dir`; screenshot projektu nyní zachycuje jeho vlastní zkušenostní sekci a skryje pevnou navigaci.
- Přidáno devět screenshotů příběhů do `docs/screenshots/project-story-*.png`. Přidání do README/gallerie zatím neprovedeno.
- Aktuální offline náhled: `tmp/public-preview-layout-2026-11-17c` (40 stránek). Screenshoty: `tmp/browser-qa-story-copy-2026-11-17`.

## Ověření

- Po poslední změně textů: `python tests/Browser/smoke.py --screenshots --screenshot-dir tmp\browser-qa-story-copy-2026-11-17` — prošlo 113 Chromium page/viewport kontrol, JS runtime, responzivita a interakce.
- `php -l templates/element/project_experience.php` prošlo před poslední textovou změnou; následný Browser runner znovu úspěšně vyrenderoval 40 stránek přes PHP.
- `git diff --check` prošlo; Git pouze upozornil na CRLF/LF převod u dvou souborů.
- `local_ai_repo:review_diff` vytvořil asynchronní review job `5e099c53549449d0931caa11b01f88a6`; následné čekání vrátilo `outcome_unknown`. Výsledek review není znám. Job neprohlašovat za úspěšný.
- Před tímto úsekem bylo zaznamenáno: PHPUnit 169 testů / 745 assertions, 9 skipped; public audit prošel; rendered audit 40 stránek; syntax 124 PHP souborů; standalone 163 pass / 0 fail; PHPCS, PHPStan 88 souborů a browser 113 checks prošly. Tyto výsledky předcházejí posledním změnám a nenahrazují finální kompletní ověření kandidáta.

## Zbývající práce

1. **Přestavět mapu projektů.** `/connections` je stále katalog seskupený do oborů. Uživatel říká, že „mapa“ nedává smysl. Během práce byl připraven jen návrh, nic z něj nebylo zapsáno: zobrazit pro každý projekt smysluplný tok `vstup → těžká práce → výsledek nebo chybějící důkaz`; seskupit do tří čtecích okruhů `připravit/provozovat`, `převést vstup na kandidáta`, `koordinovat společný stav`. Výslovně sdělit, že jde o tematické okruhy, ne technické závislosti mezi repozitáři. Zachovat funkční hledání a filtry. U Discord bota ukázat neznámý prostřední krok, ne vymyšlené chování.

2. **Dokončit osobitost všech projektů a kategorií.** Poslední přepis je první průchod a současný model stále používá společná pole. Projít všech devět CZ/EN stránek, katalog, homepage a skutečné podklady. Odlišit příběhy i strukturu podle domény; neopakovat stejný odstavec o alternativách. Neznámý původ uvést jako neznámý, ne domýšlet. Zachovat rozlišení mezi zdrojem, simulací, návrhem a runtime důkazem.

3. **Dopsat README a obnovit screenshoty.** Kořenový `README.md` dál uvádí, že přehled seskupuje projekty do sedmi oborů; to bude po přestavbě mapy nepravdivé. Nové `readme-*.png` jsou v uvedeném dočasném screenshot adresáři, ale nebyly zkopírovány do `docs/screenshots`. Devět `project-story-*.png` v `docs/screenshots` existuje, ale README je zatím neodkazuje. Přidat přehlednou galerii do `docs/PROJECT-README.md`, upravit popisy a vizuálně zkontrolovat všechny finální obrázky.

4. **Kriticky projít ostatní sekce podle požadavků uživatele.** Ověřit HomeLab služby, toky a limity; osm odlišných Playground modelů a zda každý skutečně něco počítá; issues označené jako dokončené, ne jako fail; delší a doloženou historii v „Právě teď“ a vývojovém deníku; technická rozhodnutí jako souvislé příběhy trade-offů. Odstranit statické ovládací prvky, které nic smysluplného nedělají. Výchozí stav předchozí práce je ve workspace, nevyvozovat kvalitu jen ze starých souhrnů.

5. **Finální QA a release.** Po dokončení vytvořit nový CZ/EN snapshot, zapsat přesné hashe a znovu spustit úplné relevantní testy/audity. Dřívější veřejné schválení se na nový obsah nevztahuje; před nasazením vyžádat schválení přesných aktuálních hashů. Pak ověřit izolované staging prostředí, zálohu/rollback, migrace a HTTPS smoke.

6. **Vyřešit staging HTTP 500.** Staging vracel 500 na všech URL včetně izolovaného PHP probe. Přesná příčina nebyla z dostupných logů zjistitelná. Přihlášení do administrace GIGASERVER uživatel odmítl. FTPS bez ověření certifikátu uživatel povolil, ale bezpečný read-only curl listing nebyl spuštěn: hostitelský PowerShell příkaz odmítl ještě před spuštěním. Produkce se v této fázi nezměnila. Další krok: získat přesný server error log/příčinu od hostingu nebo jiným povoleným kanálem; neopakovat odmítnutý příkaz a nenasazovat přímo na produkci, dokud staging neprojde.

## Release a bezpečnostní hranice

- Uživatel schválil dřívější obsah a potvrdil `APPROVE PUBLIC CONTENT`, ale toto schválení je vázané na starý snapshot. Nové texty a screenshoty vyžadují nové schválení přesných hashů.
- Uživatel výslovně povolil testovací DB pro živý web; tato autorizace neřeší současnou chybu stagingu ani neznamená, že aktuální kandidát je připraven k nasazení.
- Aktuální production stav se v tomto úseku neověřoval ani neměnil. Dřívější plán uvádí produkci beze změny po neúspěšném stagingu.
- Nepovažovat offline PHP/Chromium preview za CakePHP HTTP, autentizační, databázový ani produkční důkaz.

