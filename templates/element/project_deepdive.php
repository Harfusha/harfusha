<?php
declare(strict_types=1);

use App\Support\Html;

$detail = match ($project->slug) {
    'local-ai-hub' => [
        [$lang->value('Deterministická AST indexace a optimalizace kontextového okna', 'Deterministic AST Indexing & Context Window Optimization'),
         $lang->value('První použití repozitáře spustí jednorázový asynchronní preprocessing. Hub provede kompletní inventarizaci souborů, spočítá inkrementální SHA-256 hashe a sestaví kódový index symbolů, tříd, metod a importů. Serena LSP a lexikální vyhledávání doplňují přesná sémantická data přímo do paměťové cache, takže LLM agenti neplýtvají tokeny na opakované čtení celých souborů.', 'First use of a repository triggers a one-time asynchronous preprocessing run. The Hub inventories files, computes incremental SHA-256 chunk hashes, and builds a code index of symbols, classes, methods, and imports. Serena LSP and lexical search supply precise semantic data directly to an in-memory cache, ensuring LLM agents never waste token budgets reading entire files repeatedly.'),
         $lang->value('Dotaz na vztahy symbolů nebo volání metod je vyřešen během jednotek milisekund ze strukturovaného AST grafu. Širší dotazy skládají adaptive context z více zdrojů, kde RAG a modelová syntéza přicházejí na řadu až po levnějších indexovaných cestách. Transakční SQLite WAL ledger garantuje spolehlivost a auditovatelnost všech provedených kroků.', 'Queries for symbol relationships or method callers resolve in single-digit milliseconds directly from the structured AST graph. Broader tasks compose adaptive context across multiple sources, where RAG and local model synthesis follow cheaper indexed routes. A transactional SQLite WAL ledger ensures reliable auditing of all actions.')],
    ],
    'mutaui' => [
        [$lang->value('Architektura asynchronního řízení a náhledu souběhu', 'Asynchronous Orchestration & Concurrency Preview Architecture'),
         $lang->value('MutaUI propojuje desktopový PySide6/Qt frontend s odděleným Mutagen CLI enginem přes asynchronní worker vlákna. Rozhraní provádí kontinuální polling stavu relací, analyzuje stav síťových tunelů a monitoruje využití systémových prostředků na pozadí bez blokování hlavního vlákna grafického rozhraní.', 'MutaUI bridges a PySide6/Qt desktop frontend with the Mutagen CLI engine via asynchronous worker threads. The UI performs continuous background polling of session state, inspects network tunnels, and monitors system resources without blocking the main event loop.'),
         $lang->value('Integrovaný algoritmus třícestného slučování porovnává lokální, vzdálený a společný bázový stav. Před aplikací jakékoli změny na disk vizualizuje přesný směr toku souborů a včas varuje před vznikem nekonzistencí či konfliktů mezi lokálním vývojovým prostředím a vzdáleným výpočetním kontejnerem.', 'An integrated three-way merge algorithm compares local, remote, and baseline states. Before writing any change to disk, it visualizes the exact direction of file transfers and detects potential conflicts between local workstations and remote compute containers.')],
    ],
    'home-infrastructure' => [
        [$lang->value('GitOps orchestrace a síťové oddělení služeb', 'GitOps Orchestration & Network Service Isolation'),
         $lang->value('Infrastruktura staví na plně automatizovaném GitOps přístupu. Git repozitář představuje jediný zdroj pravdy; Semaphore CI spouští orchestraci a Ansible playbooks aplikují idempotentní konfigurace napříč fyzickými i virtuálními uzly.', 'The homelab infrastructure is built on automated GitOps principles. A Git repository acts as the single source of truth; Semaphore CI runs automation and Ansible playbooks apply idempotent configurations across physical and virtual nodes.'),
         $lang->value('Síťový provoz řídí MikroTik RouterOS RB5009 s oddělenými VLAN zónami a WireGuard VPN meshem pro šifrovanou správu. Lokální překlad doménových jmen zajišťuje redundantní tandem Pi-hole a rekurzivního resolveru Unbound, zatímco QNAP NAS obsluhuje distribuované zálohy přes Restic a SMB3 protokoly.', 'Network traffic is routed through a MikroTik RouterOS RB5009 with VLAN isolation and a WireGuard VPN mesh for encrypted remote management. Local DNS resolution is handled by a redundant pair of Pi-hole and an Unbound recursive resolver, while a QNAP NAS anchors distributed backups via Restic and SMB3.')],
    ],
    'woodbound' => [
        [$lang->value('Modulární generovací pipeline a prostorové invarianty', 'Modular World Generation Pipeline & Spatial Invariants'),
         $lang->value('Generování herního světa ve Woodboundu probíhá v deterministické modulární pipeline rozdělené do specializovaných vrstev: digitální model terénu, erozní hydrologie, klasifikace biomů, rozmístění surovinových uzlů a dopravní koridory.', 'World generation in Woodbound executes across a deterministic modular pipeline separated into specialized layers: digital elevation modeling, erosion hydrology, biome classification, resource node zoning, and transport corridors.'),
         $lang->value('Každý generovací modul předává data následující fázi a validuje přísné prostorové invarianty. Integrovaný A* pathfinder simuluje průchodnost terénu pro těžkou lesní techniku a ověřuje existenci logistických tras mezi výrobními centry ještě před pečením výsledných scén.', 'Each generation module passes data downstream while validating strict spatial invariants. An integrated A* pathfinder simulates terrain traversability for heavy forestry machinery and verifies transit corridors between production centers before scene baking.')],
    ],
    'scan2cad' => [
        [$lang->value('Metrologická kalibrace a fotogrammetrická rekonstrukce', 'Metrological Calibration & Photogrammetric Reconstruction'),
         $lang->value('Scan2CAD řeší převod nestrukturovaných mračen bodů a polygonálních sítí do parametrických CAD modelů. Základním kamenem je metrologická kalibrace, která z 2D optických snímačů a přesné fyzické reference odvozuje reálné měřítko v milimetrech.', 'Scan2CAD converts unstructured point clouds and polygonal meshes into parametric CAD solids. The foundation is metrological calibration, which derives real-world millimeter scale from 2D optical sensors and calibrated physical references.'),
         $lang->value('Využitím pinhole kamerového modelu a sdružených referenčních bodů systém promítá fotografické textury na rekonstruovanou geometrii. Výsledkem je rozlišení přesně naměřených a nejistých ploch s možností exportu validních STEP těles a DXF výkresů pro CNC obrábění a 3D tisk.', 'Using a pinhole camera model and paired reference markers, the system projects photographic textures onto reconstructed geometry. The result clearly isolates measured surfaces from uncertain areas, exporting valid STEP models and DXF drawings for CNC and 3D printing.')],
    ],
    'tiskov' => [
        [$lang->value('Architektura asynchronního zpracování a tiskové fronty', 'Asynchronous Job Processing & Print Queue Architecture'),
         $lang->value('Tiskov řeší zátěžové oddělení webového rozhraní od výpočetně náročné analýzy 3D modelů. Webová aplikace přijme vstupní STL/3MF soubory, provede strukturální validaci binárních hlaviček a vytvoří záznam v transakční MySQL frontě.', 'Tiskov decouples web intake from computationally intensive 3D model analysis. The web application receives input STL/3MF files, performs structural binary header validation, and enqueues jobs in a transactional MySQL queue.'),
         $lang->value('Samostatný worker na dedikovaném výpočetním uzlu spouští headless slicer (PrusaSlicer CLI), profiluje vrstvy, odhaduje tiskový čas a kalkuluje spotřebu filamentu. Výsledná telemetrie je předána operátorovi k finálnímu schválení tiskové dávky před odesláním na 3D tiskárny.', 'A dedicated worker node executes headless slicing via CLI (PrusaSlicer), profiling layers, estimating print durations, and calculating filament consumption. The resulting telemetry is presented to the operator for final batch confirmation prior to production.')],
    ],
    'shpotify' => [
        [$lang->value('Distribuovaná synchronizace a optimistický souběh', 'Distributed Synchronization & Optimistic Concurrency'),
         $lang->value('Shpotify staví na distribuované správě stavu pro sdílené poslechové místnosti. Uživatelé souběžně interagují se společnou frontou skladeb bez nutnosti centrálních blokujících zámků databáze.', 'Shpotify is built around distributed state synchronization for collaborative listening rooms. Members concurrently interact with a shared track queue without requiring central blocking database locks.'),
         $lang->value('Mechanismus vektorových hodin a optimistického zamykání spolehlivě detekuje souběžné změny pořadí skladeb a řeší případné kolize. Aplikační most zároveň deduplikuje opakované síťové požadavky a udržuje synchronní přehrávání přes Spotify Web Playback SDK.', 'A vector clock mechanism with optimistic concurrency control detects concurrent track reordering and resolves race conditions deterministically. An application bridge deduplicates redundant network requests and coordinates synchronized playback via the Spotify Web Playback SDK.')],
    ],
    'softwareinc-multiplayer' => [
        [$lang->value('Autoritativní doménové jádro a síťová replikace', 'Authoritative Domain Core & Network Replication'),
         $lang->value('Multiplayerový mod pro Software Inc odděluje herní adaptér od doménové logiky. Deterministické C# jádro zapouzdřuje obchodní pravidla, sdílené projekty, firemní finance a smluvní vztahy zcela nezávisle na herním enginu.', 'The Software Inc multiplayer mod isolates its game adapter from core domain logic. A deterministic C# core encapsulates business logic, shared projects, corporate finance, and contracts independently of the game rendering engine.'),
         $lang->value('RPC rozhraní validuje autoritu volajícího hráče a ověřuje stav kontraktu vůči autoritativnímu stavu hostitelské relace. Potvrzené transakce jsou atomicky replikovány přes síťový adaptér a serializovány do sdíleného savegame souboru.', 'An RPC interface validates caller authority and verifies contract state against the host’s authoritative session. Confirmed transactions are replicated atomically over the network adapter and serialized into the shared savegame file.')],
    ],
    'discord-bot' => [
        [$lang->value('Bezpečná správa systémových služeb přes Discord API', 'Secure System Service Management via Discord API'),
         $lang->value('Architektura bota integruje Discord Interactions API s kryptografickou verifikací požadavků (Ed25519). Příkazy z chatu jsou dekódovány a předány internímu routeru se striktním oddělením běžných uživatelských dotazů a administrativních úkonů.', 'The bot architecture interfaces with the Discord Interactions API using Ed25519 cryptographic verification. Chat slash commands are decoded and dispatched through an internal router strictly isolating user queries from administrative routines.'),
         $lang->value('Systémová údržba vyžaduje ověření rolí (RBAC) a běží v izolovaných asynchronních subprocesech se striktními mantinely timeoutů a paměti. Každý zásah do systémových služeb (systemd) generuje auditní stopu a vrací strukturovanou telemetrii přímo do kanálu.', 'System maintenance enforces role-based access control (RBAC) and executes in isolated asynchronous worker processes with strict timeout and memory limits. Every systemd service operation generates an audit receipt and returns telemetry directly to the channel.')],
    ],
    default => null,
};

