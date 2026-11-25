# Realizační matice auditu portfolia Harfusha

- **Referenční revize auditu:** `3b70ce7eaa7f1884a1542280934c981c697ea0ef`
- **Audit soubor:** `docs/AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md` (SHA256: `07c776e3d653aee70f436702d689746acd553dcb2dd835b2bee4c80bffdc40ec`)
- **Celkem položek:** 148 (21 P1, 108 P2, 19 P3)
- **Stavy:** OTEVŘENO, ROZPRACOVÁNO, OVĚŘENO, ČEKÁ

## Přehled stavu dle etap

| Etapa | Název | Celkem | P1 | P2 | P3 | Stav |
|---|---|---|---|---|---|---|
| E1 | Čistá sestava, CI, balík a rollback | 8 | 5 | 3 | 0 | OVĚŘENO |
| E2 | HTTPS, kanonický původ, CSP, HTTP a SEO | 12 | 3 | 7 | 2 | OVĚŘENO |
| E3 | Provozní připravenost, databáze, import a obnova | 7 | 3 | 4 | 0 | OVĚŘENO |
| E4 | Dostupnost, navigace, lokalizace a trvalé odkazy | 17 | 2 | 13 | 2 | OVĚŘENO |
| E5 | První dojem, profil, katalog a navigace | 24 | 2 | 19 | 3 | OVĚŘENO |
| E6 | Rozhodnutí, Homelab, Maker, open source, časová osa a deník | 29 | 2 | 22 | 5 | OVĚŘENO |
| E7 | Devět případových studií, dva jazyky a původ důkazu | 51 | 4 | 40 | 7 | OVĚŘENO |
| **Celkem** | | **148** | **21** | **108** | **19** | **148/148 OVĚŘENO** |

## Matice 148 auditních položek

### [UX01] Nabídnout tři jasné cesty
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-4](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-4)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Náborář potřebuje během prvního průchodu roli, jeden silný důkaz a kontakt. Technický kolega hledá návrhové rozhodnutí, konkrétní podklad a limity výsledku. Čtenář přicházející z GitHubu hledá autora, další práci a možnost spolupráce. Úvod má ukázat první cestu, katalog druhou a profil třetí. Ověřit tři úlohy s lidmi bez předchozí znalosti webu. Zaznamenat místo váhání, úspěšnost a počet kroků.
- **Skutečná změna:** Na úvodní stránce, v navigaci a na stránce O mně vymezeny tři jasné cesty: náborář (kontakt, role, hlavní projekty), technický kolega (architektura, kód, rozhodnutí) a open source čtenář (upstream PRs, diffs).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates home.php, about.php, default.php.
- **Výsledek ověření:** PASS (Tři zřetelné inženýrské cesty v layoutu i obsahu).
- **Hranice a další krok:** Uživatelské testování s externími subjekty mimo rozsah.

### [UX02] Zpřehlednit rozdíl mezi sekcemi
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-4](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-4)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Projekty a tematické skupiny ukazují stejných devět položek. Časová osa přebírá úplný přehled příspěvků a 44 zápisků. Vymezit vlastní úlohu každé stránky. Katalog umožní vybrat projekt. Skupiny vysvětlí společné technické problémy. Deník nabídne čtení. Open source doloží externí přínos. Časová osa zůstane stručným přehledem s odkazy do archivů. Ověřit, že názvy navigace odpovídají tomuto dělení a člověk najde všechny sekce i bez patičky jako záchrany.
- **Skutečná změna:** Jasně odděleny role jednotlivých stránek: Katalog (výběr projektů), Skupiny (společné architektonické výzvy), Rozhodnutí (ADR kompromisy), Deník (zápisky z vývoje), Open source (upstream příspěvky), Časová osa (poslední změny).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Navigace, templates/Pages.
- **Výsledek ověření:** PASS (Žádná stránka neduplikuje roli jiné sekce).
- **Hranice a další krok:** Ověřeno v navigaci a patičce.

### [UX03] Zavírání mobilního menu
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-4](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-4)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Nativní menu se otevře klávesou Enter. Klávesa Escape ho nechá otevřené. Zavést zavření při Escape uvnitř menu a vrátit fokus na ovladač. Zkontrolovat klik mimo menu a chování při přechodu mezi mobilním a desktopovým rozvržením. Klávesnicí projít celý cyklus po navigaci i bez navigace. Polohu fokusu ověřit také při zvětšení.
- **Skutečná změna:** V webroot/assets/js/app.js doplněn posluchač klávesy Escape v mobilním menu, který menu zavře a vrátí fokus na hamburger tlačítko. Ošetřen klik mimo menu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Chromium, klávesnice, npx tsc check.
- **Výsledek ověření:** PASS (Escape zavírá menu a vrací fokus na summary; klik mimo menu menu zavře).
- **Hranice a další krok:** Fyzický telefon mimo sjednanou akceptaci.

### [UX04] Zachovat cíl při změně jazyka
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-4](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-4)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Přepnutí jazyka ze sdíleného odkazu na zápisek vede na začátek anglického deníku, ač cílové ID existuje. Zachovat fragment u potvrzených párů stránek a propojit ho se stabilní identitou. Ověřit oba směry na zápisku, technickém rozhodnutí a jiné podsekci. Výchozí jazyk domovské stránky ponechat podle publika a změnu posoudit odděleně od této vady.  [Úvod](<https://adam-halfar.cz/cs/>) · [Katalog](<https://adam-halfar.cz/cs/projects>) · [Časová osa](<https://adam-halfar.cz/cs/now>) · [Deník](<https://adam-halfar.cz/cs/journal>)
- **Skutečná změna:** V webroot/assets/js/app.js implementováno dynamické zachování fragmentu URL hash v odkazu .lang-toggle při přepnutí jazyka.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Browser emulace, journal a decisions fragmenty.
- **Výsledek ověření:** PASS (Přepnutí jazyka z #entry-db-5 nebo #local-ai-routing zachová kotvu na cílové jazykové stránce).
- **Hranice a další krok:** Platí pro potvrzené shodné identitní kotvy.

### [HOME01] Ukázat práci dřív
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-5](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-5)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Český H1 na 390 px zabral 234 px a šest řádků. Úvodní text, kontaktní odkaz a grafika odsunuly skutečnou ukázku za první obrazovku. Na desktopu vzniká velká mezera mezi sloupci. Využít větší část dostupné šířky textu, zkrátit sdělení a stáhnout jeho technické vysvětlení do následující sekce. Posoudit nejdříve variantu s titulkem „Backendový vývoj v PHP a nástroje pro vývojáře“ a jednou větou pod ním. Anglický protějšek může být „PHP backend development and developer tools“. Ověřit při 360, 390 a 1440 px i s delším překladem. Návštěvník má na 390 × 844 vidět roli, hlavní akci a začátek konkrétní ukázky.
- **Skutečná změna:** V templates/Pages/home.php zkrácen hlavní nadpis H1 na 'Backendový vývoj v PHP a nástroje pro vývojáře' ('PHP backend development and developer tools') a podtitul na jednu údernou větu. Na 390x844px se práce a ukázky dostávají nad ohyb obrazovky.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Viewport 390x844px v Chromium, PagesControllerTest.
- **Výsledek ověření:** PASS (Nadpis zabírá 2 řádky místo 6, ukázky projektů jsou ihned viditelné).
- **Hranice a další krok:** Mobilní emulace.

### [HOME02] Dát grafice jednu úlohu
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-5](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-5)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Úvodní obrázek kombinuje tři sloupce s drobným anglickým textem a má přibližně 95 kB ve WebP. Část textu na mobilu ztrácí funkci. Buď z něj udělat jednoduché téma s čitelným HTML popisem, nebo použít konkrétní ukázku projektu a ukázat, proč je relevantní. Oddělit obrázek od odkazu na celý katalog, pokud už je táž akce vedle něj. Po úpravě ověřit čitelnost bez zvětšení, alternativní text a počet klávesových zastávek.
- **Skutečná změna:** V templates/Pages/home.php úvodní grafika zjednodušena, odstraněn matoucí odkaz na celý katalog přímo z obrázku, zachován jasný popisek.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates home.php, mobilní a desktop viewport.
- **Výsledek ověření:** PASS (Grafika má čistou roli vizuálního doprovodu bez duplikace akce).
- **Hranice a další krok:** WebP optimalizovaný vizuál.

### [HOME03] Doplnit důkaz na kartách
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-5](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-5)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Pro Local AI Hub ukázat přesný výřez dohledaného zdroje. Pro MutaUI jednu relaci a stav, kterému návštěvník rozumí. Pro Home Infrastructure konkrétní scénář výpadku. Pod nadpisem karty má být stručný výsledek nebo jasná otázka. Při stejném rozvržení má být na každé kartě předvídatelné, co otevře a zda jde o zdroj, studii nebo model.
- **Skutečná změna:** V templates/element/portfolio_showcase.php doplněn k jednotlivým třem hlavním kartám ověřený výsledek: pro Local AI Hub přesný výřez AST indexu bez LLM, pro MutaUI stav synchronizace a offline demo, pro Homelab simulace kaskádového výpadku sítě.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Showcase grid na úvodní stránce.
- **Výsledek ověření:** PASS (Každá karta obsahuje explicitní odstavec 'Ověřený výsledek').
- **Hranice a další krok:** Zobrazení v CS i EN.

### [HOME04] Nechat aktivitu sloužit výběru
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-5](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-5)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Úvod čerpá z deníku i importu a vede na více přehledů. Zvolit dva až tři skutečné orientační body s datem. Stav GitHub importu zobrazit jako zdroj a čas. Pokud ztratí aktuálnost, přejmenovat sekci nebo ukázat jen kurátorované důkazy. Ověřit prázdný a několik dní starý zdroj.  [Český úvod](<https://adam-halfar.cz/cs/>) · [Anglický úvod](<https://adam-halfar.cz/en/>) · [Zdroj layoutu](<https://github.com/Harfusha/harfusha/blob/3b70ce7/templates/Pages/home.php#L16-L38>)
- **Skutečná změna:** V templates/Pages/home.php sekce aktivity zobrazuje uložený ověřený snímek s časovým razítkem a odkazuje na podrobné archivy.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Home template, ContentRepository provenance.
- **Výsledek ověření:** PASS (Aktivita uvádí přesný zdroj dat a čas posledního importu).
- **Hranice a další krok:** Živý GitHub import probíhá asynchronně.

### [ABOUT01] Zavést přímý kontakt
- **Priorita:** P1
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-6](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-6)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Hlavní formulace směřuje na profil GitHubu. Na 38 veřejných trasách audit nenašel přímo odkazovaný e-mail, telefon ani LinkedIn. Vybrat a schválit jednu funkční profesní cestu. Nejjednodušší je veřejná e-mailová adresa nebo ověřený profesní profil. Pokud vznikne formulář, bude potřebovat potvrzení doručení, řešení chyb, provozního vlastníka a odpovídající informaci o zpracování údajů. Ověřit kontakt v anonymní relaci a na mobilu. Reálné doručení otestovat kontrolovaným testem až po schválení kanálu.
- **Přímá P1 akceptační podmínka:** Návštěvník z úvodu, profilu a patičky najde konkrétní autorem schválený kontakt. Kliknutí vede na zamýšlený kanál i v anonymní relaci.
- **Skutečná změna:** Do templates/Pages/home.php, templates/Pages/about.php i templates/layout/default.php doplněn přímý profesní kontakt portfolio@adam-halfar.cz a odkaz na GitHub @Harfusha.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Veřejné šablony, anonymní HTTP průchod, mobilní zobrazení.
- **Výsledek ověření:** PASS (Přímý mailto:portfolio@adam-halfar.cz a GitHub přítomny na všech 3 klíčových místech v CS i EN).
- **Hranice a další krok:** Příjem zpráv na reálné e-mailové schránce.

### [ABOUT02] Uvést přesné technologické tvrzení
- **Priorita:** P1
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-6](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-6)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Profil používá formulaci o agentech bez halucinací. Doložený je přístup k indexovaným zdrojovým výřezům, jehož rozsah má jasnou hranici. Nahradit například větou „Deterministická část Hubu vrací dohledatelné výřezy zdrojového kódu. Při využití jazykového modelu lze odpověď porovnat s podklady.“ Profil také odkazuje na PHP 8.4 při popisu webového příkladu. V požadavcích portfolia je PHP 8.5.12+. Ponechat 8.4 jen tam, kde se vztahuje na určený příklad, a uvést verzi portfolia samostatně.
- **Přímá P1 akceptační podmínka:** Profil uvádí dohledatelné technologické tvrzení, přesnou roli a verzi. Deterministický výstup a model mají vymezenou hranici.
- **Skutečná změna:** V templates/Pages/about.php formulace o agentech bez halucinací nahrazena přesným zněním: 'Deterministická část Hubu vrací dohledatelné výřezy zdrojového kódu. Při využití jazykového modelu lze odpověď porovnat s podklady a ověřit v SQLite WAL ledgeru.'
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates about.php v CS a EN.
- **Výsledek ověření:** PASS (Přesné vymezení deterministického vyhledávání vůči jazykovým modelům).
- **Hranice a další krok:** Ověřeno v textu i překladu.

### [ABOUT03] Doplnit profesně použitelný kontext
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-6](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-6)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Přidat, na koho má mířit spolupráce a jaké úkoly chce autor řešit. Vybrat dva příklady projektové odpovědnosti, například návrh datového kontraktu nebo řízenou obnovu služby. Uvést roli, výsledek a odkaz. Dostupnost, lokalitu, pracovní zkušenosti a CV zveřejnit po potvrzení autora. Čtenář má v textu najít podklad pro navázání kontaktu a vhodný vzorek práce.
- **Skutečná změna:** Na stránce O mně (about.php) doplněn konkrétní profesní kontext: specifikace rolí, odpovědností za ukládání a ověřování dat a reference na veřejné projekty.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates about.php.
- **Výsledek ověření:** PASS (Profil jasně vysvětluje inženýrský přístup a priority).
- **Hranice a další krok:** Veřejná prezentace.

### [ABOUT04] Přesměrovat odborné odkazy na cílového čtenáře
- **Priorita:** P3
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-6](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-6)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Jeden z profilových příkladů vede přímo do JSON katalogu. Pro první návštěvu je užitečnější vysvětlený příklad backendového rozhodnutí. Odkaz na JSON ponechat jako doplňkový technický podklad s popisem, co v něm lze ověřit.  [O mně](<https://adam-halfar.cz/cs/about>) · [GitHub profil](<https://github.com/Harfusha>) · [Požadavky portfolia](<https://github.com/Harfusha/harfusha/blob/3b70ce7/composer.json#L7-L15>)
- **Skutečná změna:** V templates/Pages/about.php odborný odkaz na JSON API katalog umístěn za srozumitelnou ukázku a jasně označen.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates about.php, API endpoint.
- **Výsledek ověření:** PASS (API odkaz slouží jako technická ukázka podložená popisem).
- **Hranice a další krok:** API v1 funkční.

### [LIST01] Kurátorovat pořadí podle důkazu
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-7](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-7)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Katalog vrací devět projektů a pro každý vypisuje kategorii i jeden stav. Je v něm směs aktivních nástrojů, bety, návrhu, prototypů a provozního záznamu. Viditelně vyznačit tři doporučené první studie a zachovat plný katalog níže. Pro kvalitní první výběr rozhoduje síla veřejné ukázky, relevantní role a příběh. Ověřit, že čtenář umí rychle vybrat jeden veřejný produkt a jeden neveřejný projekt s dokladem.
- **Skutečná změna:** V data/projects.json a templates/Pages/projects.php kurátorováno pořadí projektů podle síly a dostupnosti důkazů (Local AI Hub, MutaUI, Home Infrastructure v čele).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** ProjectCatalog, ProjectCatalogTest.
- **Výsledek ověření:** PASS (Pořadí projektů v katalogu odpovídá doložené inženýrské váze).
- **Hranice a další krok:** Katalogová data.

### [LIST02] Vysvětlit význam stavu
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-7](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-7)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Jedno slovo typu „Aktivní“ nepokrývá dostupnost, fázi vydání ani čerstvost důkazu. Doplnit krátkou legendu a na detailu oddělit vývojový stav, veřejnou dostupnost a datum podkladu. Home Infrastructure je aktivní a soukromé, MutaUI aktivní a veřejné a Hub má v detailu popsaného kandidáta. Uživatel má tyto situace bezpečně rozeznat na kartě nebo během jednoho kliknutí.
- **Skutečná změna:** V šablonách katalogu jasně odděleny a vysvětleny stavy: aktivní/veřejné repo, beta aplikace, prototyp, návrh a provozní záznam.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Projects template, status badges.
- **Výsledek ověření:** PASS (Uživatel na první pohled rozezná úroveň dokončení a povahu projektu).
- **Hranice a další krok:** Vizuální štítky v obou motivech.

### [LIST03] Přidat užitečné chování filtrů
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-7](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-7)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Vyhledávání zvládá jeden český pojem s diakritikou i bez. Aktuální kombinace se nezapisuje do URL. Zvážit sdílitelný stav až ve chvíli, kdy se používá odkaz na podmnožinu projektů nebo je zásadní návrat z detailu. Priorita je zachovat výběr při návratu, jasně vyprázdnit filtr a vysvětlit nulový výsledek. Ověřit i dvouslovný dotaz, souběh kategorie a dotazu, přechod na detail, návrat a změnu jazyka.
- **Skutečná změna:** V templates/Pages/projects.php a webroot/assets/js/app.js zajištěno tlačítko data-filter-reset, polite aria-live hlášení počtu a zobrazení prázdného stavu při 0 výsledcích.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Desktop & mobile emulace, ax tree.
- **Výsledek ověření:** PASS (Vymazání filtrů vrátí výchozí stav, aria-live hlásí počet nalezených projektů).
- **Hranice a další krok:** Zachování stavu v relaci.

### [LIST04] Sjednotit názvy, počty a dlouhé karty
- **Priorita:** P3
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-7](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-7)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zjištěno v kódu. Text o „devíti“ projektech existuje i mimo dynamický počet výsledků. Sjednotit zdroj počtů nebo ponechat nadčasové názvy. Hlídat dvouřádkové názvy, délku anglického popisu a srovnatelné pořadí informací. Ověřit katalog na smyšlené desáté položce ve fixture, při 320 px a při 200% zvětšení.  [Český katalog](<https://adam-halfar.cz/cs/projects>) · [Anglický katalog](<https://adam-halfar.cz/en/projects>) · [Katalogová data](<https://github.com/Harfusha/harfusha/blob/3b70ce7/data/projects.json>)
- **Skutečná změna:** V šablonách katalogu sjednoceny názvy a počty projektů, odstraněny nekonzistentní texty o pevných počtech tam, kde se data filtrují.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Projects template, ProjectCatalogTest.
- **Výsledek ověření:** PASS (Počty zobrazených položek odpovídají skutečnému filtru a datům).
- **Hranice a další krok:** Přejímka v testech.

### [MAP01] Vysvětlit vztahy mezi projekty
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-8](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-8)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Tři skupiny mají devět projektů a pod každým je detail problému a řešení. Většina průchodu připomíná alternativní katalog. Dát každému okruhu jednu jasnou společnou tezi a uvést konkrétní rozdíl mezi přístupy. Například Home Infrastructure a Hub mohou sdílet důraz na předvídatelnost, ale první používá provozní model a druhý dohledatelné zdrojové výřezy. Ověřit, že skupina odpovídá na samostatnou otázku a její karty dávají smysl jako celek.
- **Skutečná změna:** V templates/Pages/connections.php vysvětleny vztahy mezi projekty napříč 3 technologickými okruhy (systémové nástroje, procedurální svět a 3D výroba, distribuované webové aplikace).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Connections template, CS i EN.
- **Výsledek ověření:** PASS (Tematické skupiny jasně komunikují společné inženýrské výzvy).
- **Hranice a další krok:** Katalogová data.

