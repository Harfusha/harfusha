# Záznam ověření – 13. listopadu 2026

> **Historický běhový záznam.** Výsledky platí pro tehdy uvedenou revizi a prostředí; aktuální stav: [MASTER_PLAN.md](MASTER_PLAN.md).

Tento soubor zachycuje **skutečně provedené testy** u zdrojového balíčku. Nenahrazuje externí CI, runtime CakePHP, audit PHPStanu ani staging na sdíleném hostingu. Podrobný seznam práce a přejímacích podmínek obsahuje `MASTER_PLAN.md`.

| Oblast | Výsledek v autorském prostředí | Příkaz / důkaz |
|---|---|---|
| Syntaktická kontrola PHP | Prošlo, 86 souborů | `php scripts/syntax-check.php` |
| Doménové, datové a šablonové kontroly | Prošlo, 141 tvrzení | `php tests/standalone.php` |
| Interaktivní JS jádro | Prošlo, 13 testů | `node --test tests/Client/lab-core.test.cjs` |
| Statická kontrola JS | Prošlo | `node --check ...`, `tsc --noEmit --allowJs --checkJs --strict ...` |
| Audit bezpečně publikovatelných dat | Prošlo, pouze heuristický sken | `python3 scripts/audit-public.py` |
| Audit vygenerovaného HTML | Prošlo, 48 stran, odkazy, zdroje, HTML ID | `python3 scripts/audit-rendered.py` |
| Skutečný prohlížeč | Prošlo, 50 kombinací 10 obrazovek a 5 rozlišení 320–1920 px | `python3 tests/Browser/smoke.py --chromium /usr/bin/chromium` |
| CakePHP aplikační HTTP integrační testy | **Neověřeno** | Vyžadují `vendor/`, PHP 8.5, `intl`, `mbstring`, MySQL |
| PHPStan max, ShipMonk, CakeDC | **Neověřeno** | Konfigurace existuje, analyzátor se nepodařilo spustit |
| CakePHP ORM/migrace/transakční admin workflow | **Neověřeno** | Závislosti a databáze zde nejsou dostupné |
| Composer security audit a lockfile | **Neověřeno** | `composer.lock` se bez reálného řešení závislostí nevymýšlí |
| PHP 8.5.12+ produkční runtime | **Neověřeno** | Přítomná PHP CLI verze je 8.4.24 |
| Ruční privacy audit a ověření skutečných projektů | **Nevyřešeno** | Nutný souhlas autora se soukromými texty a fotografie |
| Deployment na reálném hostingu, backup, restore, rollback | **Neověřeno** | Vyžaduje přístup k cílovému hostingu |

## Opravy provedené během posledního kola

- GitHub veřejná repo data jsou kontrolována jako celek před transakční obměnou snímku.
- Administrátorské změny projektů a deníku mají transakční auditní záznam; první publikace konceptu opravuje čas zveřejnění.
- Stránka technických rozhodnutí uvádí v češtině i angličtině konkrétní kompromisy a hranice ověření.
- Produkční bootstrap odmítá nebezpečné prostředí a neplatné nastavení veřejného originu.
- Mobilní přetečení stránky Open Source na 320 px je odstraněno a znovu otestováno.
- HTTP smoke test nově kontroluje skutečnou odpověď **302** kořenové URL a obsahová očekávání odpovídají textům obou jazyků.
- Opraveno tvrzení v `PublicRoutesTest` na skutečně používanou CakePHP HTTP assertion metodu.

## Další kroky bez zkratek

1. Připravit aktualizovaný PHP 8.5 runtime s potřebnými moduly, Composer 2 a izolovanou MySQL.
2. Vyřešit a zkontrolovat závislosti, uložit skutečný `composer.lock`, spustit `composer audit --locked`.
3. Spustit celou `composer quality`, opravit každou chybu PHPStan/PHPCS/PHPUnit bez snižování přísnosti.
4. Dokončit integrační testy autentizace, migrací, importéru, transakcí a HTTP middleware na reálném CakePHP.
5. Zopakovat prohlížečové testy proti **skutečnému HTTP serveru**, vyzkoušet Safari/Firefox, přístupnost a pomalou síť.
6. Nezávisle schválit veřejné informace o domácnosti a soukromých repozitářích a doplnit vlastní screenshoty/fotografie.
7. Nasadit na staging sdíleného hostingu, ověřit HTTPS, cron, oprávnění, izolaci `webroot/`, obnovu zálohy a rollback.
8. Až po úspěchu vytvořit **produkční ZIP s `vendor/`** skriptem `scripts/build-release.py`.

Zdrojový ZIP **není** bez dalších kroků produkčně instalovatelný a nelze jej označit za 100% otestovaný.

## Doplnění po pokračování v implementaci

- Čisté DTO `PublicProjectView` a API kontroler/HTTP ETag mají zdrojové testy. **Testy vyžadující CakePHP jsou připravené, nikoliv vykonané.**
- Nový `PublicSitemap` ověřuje jazyky, x-default, duplicity a nepovolené cesty.
- `requestTrace` je čistá deterministická simulace stavu HTTP pipeline, ne skutečné middleware volání. Testuje se ve 13 JS regresních testech a skutečném offline Chromium.
- U obou SVG map je po klávesnicovém výběru obnoven fokus i po překreslení, v playgroundu fungují šipkové ARIA taby.
- U STL parseru byly odstraněny nadbytečné celo-souborové regex objekty; test se 1250 trojúhelníky potvrdil omezený sampling.
- Snímky desktop/mobile pocházejí z aktuálního skutečně renderovaného Request Labu, nikoliv z návrhu.
