# HARFUSHA LAB — implementační plán, akceptace a stav k 14. 11. 2026

> Účel: jediný zdroj pravdy pro dokončení projektu bez přikrášlování stavu. `DONE` znamená, že kód existuje a jsou dostupné uvedené důkazy; **neznamená automaticky ověřené produkční nasazení**. `BLOCKED` znamená, že není možné požadovaný důkaz získat v tomto izolovaném prostředí. Neoznačovat jako PASS, pokud příkaz skutečně neběžel.
>
> Uživatel požaduje **CakePHP 5.4, PHP 8.5.12+, MySQL, maximální PHPStan, ShipMonk, strict rules, důraz na typy, bezpečnost, výkon a interaktivní technický obsah** na běžném sdíleném hostingu. Toto jsou pevné požadavky, nikoliv volitelný backlog.

## Aktuální stav automatických kontrol — 14. 11. 2026

**PASS v GitHub Actions:** [běh #43648999069](https://github.com/Harfusha/harfusha/actions/runs/43648999069) na PHP 8.5 / MySQL 8.4 úspěšně dokončil všechny tři joby (`application`, `static`, `browser-offline`). Testované: uzamčené Composer závislosti a audit, CakePHP HTTP smoke, tři migrace nad izolovanou MySQL, PHPUnit, PHPStan `level: max` + CakeDC/strict/ShipMonk, CakePHP PHPCS, kontrola veřejných dat, JavaScript strict types a Chromium. `composer.lock` je v Git.

**Nevyřešené podmínky produkčního vydání:** PHP-FPM a Apache na konkrétním hostingu, bezpečné nastavení domény/cron, staging s úplnými HTTP testy, test záloh a návratu databáze, ruční schválení všech zveřejněných údajů a autentických fotografií/screenshotů. **CI PASS není automaticky production ready.** Pozdější oddíly s datovanými záznamy uchovávají historický stav jednotlivých iterací, nikoliv nynější výsledek.

## A. Cílový produkt a kritéria hotovosti

**Výsledek:** dvojjazyčný digitální workshop, kde lze procházet skutečné vlastní projekty, jejich vazby, anonymizovanou domácí infrastrukturu, výsledky open-source práce a ručně schválené vývojové poznámky. Veřejné interaktivní nástroje jsou **lokální bezpečné simulace**, nikoliv přístup do domácí sítě, k AI agentům, hernímu enginu či soukromému kódu.

**Definition of done = zároveň:**

1. Všechny hlavní stránky fungují bez JavaScriptu pro čtení; interaktivní demonstrace s JS fungují na desktopu, mobilu a klávesnici.
2. Veřejné údaje jsou dohledatelné, aktuálnost uvedena, historický snímek neoznačen jako „živý“.
3. Žádný soukromý repo obsah, přihlašovací údaj, IP adresa, tajný token, pracovní kontext nebo osobní údaj není automaticky publikován.
4. Veškeré vstupy administrace, GitHub API a veřejného JSON katalogu mají typovou a sémantickou validaci. Žádné tiché zpracování neúplných dat.
5. **Opravdu proběhne** Composer dependency solver, `composer.lock`, PHP 8.5.12+ kontrola, PHPStan `level: max`, strict rules, ShipMonk, CakeDC, PHPUnit, migrations, MySQL integrace, PHPCS, browser smoke, privacy scan a staging test.
6. Nasazení i rollback jsou ověřeny na skutečném sdíleném hostingu, včetně zálohy a obnovení databáze. Až tehdy lze veřejně tvrdit *production ready*.

## B. Implementované moduly — kód a akceptační testy

| Stav | Modul | Implementace | Ověření / poznámka |
|---|---|---|---|
| DONE | PHP 8.5/CakePHP struktura | `composer.json`, `src/Application.php`, `config/`, `templates/`, `webroot/` | CI PHP 8.5 / CakePHP runtime / HTTP smoke PASS; cílový hosting neověřen |
| DONE | Výběr reálných projektů | `data/projects.json`, `ProjectCatalog`, stránka `/projects`, detail `/{lang}/projects/{slug}` | Ověřený model + 13 studií; fotografie jsou nedodané |
| DONE | Dvojjazyčná navigace | CS/EN URL a texty, canonical/alternate | 48 offline stránek a kontrola odkazů |
| DONE | Vazby projektů | `data/connections.json`, `ProjectConnections`, `/connections` | 13 uzlů, klávesnice, dotyk, filtrování; browser smoke |
| DONE | Engineering Decisions | `data/decisions.json`, typovaná `EngineeringDecision`, `/{lang}/decisions` | 7 kontextů, voleb a kompromisů; browser + validace; nenahrazuje skutečné benchmarky |
| DONE | Redigovaná historie editoru | `editorial_audit`, `EditorialAuditTrail`, administrace | Transakční audit receipt a publikování draftů; MySQL integrační testy PASS v CI |
| DONE | Bezpečný GitHub repo parser | `PublicRepository` | Odmítá privátní/neúplné/nesprávné/duplicitní záznamy před transakcí |
| DONE | Produkční konfigurace | `DeploymentPolicy`, bootstrap, preflight | Vynucuje APP_ENV/HTTPS/sůl/debug; runtime PHP 8.5 PASS v CI; hosting neověřen |
| DONE | Reálné HTTP smoke CI | `scripts/http-smoke.py`, `.github/workflows/ci.yml` | Reálný CakePHP HTTP smoke prošel v CI; externí staging neověřen |
| DONE | Homelab explorer | `data/topology.json`, `/homelab`, `lab-core.js` | 7 anonymizovaných uzlů; výpadky / závislosti; DOM smoke |
| DONE | Conceptual Data Paths | `data/flows.json`, `NetworkFlow`, `/homelab`, `traceFlow()` | Four bilingual scenarios, topology-adjacent hop validation, outage propagation, PHP/JS/Chromium tests; not real network monitoring |
| DONE | Pět simulátorů | `/playground#ai,#sync,#world,#cad,#request` | 13 JS unit testů + browser smoke; **ne skutečné runtime systémy** |
| DONE | Maker Lab | `data/maker.json`, `/maker` | 5 projektů, metadata, stav; odstraněno mobilní přetečení |
| DONE | Open Source browser | `/open-source`, `data/contributions.json`, GitHub sync | Kurátorovaný fallback 14 položek, plný veřejný import vyžaduje cron a DB |
| DONE | Aktuální práce | `/now`, feed veřejných repo pushů, vývojový deník | Označení zdroje a času; fallback je historický snímek |
| DONE | RSS | `/cs/feed.xml`, `/en/feed.xml`, `JournalFeed` | Typovaný XML export, bezpečné escapování a testy |
| DONE | Typované JSON API | `ApiController`, `PublicProjectView`, `/api/v1/projects` | Redigovaná CS/EN data, ETag/304; CakePHP testy PASS v CI |
| DONE | Multijazyčný sitemap | `PublicSitemap`, `/sitemap.xml` | Testované cs/en/x-default; CakePHP integrační sada PASS v CI |
| DONE | MySQL modely | `config/Migrations`, `src/Model`, `ContentRepository` | Tři migrace a PHPUnit s izolovanou MySQL 8.4 PASS v CI; rollback neověřen |
| DONE | Editor | `/admin`, login, editace projektů, drafty, publikování | Pure `EditorialInput`, CSRF a integrační HTTP testy PASS v CI |
| DONE | GitHub import | `GithubSyncService`, `GithubSnapshot`, `GithubSyncCommand` | Veřejné API bez tokenu, atomické refresh, lock, kontrola úplnosti; remote E2E BLOCKED |
| DONE | Bezpečnostní bariéry | `PublicContentValidator`, HostGuard, SecurityHeaders, `Html::escape` | Unit/standalone testy a privacy heuristika, ale nikoliv penetrační audit |
| DONE | CI a sestavení | `.github/workflows/ci.yml`, `scripts/build-release.py`, `scripts/release_package.py` | Izolovaný build a DB guard; Composer závislosti / PHPStan / PHPUnit PASS v CI, host staging čeká |
| DONE | Offline browser QA | `tests/Browser/smoke.py` | Skutečné Chromium vykreslení DOM; ne HTTP E2E |
| DONE | Source ZIP + manifest | `scripts/build-source.py` | Přepočitatelné SHA256 a CRC kontrola ZIP |

## C. BLOKÁTORY pro skutečné produkční schválení

Každý bod vyžaduje **vnější prostředí, přístup, autorství skutečných podkladů nebo nezávislé ověření**. Není vhodné zkoušet jej „napodobit“ v ZIPu.

### P0. Composer/PHPStan/MySQL/runtime (release blokující)

1. [ ] Připravit **reálné** PHP 8.5.12+ (CLI i FPM), MySQL 8.4/MariaDB, rozšíření a Composer 2. `php -v` a `php -m` obou SAPI musí potvrdit kompatibilitu.
2. [x] Jednorázově vyřešené závislosti na PHP 8.5, `composer.lock` uložený v Git a `composer install` úspěšný v CI. Příště používat `composer install`, aktualizace lockfile pouze v kontrolovaném PR.
3. [x] GitHub Actions používají izolovanou MySQL 8.4, neprivilegovaného uživatele a `LAB_TEST_DB_CONFIRM=DB_NAME`. Pro hosting ověřit samostatně.
4. [x] `composer validate --strict`, `composer audit --locked`, `composer stan`, `composer test`, `composer cs-check` PASS v CI [#43648999069](https://github.com/Harfusha/harfusha/actions/runs/43648999069). PHPStan `level: max` a přísná rozšíření zůstávají zapnutá.
5. [ ] Všechny tři migrace na prázdné izolované MySQL **PASS v CI**. Zbývá test rollbacku na kopii databáze, upgrade z předchozí verze, obnova zálohy a porovnání schématu s `db/schema.sql`.
6. [ ] Automatické CakePHP HTTP smoke a PHPUnit integrační testy **PASS v CI** včetně relací a MySQL. Zbývá ověřit úplnou sadu scénářů na stagingu, chybové stavy databáze a zásahy administrace s bezpečnou kopií dat.
7. [ ] Založit a otestovat cron, včetně reálných HTTP 403/429/502 a návratu k poslednímu platnému snímku, případně ručně auditovat upstream výsledky. `GithubSnapshot` odmítá rozpor PR/merge i neúplné stránky.
8. [ ] Zajistit staging deployment **vendor-inclusive** buildu; teprve potom produkční publikace. Testovat stejné PHP-FPM/OPcache/rewrite/chmod jako na hostingu.

Příkazy a důvody: `docs/PROJECT-README.md`, `docs/TESTING.md`, `docs/RELEASE.md`, `docs/DEPLOYMENT.md`.

### P0. Bezpečnost a soukromí (release blokující)

1. [ ] Ručně schválit všechny popisy odvozené ze soukromých repozitářů, fotografie, architekturu sítě a časovou osu. `scripts/audit-public.py` je jen heuristika.
2. [ ] Ověřit v produkci `.env`, `src/`, `config/`, `data/`, `vendor/`, `tmp/`, `logs/` **mimo HTTP DocumentRoot**. Ověřit požadavky na skryté soubory, chybové stránky, zálohy a soubory s příponou `.bak`.
3. [ ] HTTPS/HSTS, cookies, login bruteforce, CSRF, host header, URL rewrite, rate limit, CSP, žádné inline script zásahy.
4. [ ] Otestovat restrikce MySQL uživatele a administrace; cron tajemství jen v hlavičce, ne v URL, JS ani logu.
5. [ ] Projít závislosti bezpečnostním auditem. Nedovolit neznámé vnější skripty, analytiku ani CDN.

### P1. Skutečné podklady, které musí dodat autor

1. [ ] Vlastní screenshoty aplikací Local AI Hub, MutaUI, Scan2CAD, Shpotify, Tiskov, Woodbound a modu Software Inc; neprezentovat generické ilustrace jako realitu.
2. [ ] Fotografie skutečných Maker výrobků, zařízení a dokumentovaných projektů bez identifikačních údajů domácnosti.
3. [ ] Schválené benchmarky s metodikou, hardwarem, verzemi, datem, počtem vzorků a raw daty. Žádné neověřené „x % faster“.
4. [ ] Dopracovat každou případovou studii do formátu **problém / omezení / návrhové rozhodnutí / implementace / testy / výsledky / co nefungovalo**.
5. [ ] Potvrdit, které soukromé projekty mohou mít veřejné ukázky, popisy a případné downloady bez úniku licencovaného/privátního obsahu.
6. [ ] Zvolit a ověřit finální doménu a profesní kontakt; web nyní neodhaduje osobní kontaktní informace.

### P1. UX/performance po autentickém deploymentu

1. [ ] Pomalá síť a telefony: WebPageTest/Lighthouse měřit na staging doméně (např. LCP do 2,5 s, CLS do 0,1, INP do 200 ms pro reprezentativní zařízení; cíle nikoliv tvrzení o dosažení).
2. [ ] Keyboard-only, NVDA/VoiceOver, zoom 200–400 %, `prefers-reduced-motion`, tmavý režim, dlouhé české/anglické texty, 320/390/768/1365/1920 px.
3. [ ] Vyzkoušet STL s 0, velkým, poškozeným, binárním a ASCII souborem v Safari/Chromiu/Firefoxu. Udržet omezení velikosti a žádný upload na server.
4. [ ] Ověřit RSS ve čtečce, indexování sitemap, správné kanonické linky, časové zóny a automatické obnovení cache.
5. [ ] Zachytit skutečné prod incidenty (sanitizovaně) a opravit je s reprodukčními testy.

### P2. Volitelné funkce (nejsou nutné pro první bezpečné vydání)

- Nahradit veřejný anonymizovaný model homelabu **pouze schvalovaným podepsaným agregovaným snapshotem telemetrie**; nepovolovat vzdálené admin akce.
- Přehrávač verzovaných, povolených agentických trace s naměřenými metrikami; veřejný uživatel nikdy nedostane přístup do interního orchestru agenta.
- 3D modely s explicitními licencemi a kontrolovanými soubory, případně měření odchylky vedle STL modelu.
- Přímé párování case studies s releases, changelogy a screenshoty; editor pro evidence.
- Archiv technických článků s verzovaným Markdownem a redakčním review, nikoli libovolný HTML vstup.

## D. Závěrečná akceptace a release protokol

**Důkazy, které musí existovat pro veřejné schválení:**

- SHA a verze commitu, `composer.lock`, výpis `php -v`, výpis aktivních extensions.
- Zelený CI run pro daný commit, jeho logy a žádné přeskočené povinné kontroly.
- Testy s reálným MySQL a ověřením rollbacku, ne jen statické PHP testy.
- Protokol o soukromí: schválení každého soukromého projektu a každého screenshotu.
- ZIP z `scripts/build-release.py` se závislostmi a výsledky výše uvedených quality gates.
- Staging release checklist: HTTP, přihlášení, editor, cron, RSS, SEO, responzivita, obnova ze zálohy, rollback.

Pokud chybí libovolná povinná P0 položka, vydání má stav **SOURCE CANDIDATE — NOT PRODUCTION VERIFIED**. Toto pravidlo je záměrné a nesmí se obejít jen změnou dokumentace nebo čísla verze.

## E. Další implementační iterace — 13. 11. 2026

**Dokončeno ve zdrojovém kódu:** 7 odůvodněných architektonických rozhodnutí, editorský append-only audit, bezpečnější transakce, explicitní produkční konfigurace, validace veřejných GitHub repozitářů, integrační HTTP testy, nové CakePHP TestCase a rozšíření skutečného Chromium testování na pět šířek včetně 320 px. Opravena konkrétní mobilní chyba overflow na upstream příspěvcích.

**Stále release BLOCKED:** skutečný Composer solver a lockfile, PHPStan max, PHPUnit IntegrationTestTrait, všechny tři migrace a rollback na MySQL, reálný HTTP smoke, host staging, autorovo schválení soukromých case studies a jejich screenshoty, kvalitativní měření na produkčním hardwaru. Předání ZIPu tato kritéria **nepovažuje za splněná**.

## F. Follow-up implementation — operational diagnostics (2026-11-13)

**Implemented (source + isolated test only):** authenticated admin readiness
panel, private CLI command `lab_health`, pure `OperationsStatus` classification,
no-cache public liveness endpoint, deterministic health-policy tests and
integration-test code for real ORM writes and editorial audit/draft workflow.

**Acceptance blockers:** run `bin/cake.php lab_health` against a provisioned,
migrated MySQL instance after a **real** GitHub sync; execute the new
`EditorialPersistenceTest` under PHPUnit against the disposable test database;
verify `/admin` rendering after authentication on PHP 8.5.12+; verify PHPStan
max, strict extensions, CSRF and persistence. None of those checks can be
considered green based on the isolated syntax/test runs.

## G. Implemented follow-up: bounded and atomic login throttling

**DONE (verified in isolated PHP runtime):** The previous `LoginLimiter`
created one file for each address and checked/recorded failures in separate
critical sections. It is replaced by a fixed 32-shard store, 512 identities
per shard, five attempts per sliding 15-minute window, full exclusive locks
covering both check and record, strict JSON state validation, a 256 KiB per
shard cap, safe reset after valid login, 429 `Retry-After`, and fail-closed
handling for capacity, symlinks, corrupt state and storage failure. Unit tests
and a process-level concurrency regression were added. Operational details
and capacity trade-offs: `docs/AUTH-THROTTLE.md`.

**DONE (source only):** Manual GitHub Actions PHP 8.5 lockfile resolution
workflow added to produce a reviewable `composer.lock` artifact. It is not
a claim that the workflow has been executed or dependency resolution passed.

**Still P0 BLOCKED:** Composer lock artifact must actually be resolved,
reviewed and committed; the PHP 8.5.12+ CI pipeline must run to completion
including PHPStan max, PHPUnit, MySQL migrations/rollback, actual HTTP auth
response and target-host deployment. No source-side test substitutes for that.

## H. Follow-up — API snapshot consistency and release editorial approval (2026-11-14)

**Implemented & independently tested:** pure typed GitHub search/repository page collectors reject duplicates, pagination drift, invalid Search responses, premature termination and excess page budget. Wired to actual importer, whose DB transactional refresh remains fail-closed. PHP standalone and PHPUnit test definitions cover these invariants.

**Implemented & independently tested:** local human review receipt bound to SHA-256 hashes of eight public JSON files, templates and public visual assets, rejected on any change or added file. The release builder now requires this before publishing; receipt is private, gitignored and not packaged. This verification does **not** replace human inspection of screenshots/templates or prove any privacy approval happened.

**Release blockers unchanged:** GitHub Search & MySQL end-to-end, actual PHP 8.5 with Composer and `composer.lock`, full PHPStan max/CakeDC/ShipMonk, CakePHP + PHPUnit/Migrations, staging deployment, explicit human case-study/content approval. Documented in `docs/GITHUB-IMPORT-INTEGRITY.md` and `docs/CONTENT-APPROVAL.md`.

## Dodatek — 14. 11. 2026, produkční hardening

- **DONE (offline):** TestDatabaseGuard přidaný přímo do `tests/bootstrap.php` před inicializací frameworku, 6 samostatných pozitivních/negativních kontrol a vlastní PHPUnit testy (druhé zatím BLOCKED).
- **DONE (offline):** Produkční builder kopíruje allowlist zdrojů do izolovaného stagingu, tam instaluje Composer `--no-dev`, nevystavuje `.env`, schvalovací receipt ani testy a zapisuje SHA-256 manifest; 6 nových Python testů balení úspěšných.
- **BLOCKED:** Reálný běh Composeru, PHPUnit nad CakePHP, PHPStan a MySQL v PHP 8.5; bez `composer.lock` a vendor nelze produkční archive vydat.
- **P0:** Po vygenerování lockfile projít celý gate v CI + staging deployment; následně ručně schválit veřejná data, otestovat backup/restore MySQL a přístupnost na hostingu.


## Latest implementation — verified archive & complete admin HTTP smoke (2026-11-14)

**IMPLEMENTED (source and isolated test verified):** independent archive verifier
(`scripts/verify_archive.py`) checks every file against a complete SHA-256
manifest, rejects duplicate/unsafe paths, symlinks and leaked secret filenames,
and differentiates source packages from vendor-inclusive deployment bundles.
The source and production builders invoke the verifier rather than relying only
on ZIP CRC checks. A dedicated regression suite covers failures and successful
packages. CI includes a separate source ZIP reproducibility/integrity gate.

**IMPLEMENTED (integration test code; runtime execution BLOCKED locally):**
`http-smoke.py` now tests the real admin CSRF, cookie/session and login/logout
roundtrip and rejected editorial writes. Its test credentials are random and
restricted to an explicitly disposable `APP_ENV=testing` process; CI and the
production builder prepare ephemeral password hashes. Python transport-mock
unit tests verify the test harness asserts each crucial boundary, but **do not
substitute for running against CakePHP**.

**RELEASE BLOCKERS UNCHANGED:** obtain genuine PHP 8.5.12+, Composer `vendor/`
and reviewed lockfile, pass full PHPStan max/strict + PHPUnit + MySQL migration
and HTTP tests, and personally authorize every private-derived description.
No automatic approvals or security claims are inferred from the tests above.