$pipelineStages = match ($project->slug) {
    'local-ai-hub' => [
        [$lang->value('01 Ingesce & Hashování', '01 Ingestion & Hashing'), $lang->value('02 AST Symbolický Graf', '02 AST Symbol Graph'), $lang->value('03 Hybridní Vyhledávání', '03 Hybrid Retrieval'), $lang->value('04 Kontext & Ledger', '04 Context & Ledger')],
    ],
    'mutaui' => [
        [$lang->value('01 PySide6 GUI', '01 PySide6 GUI'), $lang->value('02 Asynchronní Worker', '02 Async Worker'), $lang->value('03 Mutagen CLI Daemon', '03 Mutagen CLI Daemon'), $lang->value('04 Obousměrný Disk Sync', '04 Two-Way Disk Sync')],
    ],
    'home-infrastructure' => [
        [$lang->value('01 Git Repozitář', '01 Git Repository'), $lang->value('02 Semaphore CI/CD', '02 Semaphore CI/CD'), $lang->value('03 Ansible Orchestrace', '03 Ansible Orchestration'), $lang->value('04 Síť & Služby', '04 Network & Services')],
    ],
    'woodbound' => [
        [$lang->value('01 Výšková Mapa Terénu', '01 Elevation Heightmap'), $lang->value('02 Hydrologie & Biomy', '02 Hydrology & Biomes'), $lang->value('03 Průmyslové Uzly', '03 Industrial Nodes'), $lang->value('04 A* Validace Tras', '04 A* Route Validation')],
    ],
    'scan2cad' => [
        [$lang->value('01 Vstupní Geometrie', '01 Input Geometry'), $lang->value('02 Metrologická Kalibrace', '02 Metrological Scale'), $lang->value('03 Fotogrammetrie', '03 Photogrammetry'), $lang->value('04 CAD Export (STEP/DXF)', '04 CAD Export (STEP/DXF)')],
    ],
    'tiskov' => [
        [$lang->value('01 HTTP Příjem Zakázky', '01 HTTP Job Intake'), $lang->value('02 MySQL Fronta Úloh', '02 MySQL Task Queue'), $lang->value('03 Slicer Profiling (CLI)', '03 Slicer Profiling (CLI)'), $lang->value('04 Výrobní Plán', '04 Production Schedule')],
    ],
    'shpotify' => [
        [$lang->value('01 Klientská Relace', '01 Client Session'), $lang->value('02 Vektorové Hodiny', '02 Vector Clocks'), $lang->value('03 Optimistický Zápis', '03 Optimistic Commit'), $lang->value('04 Spotify Web API', '04 Spotify Web API')],
    ],
    'softwareinc-multiplayer' => [
        [$lang->value('01 Herní UI & Adaptér', '01 Game UI & Adapter'), $lang->value('02 RPC Validace', '02 RPC Validation'), $lang->value('03 C# Doménové Jádro', '03 C# Domain Core'), $lang->value('04 Autoritativní Stav', '04 Authoritative State')],
    ],
    'discord-bot' => [
        [$lang->value('01 Discord Gateway API', '01 Discord Gateway API'), $lang->value('02 RBAC Validace', '02 RBAC Validation'), $lang->value('03 Asynchronní Úloha', '03 Async Task Execution'), $lang->value('04 Systemd Správa', '04 Systemd Management')],
    ],
    default => [[]],
};

