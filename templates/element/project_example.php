<?php
declare(strict_types=1);

use App\Support\Html;

$example = match ($project->slug) {
    'local-ai-hub' => [
        $lang->value('Hledání příčiny regrese a deterministický kontext', 'Root Cause Analysis and Deterministic Context'),
        $lang->value('Při selhání integračního testu agent neprochází celý repozitář naivním prohledáváním. Hub poskytne přesné souborové hashe, strukturální AST výřezy a referenční diagramy přes MCP. Teprve po shromáždění tvrdých faktů je kontext předán modelu pro návrh opravy.', 'During integration test failures, the agent avoids brute-force file scanning. The Hub supplies exact file hashes, structural AST slices, and dependency graphs via MCP. Hard evidence is gathered before invoking model reasoning to draft a fix.'),
        $lang->value('Základní princip: deterministická data a lokální indexace vždy předchází inferenci a šetří tokenový rozpočet.', 'Core principle: deterministic evidence and local indexing always precede model inference, saving token budget.'),
    ],
    'mutaui' => [
        $lang->value('Bezpečná obousměrná synchronizace a řešení konfliktů', 'Safe Bidirectional Sync and Conflict Triage'),
        $lang->value('Při vývoji mezi lokálním editorem a vzdáleným Linux serverem MutaUI monitoruje stav Mutagen démona. Před aplikací změn vizualizuje směr toku dat a v případě souběžné editace zvýrazní konflikt přímo v GUI před zápisem na disk.', 'When developing across local IDE and remote Linux hosts, MutaUI tracks the Mutagen sync daemon. It visualizes data flow and flags concurrent collisions in the GUI before committing files to disk.'),
        $lang->value('Architektura: frontend komunikuje s Mutagen CLI přes bezpečný IPC soket bez ukládání hesel nebo zásahu do synchronizačního protokolu.', 'Architecture: frontend communicates with Mutagen CLI via secure IPC socket without credential caching or tampering with transport protocol.'),
    ],
    'home-infrastructure' => [
        $lang->value('Deklarativní nasazení služeb a automatizovaná obnova', 'Declarative Service Deployment and Disaster Recovery'),
        $lang->value('Změna v DNS zóně nebo přidání reverzní proxy se provádí commitem do Git repozitáře. Semaphore UI následně spustí idempotentní Ansible playbook, který zkonfiguruje cílové uzly a provede validaci běžících služeb.', 'DNS zone modifications or reverse proxy additions are committed to Git. Semaphore UI triggers idempotent Ansible playbooks that configure nodes and validate active systemd units.'),
        $lang->value('Zásada návrhu: síťové jádro (MikroTik a lokální DNS sinkhole) běží nezávisle na aplikačních Proxmox virtuálech.', 'Design rule: network core (MikroTik routing and DNS sinkhole) operates independently of application Proxmox virtual machines.'),
    ],
    'woodbound' => [
        $lang->value('Procedurální generování terénu a logistická průchodnost', 'Procedural Terrain Generation and Route Validation'),
        $lang->value('Svět hry se generuje v navazujících deterministických krocích: Perlinův šum určuje základní elevaci, hydraulická eroze vyhlazuje údolí a A* algoritmus ověřuje, že vytěžené suroviny lze po terénních svazích dopravit do hlavní základny.', 'The game world is generated via deterministic stages: multi-octave noise defines elevation, hydraulic erosion carves drainage valleys, and A* solvers verify resource corridors to the central settlement.'),
        $lang->value('Deterministická seedová pipeline garantuje identické rozložení světa pro všechny hráče bez nutnosti přenášet gigabajty terénních dat.', 'Deterministic seed-based generation guarantees identical world topology across clients without transferring large mesh blobs.'),
    ],
    'scan2cad' => [
        $lang->value('Rekonstrukce opotřebovaného dílu a kalibrace měřítka', 'Worn Part Reconstruction and Scale Calibration'),
        $lang->value('Při reverzním inženýrství poškozené řemenice sken zachytí deformované plochy. Scan2CAD zarovná 3D síť s referenčními makrofotografiemi, spočítá směrodatnou odchylku rozměrů a vygeneruje parametrický STEP profil pro CNC výrobu.', 'When reverse engineering a damaged pulley, 3D scanning captures distorted surfaces. Scan2CAD aligns point cloud meshes with calibrated reference photos, computes dimensional standard deviation, and outputs parametric STEP profiles for machining.'),
        $lang->value('Metrologická zásada: optický sken poskytuje tvarový odhad, přesné tolerance kritických průměrů se vždy kalibrují podle fyzických měřidel.', 'Metrology rule: optical scans provide topological contours; critical tolerances are always calibrated against mechanical gauge standards.'),
    ],
    'tiskov' => [
        $lang->value('Asynchronní kalkulace zakázky a ochrana tiskové farmy', 'Asynchronous Job Estimation and Print Farm Scheduling'),
        $lang->value('Zákazník nahraje 3D model (STL/STEP) přes webové rozhraní. Web okamžitě vrátí potvrzení přijetí, zatímco na pozadí dedikovaný worker na Raspberry Pi spustí PrusaSlicer CLI pro přesný výpočet gramáže filamentu a tiskového času.', 'Customers upload 3D models (STL/STEP) via web intake. The web app confirms receipt immediately while headless workers on Raspberry Pi nodes execute PrusaSlicer CLI to calculate exact filament mass and machine runtimes.'),
        $lang->value('Architektonické oddělení: těžký výpočetní slicing nikdy neblokuje HTTP vlákna webového serveru a fronta úloh je plně transakční v MySQL.', 'Architectural decoupling: compute-heavy slicing never starves HTTP request pools and job queue state remains transactional in MySQL.'),
    ],
    'shpotify' => [
        $lang->value('Kolaborativní poslechová místnost a řešení souběžných úprav', 'Collaborative Listening Room and Concurrency Control'),
        $lang->value('Několik účastníků přidává nebo přerovnává skladby v reálném čase. WebSocket hub udržuje sekvenční číslo stavu místnosti (ETag/verze). Pokud dva klienti odešlou změnu současně, druhá operace je vyhodnocena optimistickým zamykáním bez ztráty dat.', 'Multiple participants add or reorder tracks in real time. The WebSocket hub tracks room monotonic sequence numbers (ETag/version). If two clients commit concurrently, optimistic concurrency control reconciles state without silent data loss.'),
        $lang->value('Síťový model: hostitelská session drží autoritativní playback token, zatímco hosté interagují přes odlehčené WebSocket RPC bez nutnosti instalace aplikace.', 'Network model: host holds authoritative playback credentials while guests interact via lightweight WebSocket RPC requiring no client installation.'),
    ],
    'softwareinc-multiplayer' => [
        $lang->value('Autoritativní synchronizace světa a mezifiremní kontrakty', 'Authoritative World Sync and Inter-Company Contracts'),
        $lang->value('Modifikace simuluje konkurenční ekonomické prostředí více nezávislých softwarových společností. Servery ověřují transakce, sublicence zdrojových kódů a sdílené distribuční sítě přes C# doménové jádro oddělené od Unity enginu.', 'The mod orchestrates a competitive multi-company market. Dedicated host nodes validate B2B transactions, IP sub-licensing, and shared distribution networks through an engine-decoupled C# domain core.'),
        $lang->value('Klíčový princip: herní logika je striktně oddělena od grafického rozhraní (headless jádro), což umožňuje deterministické testování síťových RPC transakcí.', 'Key principle: business logic is decoupled from Unity rendering (headless core), enabling deterministic automated testing of networked RPC transactions.'),
    ],
    'discord-bot' => [
        $lang->value('Bezpečná systémová orchestrace přes Discord rozhraní', 'Secure System Orchestration via Discord Gateway'),
        $lang->value('Správce potřebuje provést restart služby nebo získat metriky paměti z mobilního telefonu. Bot ověří digitální podpis Discord interakce, zkontroluje RBAC oprávnění uživatele podle role a deleguje příkaz do izolovaného lokálního workeru.', 'An operator needs to trigger service restarts or query host telemetry from mobile devices. The bot verifies Discord cryptographic signatures, enforces strict RBAC permissions, and delegates tasks to sandboxed local execution workers.'),
        $lang->value('Bezpečnostní hranice: bot nikdy nespouští nekontrolované shellové příkazy; každá operace je definována whitelistem parametrů a auditována v systémovém logu.', 'Security boundary: raw shell execution is prohibited; commands are strictly whitelisted, argument-validated, and recorded in persistent audit logs.'),
    ],
    default => null,
};
?>
<?php if ($example !== null): ?>
<section class="section shell" data-project-example aria-labelledby="project-example-title">
 <div class="notice note-info"><span class="eyebrow"><?= $lang->value('ARCHITEKTONICKÝ SCÉNÁŘ', 'ARCHITECTURAL SCENARIO') ?></span><h2 id="project-example-title"><?= Html::escape($example[0]) ?></h2><p><?= Html::escape($example[1]) ?></p><p><strong><?= $lang->value('Architektonický princip:', 'Architectural principle:') ?></strong> <?= Html::escape($example[2]) ?></p></div>
</section>
<?php endif; ?>
