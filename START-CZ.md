# Harfusha Lab — český průvodce

Projekt je napsaný pro **CakePHP 5.4 / PHP 8.5.12+ / MySQL**, bez Node.js procesu na serveru. Všechny návštěvnické interakce běží v prohlížeči.

## Co obsahuje ZIP

- Kompletní **zdrojový kód** CakePHP aplikace (`src`, `config`, `templates`, `webroot`).
- Interaktivní síťovou topologii, simulaci výpadků, AI workflow, synchronizační sandbox, procedurální generátor a STL prohlížeč.
- **Maker Lab** se samostatným katalogem fyzických prototypů a postupů.
- Administraci s koncepty a možností upravovat, publikovat či stáhnout zápisky a rozšířené popisy projektů.
- Katalog 13 vlastních projektů a vybraných externích PR, přehled vývoje a českou/anglickou verzi.
- MySQL ORM modely, migraci, administrační stránku s editací textů, CLI importér veřejného GitHubu.
- PHPUnit testy, `phpstan.neon` s **level max**, CakeDC, strict rules, ShipMonk, CI a dokumentaci.

## Důležité omezení

V prostředí, ve kterém byl projekt vytvořen, **není Composer a chybí některá PHP rozšíření**. Proto ZIP **neobsahuje `vendor/`**, a nebylo možné spustit CakePHP framework, MySQL, PHPUnit ani PHPStan. Proběhly kontroly syntaxe PHP a JS, striktní typová kontrola samostatného JS jádra, 165 samostatných PHP testovacích tvrzení, 13 JS regresních testů, audit 48 veřejných HTML stránek a 50 prohlížečových smoke kontrol. Zdroj tedy vyžaduje ještě instalaci balíčků a kompletní CI ověření před produkčním nasazením. Nejde o již produkčně ověřenou aplikaci.

## Příprava na vlastním počítači

1. Nainstaluj PHP 8.5.12+ a Composer 2. Zajisti rozšíření `intl`, `mbstring`, `pdo_mysql`, `simplexml` a `openssl`.
2. Rozbal ZIP, zkopíruj `.env.example` do `.env`. Pro vývoj nastav `APP_ENV=development`, `DEBUG=true` a `APP_URL=http://localhost:8765`.
3. Spusť:

   ```bash
   composer update   # poprvé vygeneruje composer.lock
   composer install
   composer syntax
   composer standalone
   composer stan
   # VÝHRADNĚ na izolované testovací databázi, ne na produkci:
   export APP_ENV=testing DB_HOST=127.0.0.1 DB_NAME=harfusha_lab_ci DB_USER=lab_ci
   export LAB_TEST_DB_CONFIRM=harfusha_lab_ci
   composer test
   composer cs-check
   python3 scripts/audit-rendered.py
   node --test tests/Client/*.test.cjs
   php bin/cake.php server -p 8765
   ```

4. Otevři `http://localhost:8765/cs/`.
5. Oprav všechny případné chyby z plné PHPStan/CI analýzy a frameworkového testování. **Nesnižuj PHPStan level ani nepřidávej baseline ignorovaných chyb jen kvůli zelenému výsledku.**

## Nasazení na sdílený hosting

Preferovaná varianta je mít `webroot/` jako jediný veřejný adresář. Kód `src/`, `config/`, `vendor/`, `.env` a privátní data mají být mimo web root.

Po úspěšných testech proveď `composer install --no-dev --optimize-autoloader` a výsledný projekt včetně `vendor/` nahraj na hosting. V `.env` nastav HTTPS doménu, bezpečný salt, MySQL připojení, hash admin hesla a cron token. Databázi připrav pomocí `php bin/cake.php migrations migrate` (neimportuj `db/schema.sql` místo migrací). Pro denní veřejnou synchronizaci GitHubu použij `php bin/cake.php github_sync` v cron úloze.

Pokud hosting používá neměnné `public_html`, přečti nejprve **`docs/DEPLOYMENT.md`** — obsahuje přesnou bezpečnou strukturu složek a potřebné úpravy `WWW_ROOT`.

## Kde měnit obsah

- `data/projects.json` — veřejně viditelné projekty a jejich popisy (i soukromé případové studie).
- `data/topology.json` — zjednodušená mapa infrastruktury bez interních IP.
- `data/journal.json` — statická historie vývoje.
- `data/contributions.json` — kurátorovaný výběr PR do doby, než proběhne MySQL import.
- `data/maker.json` — fyzické prototypy, nápady, materiály a související software.
- `/admin` — změny popisů projektů a koncepty/zápisky s editací, publikováním a stažením z veřejného webu.

**Před publikováním projdi všechna soukromá projektová shrnutí a zkontroluj, že žádné neveřejné informace nechceš skrýt.**

## Finální sestavení pro sdílený hosting