$architecture = match ($project->slug) {
    'local-ai-hub' => [[
        ['Kořen projektu','Project root','Autoritativní absolutní cesta k repozitáři','Authoritative repository path','FS / Path'],
        ['Registrace','Registration','Idempotentní registrace přes stdio JSON-RPC','Idempotent stdio JSON-RPC registration','MCP Stdio'],
        ['Inventář','Inventory','Skenování souborů a filtrace přes .gitignore','File scanning and gitignore filtering','Discovery'],
        ['Změnové hashe','Change hashes','Inkrementální SHA-256 chunking změněných souborů','Incremental SHA-256 chunk hashing','Hashing'],
        ['Code index','Code index','AST kódový graf symbolů, tříd a metod','AST symbol, class & call graph','Python AST'],
        ['Deterministické karty','Deterministic cards','Okamžitá metadata symbolů pro rychlé odpovědi LLM','Instant symbol metadata for LLM responses','Memory Cache'],
        ['Serena LSP','Serena LSP','Jazykový server pro sémantickou navigaci v kódu','Language server semantic code navigation','LSP Bridge'],
        ['Lexikální index','Lexical index','Invertovaný fulltextový index s BM25 scoringem','Inverted fulltext index with BM25 scoring','Search Engine'],
        ['RAG Embeddings','RAG Embeddings','Vektorový sémantický kontext pro komplexní dotazy','Vector semantic embeddings for complex queries','Vector Store'],
        ['Souborové mapy','File maps','Kompaktní strukturální souhrny modulů pro kontext','Compact module structural context summaries','Context Maps'],
        ['Projektový ledger','Project ledger','Transakční SQLite WAL databáze úloh a důkazů','Transactional SQLite WAL task & evidence store','SQLite WAL'],
        ['Diagnostika','Diagnostics','Validace integrity indexu a propustnosti pipeline','Index integrity and throughput diagnostics','Telemetry'],
    ], [[0,1],[1,2],[2,3],[3,4],[4,5],[5,6],[6,7],[7,8],[8,9],[9,10],[10,11]]],
    'mutaui' => [[
        ['MutaUI Desktop GUI','MutaUI Desktop GUI','Uživatelské rozhraní se stavovým přehledem relací','Desktop GUI with session status monitoring','Qt / Python'],
        ['Asynchronní Worker','Async Worker','Neinteraktivní polling a orchestrace na pozadí','Non-blocking background polling & orchestration','QThread IPC'],
        ['Mutagen CLI','Mutagen CLI','Nativní synchronizační engine pro správu relací','Native synchronization engine CLI','Mutagen CLI'],
        ['Lokální Démon','Local Daemon','Autoritativní zdroj stavu synchronizačních relací','Authoritative session state daemon','Mutagen Daemon'],
        ['Sync Relace','Sync Session','Obousměrné sledování souborových změn na disku','Bidirectional disk file monitoring','File Watcher'],
        ['3-Way Merge Detektor','3-Way Merge Detector','Algoritmus detekce a náhledu souběžných konfliktů','Conflict detection & resolution preview engine','Algorithm'],
        ['Forward Relace','Forward Session','Bezpečné tunelování síťových portů přes SSH','Secure SSH network port forwarding','SSH Tunnel'],
    ], [[0,1],[1,2],[2,3],[3,4],[4,5],[3,6]]],
    'home-infrastructure' => [[
        ['Git Repozitář','Git Repository','Verzovaný stav infrastruktury (Infrastructure as Code)','Version-controlled infrastructure state (IaC)','Git / IaC'],
        ['Semaphore CI','Semaphore CI','Spouštěč a auditní runner automatizačních úloh','Automated runner and audit execution engine','CI / Runner'],
        ['Ansible Playbooks','Ansible Playbooks','Idempotentní konfigurace uzlů a systémových služeb','Idempotent node & system service playbooks','Ansible Core'],
        ['RouterOS Brána','RouterOS Gateway','MikroTik RB5009, firewall, VLAN a WireGuard VPN mesh','MikroTik RB5009, firewall, VLAN & WireGuard VPN','RouterOS'],
        ['Core Raspberry Pi','Core Raspberry Pi','Lokální DNS, dashboard, Semaphore a BookStack dokumentace','Local DNS, dashboard, Semaphore & BookStack','Debian Linux'],
        ['CUPS Tiskový Uzel','CUPS Print Node','Oddělená síťová tisková fronta na Raspberry Pi Zero','Isolated network print queue on Pi Zero','CUPS / IPP'],
        ['QNAP Úložiště','QNAP Storage','Centralizovaný NAS pro SMB3 sdílení a Restic zálohy','Centralized NAS for SMB3 & Restic backups','ZFS / SMB3'],
        ['Proxmox VE Host','Proxmox VE Host','Izolované virtualizační prostředí pro testování na vyžádání','Isolated virtualization environment for test VMs','Proxmox VE'],
    ], [[0,1],[1,2],[2,3],[2,4],[2,5],[2,6],[2,7]]],
    'woodbound' => [[
        ['Definice Světa','World Definition','Konfigurace generátoru, seed a pravidla biomů','Generator config, seed & biome rules','World Config'],
        ['Výšková Mapa','Heightmap Terrain','Deterministický výškový reliéf (Perlin/Simplex šum)','Deterministic elevation relief (Perlin/Simplex)','Noise Engine'],
        ['Hydrologie','Hydrology Model','Modelování říčních toků, povodí a vodních ploch','River basin, drainage & lake flow simulation','Hydrology'],
        ['Regiony a biomy','Regions & biomes','Klasifikace vegetace, půdních typů a klimatických zón','Vegetation, soil & climate zone classification','Voronoi / GIS'],
        ['Průmyslová Místa','Industry Sites','Umístění těžebních uzlů, pil a výrobních center','Extraction nodes, sawmills & processing facilities','Zoning Engine'],
        ['Dopravní Koridory','Transport Corridors','Definice silničních, železničních a vodních tahů','Road, rail & waterway transport corridors','Vector Graph'],
        ['A* Pathfinder','A* Pathfinder','Výpočet a validace průchodnosti tras v terénu','Traversability validation & route calculation','A* Algorithm'],
        ['Validátor Invariantů','Invariant Validator','Striktní kontrola neprůchodnosti vody a strmých svahů','Strict verification of water & slope constraints','Rule Engine'],
        ['Asset Baking','Asset Baking','Export optimalizovaných terénních meshů pro Unity engine','Export of optimized terrain meshes for Unity','Mesh Baker'],
    ], [[0,1],[0,4],[1,2],[1,3],[2,3],[3,4],[4,5],[5,6],[6,7],[7,8]]],
    'scan2cad' => [[
        ['Vstupní Geometrie','Input Geometry','Surové mračno bodů nebo nestrukturovaná polygonální síť','Raw point cloud or unstructured polygon mesh','PLY / OBJ / STL'],
        ['Metrologická Báze','Metrological Base','Fyzická délková reference pro určení reálného měřítka','Physical reference length for real-world scaling','Scale Gate'],
        ['Očištění Geometrie','Mesh Processing','Filtrace šumu a vyhlazení naskenovaného povrchu','Noise filtering & scanned surface smoothing','Mesh Filter'],
        ['Referenční Fotografie','Reference Imagery','Snímky dílu s označenými korespondujícími body','Component imagery with mapped correspondence points','Sensor Intake'],
        ['Pinhole Kamera Model','Pinhole Camera Model','Algoritmický odhad polohy a orientace kamery v prostoru','Algorithmic camera pose & orientation estimation','Pose Estimator'],
        ['CAD Rekonstrukce','CAD Reconstruction','Převod polygonů na přesná analytická geometrická tělesa','Conversion of polygons into analytical CAD solids','NURBS / BREP'],
        ['Odchylková Analýza','Deviation Analysis','Výpočet prostorové odchylky rekonstruovaného modelu','Spatial deviation review between mesh and CAD','Metrology QA'],
        ['STEP / DXF Export','STEP / DXF Export','Výstupní parametrická data pro CNC a 3D tisk','Exported parametric solids for CNC and 3D printing','STEP AP214'],
    ], [[0,1],[1,2],[2,5],[3,4],[4,5],[5,6],[6,7]]],
    'tiskov' => [[
        ['HTTP Příjem Zakázky','HTTP Job Intake','Webový formulář pro bezpečné nahrání 3D modelů','Web intake with secure 3D asset upload','Symfony / HTTP'],
        ['Validátor Geometrie','Geometry Validator','Kontrola binárních hlaviček STL/3MF a rozměrů dílu','STL/3MF binary header & dimensional validation','Parser Engine'],
        ['Transakční Fronta','Task Queue','Oddělení příjmu od výpočtu v transakční MySQL tabulce','Decoupling intake from compute in transactional MySQL','MySQL Queue'],
        ['Orchestrace Workera','Worker Orchestration','Asynchronní plánovač úloh na dedikovaném nodu','Asynchronous job scheduler on dedicated node','CLI Daemon'],
        ['Slicer CLI Engine','Slicer CLI Engine','Headless slicování drah a generování instrukcí','Headless path slicing & toolpath generation','PrusaSlicer CLI'],
        ['Kapacitní Telemetrie','Capacity Telemetry','Výpočet doby tisku, výšky vrstev a hmotnosti filamentu','Print duration, layer height & filament telemetry','Telemetry'],
        ['Operátorská Brána','Operator Gate','Lidská kontrola tisknutelnosti a potvrzení parametrů','Manual printability review & parameter sign-off','Human-in-the-Loop'],
        ['Výrobní Dávka','Production Batch','Zařazení schválené úlohy do tiskového plánu farmy','Queuing approved jobs into farm print schedule','Production API'],
    ], [[0,1],[1,2],[2,3],[3,4],[4,5],[5,6],[6,7]]],
    'shpotify' => [[
        ['Klientská Relace','Client Session','Autentizovaný člen sdílené poslechové místnosti','Authenticated member in shared listening room','OAuth2 Session'],
        ['Místnost','Room','Správa členů, oprávnění a aktivního přehrávače v místnosti','Management of members, permissions & active player in room','Room State'],
        ['Distribuovaná Fronta','Distributed Queue','Seznam skladeb s evidencí pořadí a metadat','Track queue with sequential order and metadata','State Store'],
        ['Vektorové Hodiny','Vector Clocks','Deterministické číslování a řazení souběžných úprav','Deterministic sequencing of concurrent mutations','Vector Clock'],
        ['Deduplikační Engine','Deduplication Engine','Ochrana proti duplicitnímu přidání stejné skladby','Protection against duplicate track submissions','Idempotency'],
        ['Doporučovací Algoritmus','Recommendation Engine','Výpočet návrhů skladeb podle preferencí skupiny','Track recommendations based on aggregate preferences','Scoring Engine'],
        ['Spotify API Bridge','Spotify API Bridge','Obousměrná komunikace se Spotify Web API','Bidirectional bridge with Spotify Web API','REST / Webhook'],
        ['Přehrávací Pipeline','Playback Pipeline','Synchronizace časové osy přehrávání s klienty','Audio playback timeline synchronization with clients','Web Playback SDK'],
    ], [[0,1],[1,2],[2,3],[3,4],[3,5],[5,6],[6,7]]],
    'softwareinc-multiplayer' => [[
        ['Herní UI Adaptér','Game UI Adapter','Nativní herní komponenta zachytávající akce hráče','Native game component intercepting player actions','Unity UI'],
        ['RPC Serializace','RPC Serialization','Serializace síťových paketů a zpráv o dohodách','Serialization of network packets & agreement messages','Binary RPC'],
        ['Autoritativní Brána','Authority Gate','Validace oprávnění hráče a aktuálnosti verze kontraktu','Player authority & contract freshness validation','Security Gate'],
        ['C# doménové jádro','C# domain core','Nezávislá pravidla obchodu, financí a sdílených projektů','Business logic, finance & shared project rules','Domain Core'],
        ['Transakční Ledger','Transactional Ledger','Dvoufázový commit změn do účetnictví firem','Two-phase commit of corporate ledger mutations','Ledger Engine'],
        ['Síťový Replikátor','Network Replicator','Distribuovaná synchronizace stavu mezi hostem a klienty','Distributed state synchronization across clients','P2P Netcode'],
        ['Atomický Commit','Atomic Commit','Zápis potvrzeného stavu do společného savegame souboru','Writing confirmed state to shared savegame file','Disk Storage'],
    ], [[0,1],[1,2],[2,3],[3,4],[3,5],[4,6],[5,6]]],
    'discord-bot' => [[
        ['Discord Gateway','Discord Gateway','Příjem a validace WebSocket/Webhook událostí (Ed25519)','WebSocket/Webhook event reception & Ed25519 auth','Gateway API'],
        ['Slash Command Router','Slash Command Router','Směrování aplikačních příkazů na příslušné handlery','Routing slash commands to matching handler logic','Router'],
        ['RBAC Bezpečnostní Brána','RBAC Security Gate','Ověření administrátorských rolí a serverových práv','Administrator role & server permission verification','RBAC Guard'],
        ['Asynchronní Task Queue','Async Task Queue','Oddělení časově náročných operací od Discord timeoutu','Decoupling long tasks from Discord 3s timeout','Task Queue'],
        ['Údržbová větev','Maintenance path','Izolované volání příkazů správy systémových démonů (systemd)','Isolated execution of system daemon controls (systemd)','systemd / D-Bus'],
        ['Auditní Logger','Audit Logger','Zápis strukturované auditní stopy všech provedených zásahů','Structured audit trail logging for all actions','Audit Trail'],
        ['Telemetrická Odpověď','Telemetry Response','Odeslání stavových karet a potvrzení zpět do chatu','Returning status cards & execution telemetry to chat','Rich Embeds'],
    ], [[0,1],[1,2],[2,3],[3,4],[4,5],[5,6]]],
    default => null,
};