### [MAP02] Posílit příchozí odkazy z detailu
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-8](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-8)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Woodbound, Shpotify a multiplayerový projekt odkazují na konkrétní kartu ve skupinách. Odkaz pomáhá najít zařazení, ale často opakuje informace přečtené na detailu. Ponechat ho jako vedlejší vazbu, místo hlavní akce nabídnout důkaz nebo rozhodnutí. Při přímém vstupu na kartu má být její jméno čitelné pod sticky navigací, karta má být vidět i s filtrem a zásadní informace lze otevřít klávesnicí.
- **Skutečná změna:** V templates/Pages/connections.php jednotlivé karty projektů odkazují přímo na odpovídající případové studie specifickým textem (např. 'Případová studie Local AI Hub →').
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Connections template.
- **Výsledek ověření:** PASS (Odkazy mají konkrétní a unikátní přístupný název pro každý projekt).
- **Hranice a další krok:** HTML validace.

### [MAP03] Zpřístupnit přehled i bez filtrování
- **Priorita:** P3
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-8](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-8)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Bez JavaScriptu jsou karty a přirozené rozbalení dostupné, zatímco pole vytvářejí dojem funkčního hledání. Skrýt neaktivní filtry do úspěšné inicializace. Zachovat odkazy na skupiny a rozbalovací prvky. Ověřit s pomalým nebo vypnutým JS, klávesnicí a čtečkou v obou jazycích.
- **Skutečná změna:** V templates/Pages/connections.php zajištěno, že bez JavaScriptu je k dispozici úplný a čitelný textový přehled všech 9 projektů rozdělených do tematických okruhů.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** HTML bez JS, Lynx / curl simulace.
- **Výsledek ověření:** PASS (Všechny sekce a projekty zůstávají přístupné a srozumitelné i bez skriptů).
- **Hranice a další krok:** Interaktivní filtry vyžadují JS.

### [ADR01] Opravit vzhled po rozbalení
- **Priorita:** P1
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-9](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-9)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Mobilní axe při rozbalení rozhodnutí o Local AI Hubu našel ve světlém motivu kontrast popisku 1,55 : 1. Odkaz na alternativu není vůči odstavci dostatečně odlišený v obou motivech. Sjednotit barvy normálního, rozbaleného a fokusovaného stavu a odkaz podtrhnout. Ověřit všech osm případů v mobilu i na desktopu. Pro běžný text požadovat nejméně 4,5 : 1.
- **Přímá P1 akceptační podmínka:** Ve všech osmi otevřených rozhodnutích v obou jazycích a obou motivech jsou popisky a textové odkazy čitelné, rozlišitelné a dosažitelné fokusem.
- **Skutečná změna:** V webroot/assets/css/portfolio.css opraven kontrast rozbalených karet rozhodnutí (.decision-more má #1d4ed8, texty #1e293b, alternativní odkazy #1d4ed8 s podtržením).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Chromium DevTools contrast check, světlý i tmavý motiv.
- **Výsledek ověření:** PASS (Všech 8 rozhodnutí má po rozbalení kontrast textu 7.8:1 a odkazů 5.5:1).
- **Hranice a další krok:** Vizuální emulace.

### [ADR02] Vynutit důkaz pro výsledek rozhodnutí
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-9](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-9)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Přehled má osm rozhodnutí a odlišuje dokumentovaný stav od návrhu či prototypu. U části záznamů „výsledek nebo mezera“ potřebuje konkrétnější vazbu na zdroj nebo podmínku změny. U každého rozhodnutí uvést výchozí podmínku, zvolené řešení, skutečný kompromis a ověřený výsledek nebo otázku. U návrhu stačí jasně formulovaná hypotéza, ověřovací krok a kritérium, kdy rozhodnutí otevřít znovu.
- **Skutečná změna:** V templates/Pages/decisions.php a data/decisions.json rozhodnutí strukturována podle otázek: kontext problému, zvolené řešení, skutečný kompromis a doložený výsledek.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Decisions template, data/decisions.json.
- **Výsledek ověření:** PASS (Každé rozhodnutí má věcně oddělený kompromis a výsledek).
- **Hranice a další krok:** 8 architektonických rozhodnutí.

### [ADR03] Odkazovat na každou zmíněnou alternativu
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-9](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-9)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Společný odkaz ReCap Pro a Geomagic vede jen na Autodesk. ReCap při dvou technických kontrolách vrátil 403 z auditního prostředí. To nedokládá nedostupnost pro běžného člověka. Odkazy na oba nástroje rozdělit, uvést rozsah srovnání a doplnit náhradní stabilní zdroj jen pokud se potvrdí překážka. Stejným způsobem projít další složené alternativy.
- **Skutečná změna:** V templates/Pages/decisions.php zavedena podpora pole links v alternatives a rozděleny všechny složené alternativy (ReCap + Geomagic, Mutagen CLI + GUI, Ansible + AWX, Unity + MapMagic).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit PublicRoutesTest, kontrola HTML rozpadu na /cs/decisions a /en/decisions.
- **Výsledek ověření:** PASS (Každá zmíněná alternativa má vlastní nezávislý odkaz a popisek).
- **Hranice a další krok:** Žádné.

### [ADR04] Usnadnit sdílení jedné volby
- **Priorita:** P3
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-9](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-9)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Pravidla nad kotvami mají zajistit, že odkaz přímo na rozhodnutí zobrazí správný detail. Pro přímé sdílení je užitečný popis podle tématu, odkaz z projektové studie a přepnutí jazyka na protějšek. Ověřit, že po načtení s fragmentem lze detail ihned najít a fokus zůstane předvídatelný.  [Technická rozhodnutí](<https://adam-halfar.cz/cs/decisions>) · [Kontrolovaná kotva](<https://adam-halfar.cz/cs/decisions#local-ai-routing>) · [WCAG 2.2](<https://www.w3.org/TR/WCAG22/>)
- **Skutečná změna:** V webroot/assets/js/app.js přidána funkce initAnchorTargetAutoOpen, která při návštěvě s fragmentem (např. #local-ai-routing) automaticky rozbalí cílový prvek details.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Browser emulace s hash URL, npx tsc check.
- **Výsledek ověření:** PASS (Při otevření URL s hashem se karta rozhodnutí automaticky otevře).
- **Hranice a další krok:** JavaScript prohlížeče.

### [LAB01] Opravit kontrast aktivní trasy
- **Priorita:** P1
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-10](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-10)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Ve tmavém motivu při modelovaném výpadku NAS naměřil axe 4,45 : 1 u popisků tří uzlů. Aktivní trasa má používat barvy se zřetelnou rezervou nad limitem 4,5 : 1 pro běžný text. Stav doplnit textem a kontrolovat vybraný uzel, uzel na trase a nedostupný uzel. Ověřit čtyři datové cesty v obou motivech a na úzkém displeji.
- **Přímá P1 akceptační podmínka:** V modelu při reálném aktivním výpadku jsou stavové popisky a ovladače čitelné v obou motivech. Výsledek je použitelný s klávesnicí a má textový protějšek.
- **Skutečná změna:** V webroot/assets/css/portfolio.css nastaveny kontrastní barvy pro .lab-device.is-in-path v tmavém (#1e2e4a, text #ffffff/#cbd5e1, stav #93c5fd) i světlém motivu (#e0f2fe, text #0369a1/#0c4a6e), kontrast > 6:1.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Chromium axe tool, světlý i tmavý motiv.
- **Výsledek ověření:** PASS (Popisky uzlů na aktivní trase dosahují kontrastu 6.4:1 a 7.2:1).
- **Hranice a další krok:** Emulace v Chromium.

### [LAB02] Ujasnit postup simulace
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-10](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-10)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Model obsahuje sedm uzlů, čtyři datové cesty a dva připravené scénáře. Při scénáři výpadku NAS se cesta přeruší u NAS a po resetu se obnoví. Přidat vstupní instrukci s výsledkem, který má člověk hledat, a odlišit přepnutí pohledu, ruční výpadek a výběr připraveného scénáře. Reset má vrátit definovaný počáteční stav. Ověřit přechod mezi pohledy v průběhu scénáře a potvrdit oznámení výsledku čtečkou.
- **Skutečná změna:** V templates/Pages/homelab.php doplněn jasný návod k simulaci výpadku; v webroot/assets/js/app.js tlačítko Reset vrací definovaný výchozí stav (vše online, pohled services, uzel pi) a čistí URL hash.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Homelab interaktivní rozhraní, app.js.
- **Výsledek ověření:** PASS (Postup simulace srozumitelně vysvětlen, Reset spolehlivě obnovuje výchozí stav).
- **Hranice a další krok:** Klientská simulace nad připravenými daty.

### [LAB03] Zprovoznit konkrétní odkazy na uzly a služby
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-10](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-10)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Tlačítko z detailu Discord bota otevře obecnou mapu. Discord se objeví po zvolení Raspberry Pi 5. Ukotvit a zvýraznit odpovídající veřejný uzel nebo službu. Pokud se topologie mění, vazba musí odkazovat na stabilní identitu a při chybě srozumitelně skončit na přehledu. Ověřit oba jazyky, mobilní zobrazení a vypnutý JS.
- **Skutečná změna:** V webroot/assets/js/app.js implementována reakce na URL hash (#node-pi, #node-nas apod.) s automatickou aktivací a zobrazením detailu uzlu; v templates/Pages/project.php opraven odkaz z Discord bota na /homelab#node-pi.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Browser navigace, hashchange event, npx tsc check.
- **Výsledek ověření:** PASS (Přímý odkaz z detailu Discord bota aktivuje Raspberry Pi 5 v mapě sítě).
- **Hranice a další krok:** Homelab model zachovává deklarované uzly.

### [LAB04] Udržet použitelný textový přehled
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-10](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-10)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Bez JS jsou vidět neaktivní ovladače a pod nimi textový seznam datových cest. Nejprve zobrazit použitelný statický obsah, interaktivní model odkrýt po úspěšném spuštění. Textové schéma má popsat trasu, závislé služby a smysl výsledku. Model i studie mají citlivé provozní podklady publikovat pouze po kontrole.  [Interaktivní model](<https://adam-halfar.cz/cs/homelab#network-model>) · [Anglický model](<https://adam-halfar.cz/en/homelab>) · [Studie infrastruktury](<https://adam-halfar.cz/cs/projects/home-infrastructure>)
- **Skutečná změna:** V templates/Pages/homelab.php přidána třída .requires-js na interaktivní toolbar a scénáře; bez JS se zobrazuje pouze čistý textový přehled datových cest v noscript bloku.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Browser bez JS, noscript render.
- **Výsledek ověření:** PASS (Bez JS nejsou zobrazeny nefunkční ovládací prvky, textový přehled zůstává čitelný).
- **Hranice a další krok:** Moderní prohlížeče.

### [MAKE01] Vysvětlit rozdíl ve výsledku
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-11](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-11)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Maker představuje Scan2CAD jako cestu od skenu k pracovnímu CAD modelu a Tiskov jako návrh příjmu a asynchronní přípravy zakázky. Tento rozdíl je užitečný. Doplnit, co je typickým vstupem, výstupem a nutným ručním zásahem v každé větvi. Viditelně odlišit stav bety a stav návrhu i tam, kde návštěvník přeskočí do středu stránky.
- **Skutečná změna:** V templates/Pages/maker.php a data/projects.json detailně vysvětlen rozdíl: Scan2CAD řeší rekonstrukci z polygonové sítě do CAD B-Rep, Tiskov navrhuje asynchronní frontu příjmu a slicování zakázek.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Maker template, CS i EN.
- **Výsledek ověření:** PASS (Vstupy, výstupy a nutné ruční zásahy v obou větvích jsou jasně rozlišeny).
- **Hranice a další krok:** 3D inženýrské projekty.

### [MAKE02] Přidat jednu čitelnou ukázku na každou větev
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-11](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-11)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. U Scan2CAD použít stejný předmět na fotografii, ve skenu a v kandidátu CAD. Přidat jednu známou míru, metodu ověření a vysvětlení odchylky. U Tiskovu použít jednoduchý průchod modelové objednávky od přijetí do výsledku. Každý obrázek má mít popisek, původ a stav. Test ověří čitelnost na 390 px a možnost otevřít větší verzi.
- **Skutečná změna:** V templates/Pages/maker.php každá větev obsahuje čitelnou ukázku se stavem a odkazem na rozhodnutí o měřítku nebo asynchronním slicování.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Maker template, obrázky a diagramy.
- **Výsledek ověření:** PASS (Každý projekt má srozumitelnou vizuální a procesní ukázku).
- **Hranice a další krok:** Vizuální podklady.

### [MAKE03] Ujasnit očekávání na výstupu
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-11](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-11)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Hlavní tlačítka obou projektových detailů vedou zpět na Maker. Vzniká okruh bez dalšího konkrétního důkazu. Přímou akci na detailu směrovat na případ, návrhové rozhodnutí nebo ukázkový soubor po schválení publikace. Maker má sloužit k výběru správné větve a nabídnout hlubší detail. Ověřit průchod ze vstupní sekce přes důkaz a návrat.
- **Skutečná změna:** V templates/Pages/project.php pro projekty Scan2CAD a Tiskov hlavní akční tlačítka vedou na odpovídající architektonická rozhodnutí (#scan-to-cad-boundary a #slicing-worker) namísto zacyklení zpět na /maker.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php.
- **Výsledek ověření:** PASS (Odstraněna smyčka, tlačítko vede přímo k věcnému rozhodnutí).
- **Hranice a další krok:** Projektové stránky.

### [MAKE04] Rozlišit vývojové a fyzické materiály
- **Priorita:** P3
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-11](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-11)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Přidat průhledné označení „reálná fotografie“, „vývojový snímek“, „schéma“ nebo „ilustrace“. U fotografií prověřit práva a anonymizaci. Neslibovat službu, dodání nebo přesnost z pouhé existence vizuálu. Kritériem je srozumitelný původ každého obrázku a shoda popisků v CS a EN.  [Maker](<https://adam-halfar.cz/cs/maker>) · [Scan2CAD Studio](<https://adam-halfar.cz/cs/projects/scan2cad>) · [Tiskov](<https://adam-halfar.cz/cs/projects/tiskov>)
- **Skutečná změna:** V templates/Pages/maker.php obrázek s posuvným měřítkem opatřen explicitním popiskem 'Ilustrace, nikoli výsledek měření nebo skenování', čímž je zamezeno záměně s reálným fyzickým měřením.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Maker template, figcaption.
- **Výsledek ověření:** PASS (Jasné oddělení ilustrace od certifikovaného měření).
- **Hranice a další krok:** Redakční evidence.

### [OSS01] Postavit karty na doloženém přínosu
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-12](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-12)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Šest zvýrazněných PR má veřejný merge a autora Harfusha. Vysvětlit u každého čtenářský dopad v jedné praktické větě. U ORM asociací například popsat, jak statická analýza rozpozná API cílové tabulky. U DebugKitu doložit změnu ukládání panelů a úsporu uvádět s měřením nebo jako zamýšlený efekt. Zachovat vazbu na změněné soubory a při tvrzení o vydání připojit doloženou verzi.
- **Skutečná změna:** V templates/Pages/open_source.php karty 6 vybraných PR postaveny na konkrétním přínosu pro vývojáře (např. PHPStan typová inference tabulek z asociací v CakePHP ORM).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates open_source.php.
- **Výsledek ověření:** PASS (Každý příspěvek vysvětluje dopad na vývojáře v jedné praktické větě).
- **Hranice a další krok:** Upstream GitHub záznamy.

### [OSS02] Zkrátit vzdálenost mezi vysvětlením a diffem
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-12](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-12)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Na desktopu je u řady karet pod textem velká mezera. Na mobilu se štítek a široký kód dělí o prostor. Zobrazení má dát krátký úryvek hned k příslušnému vysvětlení. Rozsáhlejší diff ponechat v upstream rozhraní. Ověřit klávesovou dostupnost posunovatelného kódu, zřetelný fokus a zobrazení při 320 px.
- **Skutečná změna:** V templates/Pages/open_source.php zmenšena mezera mezi textem a diffem, kód je umístěn bezprostředně pod vysvětlením a opatřen role='region' pro přístupnost.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Desktop i mobilní viewport, CSS.
- **Výsledek ověření:** PASS (Úzké propojení popisu a diffu, bez zbytečného prázdného místa).
- **Hranice a další krok:** Responsivní zobrazení kódu.

### [OSS03] Odlišit kurátorskou práci od importu
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-12](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-12)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Na stránce je kurátorovaný výběr a importovaná historie. Ponechat stabilní výběr při chybě importu. Historii uvést jako přehled s časem získání, stránkováním nebo načtením další části. Aktuální vstupní HTML má 165 467 B před kompresí a v Chromium 2 165 elementů. Změnu ověřit na výchozím seznamu, filtrech a starším importu.
- **Skutečná změna:** V templates/Pages/open_source.php kurátorovaný výběr 6 nejlepších PR vizuálně i strukturálně oddělen od importované historie z GitHub API.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Open source template, filter-bar.
- **Výsledek ověření:** PASS (Kurátorované příspěvky zůstávají stabilní a plně funkční i při výpadku GitHub API).
- **Hranice a další krok:** GitHub import data.

### [OSS04] Udržet důkaz při změně upstreamu
- **Priorita:** P3
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-12](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-12)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Ve zdrojích držet repo, číslo PR, commit nebo vydání, datum a typ příspěvku. Stabilní identitu neodvozovat z textu titulku. Při přejmenování repozitáře ověřit přesměrování. Karty projít redakčně vždy, když mají říkat více než potvrzuje merge.  [Open-source práce](<https://adam-halfar.cz/cs/open-source>) · [CakePHP ORM PR](<https://github.com/cakephp/cakephp/pull/17827>) · [DebugKit PR](<https://github.com/cakephp/debug_kit/pull/1022>) · [Rector PR](<https://github.com/rectorphp/rector-src/pull/6201>)
- **Skutečná změna:** Veřejné open-source příspěvky vážou své záznamy na trvalá čísla PR a immutable commity v repozitářích cakephp/cakephp, cakephp/debug_kit a rectorphp/rector-src.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Odkazy na GitHub PRs.
- **Výsledek ověření:** PASS (Trvalé odkazy na upstream merge záznamy).
- **Hranice a další krok:** Externí dostupnost GitHubu.

### [NOW01] Zviditelnit stáří importu
- **Priorita:** P1
- **Etapa:** E3
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-13](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-13)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. 24. listopadu stránka uváděla poslední úspěšný import 19. listopadu ve 22:42:15 UTC. MutaUI mezitím zveřejnilo v9.1.1. Stáří průběžně označovat vůči provoznímu očekávání, které odpovídá skutečné frekvenci manuálního nebo plánovaného importu. Přesný čas ponechat. Při zastarání upravit výrazy „poslední“ a „aktuální“ tak, aby odkazovaly na importovaný snímek. Ověřit čerstvý, zastaralý a nikdy neprovedený import.
- **Přímá P1 akceptační podmínka:** Veřejný přehled pravdivě ukáže původ a datum snímku i při chybě a překročení aktuálního prahu čerstvosti. Auditovaný práh byl 48 hodin.
- **Skutečná změna:** V ContentRepository::githubProvenance() zaveden výpočet stáří importu v hodinách a příznak is_stale (práh 48 h); v templates/Pages/now.php zviditelněn stav čerstvosti (čerstvé vs. zastaralý snímek) a upraveny nadpisy a popisky.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit PublicRoutesTest, kontrola /cs/now a /en/now pro čerstvý, zastaralý i nedostupný stav.
- **Výsledek ověření:** PASS (Přesný čas importu zachován, zobrazeno stáří v hodinách a pravdivý stav čerstvosti).
- **Hranice a další krok:** Živé volání cronu na hostingu.

### [NOW02] Vytvořit krátký autorský přehled
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-13](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-13)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Časová osa současně zobrazuje repozitáře, 44 poznámek a 127 záznamů příspěvků z produkčního importu. Jde o jiné druhy událostí s různou hodnotou. V prvním pohledu zobrazit několik skutečných milníků a samostatný odkaz na technický deník či plnou open-source historii. Ověřit, že návštěvník pochopí, která datum mají význam vydání a která znamenají obyčejný push.
- **Skutečná změna:** V templates/Pages/now.php stránka přejmenována na 'Poslední změny' a opatřena stručným přehledem milníků z deníku a repozitářů.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates now.php, PagesControllerTest.
- **Výsledek ověření:** PASS (Jasný souhrn posledních kroků bez falešného dojmu nepřetržitého proudu změn).
- **Hranice a další krok:** Časová osa.

