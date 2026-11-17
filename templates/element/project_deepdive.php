<?php
declare(strict_types=1);

use App\Support\Html;

$detail = match ($project->slug) {
    'local-ai-hub' => [
        [$lang->value('Nejdřív mapa repozitáře, potom otázka', 'Map the repository before the question'),
         $lang->value('První použití stabilního kořene spustí idempotentní registraci a asynchronní preprocessing. Ten mapuje vhodné soubory a změny, staví vestavěný code index a deterministické karty; Serena, lexikální vrstva a RAG doplňují výsledky podle dostupnosti a potřeby. Není to trénování modelu ani jediné velké embedding volání.', 'First use of a stable root triggers idempotent registration and asynchronous preprocessing. It inventories eligible files and changes, builds a built-in code index and deterministic cards; Serena, lexical search and RAG enrich results when available and useful. This is not model training or one giant embedding call.'),
         $lang->value('Přesný dotaz „kdo volá tuto metodu?“ začíná v code indexu a úzkém zdrojovém výřezu. Širší úkol může složit adaptive context z více důkazů; RAG a lokální syntéza přicházejí až po levnějších indexovaných cestách. In-progress preprocessing se nepolluje ani neopakuje; testy spouští hostitelské prostředí a model zůstává poradní. Lokální inference je volitelná.', 'The exact question “who calls this method?” starts in the code index and a narrow source slice. A broader task can compose adaptive context from multiple sources; RAG and local synthesis follow cheaper indexed routes. An in-progress preprocessing run is reused, not polled or repeated; tests run in the host environment. Model output remains advisory and local inference is optional.')],
    ],
    'mutaui' => [
        [$lang->value('Rozhraní nad existujícím enginem', 'An interface over an existing engine'),
         $lang->value('MutaUI spravuje pohled na synchronizační a forwarding relace. Neobsahuje vlastní synchronizační algoritmus: komunikuje s odděleně nainstalovaným Mutagen CLI a jeho lokálním démonem.', 'MutaUI provides a view of synchronization and forwarding sessions. It does not implement its own sync algorithm: it communicates with a separately installed Mutagen CLI and its local daemon.'),
         $lang->value('Před prvním použitím dokumentace doporučuje dvě zkušební složky, kontrolu zvoleného režimu a náhledu operace. Obrázky aplikace pocházejí z offline dema se smyšlenými daty a vypnutými operacemi.', 'For first use, the documentation recommends two disposable folders and reviewing the selected mode and operation preview. Application screenshots come from an offline demo with synthetic data and disabled operations.')],
    ],
    'home-infrastructure' => [
        [$lang->value('Konfigurace a služby', 'Configuration and services'),
         $lang->value('Git uchovává požadovaný stav, Semaphore spouští úlohy a Ansible provádí změny. Hlavní uzel provozuje Pi-hole, Unbound, Jellyfin, Semaphore a BookStack; CUPS tisk, NAS a Proxmox mají vlastní roli.', 'Git stores desired state, Semaphore runs jobs and Ansible applies changes. The primary node hosts Pi-hole, Unbound, Jellyfin, Semaphore and BookStack; CUPS printing, NAS and Proxmox each have a distinct role.'),
         $lang->value('Modelový požadavek na video projde od klienta přes router do Jellyfinu a na NAS pro soubor; tisková úloha míří do samostatného CUPS uzlu. Pi-hole a Unbound zajišťují lokální DNS služby, Semaphore spouští správu přes Ansible a Proxmox se používá jako testovací host na vyžádání. Jde o dokumentované role, nikoli živou telemetrii.', 'A modeled video request flows from a client through the router to Jellyfin and the NAS file; a print job goes to a separate CUPS node. Pi-hole and Unbound provide local DNS services, Semaphore runs Ansible management and Proxmox acts as an on-demand test host. These are documented roles, not live telemetry.')],
    ],
    'woodbound' => [
        [$lang->value('Svět jako navazující vrstvy', 'A world built in layers'),
         $lang->value('Návrh světa obsahuje oblasti, průmyslová místa a záměry tras. Generovací pipeline zpracovává terén a biomy, následně řeší umístění bodů a propojení koridorů.', 'The world design includes regions, industrial locations and route intents. A generation pipeline handles terrain and biomes, then placement and corridor connections.'),
         $lang->value('Kontrola například porovnává umístění bodů se souší a ověřuje prostorové podmínky. Vývojové kontroly a návrhové podklady samy nepotvrzují přijatý hratelný svět.', 'Checks can compare location placement with land and verify spatial invariants. Development checks and design documents alone do not establish an accepted playable world.')],
    ],
    'scan2cad' => [
        [$lang->value('Od skenu k návrhu pro kontrolu', 'From a scan to a reviewable candidate'),
         $lang->value('Dokumentovaný postup začíná sítí nebo mračnem bodů a potvrzením fyzického měřítka. Po očištění následuje rekonstrukce geometrie a export kandidáta ve formátu STEP nebo výkresu DXF.', 'The documented workflow starts with a mesh or point cloud and confirmation of physical scale. Cleanup is followed by geometry reconstruction and export of a STEP candidate or DXF drawing.'),
         $lang->value('Referenční fotografie lze navázat přes odpovídající body; odhad polohy kamery se před projekcí kontroluje a potvrzuje. Překryv i vypočtená odchylka zůstávají pomůckou, nikoliv náhradou fyzického měření.', 'Reference photographs can be linked through matching points; the camera estimate is reviewed and confirmed before projection. Overlay and computed deviation are aids, not substitutes for physical measurement.')],
    ],
    'tiskov' => [
        [$lang->value('Poptávka a oddělené zpracování', 'Intake and separate processing'),
         $lang->value('Portfolio popisuje návrh, v němž webová aplikace přijme podklady, MySQL fronta oddělí příjem od zpracování a Raspberry Pi worker vrátí výsledek k posouzení. Repozitář ani běžící aplikace nejsou k dispozici, takže tento tok není ověřenou implementací.', 'The portfolio record describes a design where a web app receives files, a MySQL queue separates intake from processing, and a Raspberry Pi worker returns a result for review. The repository and running app are unavailable, so this flow is not a verified implementation.'),
         $lang->value('Výpočet ceny, slicer, automatická platba ani konkrétní provoz nejsou doložené. Stránka proto ukazuje pouze smyšlenou frontu; původní důvod projektu se v dostupných podkladech nedochoval.', 'Pricing calculations, a slicer, automatic payment and actual operation are not evidenced. The page therefore shows only a synthetic queue; the original reason for the project is not preserved in the available material.')],
    ],
    'shpotify' => [
        [$lang->value('Místnosti, fronta a doporučení', 'Rooms, queues and recommendations'),
         $lang->value('Projekt popisuje oddělené hudební místnosti, účty a společnou frontu skladeb. Fronta má evidenci pořadí a ochranu proti opakovanému zpracování stejného požadavku; doporučení pracují s osobními a místnostními informacemi.', 'The project describes separate music rooms, accounts and a shared queue. The queue tracks order and protects against duplicate processing; recommendations use personal and room context.'),
         $lang->value('Ukázková skupina může přidat skladbu do své místnosti a projít návrhy. Propojování přehrávače a vzdáleného audio agenta dále probíhá; zdrojové moduly nejsou důkazem skutečného poslechu.', 'An example group can add a track to its room and review suggestions. Player and remote audio-agent integration remains in progress; source modules do not prove actual listening.')],
    ],
    'softwareinc-multiplayer' => [
        [$lang->value('Spolupráce jako samostatné jádro', 'Collaboration as a separate core'),
         $lang->value('Mod zkoumá společnou práci, projekty a dohody ve hře. Pravidla v C# jádru jsou oddělena od adaptéru pro herní prostředí, takže část logiky lze ověřovat bez spuštěné hry.', 'The mod explores shared work, projects and agreements in the game. Rules in a C# core are separated from the game adapter, so some logic can be checked without running the game.'),
         $lang->value('Například návrh sdíleného projektu potřebuje správně identifikovat účastníky a předat změnu do hry. Samostatné testy ani vytvořený balíček nepotvrzují hratelnou multiplayerovou verzi.', 'For example, a shared-project proposal needs to identify participants and pass a change into the game. Isolated tests and a built package do not establish a playable multiplayer release.')],
    ],
    'discord-bot' => [
        [$lang->value('Příkazy a správa služby', 'Commands and service maintenance'),
         $lang->value('Portfolio katalog popisuje aplikační příkazy a samostatnou údržbovou větev. Zdrojový checkout ale není dostupný k nezávislé kontrole; přesnou implementaci klienta, příkazů ani oprávnění proto nelze potvrdit.', 'The portfolio catalog describes application commands and a separate maintenance path. The source checkout is unavailable for independent review, so the client, commands and permissions cannot be confirmed.'),
         $lang->value('Starší poznámka katalogu hlásí syntaktickou chybu údržbového modulu. Ber ji jako evidované předchozí zjištění, nikoli jako novou kontrolu současného zdroje. Následující diagram je pouze schematický příklad.', 'An earlier catalog note reports a syntax error in the maintenance module. Treat this as a recorded prior finding, not a fresh check of current source. The diagram below is only a schematic example.')],
    ],
    default => null,
};
$architecture = match ($project->slug) {
    'local-ai-hub' => [[
        ['Kořen projektu','Project root','Stabilní absolutní cesta','Stable absolute path'], ['Registrace','Registration','Jeden idempotentní start','One idempotent start'], ['Inventář','Inventory','Vhodné zdrojové soubory','Eligible source files'],
        ['Změnové hashe','Change hashes','Co se změnilo','What changed'], ['Code index','Code index','Symboly a vztahy','Symbols and links'], ['Deterministické karty','Deterministic cards','Fakta ze zdrojů','Source-derived facts'],
        ['Serena','Serena','Jazykové symboly, pokud dostupné','Language symbols when available'], ['Lexikální index','Lexical index','Textové hledání','Text search'], ['RAG','RAG','Doplňkově, když je potřeba','Optional when needed'],
        ['Souborové mapy','File maps','Shrnutí souborů','File summaries'], ['Modul a projekt','Module & project','Vyšší úroveň kontextu','Higher-level context'], ['Diagnostika','Diagnostics','Stav průchodu','Run state'],
    ], [[0,1],[1,2],[2,3],[3,4],[4,5],[5,6],[6,7],[7,8],[8,9],[9,10],[10,11]]],
    'mutaui' => [[
        ['Uživatel','User','Kontroluje před akcí','Reviews before action'], ['MutaUI','MutaUI','Přehled a nastavení','Overview and setup'], ['Mutagen CLI','Mutagen CLI','Instaluje se zvlášť','Installed separately'],
        ['Lokální démon','Local daemon','Zdroj stavu relací','Session state source'], ['Sync relace','Sync session','Konfigurace a režim','Configuration and mode'], ['Koncové složky','Endpoints','Obsah synchronizace','Synced content'],
        ['Forward relace','Forward session','Předání portu','Port forwarding'],
    ], [[0,1],[1,2],[2,3],[3,4],[4,5],[3,6]]],
    'home-infrastructure' => [[
        ['Git','Git','Požadovaná konfigurace','Desired configuration'], ['Semaphore','Semaphore','Spouští řízené úlohy','Runs controlled jobs'], ['Ansible','Ansible','Provádí playbooky','Applies playbooks'],
        ['Router','Router','Routing · firewall · VPN','Routing · firewall · VPN'], ['Hlavní Pi','Primary Pi','Jellyfin · DNS · Semaphore · BookStack','Jellyfin · DNS · Semaphore · BookStack'], ['Tiskové Pi','Print Pi','CUPS fronta','CUPS queue'],
        ['NAS','NAS','Soubory a zálohy','Files and backups'], ['Proxmox','Proxmox','Testovací host na vyžádání','On-demand test host'],
    ], [[0,1],[1,2],[2,3],[2,4],[2,5],[2,6],[2,7]]],
    'woodbound' => [[
        ['Definice světa','World definition','Oblasti a záměry','Regions and intents'], ['Pevnina a reliéf','Land & relief','Tvar země','Land shape'], ['Hydrologie','Hydrology','Tok vody a povodí','Water flow and watershed'],
        ['Regiony a biomy','Regions & biomes','Čtyři typy oblastí','Four region types'], ['Průmyslová místa','Industry sites','Uzly a stavební plochy','Locations and build pads'], ['Koridory','Corridors','Cesty, koleje a stezky','Road, rail and trail intents'],
        ['Pathfinder','Pathfinder','Zkouší trasu v kandidátovi','Searches the candidate map'], ['Validátory','Validators','Souš a prostorové invarianty','Land and spatial invariants'], ['Asset / scéna','Asset / scene','Bake výstup k další kontrole','Baked output for review'],
    ], [[0,1],[0,4],[1,2],[1,3],[2,3],[3,4],[4,5],[5,6],[6,7],[7,8]]],
    'scan2cad' => [[
        ['Síť / mračno','Mesh / point cloud','Vstupní geometrie','Input geometry'], ['Měřítko','Scale','Potvrzený rozměr','Confirmed dimension'], ['Očištění','Cleanup','Kontrola skenu','Scan review'],
        ['Referenční foto','Reference photo','Odpovídající body','Matching points'], ['Kamera','Camera','Odhad potvrzuje člověk','Estimate reviewed by a person'], ['Geometrie','Geometry','Upravitelný kandidát','Editable candidate'],
        ['Nejistoty','Uncertainty','Kontrola odchylek','Deviation review'], ['STEP / DXF','STEP / DXF','Export ke kontrole','Export for review'],
    ], [[0,1],[1,2],[2,5],[3,4],[4,5],[5,6],[6,7]]],
    'tiskov' => [[
        ['Poptávka','Request','Zamýšlený vstup','Proposed intake'], ['Web / Symfony','Web / Symfony','Popsáno v návrhu','Described in design'], ['Soubor + metadata','File + metadata','Předpokládané podklady','Proposed input'],
        ['MySQL fronta','MySQL queue','Navržená komponenta','Proposed component'], ['Worker na Pi','Pi worker','Navržené oddělení','Proposed separation'], ['Slicer','Slicer','Běh není doložen','Run not evidenced'],
        ['Odhad','Estimate','Výpočet nedoložen','Calculation not evidenced'], ['Ruční potvrzení','Manual review','Zamýšlený krok','Intended step'],
    ], [[0,1],[1,2],[1,3],[3,4],[4,5],[5,6],[6,7]]],
    'shpotify' => [[
        ['Účet','Account','Přihlášený člen','Signed-in member'], ['Pozvánka','Invitation','Vstup do místnosti','Room access'], ['Místnost','Room','Osobní či sdílená','Personal or shared'],
        ['Fronta','Queue','Pořadí skladeb','Track order'], ['Ochrana duplicit','Idempotency','Opakovaný požadavek','Repeated request'], ['Doporučení','Recommendations','Osobní a místnostní signály','Personal and room signals'],
        ['Spotify most','Spotify bridge','Integrace ve vývoji','Integration in progress'], ['Přehrávání','Playback','End-to-end nepotvrzeno','End-to-end not verified'],
    ], [[0,1],[1,2],[2,3],[3,4],[3,5],[5,6],[6,7]]],
    'softwareinc-multiplayer' => [[
        ['Hráč','Player','Záměr spolupráce','Collaboration intent'], ['Herní adaptér','Game adapter','Vstup a nativní UI','Input and native UI'], ['C# doménové jádro','C# domain core','Pravidla projektu','Project rules'],
        ['Dohoda','Agreement','Účastníci a oprávnění','Participants and authority'], ['Persistence','Persistence','Stav a zabezpečení','State and security'], ['Testy jádra','Core tests','Bez běhu hry','Without game runtime'],
        ['Game build','Game build','Cílová verze hry','Target game version'], ['Herní přijetí','In-game acceptance','Dosud neověřeno','Not yet verified'],
    ], [[0,1],[1,2],[2,3],[2,4],[2,5],[1,6],[6,7]]],
    'discord-bot' => [[
        ['Katalog','Catalog','Popisuje příkazy','Describes commands'], ['Příkazová cesta','Command path','Detaily nedoložené','Details not evidenced'], ['Údržbová větev','Maintenance path','Nahlášena katalogem','Reported by catalog'],
        ['Oprávnění','Authorization','Konkrétní pravidla neznámá','Exact rules unknown'], ['Starší zjištění','Prior finding','Syntax chyba hlášená dříve','Syntax issue reported earlier'], ['Zdrojový checkout','Source checkout','Není k dispozici','Unavailable'],
        ['Živý běh','Live run','Neověřen','Unverified'],
    ], [[0,1],[1,2],[1,3],[3,4],[4,5],[5,6]]],
    default => null,
};
$walkthrough = match ($project->slug) {
    'local-ai-hub' => [
        ['01','Zaregistruj stabilní kořen','Register a stable root','První použití odešle jednorázový preprocess požadavek s absolutní cestou.','First use sends a one-time preprocess request with an absolute path.'],
        ['02','Postav znovupoužitelnou mapu','Build a reusable map','Pozadí projde inventář a hashe, index a deterministické karty; Serena a RAG se připojí podle dostupnosti a potřeby.','The background pass builds inventory and hashes, an index and deterministic cards; Serena and RAG join when available and useful.'],
        ['03','Zeptej se přesně','Ask precisely','Přesný dotaz na symbol použije code index a potřebné zdrojové výřezy; širší úkol skládá context z více důkazů až po levnějších indexovaných cestách.','An exact symbol question uses the code index and needed source slices; broader tasks compose context from multiple sources after cheaper indexed routes.'],
        ['04','Ověř změnu v hostu','Verify on the host','Test spouští hostitelské prostředí. Výsledek modelu zůstává návrhem, ne důkazem správnosti.','Tests run in the host environment. Model output remains a suggestion, not proof of correctness.'],
    ],
    'mutaui' => [
        ['01','Nainstaluj Mutagen zvlášť','Install Mutagen separately','Začni dvěma dočasnými složkami; MutaUI Mutagen CLI ani démon neinstaluje.','Start with two disposable folders; MutaUI does not install Mutagen CLI or its daemon.'],
        ['02','Prohlédni relace','Inspect sessions','Stav synchronizace a forwardingu čte rozhraní z lokálního démona.','The interface reads sync and forwarding state from the local daemon.'],
        ['03','Zkontroluj režim a náhled','Review mode and preview','Před spuštěním nové relace projdi zvolený režim a náhled operace.','Review the chosen mode and operation preview before starting a new session.'],
        ['04','Ověř konkrétní platformu','Verify the target platform','Přiložené snímky jsou offline demo; build sám nepotvrzuje nativní podporu systému.','Included images are an offline demo; a build alone does not prove native OS support.'],
    ],
    'home-infrastructure' => [
        ['01','Uprav požadovaný stav','Change desired state','Změna konfigurace začíná v Gitu, aby byla dohledatelná a vratná.','A configuration change starts in Git so it remains traceable and reversible.'],
        ['02','Spusť Ansible přes Semaphore','Run Ansible through Semaphore','Runner zaznamená řízený běh; Ansible aplikuje roli na zvolený cíl.','The runner records a controlled job; Ansible applies a role to the selected target.'],
        ['03','Sleduj konkrétní službu','Trace a concrete service','Video vede přes router do Jellyfinu a NAS; tisková úloha končí na CUPS uzlu.','Video flows through the router to Jellyfin and the NAS; a print job ends at the CUPS node.'],
        ['04','Rozliš konfiguraci od provozu','Separate configuration from runtime','Pi-hole, Unbound, BookStack a další služby jsou dokumentované role. Diagram nečte dostupnost ani adresy.','Pi-hole, Unbound, BookStack and other services are documented roles. The diagram reads no availability or addresses.'],
    ],
    'woodbound' => [
        ['01','Zadej záměr světa','Define world intent','Definice spojuje čtyři oblasti, průmyslová místa a záměry tras.','The definition connects four regions, industrial sites and route intents.'],
        ['02','Vypočti fyzické vrstvy','Generate physical layers','Uspořádané moduly skládají terén, hydrologii a biomy před umístěním uzlů a koridorů.','Ordered modules build terrain, hydrology and biomes before placing sites and corridors.'],
        ['03','Najdi a zkontroluj trasu','Find and validate a route','Pathfinder pracuje s kandidátní mapou; validátor odhaluje například bod na vodě nebo přerušený koridor.','The pathfinder uses a candidate map; validators can expose a site on water or a broken corridor.'],
        ['04','Považ výsledek za kandidáta','Treat output as a candidate','Asset a scéna jsou výstupy generování, nikoli přijetí hotové hratelné hry.','The asset and scene are generated outputs, not acceptance of a finished playable game.'],
    ],
    'scan2cad' => [
        ['01','Načti sken a rozměr','Load a scan and dimension','Bez fyzické reference nemá síť spolehlivé absolutní měřítko.','Without a physical reference, a mesh has no reliable absolute scale.'],
        ['02','Očisti geometrii','Review the geometry','Označ problematické části a odděl pozorované tvary od nejistých.','Mark problem areas and separate observed shapes from uncertain ones.'],
        ['03','Potvrď fotografickou projekci','Confirm photo projection','Spoj referenční body, zkontroluj odhad kamery a před projekcí ho potvrď.','Match reference points, review the camera estimate and confirm it before projection.'],
        ['04','Exportuj CAD kandidáta','Export a CAD candidate','STEP/DXF a vypočtená odchylka pomáhají kontrole; nenahrazují fyzické měření.','STEP/DXF and computed deviation aid review; they do not replace physical measurement.'],
    ],
    'tiskov' => [
        ['01','Představ modelovou poptávku','Start with a hypothetical request','Jde o ilustraci návrhu, nikoli formulář napojený na službu.','This illustrates a design; it is not a form connected to a service.'],
        ['02','Odděl příjem a práci','Separate intake and processing','Dokumentovaný návrh vkládá mezi web a výpočet frontu; její nasazení není ověřené.','The documented design puts a queue between the web app and processing; its deployment is unverified.'],
        ['03','Worker je navržený','Worker is proposed','Raspberry Pi worker je součástí popisu architektury, jeho běh ale není doložen.','A Raspberry Pi worker appears in the architecture description, but no run is evidenced.'],
        ['04','Výsledek potřebuje důkaz','The result needs evidence','Ukázková fronta nic neslicuje ani nepočítá cenu; dostupné podklady neukazují původní motiv.','The sample queue slices nothing and calculates no price; available material does not state the original motive.'],
    ],
    'shpotify' => [
        ['01','Vstup do místnosti','Enter a room','Účet a pozvánka určují, zda jde o osobní nebo sdílený prostor.','The account and invitation determine whether the space is personal or shared.'],
        ['02','Spravuj frontu společně','Manage the queue together','Členové řadí skladby; verze a idempotence pomáhají zvládat opakované požadavky.','Members reorder tracks; versions and idempotency help handle repeated requests.'],
        ['03','Prohlédni doporučení','Inspect a recommendation','Doporučovací část používá osobní a místnostní kontext, ale příklad neznamená stream.','The recommendation layer uses personal and room context, but the example is not a stream.'],
        ['04','Přehrávání zůstává otevřené','Playback remains open','Prohlížečový přehrávač a vzdálený audio agent ještě potřebují vlastní end-to-end ověření.','Browser playback and the remote audio agent still need their own end-to-end verification.'],
    ],
    'softwareinc-multiplayer' => [
        ['01','Hráč navrhne spolupráci','A player proposes collaboration','Sdílený projekt nebo dohoda začíná vstupem z herní vrstvy.','A shared project or agreement starts with an input from the game layer.'],
        ['02','Jádro ověří pravidla','The core checks rules','C# doména kontroluje účastníky a stav nezávisle na části herního adaptéru.','The C# domain checks participants and state separately from the game adapter.'],
        ['03','Adaptér předá změnu','The adapter passes the change','Nativní vazba a persistence se musí ověřit při spuštění i restartu cílové hry.','Native integration and persistence need verification during a target-game run and restart.'],
        ['04','Hratelné vydání není potvrzené','A playable release is not confirmed','Testy jádra proběhly pro konkrétní DLL, ale kandidát nebyl spuštěn ve hře.','Core tests ran for a specific DLL, but the candidate was not launched in-game.'],
    ],
    'discord-bot' => [
        ['01','Začni u katalogu','Start from the catalog','Portfolio katalog zmiňuje aplikační příkazy; checkout není k dispozici k nezávislé kontrole.','The portfolio catalog mentions application commands; no checkout is available for independent review.'],
        ['02','Oprávnění zůstávají otázkou','Authorization remains unknown','Konkrétní příkazy, role a kontroly oprávnění nejsou z dostupného zdroje potvrzené.','Exact commands, roles and permission checks are not confirmed by available source.'],
        ['03','Odděl starší hlášení','Separate the prior report','Katalog uchovává dřívější hlášení syntaktické chyby; současný kód jsme znovu nekontrolovali.','The catalog retains an earlier syntax-error report; current code was not rechecked.'],
        ['04','Běh není doložen','Runtime is unverified','Diagram je schematický. Není k dispozici důkaz spuštěného bota ani služby.','The diagram is schematic. There is no evidence of a running bot or service.'],
    ],
    default => [],
};
?>
 <?php if ($detail !== null):