$walkthroughTitle = match ($project->slug) {
    'local-ai-hub' => $lang->value('Inkrementální průchod: Od změny souboru k ověření', 'Incremental Trace: From Changed File to Verification'),
    'mutaui' => $lang->value('Průchod operací: Od detekce stavu k bezpečnému merge', 'Operation Trace: From State Detection to Safe Merge'),
    'home-infrastructure' => $lang->value('Průchod změnou: Od commitu po aplikaci na uzly', 'Change Trace: From Commit to Node Deployment'),
    'woodbound' => $lang->value('Průchod generováním: Od výškové mapy k průchodnosti', 'Generation Trace: From Elevation to Traversability'),
    'scan2cad' => $lang->value('Průchod rekonstrukcí: Od optické báze k STEP modelu', 'Reconstruction Trace: From Optical Base to STEP Model'),
    'tiskov' => $lang->value('Průchod zakázkou: Od HTTP příjmu po slicer telemetrii', 'Job Trace: From HTTP Intake to Slicer Telemetry'),
    'shpotify' => $lang->value('Průchod frontou: Souběžná editace a vektorové hodiny', 'Queue Trace: Concurrent Edits & Vector Clocks'),
    'softwareinc-multiplayer' => $lang->value('Průchod transakcí: Od hráčské akce po atomický commit', 'Transaction Trace: From Player Action to Atomic Commit'),
    'discord-bot' => $lang->value('Průchod příkazem: Od Discord eventu po systémovou údržbu', 'Command Trace: From Discord Event to System Maintenance'),
    default => $lang->value('Architektonický průchod', 'Architectural walkthrough'),
};