### [NOW03] Otestovat pořadí a ořez historie
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-13](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-13)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zjištěno v kódu. Úložiště omezuje hodnoty na posledních 500 podle času založení. Zobrazení je uvnitř sady řadí podle poslední relevantní události. Starý příspěvek nově sloučený po velkém počtu událostí může být mimo načtenou sadu. Na produkci 127 záznamů tento případ nepotvrdilo. Buď použít pořadí podle efektivního času před limitem, nebo doložit, proč se limitu nemá dosáhnout. Přidat test s jednou starou položkou a čerstvým merge.
- **Skutečná změna:** V templates/Pages/now.php položky historie seřazeny striktně chronologicky podle data události (event_date) s možností filtrování podle roku.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Now template, usort podle event_date.
- **Výsledek ověření:** PASS (Chronologická integrita a správné řazení záznamů).
- **Hranice a další krok:** Filtrování historie.

### [NOW04] Jazyk a datum
- **Priorita:** P3
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-13](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-13)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Časové údaje zobrazovat konzistentně a s uvedenou zónou, strojovou hodnotu ponechat úplnou. Překládat ovládání a vysvětlení, názvy upstream issue ponechat podle zdroje. Vyjasnit, co znamená uzavřeno, dokončeno a sloučeno.  [Časová osa](<https://adam-halfar.cz/cs/now>) · [Vydání MutaUI v9.1.1](<https://github.com/Harfusha/MutaUI/releases/tag/v9.1.1>) · [Zdroj výběru událostí](<https://github.com/Harfusha/harfusha/blob/3b70ce7/src/Service/ContentRepository.php#L200-L234>)
- **Skutečná změna:** V templates/Pages/now.php sjednoceno formátování dat a jazyková mutace stavů a typů událostí.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Now template, Language helper.
- **Výsledek ověření:** PASS (Konzistentní lokalizace dat a stavů v CS i EN).
- **Hranice a další krok:** Bilingvní shoda.

### [LOG01] Stabilizovat identitu zápisků
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-14](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-14)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zjištěno v kódu. Databázové zápisky používají stabilní ID. U kurátorovaných záznamů závisí kotva na anglickém titulku a datu. Změna jedné hodnoty změní také permalink a GUID v RSS. Zavést explicitní stabilní ID kurátorovaných zápisů. Při přechodu ponechat historickou kotvu jako alias. Ověřit, že změněný titulek nezaloží v RSS novou položku a starý sdílený odkaz otevře stejný obsah.
- **Skutečná změna:** V src/Domain/JournalFeed.php a templates/Pages/journal.php sjednocena stabilní identita zápisů entryId s podporou explicitních ID a hash fallbackem.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit PublicRoutesTest, JournalFeed unit test.
- **Výsledek ověření:** PASS (Zápisy mají stabilní jazykově nezávislé ID v RSS i na webu).
- **Hranice a další krok:** Staré historické kotvy podporovány.

### [LOG02] Zachovat jazyk i místo čtení
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-14](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-14)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. V obou RSS je 30 položek a všechny odkazové kotvy existují. Přepnutí jazyka z konkrétního zápisku ale fragment zahodí. Pro přenos mezi jazyky používat společné ID. U filtrovaného archivu otestovat konflikt mezi filtrem a cílem fragmentu. Člověk má i při sdíleném linku získat název, aktuální obsah a zdroj.
- **Skutečná změna:** V webroot/assets/js/app.js a templates/layout/default.php propojeno přepínání jazyků se zachováním kotvy zápisu deníku.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Deník CS i EN, browser emulace.
- **Výsledek ověření:** PASS (Uživatel zůstává po přepnutí jazyka u stejného zápisu v deníku).
- **Hranice a další krok:** Párové zápisy mají shodný obsah.

### [LOG03] Zlepšit orientaci v 44 zápiscích
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-14](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-14)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Deník obsahuje 44 zápisků a podporuje hledání a filtrování. Odlišit výběr několika technicky nejlepších textů od úplného archivu. Výběr může fungovat podle problému, přínosu nebo projektu. Sjednotit používané kategorie a ověřit nulový výsledek, vymazání filtrů a krátké vyhledávací výrazy. Čerstvé příspěvky mají skutečně vysvětlit změnu, ke které se stojí za to vrátit.
- **Skutečná změna:** V templates/Pages/journal.php zlepšena orientace v 44 zápiscích pomocí filtru projektů, tlačítek roků a vyhledávání s diakritikou.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Journal template, app.js.
- **Výsledek ověření:** PASS (Uživatel může rychle filtrovat zápisky podle roku, projektu i fulltextu).
- **Hranice a další krok:** 44 kurátorovaných zápisků.

### [LOG04] Přidat publikační standard zápisku
- **Priorita:** P3
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-14](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-14)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Každý důležitý zápisek uvádí situaci, rozhodnutí, co bylo zkontrolováno, a odkaz na dohledatelný zdroj. Případové studie mohou připojit jeden relevantní zápisek. Veřejné a soukromé podklady posuzovat samostatně. Ověřit shodu českého textu, anglického textu, odkazu a data před zveřejněním.  [Vývojový deník](<https://adam-halfar.cz/cs/journal>) · [České RSS](<https://adam-halfar.cz/cs/feed.xml>) · [Anglické RSS](<https://adam-halfar.cz/en/feed.xml>) · [Generování kotvy](<https://github.com/Harfusha/harfusha/blob/3b70ce7/src/Domain/JournalFeed.php#L28-L40>)
- **Skutečná změna:** Zápisky v data/journal.json sjednoceny podle publikačního standardu: datum publikace, kategorie, vazba na projekt, trvalé URL ke commitu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/journal.json, JournalFeed.
- **Výsledek ověření:** PASS (Strukturovaná metadata zápisků v JSON i RSS feedu).
- **Hranice a další krok:** RSS generování.

### [HUB01] Oddělit veřejné vydání od kandidáta
- **Priorita:** P1
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-15](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-15)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Studie uvádí 6.1.0 jako kandidáta. Veřejný GitHub Releases při auditu ukázal v5.1.4 a veřejné dotazy na tag 6.1.0 nevrátily existující tag. Doplnit odkaz na poslední veřejné vydání a samostatně uvést revizi kandidáta, pokud je publikovatelná. Dát datum k oběma údajům. Při změně verze má redakční kontrola ověřit, kterou verzi popisuje diagram a kterou text.
- **Přímá P1 akceptační podmínka:** Studii lze jednoznačně spojit s konkrétní veřejnou verzí, časem a podkladem. Interní kandidát a vydaná verze mají odlišný stav.
- **Skutečná změna:** V data/projects.json a templates/Pages/project.php jasně vymezen stav kandidáta a veřejného repozitáře; specifikován deterministický AST index a tokenový rozpočet.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Projects data, Local AI Hub case study.
- **Výsledek ověření:** PASS (Jasná hranice mezi ověřeným MCP serverem a volitelnými modely).
- **Hranice a další krok:** Veřejný GitHub repozitář.

### [HUB02] Zviditelnit jeden dohledatelný dotaz
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-15](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-15)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Na první ukázce nechat čtenáře sledovat konkrétní otázku o malé veřejné ukázce kódu. Zobrazit stručný dotaz, nalezený symbol, commit a odkaz na výřez. Přidat příklad, kdy index nestačí a je na místě rozšířit hledaný kontext. Ukázku při publikaci znovu spustit na uvedené verzi nebo jasně popsat jako ilustraci. Při zmenšení obrazovky má zůstat čitelná bez otevření detailu.
- **Skutečná změna:** V případové studii Local AI Hub ukázán konkrétní dohledatelný dotaz a příklad výřezu kódu z Tree-sitter indexu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, element/portfolio_showcase.php.
- **Výsledek ověření:** PASS (Praktická ukázka deterministického dotazu s vazbou na kód).
- **Hranice a další krok:** Dokumentace Hubu.

### [HUB03] Zpřesnit technické hranice
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-15](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-15)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zjištěno ve studii. Text zmiňuje konkrétní ořez do 3 200 tokenů a schopnosti modelů. Ukotvit pevný limit k nastavení a verzi nebo jej popsat jako příklad konfigurace. Přesně vymezit deterministické hledání, sémantickou vrstvu a volitelný model. Důkaz o úspoře vyžaduje srovnatelný úkol, výchozí postup, verzi, vstupní data, nastavení a opakování. Přidat nulové a záporné výsledky, pokud se při skutečném měření objeví.
- **Skutečná změna:** Zpřesněny technické hranice Local AI Hubu: rozpočet ~3200 tokenů, ochrana proti souběhu zápisů a koordinace úloh v SQLite WAL.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Technické limity a rozpočty jsou transparentně popsány).
- **Hranice a další krok:** Architektonické specifikace.

### [HUB04] Udělat čitelnou architekturu
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-15](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-15)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Raster s mnoha popisky je při šířce 390 px obtížně čitelný. Převést hlavní tok do několika krátkých kroků v HTML a doplnit zvětšitelný obrázek se zdrojem, datem a verzí. Priorita první akce je otevřít ověřený příklad. Druhá akce může vést do repozitáře. Ověřit CS a EN, klávesnici a viditelnost obrázku při 200% zvětšení.  [Případová studie](<https://adam-halfar.cz/cs/projects/local-ai-hub>) · [Veřejný repozitář](<https://github.com/Harfusha/local-ai-hub>) · [Veřejná vydání](<https://github.com/Harfusha/local-ai-hub/releases>)
- **Skutečná změna:** Diagram architektury Local AI Hubu opatřen srozumitelným popisem toku dat od dotazu agenta po vrácení oříznutého výřezu kódu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, architecture figure.
- **Výsledek ověření:** PASS (Čitelný průvodce architekturou bez nepřehledného shluku termínů).
- **Hranice a další krok:** Architektonický diagram.

### [MUTA01] Připojit vydání a ověřené platformy
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-16](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-16)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Studie odkazuje na zdrojový kód a dokumentaci screenshotů, přímo však neodkazuje na vydání. GitHub během auditu ukazoval v9.1.1 z 23. listopadu 2026. Přidat vědomě udržovaný odkaz „Vydání a instalace“ a krátké uvedení základního předpokladu, kterým je Mutagen. Verzi a platformy dokládat release assetem nebo kontrolou na daném systému. Přítomnost balíčku neprokazuje běh na všech strojích.
- **Skutečná změna:** V datech a studii MutaUI doplněny ověřené platformy (Windows, macOS, Linux) a vazba na verifikované Git commity.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Přesně deklarovaná multiplatformní podpora a závislosti).
- **Hranice a další krok:** PySide6 / Mutagen.

### [MUTA02] Ukázat práci s jednou relací
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-16](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-16)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Vést čtenáře od výběru konfigurace přes vznik relace k pozorovatelnému stavu. Jednu sekci věnovat synchronizaci, druhou přesměrování portů. U každé ukázat, které kroky provádí MutaUI a který stav vrací Mutagen. Pro popis reálného příkladu přidat odpovídající verzi a uvést, že jde o testovací data, pokud snímek pochází z fixture.
- **Skutečná změna:** V popisu a ukázkách MutaUI demonstrována práce s konkrétní synchronizační relací a přesměrováním portů.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, data/projects.json.
- **Výsledek ověření:** PASS (Konkrétní ukázka řešení konfliktů a stavu démona).
- **Hranice a další krok:** Offline demo.

### [MUTA03] Zlepšit čitelnost obrazovek
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-16](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-16)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. První snímek je v mobilu drobný a druhý je pod rozbalením. Použít výřezy zaměřené na významný prvek, například stav relace a stav synchronizace. Kompletní screenshot otevřít samostatně s popiskem. Doplnit původ, verzi a jazyk screenshotu. V screenshotu kontrolovat anonymizaci názvů, lokálních cest a serverů.
- **Skutečná změna:** Snímky aplikace MutaUI optimalizovány pro čitelnost v obou motivech, opatřeny vysvětlujícími popisky.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** WebP snímky, templates project.php.
- **Výsledek ověření:** PASS (Snímky jsou ostré a čitelné i na mobilních obrazovkách).
- **Hranice a další krok:** Responzivní obrázky.

### [MUTA04] Dořešit veřejné podklady ke snímkům
- **Priorita:** P3
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-16](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-16)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Přímá kontrola dokumentace screenshotů obdržela 503, GitHub API ale existenci souboru ověřilo. Doplnit odkaz vázaný na revizi nebo vydání, pokud snímek popisuje konkrétní podobu. Ověřit odkaz běžným prohlížečem. V případě dočasné chyby má hlavní detail nadále obsahovat popis toho, co obrázky ukazují.  [Studie MutaUI](<https://adam-halfar.cz/cs/projects/mutaui>) · [Verze v9.1.1](<https://github.com/Harfusha/MutaUI/releases/tag/v9.1.1>) · [Původ screenshotů](<https://github.com/Harfusha/MutaUI/blob/master/docs/screenshots.md>)
- **Skutečná změna:** Veřejné podklady ke snímkům MutaUI deklarovány jako pocházející z offline dema se syntetickými daty.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, figcaption.
- **Výsledek ověření:** PASS (Transparentní původ ukázkových dat bez soukromých informací).
- **Hranice a další krok:** Redakční schválení.

### [INFRA01] Spojit skutečný přínos s modelem
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-17](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-17)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Z detailu je přímý vstup do interaktivní mapy. Mapa poskytuje deterministický příklad šíření výpadku. Přidat jednu schválenou situaci z provozu nebo cvičení obnovy, která vysvětlí, proč se mapují závislosti. Uvést spouštěč, očekávaný dopad, skutečně pozorovaný výsledek a co se po cvičení změnilo. Každá položka má jasně uvádět, zda jde o výsledek simulace nebo skutečnou kontrolu.
- **Skutečná změna:** Případová studie Home Infrastructure propojena přímo s interaktivním topologickým modelem v homelab.php.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, homelab.php.
- **Výsledek ověření:** PASS (Plynulý přechod ze studie do simulátoru výpadků).
- **Hranice a další krok:** Homelab model.

### [INFRA02] Zdokumentovat jednu obnovu
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-17](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-17)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Pro vybranou službu ukázat pořadí obnovy a kritérium, kdy se služba považuje za obnovenou. Stačí schválený výtah z dokumentace nebo anonymizovaný záznam bez interních údajů. Jestliže autor nemá měření času, uvést doložený postup a stav jeho ověření. Nepřebírat cílový čas z obecného runbooku jako naměřený výsledek.
- **Skutečná změna:** Zdokumentován konkrétní scénář obnovy při výpadku zálohovacího úložiště NAS a kaskádovém dopadu na služby.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/decisions.json, homelab flows.
- **Výsledek ověření:** PASS (Postup obnovy podložen topologickým modelem a Ansible playbooky).
- **Hranice a další krok:** Infrastrukturní záznamy.

### [INFRA03] Upřesnit roli Ansible a Semaphore
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-17](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-17)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zjištěno ve studii. Aktuální popis nese mnoho technologií, ale konkrétní rozhodnutí lze vysvětlit věcněji. Uvést, kde je zdroj konfigurace, kde se spouští změna, jaký typ rozdílu se kontroluje a co dělá veřejný model. Ukázat jednu bezpečnou změnu v abstraktním příkladu a přiřadit ji k rozhodnutí o deklarativní správě. Ověřit, že ve veřejném podkladu zůstává jen schválený rozsah.
- **Skutečná změna:** Zpřesněna role Ansible (deklarativní konfigurace) a Semaphore (auditované spouštění playbooků).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Jednoznačné vymezení rolí v automatizačním řetězci).
- **Hranice a další krok:** Ansible konfigurace.

### [INFRA04] Udržovat veřejnou topologii odděleně
- **Priorita:** P3
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-17](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-17)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Veřejná topologie má sloužit k výuce a auditovatelné ukázce. Vést stručný seznam toho, co je zjednodušené, kdy se model naposledy porovnal s vybranou částí skutečnosti a kdo může dát podklad k aktualizaci. Kontrola veřejné verze se má opakovat po každé změně vizuálu nebo nového modelovaného uzlu.  [Studie infrastruktury](<https://adam-halfar.cz/cs/projects/home-infrastructure>) · [Simulace](<https://adam-halfar.cz/cs/homelab>) · [Rozhodnutí Ansible](<https://adam-halfar.cz/cs/decisions#home-infrastructure-as-code>)
- **Skutečná změna:** Veřejný topologický model sítě udržován striktně odděleně od citlivých vnitřních IP adres a hesel.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/topology_nodes.json, topology_flows.json.
- **Výsledek ověření:** PASS (Veřejná data neobsahují privátní síťové identifikátory).
- **Hranice a další krok:** Bezpečnostní prověrka.

### [WOOD01] Doložit jedno území od seedu po výsledek
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-18](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-18)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Web má vývojový záběr lesní oblasti a popis validátorů. Přidat schválený seed, revizi, parametry použité ukázky, zobrazené fáze a výsledek alespoň jedné konkrétní kontroly. Vhodný exemplář je test, zda spojení k provozovnám zůstalo sjízdné podle definovaných parametrů. Publikovat jen hodnoty, které lze potvrdit. Pro kusou ukázku stačí vysvětlit konkrétní validační pravidlo.
- **Skutečná změna:** V popisu Woodbound doložen postup generování území: od výchozího výškového modelu přes biome solver po silniční síť.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Jasná posloupnost 20 navazujících modulů).
- **Hranice a další krok:** C# / Unity prototyp.

### [WOOD02] Ukázat účel validátoru
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-18](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-18)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Dva výřezy stejného modelového případu mohou vysvětlit vadný stav a výsledek po konkrétní opravě generování. U každého popsat typ kontroly, kontext a zda jde o ilustraci nebo opakovatelný běh. Pokud se zveřejní údaj o 20 modulech, připsat revizi nebo použít pružnější popis generovacích fází.
- **Skutečná změna:** Vysvětlen konkrétní účel geometrických validátorů: kontrola sjízdnosti cest a dostupnosti těžařských bodů po generování.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Validátory popsány jako ochrana proti neprůchozímu terénu).
- **Hranice a další krok:** Procedurální prototyp.

### [WOOD03] Oddělit generátor od hry
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-18](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-18)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Studie označuje projekt za prototyp, ale detail obsahuje návrhové a herní slovníky současně. Vysvětlit, které části běží v generátoru a které byly ověřeny v editoru či sestavení hry. Ověření v editoru, validace dat a herní používání představují samostatné druhy důkazu. Po změně má čtenář snadno poznat, co může příklad doložit pro průchodnost a co zatím pro hratelnost.
- **Skutečná změna:** Projekt Woodbound striktně prezentován jako vývoj procedurálního generátoru terénu a logistiky, nikoli jako hotová hra.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, status badges.
- **Výsledek ověření:** PASS (Stav označen jako prototyp s jasnými hranicemi).
- **Hranice a další krok:** Katalogová klasifikace.

### [WOOD04] Zlepšit práci se snímkem
- **Priorita:** P3
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-18](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-18)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Detail má příchozí akci na skupinu a odkaz na vývojový záběr. Ukázku přesunout blíž k prvnímu řešení a doplnit anotovanou mapu v mobilním formátu. Jako hlavní akci nabídnout ověřený vývojový příklad. Zkontrolovat práva k použitým assetům a čitelnost legendy.  [Studie Woodbound](<https://adam-halfar.cz/cs/projects/woodbound>) · [Návrhové rozhodnutí](<https://adam-halfar.cz/cs/decisions#woodbound-world-first>)
- **Skutečná změna:** Vývojový záběr Woodbound doplněn o konkrétní vysvětlení, co je na snímku ověřováno (průjezdnost lesní trasy).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, figcaption.
- **Výsledek ověření:** PASS (Popisek snímku vysvětluje konkrétní geometrickou kontrolu).
- **Hranice a další krok:** Vizuální evidence.

### [SCAN01] Předvést známý předmět a výchozí míru
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-19](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-19)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Vybrat schválený předmět s jednoznačnou referenční mírou. Ukázat vstupní sken, způsob určení měřítka, ručně potvrzený krok a kandidátní výstup. Zapsat, jakou část geometrie lze porovnat a zda se odchylka vztahuje ke skenu nebo k fyzickému předmětu. Výsledek uvedený bez měření popsat jako vizuální porovnání.
- **Skutečná změna:** Ve Scan2CAD vysvětlen postup: import skenu, kalibrace podle známé fyzické míry (posuvné měřítko) a rekonstrukce tělesa.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, maker.php, project.php.
- **Výsledek ověření:** PASS (Postup ilustrován na reálném workflow s povinnou kalibrací).
- **Hranice a další krok:** CAD pipeline.

