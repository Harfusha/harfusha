# Podklady pro případové studie – 16. 11. 2026

Podklady jsou snímek přečtených souborů; HEAD není důkazem stavu necommitovaných změn. Veřejné příklady jsou označeny jako ilustrativní.

- Local AI Hub: `C:/Users/Adam/.local-ai-hub/README.md`, HEAD 070079a5; zdrojový release candidate, 7 MCP nástrojů, provoz závisí na konfiguraci.
- MutaUI: `C:/Ostatní/MutagenApp/README.md`, HEAD f94e63c; `docs/assets/images/actual-ui/forward-cs-dark.png` a `sync-en-light.png` jsou screenshoty offline dema se syntetickými daty a vypnutými akcemi.
- Home Infrastructure: `C:/Ostatní/Home/README.md`, HEAD 4c49dece; role služeb a způsob konfigurace, ne živý stav; interní adresy a údaje se nezveřejňují.
- Woodbound: `C:/Ostatní/WoodTycoon/PROJECT.md`, HEAD 72c22cdc5; návrh oblastí, terénu, tras a validace, nikoliv přijetí hratelného světa.
- Scan2CAD: `C:/Ostatní/Scan2Cad/README.md`, HEAD 90e2940; kalibrace, kontrola referenčních fotografií, CAD kandidát a limity měření.
- Shpotify: `C:/Ostatní/Shpotify/README.md`, HEAD 46742be; místnosti, fronta a doporučování; přehrávání není plně doloženo.
- Software Inc: `C:/Ostatní/SoftwareIncCoopRevamped/README.md`, HEAD c615daa; jádro a adaptér, hratelný multiplayer není potvrzen.
- Discord bot: `C:/Ostatní/vitkovskeNpc/core/client.py`, `core/featureRegistry.py`, `features/maintenance.py`, HEAD 9c0305e; lokální syntax check nalezl chybu v modulu údržby na řádku 18. Živý provoz nebyl ověřen.
- Tiskov: `C:/Ostatní/filamentApp/README.md`, HEAD dd49a23; Symfony přijímá data, MySQL drží frontu a samostatný worker provádí slicing. Cena je odhad a dohoda je ruční; aktuální provoz ani přesnost konkrétního výpočtu nejsou ověřeny.
- Maker Lab: na výslovné přání žádné jednotlivé výrobky; schéma popisuje plán budoucí dokumentace.

## Ověření a nasazení

Balíček update36 SHA-256 `8f3240ad2c8716f3034d6483a9c4d4544c131baea142a6c54f7b706e44993c86`: ověřen manifest, FTP baseline i nahrané soubory. Živých 20 detailů obsahuje text a pětikrokové schéma; oba nové snímky MutaUI vracejí PNG. Lokálně 130 prohlížečových kontrol, 175 standalone kontrol, 47 Python testů a 4 cílené PHPUnit testy. Tři Python testy a POSIX kontrola oprávnění jsou na tomto prostředí přeskočené. Hub verify_completion nevydal potvrzení, protože úloha nemá brokerové doklady; výsledek neoznačujeme jako Hub potvrzení.

## Následná revize případových studií

Původní pětikrokové pásy nahradila mapa komponent a vazeb specifická pro každý projekt (5–7 uzlů). Diagramy vycházejí z výše uvedených zdrojů. MutaUI ukazuje offline demo se syntetickými daty; Tiskov má zdrojovou grafiku, nikoli snímek aplikace; Woodbound má pracovní záběr z vývojové evidence, který nepotvrzuje hotovou hru. Dva nové obrázky mají samostatné testy původu, alt textu, lokálního souboru a PNG hlavičky. Prohlížeč dále ověřuje názvy projektů, absenci vyřazených názvů a kontrast legendy. Výsledek: 130 Chromium kontrol, public-content audit, audit 50 vykreslených stránek a PHP syntax check prošly.

Kandidát `C:/Ostatní/harfusha-web-update38.zip` obsahuje 1 447 ověřených produkčních souborů; SHA-256 `034336297efaef4b0d59b297b21a373d82a6147cfc13e01922dcddfb23112801`. **Nenasazeno:** `scripts/review-public-content.py --verify` nadále hlásí `Public content changed since editorial approval; review again`; změnily se veřejné šablony i obrázky. Živá stránka `/cs/projects/scan2cad` vrací HTTP 200 a stále zobrazuje předchozí postupové schéma; read-only staging smoke prošel. Před zveřejněním update38 je potřeba čerstvá ruční kontrola a schválení aktuálního snapshotu. Žádný plánovač nebyl vytvořen.

## Následná UX revize — 16. 11. 2026

- `/now` ukazuje pouze poslední zaznamenané veřejné změny repozitářů; redakční zápisky zůstávají ve `/journal`.
- Neověřené Maker Lab návrhy jsou odstraněné z katalogu, grafu i interních dat; `/maker` zůstává jen stručnou stránkou přípravy.
- Mapa projektů automaticky vybere první doložený uzel a případová studie Maker Lab vrací 404.
- Playground začíná konkrétní interakcí s konfliktem ve dvou kopiích souboru; text výslovně říká, že jde o simulaci v prohlížeči.
- Ověřeno: Composer quality (PHP 8.5.12), 160 PHPUnit testů / 612 kontrol (9 přeskočeno kvůli POSIX oprávněním), 172 standalone kontrol, 88 PHPStan souborů, 130 Chromium kontrol, audit veřejného obsahu a 50 vykreslených stránek.
- **Stále nenasazeno:** čerstvý obsahový otisk neodpovídá lidskému schválení. Aktuální náhled je v `tmp/public-preview`; release builder jej bez nového schválení odmítne.