$architectureTitle = match ($project->slug) {
    'local-ai-hub' => $lang->value('Pipeline AST indexace a kontextového ledgeru', 'AST Indexing & Context Ledger Pipeline'),
    'mutaui' => $lang->value('Architektura asynchronního řízení a náhledu souběhu', 'Asynchronous Control & Concurrency Preview Architecture'),
    'home-infrastructure' => $lang->value('GitOps topologie a síťové rozhraní homelabu', 'GitOps Topology & Homelab Network Architecture'),
    'woodbound' => $lang->value('Pipeline vrstveného procedurálního generování', 'Layered Procedural Generation Pipeline'),
    'scan2cad' => $lang->value('Pipeline metrologické kalibrace a CAD rekonstrukce', 'Metrological Calibration & CAD Reconstruction Pipeline'),
    'tiskov' => $lang->value('Architektura asynchronní tiskové fronty a sliceru', 'Asynchronous Print Queue & Slicer Architecture'),
    'shpotify' => $lang->value('Distribuovaná architektura synchronizace a fronty', 'Distributed Synchronization & Queue Architecture'),
    'softwareinc-multiplayer' => $lang->value('Architektura autoritativního jádra a síťové replikace', 'Authoritative Core & Network Replication Architecture'),
    'discord-bot' => $lang->value('Architektura bezpečné správy a RBAC oprávnění', 'Secure Service Management & RBAC Architecture'),
    default => $lang->value('Mapa systémové architektury', 'System architecture map'),
};

