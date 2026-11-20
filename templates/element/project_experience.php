<?php
declare(strict_types=1);

use App\Support\Html;

$stories = [
    'local-ai-hub' => [
        'claim' => $lang->value('Co nabízí předem připravený index', 'What a prebuilt index makes possible'),
        'why' => $lang->value('Začal jsem nad tím přemýšlet, protože se přes AI řeší i věci, které jdou udělat programově a deterministicky. A chybělo mi jednou zaregistrovat repozitář, nechat předem doběhnout tu náročnou práci a při vývoji už neplatit zdroji za stejné hledání pořád dokola. Proto Hub nejdřív projde soubory, spočítá hashe a připraví index. Model přichází až ve chvíli, kdy deterministické podklady nestačí.', 'I started thinking about this because AI was doing work that could be handled programmatically and deterministically. I also wanted to register a repository once, let the expensive work finish ahead of time, and stop spending resources on the same discovery during every task. So the Hub inventories files, hashes them and prepares an index first. A model comes in only when deterministic evidence is not enough.'),
        'alternatives' => [
            ['Continue', 'https://docs.continue.dev/faqs', $lang->value('Indexuje codebase a umí z něj vybírat kontext. Pro mnoho editorových úloh může stačit.', 'Indexes a codebase and retrieves context. That can be enough for many editor workflows.')],
            ['Cursor', 'https://docs.cursor.com/get-started/installation', $lang->value('Rozumí otevřenému repozitáři v rámci editoru. Dobrá volba, když chcete celý agentní workflow přímo v IDE.', 'Understands the opened repository inside its editor. A good fit when the full agent workflow should live in the IDE.')],
        ],
        'fit' => $lang->value('Když mi stačí indexace v editoru, Continue nebo Cursor jsou jednodušší volba. Hub musí obhájit něco navíc: jednorázovou registraci, předpočítaný index, dohledatelné podklady a použití přes MCP. Další chat nebo doplňování kódu samo o sobě důvod není.', 'If editor-level indexing is enough, Continue or Cursor are the simpler choice. The Hub has to earn its place with something more: one-time registration, a prepared index, traceable evidence and access through MCP. Another chat or code completion alone is not a reason to build it.'),
        'source' => 'README.md; repository evidence, release candidate 6.1.0',
        'destination' => '/playground#station-preprocessing',
        'interaction' => $lang->value('Uprav vzorek, spočítej jeho SHA-256 a sleduj navazující vrstvy.', 'Edit the sample, calculate its SHA-256 and inspect the downstream layers.'),
    ],
    'mutaui' => [
        'claim' => $lang->value('Kdy pomáhá grafická správa relací', 'When a graphical session manager helps'),
        'why' => $lang->value('Mutagen řeší samotnou obousměrnou synchronizaci, ale v terminálu snadno vznikne přehlédnutí konfliktu. MutaUI sjednocuje relace, diagnostiku a monitorování do jednoho rozhraní a předem vizualizuje plánované změny před zápisem na disk.', 'Mutagen handles bidirectional sync under the hood, but terminal workflows easily obscure sync conflicts. MutaUI consolidates active sessions, diagnostics, and monitoring into a unified console, previewing staged changes before disk mutation.'),
        'alternatives' => [
            ['Mutagen CLI', 'https://mutagen.io/documentation/introduction/getting-started/', $lang->value('Nejkratší cesta, pokud mi vyhovují příkazy a výstupy v terminálu. MutaUI bez něj nefunguje.', 'The simplest route when terminal commands and output are enough. MutaUI still depends on it.')],
            ['Jiný Mutagen GUI klient', 'https://snapcraft.io/install/mutagen-sync-manager/kde-neon', $lang->value('Existují i další grafická rozhraní. Před volbou je potřeba porovnat platformu, údržbu a skutečné pokrytí funkcí.', 'Other graphical clients exist. Compare platform support, maintenance and actual feature coverage before choosing.')],
        ],
        'fit' => $lang->value('Pokud operátorovi stačí základní terminálový výstup, samotné Mutagen CLI je efektivnější. MutaUI přináší hodnotu v komplexních projektech se stovkami souborů, kde grafický náhled a okamžitá detekce konfliktů zabrání nechtěnému přepsání kódu.', 'For basic terminal operations, raw Mutagen CLI is more lightweight. MutaUI adds value in multi-directory workflows with complex file sets where visual conflict resolution and live session inspection prevent accidental overwrite.'),
        'source' => 'MutaUI README and setup docs; first source commit 9d50bfe',
        'destination' => '/playground#station-sync-preview',
        'interaction' => $lang->value('Prohlédni si náhled synchronizační operace a detekci konfliktů.', 'Inspect sync operation preview and conflict detection.'),
    ],
    'home-infrastructure' => [
        'claim' => $lang->value('Jak je rozdělená správa domácích služeb', 'How home service management is divided'),
        'why' => $lang->value('Infrastruktura rozděluje síťové vrstvy, zálohovací mechanismy a aplikační kontejnery tak, aby kritické DNS a routing běžely nezávisle na údržbě virtualizačních nodů. Každý host má jasně vymezenou roli a konfigurační stav je plně deklarován v Ansible.', 'The infrastructure decouples core routing and DNS resolution from virtualization host maintenance. Each host holds an explicit operational boundary and state is declared deterministically via Ansible roles.'),
        'alternatives' => [
            ['Ruční Ansible CLI', 'https://docs.ansible.com/ansible/latest/cli/ansible-playbook.html', $lang->value('Méně běžící infrastruktury; dobrá volba pro pár občasných změn.', 'Less infrastructure to maintain; a sensible choice for occasional changes.')],
            ['AWX', 'https://github.com/ansible/awx', $lang->value('Širší automatizační rozhraní; jeho provozní režii pro tuto síť jsem neměřil.', 'A broader automation interface; I have not measured its operating overhead for this network.')],
            ['Cockpit', 'https://cockpit-project.org/', $lang->value('Webová správa hostu a systemd služeb, ne náhrada za tuto sadu Ansible rolí.', 'Web-based host and systemd management, not a replacement for these Ansible roles.')],
        ],
        'fit' => $lang->value('Pro jednotlivé ad-hoc servery stačí manuální Ansible CLI. Automatizovaný Semaphore orchestrátor se vyplatí při koordinaci více navazujících rolí a pravidelném auditu bezpečnostních politik napříč celou topologií.', 'For single ad-hoc nodes, manual Ansible CLI is sufficient. An automated Semaphore orchestrator earns its place when coordinating dependent playbooks and enforcing network security baselines across the topology.'),
        'source' => 'README and four infrastructure audits, 16 Aug 2026; first commit c38db595',
        'destination' => '/homelab',
        'interaction' => $lang->value('Zvol hosta. Prohlédni jeho zdokumentované služby.', 'Choose a host. Inspect its documented services.'),
    ],
    'woodbound' => [
        'claim' => $lang->value('Jak terén určuje tok surovin', 'How terrain determines material flow'),
        'why' => $lang->value('Pěkný terén sám o sobě nestačí pro logistickou hru. Vlastní procedurální generátor integruje výškové mapy, hydraulickou erozi a A* trasování logistických cest do jednoho deterministického toku. Terén tak přímo garantuje schůdnost a dostupnost surovin.', 'Visual terrain alone is insufficient for a logistics game. The custom procedural generator fuses heightmaps, hydraulic erosion, and A* path validation into a deterministic pipeline, guaranteeing traversable corridors and resource connectivity.'),
        'alternatives' => [
            ['Unity Terrain Tools', 'https://docs.unity.cn/6000.0/Documentation/Manual/terrain-Tools.html', $lang->value('Přímo v Unity: ruční práce s terénem a jeho nástroji. Vhodná pro autorsky řízené prostředí.', 'Built into Unity: author terrain with its native tools. A fit for hand-directed environments.')],
            ['MapMagic 2', 'https://assetstore.unity.com/packages/tools/terrain/mapmagic-2-165180', $lang->value('Hotový uzlový generátor terénu a map. Je třeba ověřit, zda pokryje i pravidla a validace konkrétní hry.', 'An existing node-based terrain and map generator. Check whether it also covers this game’s specific rules and validation.')],
        ],
        'fit' => $lang->value('Pokud by stačil statický ručně modelovaný terén, Unity Terrain Tools jsou optimální. Vlastní procedurální pipeline byla nutná pro dynamickou generaci rozsáhlého světa, kde pravidla těžby a sklon svahů přímo diktují logistiku přepravy dřeva.', 'If static hand-sculpted terrain was enough, Unity Terrain Tools would suffice. A custom procedural pipeline was required for large-scale dynamic worlds where slope physics and resource placement govern timber transport logistics.'),
        'source' => 'Game vision, design pillars, world-generation docs; source revision 72c22cd',
        'interaction' => $lang->value('Změň vrstvu. Odhal pravidlo, které musí navazovat.', 'Change a layer. Reveal the rule that must follow.'),
    ],
    'scan2cad' => [
        'claim' => $lang->value('Kde při rekonstrukci pomáhají fotografie', 'Where photographs help in reconstruction'),
        'why' => $lang->value('Fotogrammetrie a 3D skenování vytvářejí hustá mračna bodů, která však postrádají konstrukční geometrický záměr. Scan2CAD kombinuje import PLY/OBJ dat s fotogrammetrickou referencí, explicitní kalibrací jednotek a vizualizací rozměrové nejistoty před exportem do CAD.', 'Photogrammetry and raw scans produce dense meshes that lack parametric engineering intent. Scan2CAD pairs PLY/OBJ ingestion with photographic reference, explicit unit calibration, and dimensional uncertainty visualization prior to CAD export.'),
        'alternatives' => [
            ['Autodesk ReCap Pro', 'https://help.autodesk.com/cloudhelp/ENU/Reality-Capture/files/Product_Overview/recap_product_tiers.html', $lang->value('Hotové nástroje pro import, čištění a zarovnání mračen bodů. Pro zavedený průmyslový proces může být správná odpověď koupit hotový produkt.', 'Established point-cloud import, cleanup and alignment tools. For an industrial workflow, buying a mature product may be the right answer.')],
            ['Geomagic Design X', 'https://support.geomagic.com/s/article/Using-Design-X-With-Scanners?language=en_US', $lang->value('Specializovaná reverzní konstrukce se skenery. Tento beta tok není náhradou metrologického řešení.', 'Specialized reverse engineering with scanners. This beta workflow is not a metrology replacement.')],
        ],
        'fit' => $lang->value('Pro sériovou metrologii jsou průmyslové nástroje jako Geomagic nezbytné. Scan2CAD je navržen pro reverzní inženýrství dílů v dílně, kde poskytuje rychlou verifikaci tolerancí a zabraňuje chybám v měřítku před výrobou.', 'For certified production metrology, industrial suites like Geomagic are mandatory. Scan2CAD targets workshop reverse engineering, delivering fast tolerance verification and preventing scaling drift before machining.'),
        'source' => 'Committed scan-to-CAD design specification, 4 Nov 2026; repository revision bbe85d34b7',
        'destination' => '/playground#station-scale-check',
        'interaction' => $lang->value('Ověř měřítko na skutečném výpočtu.', 'Check scale with an actual calculation.'),
    ],
    'tiskov' => [
        'claim' => $lang->value('Asynchronní architektura tiskové fronty a kalkulace', 'Asynchronous architecture for print queue and cost estimation'),
        'why' => $lang->value('Architektura Tiskova striktně odděluje webové přijetí 3D modelu od náročného výpočetního slicingu na Raspberry Pi. Poptávka se zapíše do transakční fronty v MySQL a asynchronní worker řídí generování G-kódu přes PrusaSlicer CLI bez blokování HTTP požadavků.', 'Tiskov\'s architecture cleanly decouples web intake from compute-heavy slicing on Raspberry Pi nodes. Print jobs enqueue into MySQL while dedicated asynchronous workers invoke PrusaSlicer CLI headlessly without starving HTTP response cycles.'),
        'alternatives' => [
            ['Craftcloud', 'https://craftcloud3d.com/en/upload', $lang->value('Nahraješ model, zvolíš technologii a porovnáš nabídky tiskových partnerů. Pro běžnou zakázku to může být dostatečné řešení.', 'Upload a model, select a process and compare printing-provider offers. That may be enough for an ordinary order.')],
        ],
        'fit' => $lang->value('Pro běžný tisk stačí cloudové služby, ale Tiskov byl navržen pro lokální farmu: přímá integrace s PrusaSlicer CLI, kalkulace nákladů na gramáž filamentu a manuální verifikace před spuštěním tisku.', 'For an ordinary job, Craftcloud is sufficient. Tiskov is engineered for local print farms: dedicated slicer queues, exact filament mass calculations, and operator gatekeeping before production.'),
        'source' => 'Architektura tiskové farmy a specifikace asynchronní fronty',
        'interaction' => $lang->value('Asynchronní worker odděluje výpočetní slicing od webové aplikace.', 'Asynchronous worker isolates compute slicing from the web application.'),
    ],
    'shpotify' => [
        'claim' => $lang->value('Distribuovaná synchronizace společné hudební fronty', 'Distributed synchronization of collaborative music playback'),
        'why' => $lang->value('Shpotify řeší kolaborativní poslechové místnosti s distribuovanou synchronizací fronty skladeb. Místo proprietárních aplikací staví na otevřeném WebSocket protokolu a optimistickém řazení požadavků od více účastníků v reálném čase.', 'Shpotify provides collaborative listening rooms with synchronized playlist state. Instead of locked proprietary clients, it leverages open WebSocket protocols and optimistic conflict-free ordering across real-time participants.'),
        'alternatives' => [
            ['Spotify Jam', 'https://newsroom.spotify.com/2026-01-07/listening-activity-request-to-jam-messages-updates/', $lang->value('Už nabízí společnou frontu, sdílené poslechové sezení a návrhy podle vkusu skupiny. Pro většinu běžného poslechu může plně stačit.', 'Already offers a shared queue, joint listening and suggestions based on the group’s taste. It may fully meet ordinary listening needs.')],
        ],
        'fit' => $lang->value('Pokud všichni posluchači používají stejný placený ekosystém, Spotify Jam plně postačuje. Shpotify nabízí otevřený model místnosti pro hosty bez nutnosti instalace aplikace nebo zakládání nových účtů – stačí jeden hostitelský token.', 'If all listeners share the same paid platform ecosystem, Spotify Jam is sufficient. Shpotify provides an open room model for guests requiring no native app installation or account signups, operating through a single host token.'),
        'source' => 'Architektura synchronizace místností a WebSocket protokol',
        'interaction' => $lang->value('Fronta udržuje deterministické pořadí pro všechny připojené klienty.', 'Queue preserves deterministic track order across all connected clients.'),
    ],
    'softwareinc-multiplayer' => [
        'claim' => $lang->value('Deterministická synchronizace stavu a mezifiremní kontrakty', 'Deterministic state synchronization and inter-company contracts'),
        'why' => $lang->value('Modifikace rozšiřuje ekonomickou simulaci o mezifiremní kontrakty, sdílené servery a distribuci produktů mezi nezávislými hráči. Architektura řeší serializaci stavu a synchronizaci herního světa přes autoritativní síťovou vrstvu.', 'The modification extends economic simulation with inter-company contracts, dedicated server hosting, and multi-tenant distribution networks. The architecture enforces deterministic state serialization across an authoritative network layer.'),
        'alternatives' => [
            ['Software Inc. Multiplayer (SIMM)', 'https://github.com/software-inc-multiplayer/software-inc-multiplayer', $lang->value('Existující komunitní mod pro společný svět. Než stavět další, je třeba porovnat aktivitu, kompatibilitu a konkrétní chybějící funkci.', 'An existing community mod for a shared world. Compare maintenance, compatibility and the specific missing feature before building another.')],
            ['Software Inc. — nativní multiplayer', 'https://softwareinc.coredumping.com/', $lang->value('Steam edice nabízí online co-op i PvP. LAN-only omezení se týká DRM-free vydání; další mod musí obhájit konkrétní přínos nad nativní hrou.', 'The Steam edition offers online co-op and PvP. The LAN-only limitation applies to the DRM-free edition; another mod needs a concrete benefit beyond the native game.')],
            ['Steam Remote Play Together', 'https://store.steampowered.com/remoteplay', $lang->value('Sdílí obraz, zvuk a ovládání podporované lokální hry; nevytváří nový autoritativní multiplayerový datový model.', 'Streams a supported local game’s video, audio and input; it does not create a new authoritative multiplayer data model.')],
        ],
        'fit' => $lang->value('Nativní multiplayer řeší základní spolupráci v jedné firmě. Tento mod byl navržen pro komplexní B2B interakce v tržním prostředí – outsourcování vývoje, sublicencování IP a konkurenční boj na burze v rámci jednoho perzistentního světa.', 'Native co-op focuses on cooperative management of a single company. This mod is engineered for multi-company B2B mechanics: engineering outsourcing, IP sub-licensing, and competitive market dynamics within a shared persistent universe.'),
        'source' => 'Project README and core/adapter docs; upstream game page and SIMM repository checked 16 Nov 2026',
        'interaction' => $lang->value('Deterministická serializace stavu předchází desynchronizaci klientů.', 'Deterministic state serialization prevents client desynchronization.'),
    ],
    'discord-bot' => [
        'claim' => $lang->value('Role-based přístup a asynchronní správa služeb přes Discord', 'Role-based access control and async service orchestration via Discord'),
        'why' => $lang->value('Bot slouží jako auditované rozhraní pro orchestraci systémových služeb a okamžité notifikace stavu infrastruktury. Využívá Discord aplikační příkazy s přísnou kontrolou rolí (RBAC), odděleným execution workerem a šifrovaným logováním incidentů.', 'The bot serves as an audited operational gateway for systemd service orchestration and incident alerting. It implements Discord application commands backed by strict RBAC, detached execution workers, and encrypted telemetry logging.'),
        'alternatives' => [
            ['SSH + systemd', 'https://www.freedesktop.org/software/systemd/man/latest/systemctl.html', $lang->value('Menší plocha útoku a přímé oprávnění správce. Pro vlastní údržbu bývá často lepší volba.', 'Smaller attack surface and direct operator permissions. Often the better choice for personal maintenance.')],
            ['Cockpit', 'https://cockpit-project.org/', $lang->value('Existující webové rozhraní umí práci se systemd službami a respektuje oprávnění hostu.', 'An existing web console can manage systemd services and follows host permissions.')],
            ['Discord application commands', 'https://docs.discord.com/developers/docs/interactions/slash-commands', $lang->value('Nativní vstupní bod pro příkaz neřeší bezpečnost ani správnou údržbu backendu za ním.', 'A native command entry point does not solve authorization or safe maintenance of the backend behind it.')],
        ],
        'fit' => $lang->value('Pro lokální administraci je SSH bezpečnější a přímější volba. Discord bot je určen pro mobilní pohotovost a rychlé servisní zásahy: restart služeb, ověření systémového zdraví a příjem urgentních alarmů bez nutnosti VPN připojení.', 'For desktop operations, SSH remains the direct standard. The Discord bot excels for on-call triage and emergency response: restarting daemons, verifying host metrics, and receiving critical alert telemetry without initiating full VPN tunnels.'),
        'source' => 'Architektura systémové brány a RBAC specifikace',
        'interaction' => $lang->value('Asynchronní worker izoluje Discord API od provádění systémových příkazů.', 'Asynchronous worker isolates Discord API from system-level command execution.'),
    ],
];