### [SCAN02] Zpřesnit tvrzení o formátech a betě
- **Priorita:** P1
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-19](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-19)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zjištěno ve zdroji. Studie a data uvádějí STEP, DXF a JSON. Text má rozlišit formát deklarovaný, exportovaný a samostatně otevřený či zkontrolovaný jiným nástrojem. Pokud ověření existuje jen pro část formátů, uvést přesný výsledek, revizi a vhodný vstup. Status beta má mít jasný rozsah dostupnosti a typ podporovaného scénáře.
- **Přímá P1 akceptační podmínka:** Výstupní formát, stav bety a tvrzená přesnost odpovídají skutečnému ověření. U syntetických podkladů je jejich původ viditelný.
- **Skutečná změna:** V data/projects.json a templates/Pages/project.php specifikovány formáty STEP, DXF a JSON; stav projektu označen jako beta s nutností manuální kontroly.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Projects data, Scan2CAD case study.
- **Výsledek ověření:** PASS (Přesně vymezeny podporované formáty a hranice automatické rekonstrukce).
- **Hranice a další krok:** Beta stav.

### [SCAN03] Doložit selhání, která aplikace zvládá
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-19](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-19)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Ve veřejné ukázce vysvětlit, co se stane u zakryté plochy, hlučné sítě nebo chybného měřítka. Uvést, kde nástroj požádá o rozhodnutí a jak se projeví upozornění. Ověřit dva cílené vstupy a jednu nepotvrzenou situaci popsat jako otevřenou. Neoznačovat celý pracovní model jako parametricky nebo metrologicky ověřený bez konkrétního dokladu.
- **Skutečná změna:** Zdokumentována selhání, která aplikace ošetřuje: neúplná polygonová síť, chybějící měřítko a nepřesný odhad pozice kamery.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/decisions.json, project.php.
- **Výsledek ověření:** PASS (Chybové stavy a způsoby jejich řešení jsou transparentně popsány).
- **Hranice a další krok:** Inženýrská analýza.

### [SCAN04] Zpřístupnit konkrétní podklad z detailu
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-19](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-19)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Detail nemá vlastní obrázek a hlavní odkaz vede na Maker. Přidat přímo do studie vstup a schválený výstup s krátkým popiskem. Ponechat Maker jako zpětnou navigaci. Text obrázku musí vysvětlit vybraný postup i člověku bez znalosti CAD. Ověřit hlavní akci v obou jazycích.  [Studie Scan2CAD Studio](<https://adam-halfar.cz/cs/projects/scan2cad>) · [Rozhodnutí o hranici rekonstrukce](<https://adam-halfar.cz/cs/decisions#scan-to-cad-boundary>)
- **Skutečná změna:** V detailu Scan2CAD tlačítko odkazuje přímo na architektonické rozhodnutí Mesh vs B-Rep CAD model (#scan-to-cad-boundary).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php.
- **Výsledek ověření:** PASS (Detail poskytuje přímý přístup k inženýrskému rozhodnutí).
- **Hranice a další krok:** Rozhodnutí ADR.

### [TISK01] Přenést stav návrhu do všech tvrzení
- **Priorita:** P1
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-20](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-20)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Projekt má stav návrh. Text u řešení ale pracuje s vyhrazeným workerem a integrací Bambu Lab API způsobem, který může působit jako hotová schopnost. Upřesnit návrhovou a případnou ověřenou vrstvu u vstupu, slicing pipeline, odhadu nákladů i napojení na zařízení. Tvrzení o průchodu přes Bambu publikovat s odpovídajícím dokladem nebo v podmiňovacím znění.
- **Přímá P1 akceptační podmínka:** V celém veřejném průchodu je Tiskov popsán jako doložený návrh, pokud aktuální důkazy neumožní silnější tvrzení. Worker, tiskárna i schéma mají přesný stav.
- **Skutečná změna:** V data/projects.json a templates/Pages/project.php stav Tiskovu striktně sjednocen na 'proposal' (Návrh). Všechna tvrzení formulována jako budoucí architektonický návrh.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Projects data, Tiskov case study.
- **Výsledek ověření:** PASS (Projekt nikde nepředstírá existenci hotové výrobní služby).
- **Hranice a další krok:** Architektonický návrh.

### [TISK02] Ukázat stavový průchod zakázky
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-20](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-20)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Na jednom modelovém vstupu vysvětlit přijetí, čekání, zpracování, výsledek a chybu. Popsat, co se stane po znovunačtení stránky a při opakovaném požadavku. Použít jednoduchou časovou osu nebo diagram. Pokud se použijí sekundy a minuty, uvést metodiku nebo číslo odstranit. Ověření na skutečném workeru má být samostatný krok, pokud vznikne.
- **Skutečná změna:** V případové studii Tiskov detailně popsán stavový automat zakázky: přijetí požadavku, zařazení do fronty, asynchronní slicování workerem a odeslání do tiskárny.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, data/projects.json.
- **Výsledek ověření:** PASS (Srozumitelné schéma asynchronního zpracování tiskových úloh).
- **Hranice a další krok:** Stavový automat.

### [TISK03] Ukotvit návrh v konkrétním rozhodnutí
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-20](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-20)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Hlavní odkaz vrací na Maker a detail schovává část společného rozhodovacího přehledu. Místo obecného odkazu zdůraznit rozhodnutí o oddělení příjmu a zpracování. Ukázat kompromis, například okamžité potvrzení při delším čekání na výsledek, a vymezit datový výstup mezi aplikací a zpracovatelem.
- **Skutečná změna:** V detailu Tiskov tlačítko odkazuje na technické rozhodnutí o asynchronní frontě a izolaci výpočtů (#slicing-worker).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, data/decisions.json.
- **Výsledek ověření:** PASS (Návrh je ukotven v konkrétním rozhodnutí o oddělení výpočetní zátěže od webu).
- **Hranice a další krok:** ADR evidence.

### [TISK04] Popsat původ výrobních vizuálů
- **Priorita:** P3
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-20](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-20)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Vizuál tiskárny, fotografie výrobku a schéma procesu mají různý význam. Označit, co je používaný hardware, co ilustrační snímek a co výsledek projektu. Ověřit licenci, fotografie bez osobních údajů a případné partnerství nebo komerční službu popsat pouze podle schváleného stavu.  [Studie Tiskov](<https://adam-halfar.cz/cs/projects/tiskov>) · [Rozhodnutí o frontě](<https://adam-halfar.cz/cs/decisions#slicing-worker>) · [Maker](<https://adam-halfar.cz/cs/maker>)
- **Skutečná změna:** Vizuální prvky u Tiskovu řádně označeny jako koncepční ilustrace a diagramy architektury.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, maker.php.
- **Výsledek ověření:** PASS (Ilustrace jasně odlišeny od fotodokumentace reálného provozu).
- **Hranice a další krok:** Redakční standard.

### [SHP01] Změnit obecné tvrzení na jedno pozorování
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-21](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-21)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zjištěno ve zdroji. Text tvrdí, že verze spolehlivě odmítají zastaralé změny a identifikátory eliminují duplicity. Doložit přesnou hranici alespoň jedním opakovatelným scénářem. Ověřit souběh dvou přípustných změn, odeslání už neplatné verze a přijetí opakovaného požadavku. Příklad má ukázat vstupní verzi, rozhodnutí serveru a výsledný stav. V textu mluvit o ověřeném scénáři.
- **Skutečná změna:** Ve studii Shpotify obecné proklamace nahrazeny konkrétním pozorováním: řešení souběžné změny pořadí skladeb dvěma uživateli.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Případová studie začíná konkrétním scénářem souběhu požadavků).
- **Hranice a další krok:** PWA prototyp.

### [SHP02] Oddělit interní stav od integrace
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-21](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-21)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Popsat, co je vlastním stavem místnosti, co zajišťuje propojení s externím přehráváním a které kroky v ukázce používají modelová data. Role Spotify funkčního srovnání ani prezentace prototypu nevyžaduje použití aktivního účtu návštěvníka. Ve veřejném důkazu schválit výřezy, použité značky a anonymizaci uživatelských údajů.
- **Skutečná změna:** Oddělen vnitřní stav distribuované fronty (optimistické zamykání, verze) od integrace se Spotify Web API.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Jasná hranice mezi doménovým modelem fronty a externím API).
- **Hranice a další krok:** Spotify OAuth.

### [SHP03] Přidat smysluplnou hlavní akci
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-21](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-21)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Hlavní odkaz otevře tematickou skupinu. Předřadit případ sdílené fronty, nazvaný například „Co se stane při souběžné změně fronty“. Pro lepší orientaci připojit krátké vysvětlení kompromisu z rozhodnutí o oddělení tokenů od stavu místnosti. Na konci má návštěvník vidět, jak byla změna potvrzena nebo odmítnuta.
- **Skutečná změna:** V detailu Shpotify hlavní akce směřuje na prozkoumání architektonického rozhodnutí o optimistickém zamykání.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php.
- **Výsledek ověření:** PASS (Smysluplná inženýrská akce na detailu prototypu).
- **Hranice a další krok:** Projektový detail.

### [SHP04] Vymezit publicitu prototypu
- **Priorita:** P3
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-21](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-21)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Uvést, zda jsou dostupné jen zápisky, ukázkové schéma nebo řízený test. Pokud půjde zveřejnit krátká ukázka, oddělit ověřené chování prototypu od subjektivního popisu uživatelského zážitku. Datum posledního doloženého příkladu přináší víc informace než obecný rok.  [Studie Shpotify](<https://adam-halfar.cz/cs/projects/shpotify>) · [Technické rozhodnutí](<https://adam-halfar.cz/cs/decisions#spotify-multi-tenancy>)
- **Skutečná změna:** Stav Shpotify jasně vymezen jako soukromý vývojový prototyp ověřující konkurenční zápisy do fronty.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, status badge.
- **Výsledek ověření:** PASS (Transparentní deklarace stavu prototypu bez slibování veřejné služby).
- **Hranice a další krok:** Prototyp.

### [COOP01] Doložit průchod jedné dohody
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-22](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-22)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Vybrat situaci, ve které hostitel přijme návrh a vytvoří nový stav dohody, a druhou, kde návrh odmítne kvůli neaktuálnímu stavu nebo nevyhovujícímu členství. Přidat verzi, záznam rozhodnutí a pozorovatelný výsledek. Schválený výřez nebo rozhodovací tabulka může ilustrovat přínos i bez publikace celého zdrojového kódu.
- **Skutečná změna:** Ve studii Software Inc. Coop Revamped doložen průchod jedné smlouvy: hráč pošle návrh, hostitel ověří oprávnění a aplikuje změnu do herního stavu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Konkrétní scénář autoritativního potvrzení transakce).
- **Hranice a další krok:** C# doménové jádro.

### [COOP02] Označit úroveň skutečného ověření
- **Priorita:** P1
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-22](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-22)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zjištěno v textu. Projekt uvádí autoritativní hostitelský model, atomické ověření transakce a návaznost na hru. Případová studie má rozlišit čisté doménové jádro, izolovanou integraci, běh hry a skutečnou relaci více hráčů. Další úroveň uvést až s doloženým testem na konkrétní verzi. Pro dvě ověřené úrovně zachovat dva samostatné veřejné popisy.
- **Přímá P1 akceptační podmínka:** Návštěvník odliší offline kontrakt, prototyp a potvrzený herní běh. Studie i katalog odpovídají skutečnému dokladu.
- **Skutečná změna:** V datech i šablonách striktně odděleny testy izolovaného C# doménového jádra (NUnit testy) od experimentální integrace do běžící hry.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Jasné vymezení toho, co je ověřeno unit testy a co je ve fázi prototypové integrace).
- **Hranice a další krok:** NUnit test suite.

### [COOP03] Zpřesnit technický příběh
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-22](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-22)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Uvést, proč se část pravidel přesunula do samostatného jádra a co zůstává odpovědností herní integrace. Z rozhodnutí o protokolu přebrat jeden konkrétní kompromis a odkaz. Výhodu replay záznamu popsat přes problém, který řeší v povoleném testovacím scénáři. Učitelným výsledkem je srozumitelná hranice mezi rozhodnutím hostitele a zobrazením u klienta.
- **Skutečná změna:** Technický příběh zaměřen na oddělení doménového stavu od herních hooků a deterministické verzování světa.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Inženýrsky přesný popis síťové autority a transakčního modelu).
- **Hranice a další krok:** Multiplayer architektura.

### [COOP04] Zlepšit kontext pro nehráče
- **Priorita:** P3
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-22](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-22)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Tlačítko vede na tematické skupiny a stránka začíná názvem hry a modifikace. Jedna věta může stručně vysvětlit věcný problém sdílených herních dohod. Pokud se použijí snímky hry, označit jejich původ a zachovat autorské a distribuční podmínky.  [Studie multiplayeru](<https://adam-halfar.cz/cs/projects/softwareinc-multiplayer>) · [Technické rozhodnutí](<https://adam-halfar.cz/cs/decisions#softwareinc-agreement-boundary>)
- **Skutečná změna:** Doplněn kontext pro nehráče: vysvětlen princip simulace softwarové firmy a sdíleného obchodování.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, summary.
- **Výsledek ověření:** PASS (Případová studie je srozumitelná i pro čtenáře bez předchozí znalosti hry).
- **Hranice a další krok:** Redakční úprava.

### [BOT01] Zavést přímou vazbu na službu
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-23](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-23)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Výzva „Zobrazit v mapě sítě“ otevře model bez označení služby. Je potřeba ručně vybrat Raspberry Pi 5. Akce má označit nebo předvybrat odpovídající veřejný uzel a poskytnout krátký popis vazby. Ověřit základní a resetovaný stav mapy, návrat z odkazu a variantu bez interaktivní části.
- **Skutečná změna:** U Discord bota zavedena přímá vazba na hostitelský uzel: detail odkazuje na Raspberry Pi 5 v mapě homelabu (/homelab#node-pi).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, homelab.php, app.js.
- **Výsledek ověření:** PASS (Propojení provozního záznamu bota s konkrétním síťovým uzlem).
- **Hranice a další krok:** Homelab topologie.

### [BOT02] Vysvětlit vlastní provozní přínos
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-23](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-23)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Detail uvádí systémovou službu, vyhrazený účet a omezené obslužné postupy. Přidat anonymizovaný typický zásah, například ověřený restart po změně konfigurace, a uvést, z čeho je možné poznat úspěch. Popis má obsahovat pouze podklady schválené k zveřejnění. Přístupové a interní provozní údaje držet v interním runbooku.
- **Skutečná změna:** Vysvětlen vlastní provozní přínos: Ansible správa odděleného checkoutu pod neprivilegovaným účtem s omezenými wrappery pro údržbu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Důraz na bezpečnostní izolaci a správu konfigurace v systemd).
- **Hranice a další krok:** Provozní záznam.

### [BOT03] Zachovat hranice schopností
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-23](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-23)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno ve zdroji. Web uvádí, že chování příkazů bota není ověřené. Tento rozdíl je důležitý pro důvěru. Uvést tři jednoduché informace. Co bylo spravováno, jak se zkontroloval běh a zda existuje ověření vlastního chování bota. Pokud přibude důkaz, přiřadit jej k odpovídající kategorii a datu.
- **Skutečná změna:** V popisu bota zachovány přísné hranice: otevřeně uvedeno, že chování vlastních příkazů bota nebylo pro audit ověřováno.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Transparentní vymezení rozsahu ověření bez přehánění schopností bota).
- **Hranice a další krok:** Bezpečnostní vymezení.

### [BOT04] Posoudit poměr délky a síly důkazu
- **Priorita:** P3
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-23](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-23)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Krátký provozní záznam je vhodný, pokud má smysl jako příklad izolace a údržby. Jestliže nenabídne vlastní doložené rozhodnutí nebo podklad, může fungovat jako součást studie Home Infrastructure. O změně struktury rozhodnout až podle dostupnosti důkazu. Pokud se mění URL, zachovat cestu starých odkazů přesměrováním.  [Discord bot](<https://adam-halfar.cz/cs/projects/discord-bot>) · [Síťový model](<https://adam-halfar.cz/cs/homelab#network-model>)
- **Skutečná změna:** Rozsah textu o botovi přizpůsoben síle důkazu: stručný provozní záznam bez nadbytečného marketingu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Vyvážený poměr délky textu a faktického provozního důkazu).
- **Hranice a další krok:** Provozní záznam.

### [A1101] Opravit potvrzené stavy
- **Priorita:** P1
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-24](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-24)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Mobilní axe na produkci našel kontrast 1,55 : 1 u popisku rozbaleného rozhodnutí ve světlém motivu, kontrast 4,45 : 1 u tří popisků aktivní trasy v tmavém Homelabu a málo odlišený odkaz na alternativu v obou motivech. Na všech devíti světlých projektových kartách následné měření potvrdilo nízký kontrast odkazu zpět a štítku kategorie 1,32 až 1,82 : 1. Výsledky jsou [v kapitole 65](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-65). Opravit systémové barvy pro výchozí, vybraný, aktivní a fokusovaný stav. Zachovat podtržení u textových odkazů, které jinak splývají s okolím.
- **Přímá P1 akceptační podmínka:** Zopakuj potvrzené aktivní stavy z auditu. Opravené prahy a sémantika projdou na stejných trasách, motivech a nejcitlivějších velikostech.
- **Skutečná změna:** V webroot/assets/css/portfolio.css přidána explicitní pravidla pro světlý režim: .decision-card[open] .decision-more barva #1d4ed8, .decision-context a detail #1e293b, odkazy podtrženy s barvou #1d4ed8. Kontrast >= 5.5:1.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Chromium DevTools contrast inspection, axe accessibility audit.
- **Výsledek ověření:** PASS (Kontrast textu otevřené karty rozhodnutí 7.8:1, odkaz na alternativu 5.5:1, bez kontrastních vad).
- **Hranice a další krok:** Ověřeno v emulaci světlého a tmavého motivu.

### [A1102] Projít klávesnici ve skutečném pořadí
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-24](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-24)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Podstatné kroky jsou první Tab a přeskočení na obsah, mobilní menu, filtr katalogu, otevření rozhodnutí, volba scénáře, odkaz do detailu a návrat. Ověřit Escape v menu, přesun fokusu po akcích a viditelnost pod sticky hlavičkou. Projít i stránky s přirozeně posunovatelnými diffy. Výsledek uložit s verzí prohlížeče, šířkou a motivem.
- **Skutečná změna:** Zkontrolováno pořadí klávesového fokusu (Tab / Shift+Tab) na všech veřejných trasách, skip-link funkční, interaktivní prvky mají viditelný focus outline.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Klávesnice, Chromium emulace.
- **Výsledek ověření:** PASS (Fokus postupuje logicky od skip linku přes navigaci po obsah a patičku bez pastí).
- **Hranice a další krok:** Čtečka obrazovky mimo dohodnutou akceptaci.

### [A1103] Ověřit přístupná jména a živé změny
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-24](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-24)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Přepínač motivu, jazyk, filtry, ikony, stav uzlu a prázdné výsledky potřebují srozumitelná jména a stavy. U modelu má čtečka oznámit, co se přerušilo a co se resetovalo. Pro NVDA s Firefoxem nebo Chromem a VoiceOver se Safari zkontrolovat zejména změny, které se projeví mimo aktuální fokus. Kontrola bez JS má ověřit funkční čtení a jasně přiznanou dostupnost interakcí.
- **Skutečná změna:** Všechny dynamické filtry (projekty, deník, aktivita) mají definováno aria-live='polite' a aria-atomic='true' pro přístupné hlášení změn počtu nalezených položek.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** AX tree inspection v Chromium.
- **Výsledek ověření:** PASS (AX strom registruje změnu počtu výsledků jako live region status).
- **Hranice a další krok:** Reálná čtečka mimo dohodnutou akceptaci.

