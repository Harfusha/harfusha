# Osobitější Harfusha: návrh

## Záměr

Představit Adama a jeho skutečné projekty srozumitelně, osobně a vizuálně zapamatovatelně. Stránka má návštěvníka rychle dovést k ukázce práce a zároveň vysvětlit, co je experiment a co lze ověřit.

## Co ukázal průzkum

- Současná titulní stránka začíná sloganem a abstraktním schématem; konkrétní projekty přicházejí až po několika blocích. Mnoho anglických popisků a opakovaná ujištění o poctivosti odvádějí pozornost od práce.
- [Figma: příklady portfolií](https://www.figma.com/resource-library/portfolio-website-examples/) ukazuje hodnotu názorných ukázek, kontextu projektu a osobitého způsobu vyprávění. Použijeme je jako principy, nikoli jako šablonu.
- [MutaUI](https://github.com/Harfusha/MutaUI) je desktopové rozhraní pro existující Mutagen CLI. Repozitář obsahuje snímky offline dema se syntetickými daty a uvádí, že neobsahuje licenci pro opětovné použití; veřejný repozitář tedy sám o sobě neznamená open source licenci. Současná stránka Open Source se věnuje upstream příspěvkům; není doloženo, že tímto označením nazývá přímo MutaUI.
- [Local AI Hub](https://github.com/Harfusha/local-ai-hub) poskytuje sedm MCP nástrojů a vrstvu pro práci agentů s lokálním kontextem. Verze 6.1.0 je podle repozitáře kandidát na vydání, nikoli prohlášená produkční verze.
- Současná šablona detailu projektu opakuje stejné bloky a obecné upozornění pro každý projekt; konkrétní vizuální důkaz ani individuální stav neobsahuje. Stránky Maker a Právě teď opakují obranná vysvětlení a anglické štítky. Vhodné je přesunout přesná omezení k příslušným ukázkám a údaje o aktualizaci ponechat u dat.
- [Galerie MutaUI](https://github.com/Harfusha/MutaUI/blob/master/docs/screenshots.md) nabízí snímky offline dema. Před případným vložením ověřit konkrétní soubor, původ a případné citlivé údaje; nepředstírat, že syntetický obsah je skutečný provoz. Veřejné vyhledávání nepotvrdilo zdroj projektu Woodbound; dosavadní popis proto sám nestačí jako nezávislé ověření stavu.
- Katalog obsahuje konkrétní náměty pro vyprávění: opravu průchozích otvorů v STL a návrh skladování filamentu. U mechanické patky je 350 kg návrhová podmínka, nikoli ověřená únosnost; bezpečnostní upozornění musí zůstat přímo u položky. Podklady k technickým rozhodnutím již obsahují kontext a kompromis, které lze využít v detailech projektů.
- [W3C](https://www.w3.org/WAI/WCAG22/Understanding/animation-from-interactions) doporučuje umožnit omezení pohybových animací; [web.dev](https://web.dev/learn/html/images) popisuje rozměry obrázků jako způsob rezervace prostoru před načtením. Tyto zásady patří do provedení i kontroly nových ukázek.
- Podklady Scan2CAD (`C:/Ostatní/Scan2Cad/README.md`, 2026-11-16) popisují beta kandidáta vyžadujícího ověření měřítka. Podklady Woodbound (`C:/Ostatní/WoodTycoon/README.md` a `KNOWN_LIMITATIONS.md`, 2026-11-16) rozlišují prototyp scén od ověřené hratelné hry. Dokumentace Software Inc Coop (`C:/Ostatní/SoftwareIncCoopRevamped/README.md`, 2026-11-16) neuvádí přijatý běh ve hře. Jde o tvrzení dokumentace příslušných projektů, nikoli o nezávislé provozní ověření.
- Shpotify (`C:/Ostatní/Shpotify/README.md`, 2026-11-16) popisuje rozpracované propojení přehrávače a vzdáleného audio agenta a neprokazuje skutečný Spotify poslech. Tento limit uvádí veřejný detail; není to ověření stavu konkrétní instalace.
- Wishflix Android TV (`C:/Ostatní/wishflix/wishflix-androidtv/WISHFLIX-OFFLINE-ARTWORK.md`, 2026-11-16) popisuje záměr offline metadat a obrázků a výslovně odloženou zkoušku na fyzické televizi. Nezaměňovat s odlišným Kodi pluginem ani z dokumentu nevyvozovat provozní akceptaci.
- Snímky MutaUI pocházejí z [galerie offline dema](https://github.com/Harfusha/MutaUI/blob/master/docs/screenshots.md). České a anglické varianty zobrazují syntetická data a vypnuté akce; v detailu projektu jsou takto označené. Stav dalších soukromých projektů nelze z dosavadního průzkumu nezávisle potvrdit.
- Dne 2026-11-16 bylo všech 14 odkazů v `data/contributions.json` porovnáno s GitHub Issues API: autor Harfusha a uvedený stav souhlasí. Šest názvů jsou zkrácené nebo upravené popisy, ne přesné citace názvů. U všech dat odpovídá den založení, sloučení nebo uzavření; není však jednotně označeno, o kterou událost jde. Bez úpravy importního kontraktu nelze všechna data nazývat datem založení.

## Směr

Web pojmout jako autorský pracovní zápisník: výrazná typografie, teplejší kontrastní paleta, promyšlená nepravidelnost a vizuální detaily vycházející z nástrojů a projektů. Úvod říká, kdo Adam je a co vytváří, bez marketingových protikladů. Hned poté představí vybrané projekty prostřednictvím konkrétního problému a skutečných nebo jasně označených ukázek.

Projektové stránky mají ukázat účel, fungování, aktuální omezení a odkazy na zdroje. Ukázky MutaUI použít pouze po ověření jejich původu a možnosti použití; pokud nejsou dostupné, použít vlastní přesně označenou ilustraci. Zachovat obě jazykové verze, přístupnost, responzivitu, snížený pohyb a existující funkce. Nezveřejňovat osobní údaje ani nevymýšlet výsledky, biografii nebo provozní stav.

## Ověření

Projít všechny veřejné stránky v češtině a angličtině, odkazy a interakce; zkontrolovat telefon i desktop, klávesnici, kontrast a omezení pohybu. Provést vhodné kontroly změn a po nasazení ověřit živý web. Veřejný obsah zkontrolovat před zveřejněním podle existující release brány.

Průběžné ověření 2026-11-16: všech 48 veřejných adres vrátilo HTTP 200; kontrola vykreslených odkazů a ID prošla pro 48 stránek. Prohlížečová sada pokryla 50 kombinací stránek a rozměrů a vybrané interakce; není to kompletní audit všech detailů. Na živém úvodu funguje přeskočení navigace klávesnicí, preferenci omezeného pohybu respektuje posouvání i přechod projektové karty. Kontrast hlavního drobného textu na světlé kartě je 5,77:1. Kompletní kontrola kontrastu, čteček a všech interakcí nadále není doložena.

Kontrola přístupnosti 2026-11-16: po opravě popisků a kontrastu axe-core 4.14.0 nehlásí porušení pravidel WCAG 2.0/2.1 A/AA v základním stavu všech 48 offline vykreslených stránek při šířkách 320 a 1365 px (96 kontrol, preference omezeného pohybu). Tato automatická kontrola nenahrazuje zkoušku všech stavů, skutečnou čtečku obrazovky ani kompletní audit WCAG. Prohlížečová sada navíc prošla 126 kombinacemi stránky a rozměru; mapa má nepřekrývající se uzly a na telefonu posuvnou oblast ovladatelnou klávesnicí. Živou mapu a přístupné názvy jsme po nasazení ověřili v prohlížeči.

Kontrola interaktivních stavů 2026-11-16: axe-core prověřil 42 stavů obou jazyků a dvou rozměrů (pět demonstrací, výběr uzlu, výpadek a trasy v homelabu, otevřené rozhodnutí, prázdný filtr a mobilní navigaci). Jediný hlášený typ problému byl nízký kontrast tří voleb v simulátoru požadavku. Po opravě osm opakovaných kontrol veřejné a administrační cesty ve dvou jazycích a dvou šířkách nehlásí testovaná porušení WCAG A/AA. Nejde o prověření každé kombinace ani náhradu skutečné čtečky obrazovky.

Simulátor požadavku 2026-11-16: české popisy úspěšných i zastavených kroků a jejich stavů doplněny bez změny modelu rozhodování; angličtina zůstává výchozí pro přímé volání jádra. Prošlo 15 klientských testů, striktní kontrola TypeScript/JSDoc a 126 prohlížečových kontrol. Živé HTTP 200 a simulované HTTP 400 ověřeny v prohlížeči; nejde o skutečný síťový požadavek.

Doplnění podkladů 2026-11-16: `C:/Ostatní/Home/README.md` popisuje Git jako zdroj změn, Ansible, plánování v Semaphore a oddělení běžné správy od změn routeru; jde o dokumentovaný návrh, nikoli důkaz aktuálního provozu. `C:/Ostatní/Scan2Cad/README.md` vyžaduje fyzicky ověřené měřítko nebo referenční rozměr a výstup označuje za kandidáta. Anglické tvrzení „verified scale“ bylo proto nahrazeno formulací o kontrole měřítka.

Měření živého webu 2026-11-16 08:34 UTC: Lighthouse 12.8.2 v headless Chromium se simulovaným mobilním profilem, jedno měření úvodu `/cs/` a jedno detailu `/cs/projects/mutaui`. U obou vyšel LCP 1,4 s, CLS 0 a TBT 0 ms; kategorie performance/accessibility/best practices/SEO dosáhly 100/100. Surové reporty: `%TEMP%/harfusha-lighthouse-audit/live-mobile.json` (SHA-256 `5272a10434bd2d27fb315e5d53b756877fbd00293307ddec9079a6c9a04b4720`) a `live-mutaui-mobile.json` (`95ce01c8f0ffca53ba3b3237bd566ba38f8ac07c8c649b99b1650e9b0803fbaa`). Lighthouse doporučil menší/responzivní obrázek, delší cache a další optimalizace CSS; nejsou doložena data skutečných návštěvníků, INP ani výkon fyzického telefonu.

Případová studie Software Inc 2026-11-16: `C:/Ostatní/SoftwareIncCoopRevamped/README.md` uvádí oddělené doménové jádro a adaptér, testovaný kandidát a nepřijatý běh ve hře. Popis proto představuje spolupráci hráčů jako rozpracovaný záměr; poznámka odděluje testy kódu od ověření herního provozu. Počet testů ani přijetí hry web netvrdí.

Živý import GitHubu 2026-11-16: jeden autentizovaný POST skončil HTTP 502. Připojení k použité databázi z lokálního PHP pro čtení uspělo; tabulky příspěvků, repozitářů i záznamů synchronizace obsahovaly 0 řádků. Lokální dotazy veřejného GitHub API vrátily HTTP 200. Dostupný aplikační log neobsahoval příčinu; místní dostupnost API nedokazuje spojení z hostingu. Web nadále označuje historický snímek. Automatický plánovač ani serverový import nejsou ověřené; import znovu nespouštět bez nové diagnostiky.

GitHub import 2026-11-16: cURL kód 77 ukázal na chybějící veřejný CA bundle, který předchozí pravidlo vyřadilo kvůli příponě `.pem`. Přesný soubor závislosti composer/ca-bundle 1.6.1 byl porovnán s upstream referencí zamčenou v composer.lock a ověřen SHA-256. Balicí pravidla jej povolují pouze s tímto otiskem; ostatní PEM a soukromé klíče zůstávají zakázány. Serverový import následně vrátil HTTP 200: 127 veřejných příspěvků, z toho 62 sloučených PR a 31 issues, a 3 veřejné repozitáře. Web zobrazil čas importu. Plánovač hostingu zatím není nastaven ani ověřen.


## Úpravy podle zpětné vazby 16. 11. 2026

- Maker Lab: zveřejněné položky staženy ze stránky; původní dataset zůstal zachován pro budoucí doložené zpracování.
- Katalog: odebrány tři nechtěné záznamy a jejich vztahy; názvy Woodbound, Tiskov a Discord bot sjednoceny. Detailní popisy rozšířeny v češtině i angličtině.
- Homelab: katalog služeb vychází z README domácí infrastruktury; jde o dokumentovaný model, nikoliv živý monitoring.
- Deník: doplněno pět milníků podle veřejného changelogu Local AI Hubu a historie MutaUI, doplněn RSS odkaz. Výzkum: https://github.com/Harfusha/local-ai-hub/blob/master/CHANGELOG.md a https://github.com/Harfusha/MutaUI .
- Playground: lokální porovnání textů s limitem 200 řádků a STL prohlížeč jako výchozí panel. Data textového nástroje zůstávají v prohlížeči.
- Produkční archiv update33 SHA-256: e51e8103b3f348832f146110526a92addbf35a8b10472ddaac464bbca417f237. Přenos ověřen proti předchozímu stavu, jednotlivé soubory po nahrání ověřeny. Ověřen živý nástroj a 404 stažených projektů.
- Omezení: pravidelná synchronizace GitHubu není nastavena; automatická evidence dokončení v Local AI Hubu nebyla po výpadku aktualizována. Testy Windows vynechaly POSIX oprávnění LoginLimiteru.