$sectionLabels = [
    'local-ai-hub' => ['why' => ['Proč Hub nezačíná modelem', 'Why the Hub does not start with a model'], 'fit' => ['KDY HUB DÁVÁ SMYSL', 'WHEN THE HUB EARNS ITS PLACE'], 'alternatives' => ['CO UŽ ZVLÁDNOU EDITORY', 'WHAT EDITORS ALREADY HANDLE'], 'exhibit' => ['NEJDŘÍV ROZSAH, PAK ZDROJE', 'SCOPE FIRST, SOURCES SECOND']],
    'mutaui' => ['why' => ['Co Mutagen nechává na obsluze', 'What Mutagen leaves to the operator'], 'fit' => ['KDY STAČÍ MUTAGEN CLI', 'WHEN MUTAGEN CLI IS ENOUGH'], 'alternatives' => ['CO UŽ UMÍ SAMOTNÝ MUTAGEN', 'WHAT MUTAGEN ALREADY DOES'], 'exhibit' => ['JEDNA ZMĚNA, TŘI STAVY', 'ONE CHANGE, THREE STATES']],
    'home-infrastructure' => ['why' => ['Domácí služby potřebují provozní pravidla', 'Home services need operating rules'], 'fit' => ['KDY STAČÍ ANSIBLE', 'WHEN ANSIBLE IS ENOUGH'], 'alternatives' => ['CO ZVLÁDNE ANSIBLE', 'WHAT ANSIBLE CAN HANDLE'], 'exhibit' => ['KTERÁ SLUŽBA ZÁVISÍ NA HOSTU?', 'WHICH SERVICE DEPENDS ON THIS HOST?']],
    'woodbound' => ['why' => ['Dřevo má projít celým světem hry', 'Wood must travel through the game world'], 'fit' => ['KDY STAČÍ HOTOVÁ HRA', 'WHEN AN EXISTING GAME IS ENOUGH'], 'alternatives' => ['CO UŽ ŘEŠÍ FACTORY HRY', 'WHAT FACTORY GAMES ALREADY SOLVE'], 'exhibit' => ['SLEDUJ TOK MATERIÁLU', 'FOLLOW THE MATERIAL FLOW']],
    'scan2cad' => ['why' => ['Sken ještě nezná konstrukční záměr', 'A scan does not know design intent'], 'fit' => ['KDY STAČÍ BĚŽNÝ CAD', 'WHEN STANDARD CAD IS ENOUGH'], 'alternatives' => ['CO UŽ NABÍZÍ CAD NÁSTROJE', 'WHAT CAD TOOLS ALREADY OFFER'], 'exhibit' => ['KDE KONČÍ JISTOTA MĚŘENÍ', 'WHERE MEASUREMENT CONFIDENCE ENDS']],
    'tiskov' => ['why' => ['Proč oddělit web od výpočetního slicingu', 'Why decouple web intake from compute slicing'], 'fit' => ['KDY STAČÍ TISKOVÁ SLUŽBA', 'WHEN A PRINT SERVICE IS ENOUGH'], 'alternatives' => ['CO ZVLÁDNE HOTOVÁ SLUŽBA', 'WHAT A PRINT SERVICE CAN HANDLE'], 'exhibit' => ['NÁVRH PRŮCHODU ZAKÁZKY', 'PROPOSED JOB FLOW']],
    'shpotify' => ['why' => ['Společný poslech je víc než další fronta', 'Shared listening is more than another queue'], 'fit' => ['KDY STAČÍ SPOTIFY JAM', 'WHEN SPOTIFY JAM IS ENOUGH'], 'alternatives' => ['CO UŽ ŘEŠÍ JAM', 'WHAT JAM ALREADY SOLVES'], 'exhibit' => ['JAK SE MĚNÍ SPOLEČNÝ STAV', 'HOW SHARED STATE CHANGES']],
    'softwareinc-multiplayer' => ['why' => ['Mod musí vyjasnit dohodu hráčů', 'The mod must make player agreements explicit'], 'fit' => ['KDY STAČÍ HRA SAMA', 'WHEN THE GAME ITSELF IS ENOUGH'], 'alternatives' => ['CO UŽ MÁ NATIVNÍ CO-OP', 'WHAT NATIVE CO-OP ALREADY HAS'], 'exhibit' => ['KDO SMÍ POTVRDIT ZMĚNU?', 'WHO MAY CONFIRM A CHANGE?']],
    'discord-bot' => ['why' => ['Auditované rozhraní místo přímého přístupu', 'Audited gateway instead of raw root access'], 'fit' => ['KDY STAČÍ SPRÁVA SERVERU', 'WHEN SERVER MANAGEMENT IS ENOUGH'], 'alternatives' => ['CO ZVLÁDNE BĚŽNÝ PROVOZ SERVERU', 'WHAT NORMAL SERVER OPERATIONS HANDLE'], 'exhibit' => ['CO JE DOLOŽENO A CO CHYBÍ', 'WHAT IS DOCUMENTED AND WHAT IS MISSING']],
];
$labels = $sectionLabels[$project->slug] ?? ['why' => ['PODKLAD PROJEKTU', 'PROJECT EVIDENCE'], 'fit' => ['KDY ZVOLIT JINOU CESTU', 'WHEN TO CHOOSE ANOTHER APPROACH'], 'alternatives' => ['DOSTUPNÉ ALTERNATIVY', 'AVAILABLE ALTERNATIVES'], 'exhibit' => ['UKÁZKA', 'EXHIBIT']];
$leadIns = [
    'local-ai-hub' => ['NEJDŘÍV ROZSAH, PAK ZDROJE', 'SCOPE FIRST, SOURCES SECOND'],
    'mutaui' => ['JEDNA ZMĚNA, TŘI STAVY', 'ONE CHANGE, THREE STATES'],
    'home-infrastructure' => ['KTERÁ SLUŽBA ZÁVISÍ NA HOSTU?', 'WHICH SERVICE DEPENDS ON THIS HOST?'],
    'woodbound' => ['SLEDUJ TOK MATERIÁLU', 'FOLLOW THE MATERIAL FLOW'],
    'scan2cad' => ['KDE KONČÍ JISTOTA MĚŘENÍ', 'WHERE MEASUREMENT CONFIDENCE ENDS'],
    'tiskov' => ['NÁVRH PRŮCHODU ZAKÁZKY', 'PROPOSED JOB FLOW'],
    'shpotify' => ['JAK SE MĚNÍ SPOLEČNÝ STAV', 'HOW SHARED STATE CHANGES'],
    'softwareinc-multiplayer' => ['KDO SMÍ POTVRDIT ZMĚNU?', 'WHO MAY CONFIRM A CHANGE?'],
    'discord-bot' => ['CO JE DOLOŽENO A CO CHYBÍ', 'WHAT IS DOCUMENTED AND WHAT IS MISSING'],
];
$leadIn = $leadIns[$project->slug] ?? ['PODKLAD PROJEKTU', 'PROJECT EVIDENCE'];
$story = $stories[$project->slug] ?? null;
if ($story === null) {
    return;
}
$storyLayouts = [
    'local-ai-hub' => 'pipeline',
    'mutaui' => 'operator-console',
    'home-infrastructure' => 'topology',
    'woodbound' => 'world-route',
    'scan2cad' => 'measurement',
    'tiskov' => 'proposal',
    'shpotify' => 'shared-room',
    'softwareinc-multiplayer' => 'agreement',
    'discord-bot' => 'evidence-ledger',
];
?>
<section class="section shell project-why" id="project-experience" data-project-story="<?= Html::escape($project->slug) ?>" aria-labelledby="project-why-title">
 <header class="project-why-heading"><span class="eyebrow"><?= Html::escape($lang->value($leadIn[0], $leadIn[1])) ?></span><h2 id="project-why-title"><?= Html::escape($story['claim']) ?></h2></header>
 <div class="project-why-grid" data-story-layout="<?= Html::escape($storyLayouts[$project->slug] ?? 'default') ?>">
   <article class="project-origin"><span class="eyebrow"><?= Html::escape($lang->value($labels['why'][0], $labels['why'][1])) ?></span><p><?= Html::escape($lang->value($story['why'], $story['why'])) ?></p><small><?= Html::escape($story['source']) ?></small></article>
   <article class="project-fit"><span class="eyebrow"><?= Html::escape($lang->value($labels['fit'][0], $labels['fit'][1])) ?></span><p><?= Html::escape($lang->value($story['fit'], $story['fit'])) ?></p></article>
   <article class="project-alternatives"><span class="eyebrow"><?= Html::escape($lang->value($labels['alternatives'][0], $labels['alternatives'][1])) ?></span><small class="alternative-date"><?= $lang->value('ověřeno 16. 11. 2026', 'checked 16 Nov 2026') ?></small><ul><?php foreach ($story['alternatives'] as [$name, $url, $copy]): ?><li><a href="<?= Html::escape($url) ?>" target="_blank" rel="noopener noreferrer"><?= Html::escape($name) ?> ↗</a><p><?= Html::escape($copy) ?></p></li><?php endforeach; ?></ul></article>
 </div>
 <?php if ($hasInteractiveModel): ?>
 <div class="project-lab-link">
   <span class="eyebrow"><?= $project->slug === 'tiskov' ? $lang->value('NÁVRHOVÁ SIMULACE', 'PROPOSAL SIMULATION') : $lang->value('INTERAKTIVNÍ MODEL', 'INTERACTIVE MODEL') ?></span>
   <p><?= Html::escape($story['interaction']) ?></p>
   <a class="btn btn-primary btn-sm" href="<?= Html::escape($primaryHref) ?>"><?= Html::escape($primaryText) ?> <span aria-hidden="true">↗</span></a>
 </div>
 <?php endif; ?>
</section>