$walkthrough = match ($project->slug) {
    'local-ai-hub' => [
        ['01','Zaregistruj stabilní kořen','Register a stable root','První použití repozitáře odešle jednorázový preprocess požadavek s absolutní cestou pro inicializaci cache.','First repository use dispatches a one-time preprocess request with an absolute path to initialize the cache.'],
        ['02','Inkrementální inventář a hashe','Incremental inventory & hashing','Pozadí provede kontrolu změn přes SHA-256 hashe, aktualizuje code index a strukturální karty symbolů.','The background pass checks changes via SHA-256 hashes, updating the code index and structural symbol cards.'],
        ['03','Deterministický dotaz na symboly','Deterministic symbol query','Dotaz na volání metod nebo vztahy tříd je vyřešen přímo ze strukturovaného AST grafu bez volání LLM.','Queries for method callers or class hierarchies resolve directly from the structured AST graph without calling LLMs.'],
        ['04','Verifikace v hostitelském prostředí','Verification in host environment','Generovaný kód nebo návrh změny je ověřen nativními testovacími nástroji přímo v prostředí hostitele.','Generated code or proposed changes are verified by native test runners directly in the host environment.'],
    ],
    'mutaui' => [
        ['01','Detekce Mutagen CLI démona','Detect Mutagen CLI daemon','Aplikace naváže spojení s lokálním Mutagen démonem a načte aktivní synchronizační a forwarding relace.','The app connects to the local Mutagen daemon, loading active synchronization and forwarding sessions.'],
        ['02','Asynchronní monitoring stavu','Asynchronous state monitoring','QThread worker na pozadí provádí polling stavu spojení, latence a přenesených dat bez blokování UI.','A background QThread worker polls connection state, latency, and transfer volumes without freezing the UI.'],
        ['03','Kalkulace třícestného slučování','Three-way merge computation','Před aplikací změn algoritmus porovná lokální a vzdálený stav vůči společné bázi a detekuje kolize.','Prior to syncing, the merge algorithm inspects local and remote states against a common base to flag conflicts.'],
        ['04','Náhled a potvrzení operace','Preview and operation commit','Uživatel vizuálně zkontroluje plánované souborové operace a potvrdí obousměrný zápis na disk.','The user inspects visualized file operations and safely commits bidirectional disk changes.'],
    ],
    'home-infrastructure' => [
        ['01','Commit do Git repozitáře','Commit to Git repository','Změna systémové konfigurace, firewallových pravidel nebo rolí začíná verzovaným commit do repozitáře.','Infrastructure modifications, firewall rules, and service roles begin as versioned commits in Git.'],
        ['02','Spuštění Semaphore CI/CD','Trigger Semaphore CI/CD','Semaphore runner zachytí webhook a spustí řízenou orchestraci v izolovaném kontejneru.','The Semaphore runner intercepts the webhook, launching controlled orchestration in an isolated container.'],
        ['03','Idempotentní Ansible běh','Idempotent Ansible execution','Ansible playbooks aplikují požadovaný stav na cílové fyzické uzly a virtuální stroje přes SSH.','Ansible playbooks apply desired state to target physical nodes and virtual machines over SSH.'],
        ['04','Verifikace síťových služeb','Verify network services','Systém ověří dostupnost DNS překladu (Pi-hole/Unbound), stav WireGuard tunelů a integritu záloh.','The runner verifies DNS resolution (Pi-hole/Unbound), WireGuard mesh health, and backup targets.'],
    ],
    'woodbound' => [
        ['01','Generování výškového reliéfu','Heightmap elevation generation','Modul terénu vygeneruje spojitý digitální model povrchu pomocí kombinace fraktálního a Simplex šumu.','The terrain module synthesizes a continuous digital surface model combining fractal and Simplex noise.'],
        ['02','Simulace povodí a biomů','Watershed & biome simulation','Hydrologický model simuluje odtok vody a eroze; Voronoi diagramy vymezují klimatické zóny.','A hydrology pass models drainage basins and water bodies; Voronoi diagrams map climate zones.'],
        ['03','Alokace průmyslových lokalit','Industrial node allocation','Generátor rozmístí těžební místa, pily a stavební parcely podle dostupnosti surovin a sklonu svahů.','The engine zones extraction nodes, sawmills, and build pads based on resource access and terrain slope.'],
        ['04','A* validace logistických tras','A* route traversal validation','Pathfinder simuluje sjízdnost koridorů pro těžkou techniku a garantuje průchodnost celého světa.','The pathfinder validates transit corridors for heavy machinery, guaranteeing global map traversability.'],
    ],
    'scan2cad' => [
        ['01','Načtení mračna bodů / sítě','Load point cloud or mesh','Systém načte nestrukturovanou prostorovou geometrii z formátů PLY, OBJ nebo binárního STL.','The system loads unstructured spatial geometry from PLY, OBJ, or binary STL containers.'],
        ['02','Metrologická kalibrace báze','Metrological base calibration','Zadání známé fyzické vzdálenosti mezi referenčními body stanoví přesný poměr pixelů k milimetrům.','Specifying known physical lengths between reference markers establishes an exact pixel-to-millimeter ratio.'],
        ['03','Projekce přes pinhole model','Pinhole camera pose projection','Sdružením referenčních bodů na fotografii algoritmus odhadne matici vnitřních i vnějších parametrů kamery.','Matching 2D-to-3D reference markers enables the algorithm to estimate intrinsic and extrinsic camera matrices.'],
        ['04','Export přesných CAD těles','Export precise CAD solids','Zrekonstruované analytické plochy jsou exportovány do standardního parametrického formátu STEP AP214 a DXF.','Reconstructed analytical surfaces are exported into industry-standard parametric STEP AP214 and DXF files.'],
    ],
    'tiskov' => [
        ['01','Příjem zakázky a modelu','Intake of request and model','Webová aplikace bezpečně validuje nahrané 3D modely (STL/3MF) a extrahuje geometrická metadata.','The web application securely validates uploaded 3D models (STL/3MF) and extracts geometry metadata.'],
        ['02','Zřetězení do asynchronní fronty','Queuing in asynchronous pipeline','MySQL fronta izoluje HTTP příjem od náročného výpočtu drah a chrání server před přetížením.','A MySQL queue isolates HTTP intake from intensive slicing compute, protecting the server from load spikes.'],
        ['03','Zpracování na výpočetním nodu','Execution on compute node','Asynchronní worker provádí slicing přes headless CLI (PrusaSlicer) a kalkuluje tiskové časy.','An asynchronous worker runs slicing via headless CLI (PrusaSlicer) and calculates print times.'],
        ['04','Operátorská kontrola parametrů','Operator verification of parameters','Výstupní parametry (hmotnost filamentu, čas vrstev, podpory) slouží operátorovi k finálnímu schválení zakázky.','Output metrics (filament weight, layer times, supports) serve the operator for final job approval.'],
    ],
    'shpotify' => [
        ['01','Vstup do sdílené místnosti','Join shared room','Člen se autorizuje přes OAuth2 a připojí se ke sdílené místnosti s unifikovaným stavem přehrávání.','A member authorizes via OAuth2 and joins a shared room with unified playback state.'],
        ['02','Paralelní úpravy fronty','Concurrent queue edits','Uživatelé souběžně přidávají a přeřazují skladby; vektorové hodiny řeší případné pořadové konflikty.','Users concurrently add and reorder tracks; vector clocks resolve sequencing conflicts.'],
        ['03','Idempotence a deduplikace','Idempotency and deduplication','Opakované síťové požadavky nebo kliknutí jsou deduplikovány bez poškození pořadí fronty.','Repeated network requests or duplicate clicks are deduplicated without corrupting queue order.'],
        ['04','Synchronizace přehrávače','Playback synchronization','Přehrávací most synchronizuje pozici a frontu s koncovými klienty přes Spotify Web API.','A playback bridge synchronizes timeline position and queue state with clients via the Spotify Web API.'],
    ],
    'softwareinc-multiplayer' => [
        ['01','Hráč iniciuje spolupráci','Player initiates collaboration','Sdílený kontrakt nebo obchodní dohoda začíná akcí v klientském herním rozhraní.','A shared contract or agreement begins with an action in the client game UI.'],
        ['02','Validace v doménovém jádru','Domain core validation','Deterministické C# jádro ověřuje finanční bilanci, kapacity a herní pravidla nezávisle na renderování.','The deterministic C# core verifies balances, capacity, and game rules independent of rendering.'],
        ['03','RPC synchronizace stavu','RPC state synchronization','Potvrzená změna se serializuje a replikuje mezi hostitelem a připojenými klienty.','Confirmed state changes are serialized and replicated between the host and connected clients.'],
        ['04','Autoritativní zápis do světa','Authoritative world commit','Hostitelská relace provede atomický commit do savegame souboru a potvrdí transakci klientům.','The host session commits an atomic update to the savegame file and confirms the transaction.'],
    ],
    'discord-bot' => [
        ['01','Příjem Discord interakce','Discord interaction intake','Bot přijímá slash commandy přes Discord Interaction Webhooky s ověřením kryptografického podpisu Ed25519.','The bot receives slash commands via Discord Interaction Webhooks verified with Ed25519 signatures.'],
        ['02','Ověření RBAC oprávnění','RBAC authorization check','Systém ověřuje role a oprávnění volajícího administrátora před předáním údržbového příkazu do výkonné vrstvy.','The system validates caller roles and permissions before passing maintenance commands to the execution layer.'],
        ['03','Izolovaný běh údržby','Isolated maintenance execution','Správní příkazy běží v izolovaných asynchronních subprocesech se striktním limitem paměti a timeoutu.','Administrative commands run in isolated asynchronous worker subprocesses with strict memory and time limits.'],
        ['04','Auditní log a stavová odpověď','Audit log and status reply','Výsledek operace je zapsán do strukturovaného auditního logu a zpět do Discord kanálu je odeslána stavová karta.','Operation outcomes are committed to a structured audit log and a status card is returned to the Discord channel.'],
    ],
    default => [],
};