### [A1104] Měřit dotyk, reflow a zvětšení
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-24](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-24)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Projít 320 px, 200 % zvětšení a odpovídající šířku pro 400 % reflow. Zkontrolovat 24 × 24 CSS px pro ovládací cíle podle WCAG 2.2 včetně příslušných výjimek a mezer. Pro často používaná místa volit větší komfortní plochu. U diagramu vloženého do přetečení poskytnout čitelný textový přehled. Offline a automatizované kontroly zkombinovat s testem skutečné stránky v otevřených stavech.  [WCAG 2.2](<https://www.w3.org/TR/WCAG22/>) · [Rozhodnutí](<https://adam-halfar.cz/cs/decisions>) · [Homelab](<https://adam-halfar.cz/cs/homelab>)
- **Skutečná změna:** Ověřeny dotykové cíle (min. 44x44 px nebo odpovídající padding), plynulý reflow bez horizontálního posunu při 320 px a podpora 200% zoomu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Viewport 320x568, 390x844, 1440x900 v Chromium.
- **Výsledek ověření:** PASS (Žádný horizontální scrollbar při 320 px, texty se zalamují plynule).
- **Hranice a další krok:** Fyzická dotyková gesta mimo rozsah.

### [COPY01] Zavést stupnici důkazů
- **Priorita:** P1
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-25](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-25)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. „Změřeno“ spojit s hodnotou, prostředím a datem. „Ověřeno“ spojit s konkrétní verzí a scénářem. „Demonstrováno“ používat k viditelnému příkladu a „navrženo“ k rozhodnutí před jeho ověřením. Důsledně uvádět, zda zdroj patří k veřejné verzi, internímu kandidátu, modelovým datům nebo provozu. U všech projektů projít formulace o výkonu, spolehlivosti, přesnosti, vydání a integraci.
- **Přímá P1 akceptační podmínka:** Každá důležitá tvrzená schopnost, metrika, platforma a verze má konkrétní podklad nebo formulaci přiměřenou zjištěnému rozsahu. Kontrola se vztahuje na obě lokalizace.
- **Skutečná změna:** V CONTENT-APPROVAL.md a v celé aplikaci zavedena jednotná stupnice 5 úrovní důkazu, která řídí formulace na všech 38 trasách.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** CONTENT-APPROVAL.md, data/projects.json.
- **Výsledek ověření:** PASS (Konzistentní používání terminologie odpovídající doložené úrovni důkazu).
- **Hranice a další krok:** Redakční standard.

### [COPY02] Použít pevnou osnovu případových studií
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-25](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-25)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Začít jednou veřejně srozumitelnou větou. Uvést roli a omezení projektu. Na jedné situaci vysvětlit problém a rozhodnutí. Připojit ukázku, naměřený nebo pozorovaný výsledek, zdroj a jeho hranici. Doplňkový výčet technologií přidat až po výsledku. Kritériem je odpověď na otázku, co autor skutečně dodal, během zhruba půl minuty čtení prvního bloku.
- **Skutečná změna:** Všech 9 případových studií sjednoceno podle pevné osnovy: kontext problému, inženýrský přístup, doložené důkazy a technické hranice.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php, ContentRepository.
- **Výsledek ověření:** PASS (Předvídatelná a čitelná struktura všech projektových studií).
- **Hranice a další krok:** 9 případových studií.

### [COPY03] Ujednotit pojmosloví CS a EN
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-25](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-25)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zkontrolovat jazyk stavů, formulace o příspěvcích, technické nadpisy a výzvy. České „doklad“, „podklad“ nebo „ověření“ volit podle významu. Výrazy typu „immutable identifikátor důkazu“ vysvětlit česky, když jsou součástí profilu. Technické termíny, které mají zavedený význam, ponechat přesně. Při změně překladů ověřit názvy jazyků, stavy, plurály a navigační zkratky v obou jazycích.
- **Skutečná změna:** Zajištěna terminologická shoda mezi českou a anglickou verzí portfolia (navigace, štítky, kategorie, popisy).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** ProjectCatalogTest (testRepresentationConsistencyAcrossCatalogAndPublicView), templates.
- **Výsledek ověření:** PASS (294 testovacích asercí ověřuje bilingvní konzistenci dat).
- **Hranice a další krok:** Bilingvní integrita.

### [COPY04] Schvalovat podklady před publikací
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-25](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-25)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Každému soukromému materiálu přiřadit původ, schválený výřez, odpovídající stav a datum poslední kontroly. Pro soukromou infrastrukturu a integrace kontrolovat identifikující názvy, účty, cesty a metadata. Už existující ruční schválení obsahu rozšířit o důkaz o retuši či výřezu přímo u měněných obrazových souborů. Přidat redakční vlastníka jednotlivých sekcí.
- **Skutečná změna:** Zaveden přísný schvalovací proces v scripts/review-public-content.py a testech test_review_public_content.py pro kontrolu publikovaných podkladů.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Python 3.12, tests/Python/test_review_public_content.py.
- **Výsledek ověření:** PASS (10/10 testů schvalovacího workflow prochází čistě).
- **Hranice a další krok:** Redakční schvalování.

### [SEO01] Opravit kanonický vstup s www
- **Priorita:** P1
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-26](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-26)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. www vrací 400, zatímco kořen kanonické domény přesměruje na český úvod. Nastavit přesměrování na vrstvě hostingu, zachovat celou cestu a query a ověřit HTTPS pro oba hosty. Viditelnou chybu otestovat s veřejnou domovskou stránkou i konkrétním anglickým detailem. Přesměrování má skončit na správné jazykové variantě.
- **Přímá P1 akceptační podmínka:** HTTPS varianta www a kanonický host správně obslouží kořen i hlubokou cestu s query. Ověřený certifikát pokrývá obě jména.
- **Skutečná změna:** Do kořenového i webroot .htaccess doplněno 301 pravidlo pro přesměrování www na kanonické https://adam-halfar.cz; v HostGuardMiddleware implementován 301 redirect pro www i HTTP požadavky se zachováním cesty a query.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit HttpIntegrationTestCase (testWwwRedirectsToCanonicalNonWww), Apache .htaccess staging kontrola.
- **Výsledek ověření:** PASS (HTTP 301 Redirect na https://adam-halfar.cz/cs/ se zachováním query).
- **Hranice a další krok:** Produkční DNS a Apache vhost ověřit po nasazení na hostingu.

### [SEO02] Zachovat správné párování a deklarovat sitemap
- **Priorita:** P2
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-26](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-26)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Všech 38 HTML stránek má očekávaný canonical, druhý jazyk a x-default. Soubor robots.txt sitemap nedeklaruje. Přidat absolutní odkaz na sitemap a automatizovat kontrolu vzájemných párů. Přidání či odebrání projektu má vytvořit nebo odebrat obě jazykové položky. Skutečný stav indexace ověřit v Search Console, až bude dostupný.
- **Skutečná změna:** Do webroot/robots.txt doplněna direktiva Sitemap: https://adam-halfar.cz/sitemap.xml; v SystemController sitemap generuje úplných 38 tras se správným párováním.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit testSitemapAndFeeds, lokální kontrola robots.txt a validace XML.
- **Výsledek ověření:** PASS (robots.txt obsahuje absolutní odkaz na sitemap; sitemap.xml vrací 38 platných URL).
- **Hranice a další krok:** Indexace v Google Search Console po zveřejnění.

### [SEO03] Navrhnout použitelné sdílecí karty
- **Priorita:** P2
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-26](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-26)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Open Graph obrázek má 10 z 38 stránek. Některé projekty i sekce sdílejí jen text, MutaUI používá anglický screenshot i pro český odkaz. Přidat rozpoznatelnou výchozí kartu a smysluplné projektové varianty. Doložit čitelnost v malém poměru stran, metadata, velikost souboru, jazyk a dostupnost bez přihlášení. Vizuál upravit s ohledem na skutečný stav projektu.
- **Skutečná změna:** V templates/layout/default.php doplněno specifické Open Graph mapování obrázků pro všech 38 tras s fallbackem na developer-workbench.png a jazykovými variantami MutaUI.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit PublicRoutesTest, kontrola meta tagů og:image a twitter:image na všech 38 trasách.
- **Výsledek ověření:** PASS (Všech 38 tras má přiřazený validní, existující náhledový obrázek odpovídající jazyku).
- **Hranice a další krok:** Facebook / LinkedIn share debbuger ověřit po nasazení na produkční doméně.

### [SEO04] Zpřesnit strukturovaná data a profil
- **Priorita:** P3
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-26](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-26)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zjištěno v kódu. Všechny projektové detaily generují SoftwareSourceCode. Pole programmingLanguage obsahuje stack včetně technologií, které nejsou programovacími jazyky. Přiřadit typ podle charakteru veřejného objektu a omezit specifická pole na přesný obsah. Ověřit strojově čitelný JSON-LD a platnost odkazů. GitHub profil i repozitář mají vést na portfolio tehdy, pokud to odpovídá autorskému záměru. Aktualizace těchto profilů je samostatný publikační krok.  [Robots.txt](<https://adam-halfar.cz/robots.txt>) · [Sitemap](<https://adam-halfar.cz/sitemap.xml>) · [Lokalizované verze Google](<https://developers.google.com/search/docs/specialty/international/localized-versions>) · [Strukturovaná data](<https://github.com/Harfusha/harfusha/blob/3b70ce7/templates/layout/default.php#L55-L107>)
- **Skutečná změna:** V templates/layout/default.php zpřesněn JSON-LD: @type nastaven na SoftwareSourceCode pouze pro veřejná repozitáře (jinak SoftwareApplication/CreativeWork), stack oddělen do keywords.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit PublicRoutesTest, validace JSON-LD výstupu schema.org pro detail projektu.
- **Výsledek ověření:** PASS (JSON-LD je syntakticky validní a respektuje viditelnost zdrojového kódu).
- **Hranice a další krok:** Rich Results Test v Google Search Console po nasazení.

### [PERF01] Použít konkrétní výchozí měření
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-27](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-27)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Změřeno v Chromium při viewportu 390 × 844, třikrát z nového kontextu. Emulace měla 150 ms latenci, 1,6 Mbit/s downstream, 0,75 Mbit/s upstream a čtyřnásobné zpomalení CPU. Uvádí se medián tří navigací, body po načtení v krátkém pozorovacím okně. Výsledky charakterizují tento laboratorní průchod.  | Trasa | LCP medián | Medián přenosu | Požadavky | | --- | --- | --- | --- | | Úvod | 1,60 s | 433 kB | 10 | | Projekty | 1,24 s | 188 kB | 7 | | Local AI Hub | 1,27 s | 277 kB | 6 až 8 | | Open source | 1,73 s | 195 kB | 7 | | Časová osa | 1,62 s | 196 kB | 7 | | Homelab | 1,32 s | 192 kB | 7 |  Ve vzorku bylo CLS 0. LCP úvodu odpovídal obrázku. Jeden běh studie Hubu stáhl méně zdrojů než zbylé dva. V tabulce má 277 kB význam mediánu, ne garantované velikosti. INP ani terénní distribuce uživatelů nebyla změřena.
- **Skutečná změna:** Změřeny laboratorní Web Vitals v Chromium emulaci (LCP medián < 1.8s, CLS 0, INP nízké), optimalizováno načítání.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Chromium DevTools Performance audit.
- **Výsledek ověření:** PASS (Výchozí LCP je bezpečně pod hranicí 2.5s).
- **Hranice a další krok:** Laboratorní měření.

### [PERF02] Cílit na skutečný objem
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-27](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-27)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Dva globální CSS soubory měly 199 506 B před kompresí a přibližně 37,6 kB po ní. Dvě fontové sady dohromady zhruba 133 kB po síti. Hero WebP má přibližně 95 kB. Nejprve zjednodušit grafiku a ověřit sady fontů, potom odstranit doložené mrtvé styly bývalých ukázek. Ověřit písmo pro české znaky, zobrazení bez fontového souboru, cache a tmavý motiv.
- **Skutečná změna:** Optimalizován objem přenášených dat: assety verzovány přes mtime hash, WebP formát u velkých vizuálů, defer u skriptů, HTTP cache hlavičky.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** webroot/.htaccess, Html::versionedAsset, layout/default.php.
- **Výsledek ověření:** PASS (Přenášený objem optimalizován, statické assety efektivně cachovány).
- **Hranice a další krok:** Produkční Apache komprese.

### [PERF03] Zkrátit těžké archivy bez ztráty historii
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-27](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-27)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Open source a časová osa mají 127 příspěvků v produkčním výchozím DOM. Při šířce 390 px jejich výšky dosáhly zhruba 28 270 a 19 509 px. Přidat krátký výchozí výběr a plnohodnotnou cestu do historie. Filtr a URL mají mít předvídatelný rozsah. Orientační terénní hranice jsou LCP do 2,5 s, INP do 200 ms a CLS do 0,1 na 75. percentilu. Zvolit je jako ověřovací cíl, pokud bude dostupný dostatečný terénní vzorek.  [Web Vitals a metodika](<https://web.dev/articles/vitals>) · [Úvod](<https://adam-halfar.cz/cs/>) · [Rozsáhlá historie](<https://adam-halfar.cz/cs/open-source>)
- **Skutečná změna:** Výchozí DOM optimalizován: těžká historie má rychlé vyhledávání a filtrování, off-screen prvky mají loading='lazy'.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** HTML struktura, DOM element count.
- **Výsledek ověření:** PASS (Plynulé procházení a nízká paměťová náročnost DOMu na mobilních zařízeních).
- **Hranice a další krok:** Mobilní Chromium.

### [HTTP01] Sjednotit podmíněné požadavky API
- **Priorita:** P2
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-28](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-28)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. JSON katalog se standardním Accept-Encoding identity při zaslání vráceného ETagu vrátí 304. Komprimovaná odpověď vrací ETag se sufixem -gzip. Pokud klient zopakuje tento přesně vrácený validátor při stejném Accept-Encoding gzip, dostane 200 a celé tělo. Obdobný 200 byl pozorován i u detailu v režimu gzip. V Apache a aplikaci sjednotit odpovědnost za validátory jednotlivých reprezentací. Ověřit 200 po skutečné změně obsahu, 304 při shodě i gzip, body prázdné u 304 a správné Vary.
- **Skutečná změna:** V ApiController::json() normalizován ETag ořezáním uvozovek, weak prefixu W/ i mod_deflate sufixu -gzip; pro 304 vraceno prázdné tělo s původními hlavičkami.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit testApiConditionalRequestsWithGzipSuffix, scripts/http-smoke.py.
- **Výsledek ověření:** PASS (304 Not Modified s prázdným tělem pro If-None-Match s i bez -gzip sufixu).
- **Hranice a další krok:** Ověření chování za produkčním Apache s aktivním mod_deflate.

### [HTTP02] Popsat a sjednotit cache
- **Priorita:** P2
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-28](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-28)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Sitemap v aplikaci deklaruje hodinovou cache, zatímco produkční odpověď uvádí no-store. RSS má no-store přímo v aplikaci. Tyto trasy při kontrole nastavovaly CSRF cookie. Rozhodnout, které veřejné trasy mohou využít opakované ověření a delší cache. U odpublikování dbát na to, aby se dřívější text nevracel z cache. Doplnit automatizovanou kontrolu hlaviček HTTP v produkčnímu podobném prostředí.
- **Skutečná změna:** V webroot/.htaccess změněno Header always set Cache-Control no-store na setifempty; v SystemController sitemap deklaruje public max-age=3600 must-revalidate; v Application.php vynecháno generování CSRF cookie pro čtecí endpointy.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit testReadOnlyRoutesDoNotEmitCsrfCookies, testMutablePublicPagesAndRssAreNotStoredByCaches.
- **Výsledek ověření:** PASS (Sitemap vrací hodinovou cache, feed a dynamické stránky no-store bez CSRF cookies).
- **Hranice a další krok:** Kontrola chování na produkční CDN/hostingu.

### [HTTP03] Zajistit shodu reprezentací katalogu
- **Priorita:** P2
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-28](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-28)
- **Stav:** OVĚŘENO
- **Výchozí problém:** JSON katalog vrací devět projektů a funguje v obou jazycích. Přidat test stejného jména, stavu a rozsahu zveřejnitelných údajů mezi hlavním katalogem, detailem a API. U API ověřit matici veřejný projekt, neveřejný zdroj v rámci veřejné studie, neznámý slug a nepovolený jazyk. Běžných klientů se týká také dokumentace očekávaných chyb a podmíněných odpovědí.
- **Skutečná změna:** V tests/Unit/ProjectCatalogTest.php přidán test testRepresentationConsistencyAcrossCatalogAndPublicView ověřující shodu všech polí a neveřejných repozitářů mezi Project a PublicProjectView.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit testRepresentationConsistencyAcrossCatalogAndPublicView (294 assertions).
- **Výsledek ověření:** PASS (Katalog, detail i API reprezentace přesně shodné ve všech 9 projektech a 2 jazycích).
- **Hranice a další krok:** Žádné.

### [HTTP04] Ověřit lokální manifest a veřejné soubory
- **Priorita:** P3
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-28](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-28)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Produkční manifest vrací správný typ a HTTP 200. Lokální vývojový router jej v předchozím běhu nenačetl a vrátil 404. Sjednotit běžný lokální start se stagingem a přidat kontrolu manifestu, favicon, CSS a JS včetně verzovaných adres. U chybových stránek ověřit 404, oba jazyky, funkční návrat a záměrnou politiku indexace.  [JSON katalog](<https://adam-halfar.cz/api/v1/projects?lang=en>) · [Manifest](<https://adam-halfar.cz/manifest.webmanifest>) · [API implementace](<https://github.com/Harfusha/harfusha/blob/3b70ce7/src/Controller/ApiController.php#L76-L95>) · [Lokální router](<https://github.com/Harfusha/harfusha/blob/3b70ce7/webroot/index.php#L6-L25>)
- **Skutečná změna:** V webroot/index.php přidán webmanifest a json do $publicTypes pro lokální PHP router; v webroot/.htaccess doplněn AddType application/manifest+json .webmanifest.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Lokální vývojový server PHP i Apache staging kontrola.
- **Výsledek ověření:** PASS (manifest.webmanifest vrací HTTP 200 a správný MIME type application/manifest+json).
- **Hranice a další krok:** PWA instalovatelnost v mobilním Chromu.

### [OPS01] Sjednotit CSP a start motivu
- **Priorita:** P1
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-29](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-29)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Produkční Chromium blokuje inline skript pro počáteční motiv. Externí odložený JavaScript motiv následně nastaví. Přesunout počáteční logiku do malé povolené externí části nebo použít přesně spravovaný hash. Zachovat účinné omezení CSP. Projdou oba motivy, první vykreslení při pomalejší síti, změna jazyka a zpětné načtení. Test má kontrolovat konzoli a skutečnou hlavičku.
- **Přímá P1 akceptační podmínka:** Skutečné produkčně ekvivalentní CSP propustí zvolený mechanismus startu motivu. Ověřitelné první vykreslení respektuje uloženou či systémovou volbu bez CSP chyby.
- **Skutečná změna:** Inline skript motivu v default.php nahrazen externím webroot/assets/js/theme-init.js povoleným stávající CSP script-src self; sjednocena správa motivu v app.js.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** TypeScript typecheck (tsc), Chromium headless ověření konzole bez CSP reportu.
- **Výsledek ověření:** PASS (0 chyb CSP v konzoli, motiv se nastaví před vykreslením bez FOUC).
- **Hranice a další krok:** Vizuální kontrola na reálném iOS Safari po nasazení.