Po dokončení všech kontrol vytvoř na počítači s **PHP 8.5.12+**, Composerem a rozšířeními **samostatný distribuční balíček včetně vendor/**:

```sh
composer update   # jen do vytvoření a zamknutí composer.lock
npm install --no-save --ignore-scripts typescript@5.8.3
APP_ENV=testing DB_HOST=127.0.0.1 DB_NAME=harfusha_lab_ci DB_USER=lab_ci LAB_TEST_DB_CONFIRM=harfusha_lab_ci python3 scripts/build-release.py --output ../harfusha-lab-deploy.zip
```

Pokud některá kontrola neprojde, balíček nepublikuj. Aktuálně předaný ZIP obsahuje **zdrojový kód**, nikoli již ověřené produkční `vendor/`. Další kritéria jsou v `docs/RELEASE.md` a skutečně ověřený stav v `docs/VERIFICATION-2026-11-13.md`. Při nahrávání na hosting musí pouze `webroot/` být veřejně přístupný.

## Volitelný vývoj v Dockeru

Pro izolovaný vývoj se souběžným PHP 8.5 a MySQL můžeš použít `docker compose -f compose.dev.yml up --build`. Konfigurace je **jen pro vývoj**, obsahuje lokální testovací hesla a není ověřena na hostingu.

## Důležité před ostrým nasazením

`composer.lock` zde není, protože nebylo možné skutečně stáhnout balíčky a bezpečně spočítat jejich závislosti. Vytvoř jej na PHP 8.5, projdi celý CI a až potom použij produkční builder. Ani tento balíček **není hotový ověřený produkční ZIP** — zdrojový projekt je dokončenější, ale frameworkové a databázové testy musí teprve reálně projít.

### Opětovné vytvoření kompletního zdrojového ZIPu

```sh
python3 scripts/build-source.py --output ../Harfusha-Lab-full-source.zip
```

Zdrojový ZIP obsahuje kontrolní `SHA256SUMS`; neobsahuje soukromé `.env`, dočasné soubory ani složku `vendor/`. **Pro skutečné nasazení** je nutný zvláštní produkční build z `scripts/build-release.py` po úspěšném absolvování všech kontrol.

### Co je nového v této verzi

Vedle původních čtyř playgroundů je v záložce `CakePHP Request Lab` pátý interaktivní výukový modul. API `GET /api/v1/projects?lang=cs` (a `lang=en`) vrací veřejné studie jako typovaný JSON bez zdrojů soukromých repozitářů. Dokumentace kontraktu je v `docs/API.md`. Vícejazyčný `sitemap.xml` uvádí i alternativní jazykové URL. Viz `RELEASE-NOTES.md`.


### Soukromá provozní diagnostika

Po migracích a první úspěšné GitHub synchronizaci spusť
`php bin/cake.php lab_health`. Příkaz ověřuje dostupnost MySQL, práva k zápisu
a stáří synchronizace (max. 48 h); neúspěch vrací nenulový exit code.
Veřejná `/healthz` je jen kontrola běhu aplikace, **nikoliv** databáze.
Podrobnosti jsou v `docs/OPERATIONS.md`.

### Bezpečnost přihlášení (nová verze)

Omezení přihlášení používá maximálně 32 souborů v `tmp/auth/`, 5 pokusů na IP
za 15 minut, atomické rezervování pokusu a bezpečné odmítnutí při chybě zápisu.
Staré soubory `auth-<sha256>.json` lze po odstavení staré verze zkontrolovat a
ručně smazat; nové `login-*.json` zachovej. Podrobnosti: `docs/AUTH-THROTTLE.md`.

Pro vygenerování chybějícího `composer.lock` lze použít ručně spuštěnou GitHub
Actions workflow **Resolve Composer lockfile on PHP 8.5**. Výsledek zkontroluj,
ulož do repozitáře a spusť hlavní CI; samostatná existence workflow nezaručuje
kompatibilitu závislostí.


## Novinka 14. 11. 2026: interaktivní síťové cesty

Na `/cs/homelab` otevři přepínač **Cesty dat**. Vyber modelové přehrávání videa, vzdálený přístup, síťový tisk nebo zálohování. Zapni simulaci výpadku a klikni na zařízení: postup ukáže, na kterém uzlu se modelová cesta přeruší. Všechna data jsou statická a bezpečně abstrahovaná (`data/flows.json`); nejde o živý přístup k domácí síti. Postup rozšíření a kontroly jsou v `docs/DATA-PATHS.md`.

## Kontrola zveřejnění soukromých projektů

Před **produkčním** sestavením musíš ručně zkontrolovat všech osm souborů v `data/` a zvlášť obrázky/šablony. Spusť `python3 scripts/review-public-content.py --review`, po skutečné kontrole `python3 scripts/review-public-content.py --approve --reviewer 'Tvé označení'` a potvrď výzvu v terminálu. Soukromé potvrzení zůstává pouze u tebe a **není** v ZIPu. Bez něj se produkční balíček nevytvoří. Více `docs/CONTENT-APPROVAL.md`.

## Bezpečnější vydání – 14. 11. 2026

Produkční ZIP se nyní sestavuje z čistého staging adresáře; `composer install --no-dev` se již nespouští nad zdrojovým `vendor/`. Testy navíc **odmítnou běžet bez `LAB_TEST_DB_CONFIRM`**, i když se databáze jmenuje `*_test`. Produkční archiv obsahuje `scripts/preflight.php`, nikoli `.env` nebo privátní schvalovací protokol. Viz `docs/RELEASE-HARDENING-2026-11-14.md`.
