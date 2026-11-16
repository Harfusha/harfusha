# Podklady k veřejným případovým studiím — 16. 11. 2026

Tento záznam shrnuje kontrolované zdrojové revize a hranice toho, co lze tvrdit na veřejném webu. Neobsahuje interní adresy, přihlašovací údaje ani absolutní cesty z pracovní stanice. Příklady na webu jsou označené jako ilustrativní, pokud nejde o přímo ověřený běh.

## Kontrolované zdroje

- **Local AI Hub — `ae765656`**. Aktuální README potvrzuje, že zdroj 6.1.0 je stále release candidate; pracovní změny v ostatních souborech checkoutu jsem do tvrzení nepřebíral. README popisuje sedm MCP nástrojů, jednorázovou registraci stabilního kořene a asynchronní pipeline: inventory → hash → code index → deterministic → Serena → lexical → RAG → file/module/project maps → hot queries → diagnostics → complete. Přesné dotazy míří nejdřív na deterministická fakta a indexy; složitější úkoly skládají adaptive context z více zdrojů. RAG a lokální syntéza nastupují až po levnějších indexovaných cestách. Serena a RAG jsou volitelné; lokální model není nutný pro přesné indexované dotazy. Testy spouští hostitelské prostředí.
- **MutaUI — `f94e63c`**. Ovládací plocha pro samostatně instalovaný Mutagen CLI a lokální démon; vlastní synchronizační algoritmus ani vzdáleného agenta neobsahuje. Přiložené snímky jsou offline demo se syntetickými daty a vypnutými akcemi. Podpora buildů není důkaz nativního přijetí na každém systému.
- **Home Infrastructure — `4c49dece`**. Git je zdroj požadované konfigurace, Semaphore spouští úlohy a Ansible je provádí. Veřejný model uvádí Pi-hole, Unbound, Jellyfin, Semaphore, BookStack, CUPS, NAS zálohy, WireGuard a testovací Proxmox. Jde o dokumentované role, ne živý monitoring; žádné interní adresy se nepublikují.
- **Woodbound — `72c22cdc5`**. Zdrojové návrhy popisují čtyři oblasti, hydrologii, průmyslová místa, trasové záměry, dvacet kroků fyzické pipeline a validátory. Vývojové kontroly ani bake nepotvrzují přijatý hratelný svět.
- **Scan2CAD Studio — `90e2940`**. Dokumentovaný beta postup vyžaduje fyzicky potvrzené měřítko, kontrolu geometrie a schválení odhadu kamery u referenčních fotografií. STEP/DXF je kandidát k odborné a fyzické kontrole, ne certifikované měření.
- **Shpotify — `46742be`**. Zdroj popisuje účty, pozvánky, osobní a sdílené místnosti, fronty a doporučování. Přehrávání v prohlížeči a vzdálený audio agent nejsou end-to-end ověřené.
- **Software Inc co-op mod — `c615daa`**. Doménové jádro, persistence a adaptér existují; aktuální README výslovně uvádí, že kandidát nebyl spuštěn ve hře. Hratelný multiplayer se netvrdí.
- **Discord bot — `9c0305e`**. Zdrojová kontrola dříve nalezla syntaktickou chybu v modulu údržby. Živý provoz není doložen; veřejný text projekt označuje jako rozpracovaný a v opravě.
- **Tiskov — `dd49a23`**. Symfony přijímá podklady, MySQL drží frontu a samostatný Raspberry Pi worker provádí slicing. Odhad pro Bambu Lab A1 je orientační, bez online platby a s ruční konečnou dohodou; aktuální běh a přesnost výpočtu nejsou ověřené.
- **Maker Lab**. Na přání nejsou zveřejněné jednotlivé výrobky; stránka zůstává neutrálním placeholderem.

## Previous candidate — superseded 16 Nov 2026

This section recorded the earlier candidate and is kept only as history. Its generic diagrams and multi-panel Playground have been superseded. Current source reconciliation and alternatives are recorded in [PROJECT-EVIDENCE-RECONCILIATION-2026-11-16.md](PROJECT-EVIDENCE-RECONCILIATION-2026-11-16.md) and [PROJECT-ALTERNATIVES-RESEARCH-2026-11-16.md](PROJECT-ALTERNATIVES-RESEARCH-2026-11-16.md).

## Nasazovací hranice

Dříve nasazený baseline `a4c39ca` prošel třemi CI joby, kontrolou archivu a živými HTTP/asset kontrolami. Izolovaný HTTPS staging se nepodařilo zřídit. Novější veřejný obsah vyžaduje nové kontroly a schválení přesného snapshotu; žádné starší schválení se na něj nepřenáší.