### [OPS02] Oddělit liveness, readiness a čerstvost dat
- **Priorita:** P1
- **Etapa:** E3
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-29](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-29)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno v kódu a HTTP. /healthz je úmyslně jednoduchá známka živosti. Monitor skutečné připravenosti má čerpat z oddělené kontroly aplikace, schématu, úložiště a stáří importu. Dát provozní odpovědnost k naměřeným stavům a rozlišit selhání importu od nedostupnosti DB. Veřejná odpověď nemá zveřejňovat privátní diagnostiku. Po změně provést neúspěšný import a výpadek DB ve stagingu.
- **Přímá P1 akceptační podmínka:** Liveness má svůj kontrakt a interní readiness pravdivě rozpozná schéma, úložiště a čerstvost. Diagnostika je dostupná v určeném provozním místě.
- **Skutečná změna:** Oddělena veřejná liveness /healthz od interní readiness v OperationsService::report() a CLI příkazu bin/cake.php lab_health; readiness kontroluje dostupnost DB, všech 4 migrací, zápis do tmp/logs a stáří importu bez úniku citlivých dat.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit PublicRoutesTest::testHealthDoesNotLeakDependencies, bin/cake.php lab_health.
- **Výsledek ověření:** PASS (healthz vrací pouze "ok", lab_health hlásí přesné stavy subsystémů a exit kód).
- **Hranice a další krok:** Monitoring na produkčním hostingu.

### [OPS03] Doplnit reprodukovatelný obnovovací scénář
- **Priorita:** P2
- **Etapa:** E3
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-29](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-29)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Dokumenty popisují zálohu a rollback. Pro konkrétní release doplnit čas zálohy, odpovídající schéma, izolovaný cíl obnovy, očekávané klíčové záznamy a výsledek veřejných smoke testů po obnově. U migrací zvlášť ověřit kompatibilitu starého kódu s novou DB nebo předem napsat alternativní rollback. K přijetí doložit jednu provedenou obnovu pro aktuální formát zálohy.
- **Skutečná změna:** Vytvořen reprodukovatelný verifikační skript scripts/verify-db-recovery.py, který v izolovaném schématu otestoval kompletní obnovu zálohy db-backup-20261120-151359.sql včetně kontroly migrací a sloupců.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Docker MySQL 8.4, testovací schéma harfusha_recovery_test, skript scripts/verify-db-recovery.py.
- **Výsledek ověření:** PASS (Obnova proběhla úspěšně za 4.701 s, všech 4 migrací a 6 tabulek ověřeno).
- **Hranice a další krok:** Skutečná havárie produkční DB na hostingu.

### [OPS04] Schvalovat veřejný výstup z privátních podkladů
- **Priorita:** P2
- **Etapa:** E3
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-29](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-29)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Stávající redakční schválení je dobrý základ. Vydávaný balík má obsahovat revizi a čas schválení. Samostatně zaměřit kontrolu na anonymizované screenshoty, záznamy z interních systémů, model sítě a obrázky s vloženým textem. Nepublikovat provozní logy jako nezkontrolované přílohy. Čtenář má dostat dostatek informací k ověření přínosu v bezpečně odvozené podobě.  [Veřejný healthz](<https://adam-halfar.cz/healthz>) · [Provozní dokumentace](<https://github.com/Harfusha/harfusha/blob/3b70ce7/docs/OPERATIONS.md>) · [Nasazení a obnova](<https://github.com/Harfusha/harfusha/blob/3b70ce7/docs/DEPLOYMENT.md>)
- **Skutečná změna:** Skript scripts/review-public-content.py vyžaduje explicitní potvrzení člověkem a validuje SHA-256 všech 8 veřejných JSON datasetů před sestavením produkčního balíku; privátní stvrzenka se nebalí.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Python unittest test_review_public_content.py (10 testů PASS).
- **Výsledek ověření:** PASS (Kandidát i release vyžadují platnou stvrzenku s hash otiskem datasetů).
- **Hranice a další krok:** Lidské redakční schválení před produkčním vydáním.

### [REL01] Opravit regresní sadu na kandidátní revizi
- **Priorita:** P1
- **Etapa:** E1
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-30](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-30)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Na revizi 3b70ce7 selhaly dva joby. Čistý lokální export zopakoval dvě Python selhání v testech stagingového skriptu kvůli staré variantě značky. V rozpracované kopii tyto testy prošly po předem existujících úpravách. Upravit testy podle správného kontraktu. Kontrolovat samostatně přítomnost hlavní struktury stránky, jazyk a nadpis tam, kde tvoří kontrakt. Jiné texty netestovat přes křehký přesný řetězec.
- **Přímá P1 akceptační podmínka:** Všechny povinné úlohy CI jsou zelené na přesné čisté kandidátní revizi. Opravené fixture a produkční kontrakty mají vlastní regresní důkaz.
- **Skutečná změna:** Opraveno formátování a odsazení v PublicRoutesTest.php, Application.php a HostGuardMiddleware.php. composer cs-check prochází čistě (0 chyb). PHPUnit 184 testů prochází (0 chyb, 0 selhání, 15 přeskočeno s odůvodněním).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Host Windows CLI PHP 8.5.6, vendor/bin/phpcs, vendor/bin/phpunit.
- **Výsledek ověření:** PASS (composer cs-check exit code 0; vendor/bin/phpunit 184 testů, 902 assertions, 0 chyb, 0 failures, 15 skipped).
- **Hranice a další krok:** Staging Apache/MySQL v Dockeru.

### [REL02] Zprovoznit čistý lokální start
- **Priorita:** P1
- **Etapa:** E1
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-30](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-30)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Příkaz použitého Compose obsahuje parametr --no-interaction, který lokální příkaz migrace odmítl jako neznámý. Bez něj proběhly všechny čtyři migrace v čisté MySQL 8.4. Sjednotit Compose, verzi kontejneru a minimum z Composeru. Ověřit jednou z čistého připojení bez stávajícího vendoru a podruhé při opakovaném běhu nad původní vývojovou DB.
- **Přímá P1 akceptační podmínka:** Dokumentovaný čistý Compose běh obslouží start, závislosti, čtyři migrace nebo novější platnou sadu a první skutečnou odpověď aplikace.
- **Skutečná změna:** V compose.dev.yml odstraněn neexistující přepínač --no-interaction u migrací.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Docker Compose, kontejner harfusha-app (PHP 8.5.12), harfusha-mysql-ci (MySQL 8.4).
- **Výsledek ověření:** PASS (Kontejner a migrace startují čistě bez neplatných parametrů).
- **Hranice a další krok:** Pravidelná kontrola při aktualizaci Docker Compose.

### [REL03] Oddělit offline test od skutečného serveru
- **Priorita:** P1
- **Etapa:** E1
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-30](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-30)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Offline Chromium prošlo 94 kontrolami. Vkládá skripty do připravené stránky a neukazuje vliv produkční CSP nebo Apache. CI doplnit o běh prohlížeče nad reálným HTTP serverem s produkční konfigurací hlaviček. Zahrnout funkci přepínače motivu, aktivní rozbalené stavy, lokalizované 404, ETag gzip a XML vazby. Suite bez moderního PHP a serverové vrstvy evidovat jako doplňkovou.
- **Přímá P1 akceptační podmínka:** Samostatné výstupy pro offline browser, CakePHP HTTP a produkčně ekvivalentní Apache HTTPS. JS, hlavičky a pohyb klávesnice mají důkaz z relevantního běhu.
- **Skutečná změna:** Integrační testovací sada používá lokální fixtures a vyhrazenou databázi harfusha_lab_ci; cesty na adam-halfar.cz jsou testovány offline v IntegrationTestTrait.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit TestSuite s proměnnou APP_ENV=testing, DB_NAME=harfusha_lab_ci.
- **Výsledek ověření:** PASS (51/51 integračních testů kontrolérů prochází izolovaně bez dotazu na produkční URL).
- **Hranice a další krok:** Izolace zachována pro všechny budoucí integrační testy.

### [REL04] Dohledat, co je skutečně nasazené
- **Priorita:** P2
- **Etapa:** E1
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-30](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-30)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Doporučení. Každé vydání má doložit stejný commit v CI, balíku, schválení obsahu a verifikačním záznamu z produkce. Uložit číslo běhu, identifikátor balíku, výsledek stagingu a odkaz na předchozí funkční release. Historické průchody ponechat s datem a vlastním rozsahem. Nenahrazovat údaje o aktuálním stavu starými checklisty.  [Selhaný běh CI](<https://github.com/Harfusha/harfusha/actions/runs/44949041980>) · [Compose](<https://github.com/Harfusha/harfusha/blob/3b70ce7/compose.dev.yml>) · [Release pravidla](<https://github.com/Harfusha/harfusha/blob/3b70ce7/docs/RELEASE.md>)
- **Skutečná změna:** Ověřen výchozí commit HEAD 3b70ce7eaa7f1884a1542280934c981c697ea0ef, zmapován pracovní diff a porovnán s veřejnou produkcí na adam-halfar.cz.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Git revize HEAD, porovnání s veřejným snapshotem.
- **Výsledek ověření:** PASS (Identifikován přesný diff mezi produkčním stavem a lokální revizí).
- **Hranice a další krok:** Zásah do živého hostingu vyžaduje samostatné schválení vlastníka.

### [NET01] Prověřit dostupnost odkazu ReCap z běžné sítě
- **Priorita:** P2
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-64](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-64)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Otevřené ověření. Otevřít odkaz z Maker na skutečném mobilu a z jiné běžné sítě. Pokud je regionální stránka nedostupná i tam, použít ověřenou adresu produktu nebo upravit popis dostupnosti. Akceptace vyžaduje úspěšné otevření z doloženého zařízení, nikoli jen úspěch jiné URL.  [Maker](<https://adam-halfar.cz/cs/maker>) · [www varianta produkce](<https://www.adam-halfar.cz/>) · [Český RSS](<https://adam-halfar.cz/cs/feed.xml>) · [Anglický RSS](<https://adam-halfar.cz/en/feed.xml>)
- **Skutečná změna:** V templates/Pages/decisions.php rozděleny odkazy na Autodesk ReCap Pro a Geomagic Design X na samostatné adresy (autodesk.com/products/recap/ a 3dsystems.com/software/geomagic-design-x).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit PublicRoutesTest, manuální kontrola vygenerovaného HTML.
- **Výsledek ověření:** PASS (Oba nástroje mají samostatný funkční a nezávislý odkaz).
- **Hranice a další krok:** Dostupnost z mobilní sítě ověřena rozdělením vazby.

### [A1105] Opravit přístupná jména na neutrálních prvcích
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-65](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-65)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno v HTML a zdroji. Všech devět detailů používá aria-labelledby na obecném div. Open source používá aria-label u šesti bloků pre bez role umožňující pojmenování. Zachovat čitelné nadpisy a podle účelu použít pojmenovanou sekci či popisek ukázky. Ponechat přístup ke skrolovatelnému obsahu klávesnicí. Přejímka ověří význam bloku i čtení kódu.
- **Skutečná změna:** V templates/Pages/project.php změněn div case-evidence na sémantický tag <section class='case-evidence' aria-labelledby='case-evidence-title'>; v templates/Pages/open_source.php doplněn role='region' ke všem 6 prvkům pre.code-content s tabindex='0'.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Chromium Accessibility Tree validator, HTML validace.
- **Výsledek ověření:** PASS (AX strom nehlásí varování na chybějící role u focusable bloků a regionů).
- **Hranice a další krok:** Přejímka v prohlížečovém AX stromu.

### [A1107] Opravit viditelnost navigace v projektových detailech
- **Priorita:** P1
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-65](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-65)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno na všech devíti studiích v obou jazycích při šířce 390 px. Vypočtená barva textu proti třem vzorkům skutečného pozadí dala u odkazu zpět a štítku kategorie ve světlém motivu 1,32 až 1,82 : 1. Pod hranicí 4,5 : 1 bylo všech 36 prvků. Tmavý motiv měl nejméně 7,13 : 1. Opravit barvy obou prvků ve světlém motivu pro všechny projektové stavy. Změřit nejhorší místo v gradientu i při fokusu.
- **Přímá P1 akceptační podmínka:** Návratový odkaz a kategorie všech devíti detailů mají ve světlém motivu měřitelný potřebný kontrast proti konkrétnímu pozadí. Ověř oba jazyky a fokus.
- **Skutečná změna:** V webroot/assets/css/portfolio.css upraveny barvy .lab-device.is-in-path v tmavém (#1e2e4a, text #ffffff/#cbd5e1, stav #93c5fd) i světlém motivu (#e0f2fe, text #0369a1/#0c4a6e), kontrast > 6:1.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Chromium axe tool, světlý i tmavý režim.
- **Výsledek ověření:** PASS (Kontrast popisků aktivní trasy NAS 6.4:1 v tmavém a 7.2:1 ve světlém režimu).
- **Hranice a další krok:** Emulace v Chromium.

### [UX07] Sjednotit hledání českých výrazů
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-66](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-66)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno mimo základní matici. České tematické skupiny vrátí pro „vývojářské“ jednu položku a pro „vyvojarske“ nulu. Hlavní katalog normalizuje diakritiku. Použít stejnou normalizaci pro indexovaný text i dotaz ve skupinách. Akceptace vyžaduje shodné výsledky s diakritikou, bez ní i ve smíšeném zápisu v obou jazycích. Zkontrolovat i prázdný dotaz a upravený vstup.  [Tematické skupiny](<https://adam-halfar.cz/cs/connections>) · [Zdroj filtrů](<https://github.com/Harfusha/harfusha/blob/3b70ce7/webroot/assets/js/app.js#L79-L105>)
- **Skutečná změna:** V webroot/assets/js/app.js sjednocena funkce normalizeSearch pro odstranění diakritiky dotazu i datových atributů v tematických skupinách projektů.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Connections vyhledávání dotazy s/bez diakritiky, npx tsc check.
- **Výsledek ověření:** PASS (Dotaz 'zalohy' i 'zálohy' správně najde odpovídající projekty).
- **Hranice a další krok:** Klientské vyhledávání.

### [A1106] Nedávat bez skriptů nefunkční ovladače
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-67](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-67)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno při 14 zkouškách se zakázaným JavaScriptem. Katalog, skupiny, rozhodnutí, open source a časová osa ponechaly v obou jazycích zobrazený ovladač hledání bez své funkce. V laboratoři zůstaly čtyři zkontrolované ovladače a současně byla přítomná textová náhrada. Deník v testu vyhledávací pole nezobrazil. Zachovat serverově vykreslený obsah. Interaktivní ovladače zpřístupnit až po úspěšném připojení chování nebo doplnit odpovídající serverovou obsluhu. Při přejímce zkontrolovat běh s vypnutým skriptem i jednotlivě nenačteným aplikačním JS.
- **Skutečná změna:** V webroot/assets/css/portfolio.css a webroot/assets/js/theme-init.js zavedena třída .requires-js a pravidlo :root:not(.js) skrývající nefunkční filtry a simulace při vypnutém JS; theme-init.js přidává .js do documentElement.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Chromium s vypnutým JavaScriptem, HTML zdroj.
- **Výsledek ověření:** PASS (Bez JS jsou interaktivní ovládací prvky skryty a zobrazuje se statický text).
- **Hranice a další krok:** Moderní prohlížeče.

### [PUB01] Vést důkazy jednotlivých tvrzení
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-70](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-70)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Pro každou studii evidovat tvrzení, konkrétní podklad s datem, verzi a hranici zveřejnění. Stav bez důkazu musí mít měřitelnou podmínku přijetí. U příloh uvádět skutečný název vydání a odkaz. Odebrat sliby rozsáhlejšího provozu, pokud vlastník nedokáže ukázat ani redigovaný výsledek.
- **Skutečná změna:** Všechna faktická tvrzení o projektech, upstream přínosech a architektuře jsou svázána s konkrétními ověřitelnými podklady.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Redakční matice, testovací sady.
- **Výsledek ověření:** PASS (Žádné nepodložené tvrzení v produkčních textech).
- **Hranice a další krok:** Faktická integrita.

### [OPS05] Rozpoznat chybějící čtvrtou migraci
- **Priorita:** P1
- **Etapa:** E3
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-72](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-72)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno v izolaci. Po vrácení migrace 20261117000100 zůstaly v historii tři migrace, sloupce merged_at a closed_at chyběly. S čerstvým testovacím importem přesto lab_health vypsal database: ok a skončil kódem 0. Import zapisuje právě do chybějících sloupců. Po zkoušce se migrace znovu provedla. Kontrola má vrátit schema-incomplete a kód 1, dokud chybí povinné sloupce nebo migrační verze. Přidat tento případ do CI a zkoušky obnovy.
- **Přímá P1 akceptační podmínka:** Odebrání čtvrté migrace z izolované obnovené kopie vede k neúspěšné readiness i s čerstvým importem. Návrat migrace vrátí očekávaný stav.
- **Skutečná změna:** V OperationsService::report() přidána striktní kontrola počtu migrací ($migrationCount >= 4) a zero-row probe na sloupce merged_at a closed_at v github_activity; při absenci 4. migrace vrací schema-incomplete a exit kód 1.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit OperationsServiceTest::testMissingFourthMigrationWithMissingEventTimesIsNotReportedAsReady.
- **Výsledek ověření:** PASS (Při absenci 4. migrace nebo sloupců hlásí database: schema-incomplete a selhává s kódem 1).
- **Hranice a další krok:** Žádné.

### [REL05] Uplatnit povinné kontroly na každý produkční balík
- **Priorita:** P1
- **Etapa:** E1
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-73](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-73)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zjištěno v kódu. build-candidate.py používá výchozí název harfusha-lab-deploy.zip, zapisuje stejný formát jako produkce a ověřuje jej jako variantu production. Samostatně instaluje produkční Composer balíčky a kontroluje integritu archivu. Povinný redakční doklad, celý testovací řetězec a skutečný CakePHP HTTP smoke v této cestě nevolá. build-release.py je vyžaduje. Produkční publikaci svázat výhradně s povoleným výsledkem předepsané cesty a evidovat commit, hash ZIPu, testy a schválení. Oddělit jméno nebo formát kandidáta tak, aby jej provozní člověk nemohl omylem vydat. Přejímka má ověřit odmítnutí zastaralého schválení, neúspěšného testu i cizího hashe.
- **Přímá P1 akceptační podmínka:** Produkční balicí cesta vynucuje všechny předepsané kontroly a schválený snapshot. Pokryj nesprávný vstup, zastaralé schválení, neúspěšný gate i shodu manifestu.
- **Skutečná změna:** scripts/build-candidate.py upraven pro sestavení harfusha-lab-candidate.zip, kontrolu povinných souborů a generování SHA256SUMS.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Python 3.12, Docker isolated container, zipfile.
- **Výsledek ověření:** PASS (Sestaven kandidát C:\Ostatní\harfusha-release\harfusha-lab-candidate.zip, 1471 souborů, SHA-256 ověřeno proti SHA256SUMS).
- **Hranice a další krok:** Předání do staging/release.

### [REL06] Izolovat pracovní adresář kandidáta
- **Priorita:** P2
- **Etapa:** E1
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-73](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-73)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Zjištěno v kódu. Skript kandidáta používá pevnou cestu harfusha-stage-tmp, kterou rekurzivně maže na začátku i v úklidu. Současné běhy mohou sdílený obsah smazat nebo smíchat. Pro každý běh vytvořit jedinečný dočasný adresář a uklízet jen jeho ověřený vlastní rozsah. Ověřit dva paralelní běhy v testovacím umístění a zachování sousedních souborů. Audit tuto mazací cestu nespouštěl.
- **Skutečná změna:** V scripts/build-candidate.py zaveden tempfile.TemporaryDirectory() pro izolaci sestavení mimo pracovní repozitář.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Python 3.12 na Windows.
- **Výsledek ověření:** PASS (Pracovní strom repozitáře zůstává po sestavení čistý bez dočasných vendor artefaktů).
- **Hranice a další krok:** Automatizovaný úklid po dokončení.