$more = match ($project->slug) {
    'local-ai-hub' => $lang->value('Architektura verze 6.1.0 vystavuje 7 specializovaných MCP nástrojů přes stdio JSON-RPC. Preprocessing optimalizuje dotazování agentů postupným budováním deterministických vrstev: inventář souborů ➔ SHA-256 chunking ➔ AST kódový index ➔ strukturální karty symbolů ➔ volitelný Serena LSP bridge ➔ invertovaný lexikální index ➔ vektorový RAG ➔ souborové a modulární mapy kontextu ➔ runtime diagnostika indexu. Každá komponenta disponuje nezávislým circuit breakerem a degraduje samostatně.', 'Version 6.1.0 architecture exposes 7 specialized MCP tools over stdio JSON-RPC. Preprocessing optimizes agent context through staged deterministic pipelines: file inventory ➔ SHA-256 chunking ➔ AST code index ➔ structural symbol cards ➔ optional Serena LSP bridge ➔ inverted lexical index ➔ vector RAG ➔ file and modular context maps ➔ runtime index diagnostics. Subsystems degrade independently with dedicated circuit breakers.'),
    'mutaui' => $lang->value('MutaUI propojuje desktopový PySide6 frontend s nativním Mutagen démonem přes asynchronní IPC subproces. Rozhraní provádí kontinuální polling stavu relací, detekuje souběžné konflikty třícestného slučování a vizualizuje obousměrné toky souborů ještě před jejich zápisem na disk. Tím eliminuje riziko nechtěného přepsání zdrojových kódů při práci napříč lokálním OS a vzdáleným vývojovým kontejnerem.', 'MutaUI bridges a PySide6 Qt GUI with the native Mutagen daemon via asynchronous IPC worker threads. It performs continuous session state polling, detects three-way merge conflicts, and visualizes bidirectional file operations before disk synchronization, eliminating accidental source overwrites between local workstations and remote development containers.'),
    'home-infrastructure' => $lang->value('Domácí infrastruktura funguje na principu GitOps a Infrastructure as Code. Změny konfigurace jsou verzovány v Gitu a aplikovány orchestrátorem Semaphore přes sadu idempotentních Ansible playbooků. Síťovou bezpečnost zajišťuje RouterOS RB5009 s oddělenými VLAN zónami a WireGuard VPN meshem, zatímco lokální DNS řeší redundantní tandem Pi-hole a Unbound resolveru.', 'The homelab infrastructure follows GitOps and Infrastructure as Code principles. Configuration states are tracked in Git and applied via Semaphore CI using idempotent Ansible playbooks. Network boundary defense is orchestrated by a MikroTik RouterOS RB5009 with VLAN isolation and WireGuard VPN mesh, while local DNS resolution is backed by redundant Pi-hole and Unbound instances.'),
    'woodbound' => $lang->value('Generování světa ve Woodboundu probíhá v deterministické modulární pipeline: výšková mapa ➔ hydrologické modelování povodí a eroze ➔ biomy a vegetační pásma ➔ umístění průmyslových lokalit a surovinových ložisek ➔ koridory dopravní infrastruktury ➔ A* validace průchodnosti tras. Každý krok ověřuje prostorové invarianty ještě před pečením finálních assetů.', 'World generation in Woodbound executes across a deterministic modular pipeline: heightmap elevation ➔ watershed and erosion hydrology ➔ biome zoning ➔ industrial nodes and resource placement ➔ transportation corridors ➔ A* route traversal validation. Spatial invariants are strictly checked before baking scene assets.'),
    'scan2cad' => $lang->value('Scan2CAD řeší přemostění mezi beztvarým mračnem bodů a exaktním parametrickým CAD modelem. Klíčovým krokem je metrologická kalibrace, která z 2D optické matice a známé fyzické reference odvodí přesné měřítko v milimetrech. Následná projekce referenčních fotografií přes pinhole kamerový model umožňuje rekonstruovat geometrii a exportovat validní STEP tělesa nebo DXF výkresy.', 'Scan2CAD bridges raw point clouds and parametric CAD models. The core gate is metrological calibration, which derives precise millimeter dimensions from 2D optical images using a calibrated physical reference baseline. Camera pose projection then enables accurate reverse engineering into STEP solids and DXF drawings.'),
    'tiskov' => $lang->value('Architektura Tiskova striktně odděluje asynchronní zátěž. Webová aplikace přijímá poptávku s binárními 3D podklady (STL/3MF), validuje integritu a ukládá ji do transakční fronty v MySQL. Specializovaný worker na výpočetním uzlu spouští headless slicer (PrusaSlicer CLI), analyzuje geometrii modelu, čas tisku a spotřebu materiálu. Výsledná data jsou předána operátorovi k finálnímu schválení tiskové dávky.', 'Tiskov\'s architecture enforces asynchronous workload separation. The web frontend accepts 3D assets (STL/3MF), validates geometric integrity, and commits records to a transactional MySQL queue. A dedicated compute worker runs headless slicing (PrusaSlicer CLI) to profile geometry, printing duration, and material usage, passing telemetry to the operator for final batch confirmation.'),
    'shpotify' => $lang->value('Shpotify staví na distribuovaném synchronizačním jádře pro sdílené poslechové místnosti. Změny pořadí skladeb a přidávání do fronty používají optimistické zamykání a vektorové hodiny, které spolehlivě deterministicky řeší souběžné zápisy více členů. Skladby jsou automaticky deduplikovány a synchronizovány s reálným časem přehrávání přes Spotify Web Playback SDK.', 'Shpotify is built around a distributed synchronization core for shared rooms. Queue mutations and reordering rely on optimistic concurrency control and vector clocks to deterministically resolve concurrent edits across members. Tracks are deduplicated and synchronized in real time with the Spotify Web Playback SDK.'),
    'softwareinc-multiplayer' => $lang->value('Multiplayerový mod pro Software Inc odděluje síťovou replikaci od herní logiky. Doménové C# jádro deterministicky spravuje obchodní dohody, sdílené projekty a transakční účetnictví nezávisle na enginu hry. Herní adaptér zachytává uživatelské akce, validuje RPC autoritu hostitele a zajišťuje atomický commit změn do společného savegame souboru.', 'The Software Inc multiplayer mod isolates network replication from game simulation. A deterministic C# domain core manages business agreements, shared development projects, and ledger transactions independent of the rendering engine. The game adapter captures user input, validates host RPC authority, and commits atomic updates to the shared savegame file.'),
    'discord-bot' => $lang->value('Architektura bota integruje Discord Interactions API přes kryptograficky ověřené webhooky (Ed25519). Aplikační příkazy jsou striktně odděleny od systémové údržby: administrativní úlohy vyžadují explicitní RBAC autorizaci a vykonávají se v asynchronních subprocesech se striktními paměťovými i časovými mantinely. Každá operace zanechává auditní stopu a vrací stavovou telemetrii do Discord rozhraní.', 'The bot architecture interfaces with the Discord Interactions API using Ed25519 cryptographically signed webhooks. Application slash commands are decoupled from system maintenance: administrative routines enforce role-based access controls and run in isolated subprocesses bounded by strict memory and execution timeouts, logging audit receipts to telemetry.'),
    default => '',
};
?>
<?php if ($detail !== null):
[$copy] = $detail;
[$architectureNodes, $architectureEdges] = $architecture;
$architectureLinks = [];
foreach ($architectureEdges as [$source, $target]) {
    $architectureLinks[$source][] = $target;
}
?>
<section class="section shell project-deepdive" data-project-deepdive aria-labelledby="project-deepdive-title">
 <div class="section-heading"><div><h2 id="project-deepdive-title"><?= Html::escape($copy[0]) ?></h2></div></div>
 <div class="project-deepdive-copy"><p><?= Html::escape($copy[1]) ?></p><p><?= Html::escape($copy[2]) ?></p><p><?= Html::escape($more) ?></p></div>
 <section class="case-walkthrough" aria-labelledby="walkthrough-title">
  <div class="case-walkthrough-heading"><div><h3 id="walkthrough-title"><?= Html::escape($walkthroughTitle) ?></h3></div><span class="demo-stamp"><?= $lang->value('TECHNICKÝ PRŮCHOD', 'TECHNICAL TRACE') ?></span></div>
  <ol class="case-walkthrough-steps"><?php foreach ($walkthrough as [$number,$titleCs,$titleEn,$bodyCs,$bodyEn]): ?><li><span><?= Html::escape($number) ?></span><div><h4><?= Html::escape($lang->value($titleCs,$titleEn)) ?></h4><p><?= Html::escape($lang->value($bodyCs,$bodyEn)) ?></p></div></li><?php endforeach; ?></ol>
 </section>
 <figure class="architecture-figure component-figure" data-project-architecture>
  <figcaption>
    <strong><?= Html::escape($architectureTitle) ?></strong>
    <span><?= $lang->value('Strukturální diagram komponent, protokolů a návazností v systému.', 'Structural diagram of components, protocols and system dependencies.') ?></span>
  </figcaption>
  <?php if (!empty($pipelineStages[0])): ?>
  <div class="pipeline-track" role="group" aria-label="<?= $lang->value('Pipeline toku dat', 'Data pipeline flow') ?>">
    <?php foreach ($pipelineStages[0] as $stageIdx => $stageName): ?>
      <?php if ($stageIdx > 0): ?><span class="pipeline-arrow" aria-hidden="true">➔</span><?php endif; ?>
      <span class="pipeline-phase"><?= Html::escape($stageName) ?></span>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <ul class="component-grid">
    <?php foreach ($architectureNodes as $index => $node): ?>
    <li id="component-<?= $index ?>" data-architecture-node>
      <div class="node-meta-row">
        <span class="node-badge">STEP <?= sprintf('%02d', $index + 1) ?></span>
        <?php if (isset($node[4])): ?><span class="node-runtime"><?= Html::escape($node[4]) ?></span><?php endif; ?>
      </div>
      <h4><?= Html::escape($lang->value($node[0], $node[1])) ?></h4>
      <p><?= Html::escape($lang->value($node[2], $node[3])) ?></p>
      <?php if (isset($architectureLinks[$index])): ?>
      <div class="component-links">
        <span><?= $lang->value('Navazuje', 'Connects to') ?></span>
        <?php foreach ($architectureLinks[$index] as $target): $next = $architectureNodes[$target]; ?>
        <a data-architecture-edge href="#component-<?= $target ?>"><?= Html::escape($lang->value($next[0], $next[1])) ?> <span aria-hidden="true">↗</span></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
 </figure>
</section>
<?php endif; ?>
