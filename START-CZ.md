# Harfusha Lab — český průvodce

> Úplný technický README projektu: [`docs/PROJECT-README.md`](docs/PROJECT-README.md). Kořenový `README.md` je záměrně určený pro veřejný profil GitHubu.


Projekt je napsaný pro **CakePHP 5.4 / PHP 8.5.12+ / MySQL**, bez Node.js procesu na serveru. Všechny návštěvnické interakce běží v prohlížeči.

## Co obsahuje ZIP

- Kompletní **zdrojový kód** CakePHP aplikace (`src`, `config`, `templates`, `webroot`).
- Interaktivní síťovou topologii, simulaci výpadků, AI workflow, synchronizační sandbox, procedurální generátor a STL prohlížeč.
- **Maker Lab** se samostatným katalogem fyzických prototypů a postupů.
- Administraci s koncepty a možností upravovat, publikovat či stáhnout zápisky a rozšířené popisy projektů.
- Katalog 13 vlastních projektů a vybraných externích PR, přehled vývoje a českou/anglickou verzi.
- MySQL ORM modely, migraci, administrační stránku s editací textů, CLI importér veřejného GitHubu.
- PHPUnit testy, `phpstan.neon` s **level max**, CakeDC, strict rules, ShipMonk, CI a dokumentaci.

## Aktuální stav ověření (15. 11. 2026)

V repozitáři je **`composer.lock`** a [GitHub Actions běh #43648999069](https://github.com/Harfusha/harfusha/actions/runs/43648999069) úspěšně dokončil PHP 8.5, Composer audit, CakePHP HTTP smoke, izolované MySQL migrace, PHPUnit, PHPStan max + strict pravidla, PHPCS, JavaScript i offline browser testy. To ověřuje daný CI běh, **nikoli nasazení na cílovém hostingu**. Zdrojový ZIP stále záměrně neobsahuje `vendor/`; produkční ZIP s `vendor/` se vytváří samostatným procesem `scripts/build-release.py` až po skutečném ručním schválení veřejného obsahu a splnění všech kontrol.

## Příprava na vlastním počítači

1. Nainstaluj PHP 8.5.12+ a Composer 2. Zajisti rozšíření `intl`, `mbstring`, `pdo_mysql`, `simplexml` a `openssl`.
2. Rozbal ZIP, zkopíruj `.env.example` do `.env`. Pro vývoj nastav `APP_ENV=development`, `DEBUG=true` a `APP_URL=http://localhost:8765`.
3. Spusť:

   ```bash
   composer install  # použije existující ověřený composer.lock
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
5. Po lokálních úpravách zopakuj CI a oprav případné nové chyby. **Nesnižuj PHPStan level ani nepřidávej baseline ignorovaných chyb jen kvůli zelenému výsledku.**

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
composer install  # respektuje již verzovaný a ověřený composer.lock
npm install --no-save --ignore-scripts typescript@5.8.3
APP_ENV=testing DB_HOST=127.0.0.1 DB_NAME=harfusha_lab_ci DB_USER=lab_ci LAB_TEST_DB_CONFIRM=harfusha_lab_ci python3 scripts/build-release.py --output ../harfusha-lab-deploy.zip
```

Pokud některá kontrola neprojde, balíček nepublikuj. Aktuálně předaný ZIP obsahuje **zdrojový kód**, nikoli již ověřené produkční `vendor/`. Další kritéria jsou v `docs/RELEASE.md` a skutečně ověřený stav v `docs/VERIFICATION-2026-11-13.md`. Při nahrávání na hosting musí pouze `webroot/` být veřejně přístupný.

## Volitelný vývoj v Dockeru

Pro izolovaný vývoj se souběžným PHP 8.5 a MySQL můžeš použít `docker compose -f compose.dev.yml up --build`. Konfigurace je **jen pro vývoj**, obsahuje lokální testovací hesla a není ověřena na hostingu.

## Důležité před ostrým nasazením

`composer.lock` již existuje a CI na PHP 8.5 / MySQL 8.4 prošlo. **Dosud však není ověřený produkční ZIP ani cílový hosting.** Před vydáním je nutné skutečně sestavit distribuční balíček včetně `vendor/`, ručně schválit veřejný obsah, provést staging na cílovém hostingu a otestovat zálohu i obnovu databáze.

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

Při budoucí vědomé aktualizaci `composer.lock` lze použít ručně spuštěnou GitHub
Actions workflow **Resolve Composer lockfile on PHP 8.5**. Výsledek nejprve zkontroluj,
poté commitni a spusť hlavní CI. Pro běžnou instalaci používej `composer install`,
nikoliv automatický `composer update`.


## Novinka 14. 11. 2026: interaktivní síťové cesty

Na `/cs/homelab` otevři přepínač **Cesty dat**. Vyber modelové přehrávání videa, vzdálený přístup, síťový tisk nebo zálohování. Zapni simulaci výpadku a klikni na zařízení: postup ukáže, na kterém uzlu se modelová cesta přeruší. Všechna data jsou statická a bezpečně abstrahovaná (`data/flows.json`); nejde o živý přístup k domácí síti. Postup rozšíření a kontroly jsou v `docs/DATA-PATHS.md`.

## Kontrola zveřejnění soukromých projektů

Před **produkčním** sestavením musíš ručně zkontrolovat všech osm souborů v `data/` a zvlášť obrázky/šablony. Spusť `python3 scripts/review-public-content.py --review`, po skutečné kontrole `python3 scripts/review-public-content.py --approve --reviewer 'Tvé označení'` a potvrď výzvu v terminálu. Soukromé potvrzení zůstává pouze u tebe a **není** v ZIPu. Bez něj se produkční balíček nevytvoří. Více `docs/CONTENT-APPROVAL.md`.

## Bezpečnější vydání – 14. 11. 2026

Produkční ZIP se nyní sestavuje z čistého staging adresáře; `composer install --no-dev` se již nespouští nad zdrojovým `vendor/`. Testy navíc **odmítnou běžet bez `LAB_TEST_DB_CONFIRM`**, i když se databáze jmenuje `*_test`. Produkční archiv obsahuje `scripts/preflight.php`, nikoli `.env` nebo privátní schvalovací protokol. Viz `docs/RELEASE-HARDENING-2026-11-14.md`.