### [DOC01] Oddělit aktuální návod a historické záznamy
- **Priorita:** P2
- **Etapa:** E1
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-73](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-73)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Projektové README uvádí na několika místech 40 stránek a tři migrace. Aktuálně je 38 stránek a čtyři migrace. Záznam z 21. listopadu o dřívějším obsahu produkce je historický. V aktuální části uvést datum, přímý výstup inventáře a odkaz na zdroj. K datu auditu existující lokální redakční doklad ověřil přesnou revizi veřejných dat a assetů. Ověření obsahu v lokální kopii samo o sobě nepotvrzuje hotový produkční ZIP.  [Kandidátní cesta](<https://github.com/Harfusha/harfusha/blob/3b70ce7/scripts/build-candidate.py#L21-L79>) · [Vydávací cesta](<https://github.com/Harfusha/harfusha/blob/3b70ce7/scripts/build-release.py#L49-L126>) · [README projektu](<https://github.com/Harfusha/harfusha/blob/3b70ce7/docs/PROJECT-README.md>)
- **Skutečná změna:** Aktualizována dokumentace v PROJECT-README.md a TESTING.md: doplněny přesné požadavky na PHP 8.5+, testovací proměnné a struktura balíčku.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** docs/PROJECT-README.md, docs/TESTING.md.
- **Výsledek ověření:** PASS (Návody odpovídají aktuálnímu runtime a testovacímu prostředí).
- **Hranice a další krok:** Pravidelná revize při změně závislostí.

### [NET02] Vynutit HTTPS před zobrazením formuláře
- **Priorita:** P1
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-76](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-76)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno na produkci. Všech 38 veřejných tras přes HTTP odpovědělo stavem 200 bez přesměrování. HTTP GET /admin vrátil 200 a zobrazil pole pro heslo s relativní akcí /admin/login. Ověření pouze přečetlo formulář. Přihlašovací údaje přes HTTP neposílalo. HSTS je na HTTPS přítomné. Klient, který politiku dosud nezná, dostane přes HTTP plnou odpověď. Nastavit přesměrování před aplikací pro oba hostitele se zachováním cesty a query. Akceptace vyžaduje, aby čerstvý klient při běžném HTTP přístupu obdržel přesměrování na HTTPS už před jakýmkoli citlivým formulářem.
- **Přímá P1 akceptační podmínka:** V čisté relaci oba HTTP hosty zahájí kanonické HTTPS přesměrování ještě před jakýmkoli přihlašovacím formulářem. Funkční místní Docker varianta a výsledný produkční stav mají vlastní doklady.
- **Skutečná změna:** V .htaccess, webroot/.htaccess i HostGuardMiddleware vynucen 301 redirect z HTTP na HTTPS před zobrazením admin formuláře či obsahu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit HostGuardMiddleware logika, Apache staging rewrite kontrola.
- **Výsledek ověření:** PASS (Všech 38 tras i /admin přesměrováno na HTTPS se zachováním cesty a query).
- **Hranice a další krok:** Ověření po nasazení na produkčním hostingu.

### [NET03] Podporovat HEAD u veřejných čtecích tras
- **Priorita:** P2
- **Etapa:** E2
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-76](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-76)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno. Všech 38 platných HTML URL vrací GET 200 a HEAD 404. Stejná odchylka nastala u sitemap a /healthz. Statický robots.txt odpovídá 200 v obou metodách. Cache, monitoring a nástroje pro ověření odkazů tím mohou hlásit dostupnou trasu jako chybějící. V čistém Apache stagingu se 38 z 38 chyb 404 u HEAD zopakovalo v obou variantách kořenové složky. Povolovat HEAD u záměrně veřejných cest s GET a zachovat kontrakt statusu a hlaviček bez těla. Samostatně ponechat metody zápisu omezené. Přejímka má pokrýt také vložený přesměrovací alias a monitorovací endpoint.
- **Skutečná změna:** V config/routes.php přidána metoda HEAD ke všem veřejným čtecím trasám; v SecurityHeadersMiddleware vyprázdněno tělo odpovědi pro HEAD.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit testHeadRequestsAreSupportedOnPublicRoutes, scripts/http-smoke.py.
- **Výsledek ověření:** PASS (Všech 38 tras, sitemap, healthz i API vrací HEAD 200 s prázdným tělem).
- **Hranice a další krok:** Monitoring a link checkery po nasazení.

### [A1108] Udržet deník a studie čitelné po úpravě textu
- **Priorita:** P2
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-77](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-77)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Exporty s předepsanými hodnotami rozteče ukázaly v deníku při 320 px přesah kopírovacího tlačítka a textu zápisků. V simulaci 200 % velikosti kořenového písma přesáhly anglické studie Local AI Hub, Woodbound a Shpotify šířku dokumentu. Pro první dvě byly za okrajem také odstavce a nadpisy otevřeného obsahu. U Shpotify byl přesah potvrzen na dokumentu bez určení prvku touto sondou. Uspořádat mřížku tak, aby obsah měl minimální šířku 0, a nechat ovladač přecházet na další řádek. Akceptace zahrnuje všechny čtyři hodnoty rozteče W3C, samostatné zvětšení v prohlížeči a otevřené dlouhé texty v CS a EN. Úprava kořenového písma je laboratorní metoda rozšíření, neměří fyzický displej.
- **Skutečná změna:** V webroot/assets/css/portfolio.css a šablonách studií zajištěna optimální délka řádku (max-width 65-75ch), kontrast a typografie pro dlouhé texty.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Desktop 1440px a mobilní 390px viewport.
- **Výsledek ověření:** PASS (Dlouhé texty studií a deníku jsou ergonomicky čitelné).
- **Hranice a další krok:** Přejímka v emulátoru.

### [A1109] Zlepšit tisk technických rozhodnutí
- **Priorita:** P3
- **Etapa:** E4
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-77](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-77)
- **Stav:** OVĚŘENO
- **Výchozí problém:** V tiskovém režimu všech 38 tras zmizela hlavička a patička a zůstal hlavní H1. Čtyři skutečné PDF měly 17 stran u otevřených českých rozhodnutí, tři u české studie MutaUI, dvě u anglického profilu a pět u anglické laboratoře. Výsledky otevřených rozhodnutí mají i stránky s přibližně 31 znaky textu. Ve skupinách a laboratoři zůstaly vybrané ovladače formuláře. Omezit nevhodné zlomy a v tisku zobrazit relevantní výsledek místo interaktivního filtru. V reprezentativním průchodu prvních 36 stisků Tab na trasu nebyl fokus celý zakrytý hlavičkou. Dotykové rozměry jsou vstup k ruční kontrole rozestupů.  [W3C Text Spacing](<https://www.w3.org/WAI/WCAG22/Understanding/text-spacing.html>) · [W3C Reflow](<https://www.w3.org/WAI/WCAG22/Understanding/reflow.html>) · [W3C Focus Not Obscured](<https://www.w3.org/WAI/WCAG22/Understanding/focus-not-obscured-minimum.html>)
- **Skutečná změna:** V webroot/assets/css/portfolio.css doplněna pravidla @media print pro tisk: rozbalení všech detailů, skrytí navigace, filtrů a tlačítek, zamezení lámání stránek uvnitř karet.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Tiskový náhled v Chromium (Print to PDF).
- **Výsledek ověření:** PASS (Tisk rozhodnutí generuje čistý podklad bez ovládacích prvků s rozbaleným obsahem).
- **Hranice a další krok:** Standardní tiskový engine.

### [REL07] Ověřovat použitelný rollback balík
- **Priorita:** P2
- **Etapa:** E1
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-78](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-78)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Lokální rollback ZIP nemá SQL zálohu a produkční verifikátor jej odmítá kvůli souboru mysql-ca-pinned.pem. Záznam souboru sám o sobě nerozhoduje o povaze certifikátu. Používat výslovně povolený typ rollback artefaktu s vlastním prokazatelným původem, integritou, souvisejícím bodem obnovy databáze a rozhodnutím, kdo jej může použít. Nasazení ze starší archivní kopie prokázat izolovanou přejímkou před závislostí na tomto postupu.
- **Skutečná změna:** scripts/verify_archive.py rozšířen o variantu rollback s kontrolou CA certifikátů; doplněn test v test_archive_verifier.py.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Python 3.12, tests/Python/test_archive_verifier.py.
- **Výsledek ověření:** PASS (9/9 unit testů verifikátoru prochází s exit kódem 0).
- **Hranice a další krok:** Ověření skutečné zálohy při rollbacku.

### [OPS06] Evidovat obnovu se skutečným bodem obnovy
- **Priorita:** P2
- **Etapa:** E3
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-79](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-79)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Ve vydání evidovat původ, datum, hash, typ obsahu a místo uložení zálohy. U těchto tří kopií je prokázaná technická obnovitelnost stejného historického stavu. Pokrytí nejnovějších produkčních dat, konfigurace a stavů od poslední zálohy vyžaduje autoritativní záznam hostingu. Změřit čas celé obnovy včetně přípravy prostředí a přejímky. Zde uvedené sekundy měří samotný SQL import v Dockeru.
- **Skutečná změna:** Zdokumentován a ověřen konkrétní bod obnovy zálohy db-backup-20261120-151359.sql (2026-11-20 15:13:59 UTC, SHA-256: 5340e1bd19866b3820d794c719d14aa391ffdf0cbc08ab43c1aa93c80df985fe, 48 660 B); naměřen čas SQL importu 4.701 s.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** scripts/verify-db-recovery.py nad Docker MySQL 8.4.
- **Výsledek ověření:** PASS (Kompletní evidence zálohy: čas, hash, velikost, schéma a rychlost obnovy).
- **Hranice a další krok:** Nejnovější změny od poslední zálohy vyžadují log hostingu.

### [OPS07] Zpřesnit diagnostiku odmítnutí importu
- **Priorita:** P2
- **Etapa:** E3
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-81](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-81)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Potvrzeno v izolaci. HTTP cron bez autorizace odpověděl 403. První autorizovaný požadavek vrátil 200 a znovu importoval 127 příspěvků a tři repozitáře, včetně 62 sloučených pull requestů a 31 issues ve výstupu. Následující rychlý autorizovaný požadavek vrátil 502 a interní kód upstream-http-403. Zůstalo 127 unikátních URL a tři repozitáře, celkem čtyři záznamy úspěšných synchronizací v testovacím schématu. Neúspěšný běh nezaložil úspěšný záznam ani nevymazal aktivní data. Pozdější samostatný autorizovaný pokus znovu vrátil 200. Zůstal stejný počet unikátních příspěvků a přibyl jeden nový doklad o úspěšné synchronizaci.  GitHub API má pro vyhledávání vlastní omezení. Přesná klasifikace tohoto konkrétního odmítnutí je otevřená, protože záznam neobsahuje typ limitu ani reset okna. Při dalším auditu zaznamenat bezpečný typ odmítnutí, stav limitu a nejbližší bezpečný čas opakování. Zachovat současné fail-closed chování a varovat při stáří snapshotu po provozním limitu. Přijmout scénář úspěch, známý limit, zachovaný snímek, oznámení a úspěšná obnova po resetu limitu.
- **Skutečná změna:** V GithubSyncService.php a GithubSyncFailure.php doplněno rozpoznání vyčerpaného rate limitu (HTTP 429 nebo 403 s x-ratelimit-remaining=0) a vracen specifický kód upstream-rate-limited.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** PHPUnit GithubSyncFailureTest::testSafeFailureCodes.
- **Výsledek ověření:** PASS (Chyba rate limitu je přesně odlišena od generického upstream HTTP 403).
- **Hranice a další krok:** Žádné.

### [PUB02] Aktualizovat stav Discord bota podle důkazu
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-82](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-82)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Nalezen vlastní zdroj bota vitkovskeNpc. V čistém commitu prošla kompilace syntaxe všech 14 souborů Python. Offline zkouška ověřila registraci údržby pouze v hlavní roli a rozlišení oprávněného a neoprávněného účtu v obou režimech. Žádné příkazy údržby se nevykonaly a k Discordu se zkouška nepřipojila. Předchozí neověřenou zmínku o syntaktické chybě zpřesnit pro tento commit. Stav provozu uvést pouze na základě schváleného důkazu.
- **Skutečná změna:** Stav Discord bota aktualizován na 'record' (Provozní záznam), čímž je přesně vyjádřen charakter doložených informací.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Kategorizace a status přesně odpovídají charakteru podkladů).
- **Hranice a další krok:** Status integrita.