[$copy] = $detail;
[$architectureNodes, $architectureEdges] = $architecture;
$architectureColumns = match ($project->slug) {
 'local-ai-hub', 'woodbound', 'tiskov' => 4,
 'mutaui', 'scan2cad', 'softwareinc-multiplayer', 'discord-bot' => 2,
 default => 3,
};
$architectureWidth = 40 + $architectureColumns * 280 + max(0, $architectureColumns - 1) * 30;
$architectureHeight = max(300, (int)ceil(count($architectureNodes) / $architectureColumns) * 142 + 20);
$walkthroughTitle = match ($project->slug) {
 'local-ai-hub' => $lang->value('Od změny souboru k ověřitelnému výsledku', 'From a changed file to a verifiable result'),
 'mutaui' => $lang->value('Co se musí stát před synchronizací', 'What must happen before sync'),
 'home-infrastructure' => $lang->value('DNS od domácího klienta k fallbacku', 'DNS from a home client to fallback'),
 'woodbound' => $lang->value('Od generace světa k průchodné trase', 'From world generation to a traversable route'),
 'scan2cad' => $lang->value('Od fotografie k měřítku, které jde ověřit', 'From a photo to a scale that can be checked'),
 'tiskov' => $lang->value('Co by následovalo po přijetí poptávky', 'What would happen after a request is accepted'),
 'shpotify' => $lang->value('Co se stane, když dva lidé změní frontu', 'What happens when two people edit the queue'),
 'softwareinc-multiplayer' => $lang->value('Od příkazu k povolené změně hry', 'From a command to an authorized game change'),
 'discord-bot' => $lang->value('Co lze doložit bez zdrojového kódu', 'What can be evidenced without source code'),
 default => $lang->value('Jeden konkrétní průchod', 'One concrete trace'),
};
$architectureTitle = match ($project->slug) {
 'local-ai-hub' => $lang->value('Preprocessing a cesta k důkazu', 'Preprocessing and the path to evidence'),
 'mutaui' => $lang->value('Rozhraní, daemon a koncové body', 'UI, daemon and endpoints'),
 'home-infrastructure' => $lang->value('Hosty, služby a datové cesty', 'Hosts, services and data paths'),
 'woodbound' => $lang->value('Vazby mezi fázemi generování', 'Links between generation stages'),
 'scan2cad' => $lang->value('Měřítko, registrace a lidská kontrola', 'Scale, registration and human review'),
 'tiskov' => $lang->value('Navržená fronta, ne běžící služba', 'A proposed queue, not a running service'),
 'shpotify' => $lang->value('Místnosti, fronta a hranice přehrávání', 'Rooms, queue and playback boundary'),
 'softwareinc-multiplayer' => $lang->value('Jádro, persistence a herní adaptér', 'Core, persistence and game adapter'),
 'discord-bot' => $lang->value('Katalogová cesta s neověřenými kroky', 'Catalog path with unverified steps'),
 default => $lang->value('Mapa architektury', 'Architecture map'),
};
$more = match ($project->slug) {
 'local-ai-hub' => $lang->value('Zdrojová verze 6.1.0 vystavuje sedm MCP nástrojů. README popisuje preprocessing jako inventory → hash → code index → deterministické karty → Serena → lexical → případné RAG → file/module/project mapy → diagnostiku. Některé back-endy jsou volitelné a degradují samostatně; status complete proto dokládá průchod pipeline, ne správnost každého výsledku ani přítomnost modelu. Verze je release candidate, nikoli přijaté produkční vydání.', 'Source version 6.1.0 exposes seven MCP tools. Its README describes preprocessing as inventory → hashes → code index → deterministic cards → Serena → lexical search → optional RAG → file/module/project maps → diagnostics. Some backends are optional and degrade independently; a complete pipeline status does not prove each result correct or a model present. This version is a release candidate, not an accepted production release.'),
 'mutaui' => $lang->value('Synchronizační relace řeší obsah mezi koncovými body; forwarding slouží k předání síťového spojení. Rozhraní nabízí přehled, seskupení a diagnostiku, ale stav zůstává odvozený od Mutagenu. Vytvořený instalační balíček sám o sobě nedokazuje, že prošel nativní zkouškou na všech podporovaných systémech.', 'Synchronization sessions handle content between endpoints; forwarding carries network connections. The interface offers overview, grouping and diagnostics, while Mutagen remains the source of state. A built installer alone does not prove native acceptance on every supported system.'),
 'home-infrastructure' => $lang->value('Názorný příklad je DNS: domácí klient pošle dotaz přes router, který za běžných podmínek používá lokální filtr a resolver. Dokumentace popisuje záložní resolver při jejich nedostupnosti. Zálohovací úložiště a testovací server mají odlišný režim dostupnosti; mapa není živé měření.', 'DNS offers a concrete example: a home client sends a query through the router, which normally uses a local filter and resolver. Documentation describes a fallback resolver if they are unavailable. Backup storage and the test server have different availability patterns; the map is not live telemetry.'),
 'woodbound' => $lang->value('Návrhová dokumentace popisuje několik oblastí, průmyslové body a různé typy dopravních spojení. Záměr trasy ovšem ještě není důkazem její fyzické průchodnosti: kontrola musí vyhodnotit skutečně vytvořený terén a konkrétní kandidátní svět.', 'Design documents describe several regions, industrial locations and different transport connections. A route intent is not proof of physical traversability: a check must evaluate the terrain and the specific generated candidate.'),
 'scan2cad' => $lang->value('Při práci s fotografií dokumentace popisuje alespoň šest odpovídajících dvojic bodů a odhad polohy kamery. Před projekcí označených oblastí uživatel odhad potvrdí; výsledek rozlišuje pozorované a nejisté plochy a sám geometrii neopravuje.', 'For a photograph, the documentation describes at least six matching point pairs and a camera-pose estimate. The user confirms the estimate before projecting marked regions; the result distinguishes observed and uncertain areas and does not itself repair geometry.'),
 'tiskov' => $lang->value('K dispozici je jen portfolio záznam a návrhový tok. Repozitář, skutečná fronta, worker, slicer ani cenový výpočet nejsou ověřené; interakce používá výhradně smyšlené položky.', 'Only a portfolio record and proposed flow are available. No repository, real queue, worker, slicer or pricing calculation is verified; the interaction uses synthetic items only.'),
 'shpotify' => $lang->value('Ve zdrojovém popisu jsou uvedené osobní, dočasné a trvalé místnosti, práce s frontou a doporučování. Zároveň výslovně odlišuje tyto moduly od rozpracovaného přehrávání v prohlížeči a vzdáleného audio agenta. Příklad místnosti proto neznamená ověřený přenos zvuku.', 'The source description lists personal, temporary and persistent rooms, queue operations and recommendations. It explicitly distinguishes these modules from browser playback and the remote audio agent, which remain in progress. The room example therefore does not establish verified audio transmission.'),
 'softwareinc-multiplayer' => $lang->value('Projekt obsahuje také práci na perzistenci, zabezpečení a rozhraní. Rozdíl mezi testem samostatného jádra a ověřením ve hře je zásadní: teprve běh konkrétního kandidáta může ukázat, zda herní adaptér správně spolupracuje se skutečnou verzí hry.', 'The project also contains persistence, security and UI work. A core test differs from verification in the game: only running the specific candidate can show whether the adapter works with the actual game version.'),
 'discord-bot' => $lang->value('Katalog uvádí příkazy a oddělenou údržbovou větev; checkout není dostupný a přesná implementace se nedá ověřit. Dřívější hlášení syntaktické chyby je historická poznámka, nikoli aktuální kontrola. Žádný živý běh není doložen.', 'The catalog mentions commands and a separate maintenance path; the checkout is unavailable, so implementation details cannot be verified. The earlier syntax-error report is historical, not a current check. No live run is evidenced.'),
 default => '',
};
?>
<section class="section shell project-deepdive" data-project-deepdive aria-labelledby="project-deepdive-title">
 <div class="section-heading"><div><span class="eyebrow"><?= $lang->value('PODROBNĚJI', 'IN DEPTH') ?></span><h2 id="project-deepdive-title"><?= Html::escape($copy[0]) ?></h2></div></div>
 <div class="project-deepdive-copy"><p><?= Html::escape($copy[1]) ?></p><p><?= Html::escape($copy[2]) ?></p><p><?= Html::escape($more) ?></p></div>
 <section class="case-walkthrough" aria-labelledby="walkthrough-title">
  <div class="case-walkthrough-heading"><div><span class="eyebrow"><?= $lang->value('JEDEN KONKRÉTNÍ PRŮCHOD', 'ONE CONCRETE TRACE') ?></span><h3 id="walkthrough-title"><?= Html::escape($walkthroughTitle) ?></h3></div><span class="demo-stamp"><?= $lang->value('ILUSTRATIVNÍ PRŮCHOD', 'ILLUSTRATIVE TRACE') ?></span></div>
  <ol class="case-walkthrough-steps"><?php foreach ($walkthrough as [$number,$titleCs,$titleEn,$bodyCs,$bodyEn]): ?><li><span><?= Html::escape($number) ?></span><div><h4><?= Html::escape($lang->value($titleCs,$titleEn)) ?></h4><p><?= Html::escape($lang->value($bodyCs,$bodyEn)) ?></p></div></li><?php endforeach; ?></ol>
 </section>
 <figure class="architecture-figure" data-project-architecture>
  <figcaption><strong><?= Html::escape($architectureTitle) ?></strong><span><?= $lang->value('Šipky ukazují návaznosti popsané v podkladech; diagram není snímek ani živý stav.', 'Arrows show relationships described in the source material; this is not a screenshot or live status.') ?></span></figcaption>
  <div class="architecture-canvas" data-architecture-canvas role="region" tabindex="0" aria-label="<?= $lang->value('Diagram lze na úzké obrazovce posouvat vodorovně', 'Scroll the diagram horizontally on a narrow screen') ?>">
   <svg viewBox="0 0 <?= $architectureWidth ?> <?= $architectureHeight ?>" role="img" aria-label="<?= $lang->value('Komponenty projektu a doložené návaznosti; popisy jsou v seznamu pod schématem', 'Project components and documented relationships; descriptions follow below') ?>" xmlns="http://www.w3.org/2000/svg">
    <defs><marker id="architecture-arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M 0 0 L 10 5 L 0 10 z" fill="#49665f" /></marker></defs>
    <?php foreach ($architectureEdges as [$from, $to]): $sourceX = 20 + ($from % $architectureColumns) * 310; $sourceY = 24 + intdiv($from, $architectureColumns) * 142; $targetX = 20 + ($to % $architectureColumns) * 310; $targetY = 24 + intdiv($to, $architectureColumns) * 142; ?>
    <?php $dx = ($targetX + 140) - ($sourceX + 140); $dy = ($targetY + 48) - ($sourceY + 48); $ratio = min(140 / max(1, abs($dx)), 48 / max(1, abs($dy))); $x1 = $sourceX + 140 + $dx * $ratio; $y1 = $sourceY + 48 + $dy * $ratio; $x2 = $targetX + 140 - $dx * $ratio; $y2 = $targetY + 48 - $dy * $ratio; ?>
    <path data-architecture-edge d="M <?= $x1 ?> <?= $y1 ?> L <?= $x2 ?> <?= $y2 ?>" fill="none" stroke="#49665f" stroke-width="3" marker-end="url(#architecture-arrow)" />
    <?php endforeach; ?>
    <?php foreach ($architectureNodes as $i => $node): $x = 20 + ($i % $architectureColumns) * 310; $y = 24 + intdiv($i, $architectureColumns) * 142; ?>
    <g data-architecture-node><rect x="<?= $x ?>" y="<?= $y ?>" width="280" height="96" rx="12" fill="#142f38" stroke="#3cdeb3" stroke-width="2" /><text x="<?= $x + 140 ?>" y="<?= $y + 40 ?>" text-anchor="middle" fill="#eff8f8" font-size="17" font-family="sans-serif"><?= Html::escape($lang->value($node[0], $node[1])) ?></text><text x="<?= $x + 140 ?>" y="<?= $y + 67 ?>" text-anchor="middle" fill="#b8d7d0" font-size="12" font-family="sans-serif"><?= Html::escape($lang->value($node[2], $node[3])) ?></text></g>
    <?php endforeach; ?>
   </svg>
  </div>
  <ol><?php foreach ($architectureNodes as $node): ?><li><strong><?= Html::escape($lang->value($node[0], $node[1])) ?></strong><span><?= Html::escape($lang->value($node[2], $node[3])) ?></span></li><?php endforeach; ?></ol>
 </figure>
</section>
<?php endif; ?>