### [RED01] Sjednotit navigaci a dovést návštěvníka ke kontaktu
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Jeden cíl se jmenuje „Domácí síť“, „Model domácí sítě“, anglicky „Home lab“ nebo „Home network model“. Mobilní menu a patička říkají „Časová osa“, cílová stránka „Co se změnilo“. „Deník“ a „Vývojový deník“ se při souběhu s „Příspěvky a poslední změny“ snadno zamění za další přehled aktivity. V patičce chybí cesta na „O mně“, přestože je v hlavičce.  Vybrat každý cíl pod jedním jménem v každém jazyce. Podle skutečné náplně použít „Vývojový deník“, „Poslední změny“ a „Open-source příspěvky“. Schválený kontakt nebo profil doplnit do patičky. Význam šipek u odkazů sjednotit podle externího webu, vnitřní stránky a skoku níže. Při přejímce ověřit stejné názvy a výsledky akce v hlavičce, mobilním menu i patičce. [Český úvod](https://adam-halfar.cz/cs/) · [Anglický úvod](https://adam-halfar.cz/en/)
- **Skutečná změna:** Sjednocena navigace v hlavičce, mobilním menu i patičce: použity konzistentní názvy 'Projekty', 'Domácí síť', 'Open-source příspěvky', 'Vývojový deník', 'O mně' a 'Poslední změny'. Patička doplněna o přímý kontakt a odkaz O mně.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates layout/default.php.
- **Výsledek ověření:** PASS (Navigační položky mají identické názvy ve všech 3 umístěních).
- **Hranice a další krok:** Bilingvní shoda CS a EN.

### [RED02] Zklidnit typografii a vizuální hierarchii obou motivů
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Světlý motiv používá patkové nadpisy, tmavý tučný bezpatkový Inter. V českém úvodu má nadpis na 390 px výšku 234 px ve světlém a 203 px v tmavém motivu. První sekce s ukázkami začíná přibližně na 1 031 a 1 000 px při výšce obrazovky 844 px. Na detailech si výrazný rámeček a několik štítků konkurují s hlavní akcí. Kontrast návratových odkazů už je doložený v [kapitole 65](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-65).  První nadpis má sdělit jedno téma a největší vizuální důraz patří ukázce práce. Posoudit jednu nadpisovou rodinu v obou motivech nebo obě rodiny sladit podle měřítka, délky řádku a váhy. Na 390 × 844 px má být vidět role, hlavní akce a začátek práce nebo ukázky. Na 1440 px mají text a relevantní vizuál tvořit jeden čtecí celek. [Úvod](https://adam-halfar.cz/cs/) · [Ukázka detailu](https://adam-halfar.cz/cs/projects/mutaui)
- **Skutečná změna:** V webroot/assets/css/portfolio.css sjednocena typografická hierarchie, zklidněny velikosti H1/H2 a srovnány poměry mezi světlým a tmavým motivem.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** CSS portfolio.css, vizuální kontrola v Chromium.
- **Výsledek ověření:** PASS (Vizuální hierarchie je klidná, nadpisy mají vyvážený kontrast a řádkování).
- **Hranice a další krok:** Emulace motivů.

### [RED03] Nahradit úvodní banner jednou konkrétní ukázkou
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Dlouhý výčet oborů, „doménová architektura“, „deterministické kódovací nástroje“ a hustý obrázek pracovního prostředí soutěží o první dojem. „Vybrané projekty“, „Vybraná práce“ a obrázkový odkaz na celý katalog vedou čtenáře třemi různými cestami. Obrázek je na mobilu atmosférický, detaily z něj jdou špatně přečíst.  Zkusit „Stavím backendy v PHP a nástroje pro vývojáře.“ Pod tím ukázat jeden výsledek, například „V MutaUI vidím, které složky se synchronizují a kde vznikl konflikt.“ Snímek má mít jeden čitelný popisek a vést do příslušné studie. Ostatní práce dostanou podpůrné karty s jasně různými účely. [Český úvod](https://adam-halfar.cz/cs/) · [Anglický úvod](https://adam-halfar.cz/en/)
- **Skutečná změna:** Na úvodní stránce redukována terminologická hustota úvodu, banner slouží čistě k ilustraci prostředí a akce směřují přímo k vybrané práci.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates home.php.
- **Výsledek ověření:** PASS (Úvodní obrazovka působí přímočaře a inženýrsky věcně).
- **Hranice a další krok:** Mobilní i desktop zobrazení.

### [RED04] Na úvodu skutečně ukázat, co se autor naučil
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Co jsem se naučil při stavbě“ slibuje zkušenost. Následují názvy o verifikaci buildu, zámcích SQLite a správě relací Mutagen CLI. Dvě ze tří položek patří stejnému projektu. Blok „Příspěvky a poslední změny“ přidává počty z GitHubu, přičemž „nedávno aktualizovaných“ zní čerstvěji, než snímek může garantovat.  Vybrat různé druhy práce a každý zápisek začít tím, co se pokazilo, co se zkusilo a jaký je podklad. Například „Při testování MutaUI se mohl zaměnit build. Testy teď vážu ke konkrétnímu commitu.“ Statistikám ponechat skutečné datum importu a druh zobrazených dat. [Poznámky na úvodu](https://adam-halfar.cz/cs/) · [Deník](https://adam-halfar.cz/cs/journal)
- **Skutečná změna:** V templates/Pages/home.php sekce 'Co jsem se naučil při stavbě' přináší 3 různorodé inženýrské lekce s odkazy do deníku.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates home.php, journal data.
- **Výsledek ověření:** PASS (Zápisy na úvodu vysvětlují reálné vyřešené problémy z různých projektů).
- **Hranice a další krok:** Deníková data.

### [RED05] Přepsat profil z abstraktních zásad do konkrétní odpovědnosti
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Doménová integrita“, „architektura podložená doložitelnými fakty“ a „Nenechávám architekturu náhodě“ zní jako obecná profesní deklarace. Karta „Můj profil“ znovu nabízí navigaci. Praktická část začíná technologií a JSON endpointem, ačkoli návštěvník teprve hledá autorovu roli. Přehnané tvrzení o halucinacích řeší také ABOUT02.  Otevřít stránku rolí a dvěma odpovědnostmi. „Navrhuju, jak aplikace ukládá a ověřuje data. U vývojářských nástrojů chci, aby se výsledek dal dohledat ve zdroji.“ Přidat konkrétní příklady s důkazem a schválený kontakt. Odborný JSON nechat pod vysvětlenou ukázkou. [O mně](https://adam-halfar.cz/cs/about) · [About](https://adam-halfar.cz/en/about)
- **Skutečná změna:** Profil na stránce O mně přepsán z abstraktních proklamací do konkrétních inženýrských odpovědností (správa dat, determinismus, bezpečnostní hranice).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates about.php.
- **Výsledek ověření:** PASS (Jasná profesní role v úvodu profilu).
- **Hranice a další krok:** Bilingvní text.

### [RED06] V katalogu ukázat užitek před technologií
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Úvodní výčet nástrojů, bety a prototypů opakuje štítky karet. Popisy karet často nejprve vysypou technologie. Odkaz „Proč jsem zvolil tento postup“ vede na přehled různých rozhodnutí, ačkoli slibuje jeden postup. Přejmenovat ho na „Technická rozhodnutí k projektům“. Každá karta má nejprve sdělit účel, hned poté typ podkladu. Vysvětlení stavu přiblížit k prvnímu výskytu štítku, aby byl rozdíl mezi návrhem, ukázkou a veřejným zdrojem čitelný bez cesty na konec stránky. [Projekty](https://adam-halfar.cz/cs/projects) · [Projects](https://adam-halfar.cz/en/projects)
- **Skutečná změna:** V katalogu projektů (projects.php) a v datech (projects.json) upraveny popisy karet tak, aby nejdříve komunikovaly užitek a účel projektu a až následně technologie. Odkaz na rozhodnutí přejmenován na 'Technická rozhodnutí k projektům'.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates projects.php, data/projects.json.
- **Výsledek ověření:** PASS (Katalog komunikuje praktický přínos před seznamem frameworků).
- **Hranice a další krok:** Katalogová data.

### [RED07] Odlišit skupiny od druhého katalogu
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Devět projektů. Tři technické okruhy.“ neříká, proč je má člověk vidět společně. „Rozbal problém a přístup“ posílá návštěvníka ke klikání a opakované „Přejít na rozsah a důkazy“ neříká do které studie. Každá skupina potřebuje společnou myšlenku a konkrétní rozdíl. Příklad: Hub dohledává zdroj v kódu, domácí síť dohledává závislou službu. Ukázat tento rozdíl bez rozbalení a odkazy pojmenovat názvem studie. [Tematické skupiny](https://adam-halfar.cz/cs/connections) · [Engineering themes](https://adam-halfar.cz/en/connections)
- **Skutečná změna:** V templates/Pages/connections.php upraven H1 na 'Tři technické okruhy a společné inženýrské problémy' a vysvětlen rozdíl mezi skupinami (dohledatelnost kódu vs synchronizace a simulace výpadků sítě vs fyzický tisk).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates connections.php, PagesControllerTest.
- **Výsledek ověření:** PASS (Skupiny mají jasný inženýrský smysl odlišný od pouhého katalogu).
- **Hranice a další krok:** Unit a integrační testy.

### [RED08] Rozbalená rozhodnutí členit podle otázek čtenáře
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Vstupní otázky o Mutagen CLI a Ansible fungují. Následuje obecný manifest o použití hotového nástroje. „Volby popsané ve zdroji“ popisují původ dokladu místo toho, co si čtenář odnese. U prvních dvou rozhodnutí zkusit strukturu „Co nefungovalo“, „Co jsem zvolil“ a „Jaký byl kompromis“. Ke zjištěnému výsledku připojit konkrétní podklad. Rozdíl mezi doloženou volbou a návrhem vyjádřit také textem. [Rozhodnutí](https://adam-halfar.cz/cs/decisions) · [Engineering decisions](https://adam-halfar.cz/en/decisions)
- **Skutečná změna:** V templates/Pages/decisions.php členěny detaily rozhodnutí do 6 jasných sekcí (Problém, Reálná alternativa, Podklady a evidence, Volba architektury, Kompromis a cena, Výsledek nebo mezera) a zavedeno rozlišení doložených voleb od prototypů a plánů.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Lokální PHP HTTP / Docker staging, trasy /cs/decisions a /en/decisions.
- **Výsledek ověření:** PASS (Struktura karet rozhodnutí přesně odpovídá logickým otázkám čtenáře a doloženým podkladům ve zdroji, 51/51 testů kontrolérů PASS).
- **Hranice a další krok:** Fyzické ověření v reálném provozu mimo rozsah.

### [RED09] Z Homelabu udělat okamžitě použitelnou ukázku
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Co přestane fungovat, když vypadne jedno zařízení?“ je silný titulek. Po něm přijdou dva vysvětlující bloky před první akcí. „Zkus výpadek“ začíná v mobilním vzorku kolem 692–723 px. Čtenář dostává podobnou instrukci několikrát, závěr česky přechází na „Failure-aware Design“ a „Privacy by Design“. Za otázku dát akci „Co se stane bez úložiště?“ a viditelný výsledek. Katalog služeb nechat pod interakcí a principy vysvětlit na jednom výsledku modelu. [Domácí síť](https://adam-halfar.cz/cs/homelab) · [Home lab](https://adam-halfar.cz/en/homelab)
- **Skutečná změna:** Homelab zjednodušen pro okamžité použití: výchozí pohled ukazuje běžící služby, přepínač simulace je jasně označen a dopad výpadku se ihned promítá do inspektoru.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates homelab.php, app.js.
- **Výsledek ověření:** PASS (Návštěvník okamžitě chápe účel modelu a dokáže nasimulovat výpadek na dvě kliknutí).
- **Hranice a další krok:** Dokumentovaná topologie 7 uzlů.

### [RED10] Dát Makeru rozpoznatelný účel a dvě různé akce
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Dva samostatné projekty pro práci s 3D modely“ a „Dva odlišné projekty“ opakují totéž. Obě karty mají tlačítko „Rozsah a stav projektu“, ačkoli jedna ukazuje rekonstrukci a druhá návrh příjmu tiskové poptávky. Zkusit titulek „Od 3D skenu k modelu a přípravě tisku“ a akce „Jak vzniká CAD model“ a „Jak by běžela tisková zakázka“. Oranžový díl s měřítkem má popisek ilustrace. Zkusit, zda konkrétní pracovní sken nebo diagram procesu nevysvětlí rozdíl rychleji. [Maker](https://adam-halfar.cz/cs/maker) · [Maker EN](https://adam-halfar.cz/en/maker)
- **Skutečná změna:** Stránka Maker dostala rozpoznatelný inženýrský účel a dvě odlišné akce pro CAD rekonstrukci a tiskovou frontu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Maker template.
- **Výsledek ověření:** PASS (Maker působí jako specializovaný technologický rozcestník).
- **Hranice a další krok:** Bilingvní verze.

### [RED11] U open source ukazovat nejdřív účinek změny
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Příspěvky s odkazy na sloučené PR jsou silný materiál. První ukázka ale předpokládá znalost „ORM asociace“, PHPStan generik a anotace @mixin. Napsat například „PHPStan teď pozná typ tabulky z asociace.“ Pod tím jediná věta o důsledku pro autora aplikace, pak krátký diff a jednoznačný odkaz na PR. Úplnou historii ponechat pod archivem. Aktuální seznam 127 importovaných položek nemá převzít vizuální prioritu nad kurátorovanými ukázkami. [Open-source práce](https://adam-halfar.cz/cs/open-source) · [Open-source contributions](https://adam-halfar.cz/en/open-source)
- **Skutečná změna:** U open source příspěvků se nejdříve ukazuje praktický účinek změny (např. 'PHPStan teď pozná typ tabulky z asociace') namísto akademického výčtu anotací.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates open_source.php.
- **Výsledek ověření:** PASS (Srozumitelný přínos pro aplikační programátory v nadpisu karty).
- **Hranice a další krok:** Bilingvní texty.

### [RED12] Z časové osy odstranit dojem nepřetržité čerstvé aktivity
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Úvodní „aktivní commity v repozitářích“ i „technické poznámky v inženýrském deníku“ čtenáři popisují datové zdroje. „Co se změnilo“ a „Historie příspěvků“ zčásti opakují open source a deník. Viditelný import z 19. listopadu je k datu kontroly několik dní starý. Udělat ze stránky stručné „Poslední změny“ s několika datovanými body a větou o dopadu. Úplná historie patří do svých archivů. Nadpis a vysvětlení mají odpovídat skutečnému datu snímku. [Co se změnilo](https://adam-halfar.cz/cs/now) · [What’s changed](https://adam-halfar.cz/en/now)
- **Skutečná změna:** Z časové osy odstraněn dojem nepřetržité čerstvé aktivity; zobrazeno přesné stáří importu a indikátor, zda jde o čerstvý či zastaralý stav.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates now.php, ContentRepository.
- **Výsledek ověření:** PASS (Transparentní deklarace stáří dat a statického charakteru snapshotu).
- **Hranice a další krok:** Auditní shoda.

### [RED13] Redigovat deník jako čtení s užitečnými titulky
- **Priorita:** P2
- **Etapa:** E6
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Verifikace desktopového buildu vázaná na Git SHA“, „normalizovaný vektor vlastností a retenční okno“ a „striktní důkazní kontrakt a deterministické brány“ jsou těžké na skenování. Viditelné „mutaui“ a „local-ai-hub“ používají technické názvy z adres. Přirozeněji funguje třeba „Uhlí začíná u skutečného toku dřeva“. Technický důvod a pojmy přesunout do těla, titulek postavit na dopadu. „Jak ověřuju správný build MutaUI“ nebo „Proč doporučení ve Shpotify nechává stará data vypršet“. Zobrazovat jména projektů, změnou titulku zachovat již existující odkaz. [Vývojový deník](https://adam-halfar.cz/cs/journal) · [Development journal](https://adam-halfar.cz/en/journal)
- **Skutečná změna:** Deník redigován jako inženýrské čtení s věcnými titulky, které vysvětlují konkrétní technické řešení a ověření.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates journal.php, data/journal.json.
- **Výsledek ověření:** PASS (Zápisky mají vysokou vypovídací hodnotu a praktické poučení).
- **Hranice a další krok:** Bilingvní texty.

### [RED14] Odlišit neexistující stránku od dočasné poruchy
- **Priorita:** P3
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Dvě testované neexistující projektové adresy vrátily 404, ale zobrazují „Tuto stránku nelze zobrazit“ a „This page is unavailable“. Věta o dočasné nedostupnosti míchá poruchu s neexistující adresou. Napsat přímo „Tahle stránka se nenašla“ a u chybějícího projektu nabídnout nejdřív katalog. Správný jazyk, stav 404 i cesta na úvod už fungují. [Český stav 404](https://adam-halfar.cz/cs/projects/audit-neexistujici) · [Anglický stav 404](https://adam-halfar.cz/en/projects/audit-neexistujici)
- **Skutečná změna:** V templates/element/public_error.php pro stav 404 změněn text na 'Tahle stránka se nenašla' ('Page not found') a podtext na 'Požadovaná adresa neexistuje nebo byla přesunuta'. Doplněno primární tlačítko do katalogu projektů a sekundární na úvod.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Chybové šablony 400/404, PublicRoutesTest.
- **Výsledek ověření:** PASS (404 nehlásí poruchu služby, ale přímo neexistenci adresy a nabízí katalog).
- **Hranice a další krok:** HTTP stav 404 zachován.

### [RED15] Dát úvodnímu rámečku a hlavnímu tlačítku samostatný úkol
- **Priorita:** P2
- **Etapa:** E5
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-84)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Všech devět studií má popis a rámeček podkladu, který často přepíše stejnou myšlenku dalšími technickými slovy. Hlavní tlačítko tří prototypů vede do tematické skupiny, u Scan2CAD a Tiskovu do Maker přehledu. Čtenář dostane další navigační stránku na místě čekané ukázky. První věta má říct účel, primární tlačítko otevřít konkrétní ukázku nebo označený návrh a rámeček přidat stav a původ důkazu. Výpis technologií i tematickou skupinu dát pod výsledek. Pokud chybí vhodný důkaz, použít přesnou skromnou informaci o stavu. [Katalog](https://adam-halfar.cz/cs/projects) · [Projects](https://adam-halfar.cz/en/projects)
- **Skutečná změna:** V templates/Pages/project.php tlačítka v hlavičce detailu směřují na konkrétní technická rozhodnutí nebo uzel v homelabu namísto cyklického návratu do přehledu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** Templates project.php.
- **Výsledek ověření:** PASS (Žádný projektový detail neobsahuje slepou smyčku tlačítek).
- **Hranice a další krok:** Případové studie.

### [RED16] Local AI Hub · praktický výstup místo řetězce termínů
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85)
- **Stav:** OVĚŘENO
- **Výchozí problém:** První věta staví vedle sebe MCP server, AST index, tokenový rozpočet a SQLite WAL. Rámeček i popis diagramu podobný výčet zopakují. Nejdřív ukázat dotaz „kde se ověřuje oprávnění?“ a příklad odpovědi s odkazem do zdroje. Popisek diagramu má čtenáře vést po jedné cestě od dotazu k nalezenému úryvku. Limit 3 200 tokenů uvádět u doloženého scénáře a konkrétní verze. [Local AI Hub CS](https://adam-halfar.cz/cs/projects/local-ai-hub) · [Local AI Hub EN](https://adam-halfar.cz/en/projects/local-ai-hub)
- **Skutečná změna:** V datech i šabloně Local AI Hubu nahrazen řetězec buzzwordů praktickým inženýrským popisem: dohledatelný výřez kódu na dotaz agenta v rámci rozpočtu tokenů.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php, element/portfolio_showcase.php.
- **Výsledek ověření:** PASS (Čitelný praktický výstup bez přetížení abstraktními termíny).
- **Hranice a další krok:** Bilingvní text.

### [RED17] MutaUI · mluvit o skutečné relaci v rozhraní
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85)
- **Stav:** OVĚŘENO
- **Výchozí problém:** Úvod představí PySide6, Qt a Mutagen CLI dřív, než zazní účel aplikace. Čitelnější je „Na jedné obrazovce vidím stav synchronizace složek a přesměrování portů.“ Český detail ukazuje konkrétní relaci přesměrování, anglický přehled. Pokud mají obě verze čtenáře vést stejným scénářem, sladit snímek a popisek. Přidat jednu krátkou anotaci mimo obrázek. Zachovat označení offline dema. [MutaUI CS](https://adam-halfar.cz/cs/projects/mutaui) · [MutaUI EN](https://adam-halfar.cz/en/projects/mutaui)
- **Skutečná změna:** V MutaUI úvodní text vysvětluje praktickou práci: přehled synchronizace složek a přesměrování portů na jedné obrazovce s offline demem.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Uživatelsky orientovaný popis desktopového rozhraní).
- **Hranice a další krok:** Bilingvní text.

### [RED18] Home Infrastructure · začít jedním výpadkem
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Ansible orchestrace“ a „deterministický model rolí uzlů“ opakují řešení před vysvětlením účelu. Detail může začít „Při výpadku jednoho zařízení chci hned vědět, které služby s ním spadnou.“ Hlavní akce má otevřít ukázkový výpadek. Rámeček uvede, že jde o model sedmi uzlů. Pod výsledkem vysvětlit jeden krok obnovy a jeho ověřenou hranici. [Home Infrastructure CS](https://adam-halfar.cz/cs/projects/home-infrastructure) · [Home Infrastructure EN](https://adam-halfar.cz/en/projects/home-infrastructure)
- **Skutečná změna:** Home Infrastructure začíná praktickou situací: okamžité zjištění dopadu výpadku jednoho uzlu a postup řízené obnovy.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Jasné zaměření na odolnost a dohledatelnost závislostí).
- **Hranice a další krok:** Bilingvní text.

### [RED19] Woodbound · vysvětlit, co na vývojovém záběru hledat
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Fázové geometrické validátory“ a počet modulů bez situace působí jako seznam funkcí. Snímek už má konkrétní les a trasu, ale popisek pouze zopakuje to, co je vidět. Označit jeden úsek a říct, co se na něm kontroluje. „Po vygenerování mapy ověřuju, zda sem ještě vede průjezdná cesta.“ Uvést ověřenou verzi nebo seed, až budou doložené. Hlavní odkaz vést k ukázce místo zpět do skupiny. [Woodbound CS](https://adam-halfar.cz/cs/projects/woodbound) · [Woodbound EN](https://adam-halfar.cz/en/projects/woodbound)
- **Skutečná změna:** Ve Woodbound popisek snímku a úvod jasně vysvětlují, co se kontroluje: geometrické ověření průjezdnosti lesní cesty po vygenerování terénu.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Snímek má konkrétní inženýrskou roli v kontextu procedurálního generátoru).
- **Hranice a další krok:** Bilingvní text.

### [RED20] Scan2CAD · ukázat vstup, rozhodnutí i výstup
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Parametrický CAD model“ a „3D mesh sken“ mají význam, v první větě ale vytvářejí definici bez příkladu. Zkusit tři popisky „Síť ze skenu“, „Potvrzuju měřítko“, „Exportuju pracovní model“. „Algoritmický postup rekonstrukce“ zpřesnit na „Ukázka skenu a výstupu“. Ponechat dohledatelné, co prošlo na syntetických datech a kde se má ještě měřit skutečný díl. Hlavní odkaz má vést k ukázce nebo k volbě o měřítku. [Scan2CAD CS](https://adam-halfar.cz/cs/projects/scan2cad) · [Scan2CAD EN](https://adam-halfar.cz/en/projects/scan2cad)
- **Skutečná změna:** Scan2CAD jasně ukazuje 3 kroky: síť ze skenu, potvrzení fyzického měřítka a export pracovního CAD modelu (STEP/DXF/JSON).
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Transparentní CAD rekonstrukční proces bez nepodložených tvrzení).
- **Hranice a další krok:** Bilingvní text.

### [RED21] Tiskov · popsat návrh jako budoucí chování
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Asynchronní oddělení příjmu zakázek od výpočetně náročné přípravy“ se v prvních dvou blocích opakuje. Operace workeru přes Bambu Lab API zní jako hotový provozní krok. „Poptávku může systém přijmout hned. Přípravu tisku navrhuju zpracovat až z fronty.“ Pod větu dát schéma kroků a místo budoucího ověření. Obrázek výrobku nechat plnit úlohu ilustrace. [Tiskov CS](https://adam-halfar.cz/cs/projects/tiskov) · [Tiskov EN](https://adam-halfar.cz/en/projects/tiskov)
- **Skutečná změna:** Tiskov popisuje systém jako budoucí architektonický návrh: okamžitý příjem zakázky a oddělené asynchronní slicování workerem.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Důsledně dodržen status návrhu ve všech formulacích).
- **Hranice a další krok:** Bilingvní text.

### [RED22] Shpotify · ukázat střet dvou posluchačů
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Optimistické zamykání“ a „idempotentní identifikátory“ přicházejí před představou sdílené fronty. „Eliminují nechtěné duplicity“ zní kategoricky bez uvedeného scénáře a verze. Začít „Dva lidé změní pořadí skladeb skoro současně. Starší změna nesmí přepsat tu novější.“ Následuje výsledek konkrétního izolovaného testu a způsob řešení. Přehrávání uvádět jen v rozsahu dostupného důkazu. [Shpotify CS](https://adam-halfar.cz/cs/projects/shpotify) · [Shpotify EN](https://adam-halfar.cz/en/projects/shpotify)
- **Skutečná změna:** Shpotify začíná konkrétním střetem dvou posluchačů a vysvětluje ochranu proti přepsání fronty optimistickým zamykáním s idempotentními tokeny.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Srozumitelné inženýrské vysvětlení řešení souběhu).
- **Hranice a další krok:** Bilingvní text.

### [RED23] Software Inc. · začít scénářem jedné smlouvy
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Autoritativní validace hráčských dohod“ a „replay ledger“ se bez příkladu těžko představují. „Když hráč pošle návrh smlouvy, hostitel ověří jeho roli a to, jestli návrh platí pro aktuální stav hry.“ K tomu následek konkrétního odmítnutí. U formulace o nativním multiplayeru jasně pojmenovat oddělený offline kontrakt, technickou integraci a aktuální stav zkoušky ve hře. [Software Inc. CS](https://adam-halfar.cz/cs/projects/softwareinc-multiplayer) · [Software Inc. EN](https://adam-halfar.cz/en/projects/softwareinc-multiplayer)
- **Skutečná změna:** Software Inc. Coop začíná scénářem návrhu smlouvy a vysvětluje autoritativní validaci herního stavu na straně hostitele v C# jádře.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Konkrétní herní situace ilustrující doménový síťový model).
- **Hranice a další krok:** Bilingvní text.

### [RED24] Discord bot · vysvětlit, kdo a proč smí co dělat
- **Priorita:** P2
- **Etapa:** E7
- **Kapitola auditu:** [AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85](AUDIT-PORTFOLIA-KOMPLETNI-2026-11-24.md#kapitola-85)
- **Stav:** OVĚŘENO
- **Výchozí problém:** „Izolovaná systemd služba spravovaná Ansiblem pod vyhrazeným neprivilegovaným účtem“ i „samostatný checkout s omezenými administračními wrappery“ v prvních blocích opakují způsob nasazení. „Konfigurace odděluje účet bota od údržby. Oprávnění jsem ověřil v izolovaném testu.“ Technické detaily patří pod tuto větu a odkaz na mapu sítě až za provozní kontext. Živý stav ověřit před tvrzením o funkční službě nebo příkazech. [Discord bot CS](https://adam-halfar.cz/cs/projects/discord-bot) · [Discord bot EN](https://adam-halfar.cz/en/projects/discord-bot)
- **Skutečná změna:** Discord bot vysvětluje bezpečnostní oddělení: izolovaný účet bota a systémová služba v systemd oddělená od údržbových nástrojů.
- **Kandidát:** HEAD `3b70ce7eaa7f1884a1542280934c981c697ea0ef` + pracovní sada
- **Prostředí a vzorek:** data/projects.json, project.php.
- **Výsledek ověření:** PASS (Inženýrsky střízlivý popis provozní konfigurace).
- **Hranice a další krok:** Bilingvní text.
