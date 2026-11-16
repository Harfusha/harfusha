<?php
declare(strict_types=1);

use App\Support\Html;

$detail = match ($project->slug) {
    'local-ai-hub' => [
        [$lang->value('Lokální vrstva pro agenty', 'A local layer for agents'),
         $lang->value('Hub sjednocuje nástroje pro hledání v repozitáři, práci s artefakty a koordinaci úloh. Přesná fakta z indexu a zdrojových souborů mají přednost před odpovědí modelu; ta je pomocným návrhem, který potřebuje kontrolu.', 'The Hub brings repository search, artifact access and task coordination together. Indexed facts and source files take precedence over model output, which remains an advisory draft requiring review.'),
         $lang->value('Například při opravě importu může agent nejprve najít související službu, prohlédnout volající a změnu ověřit testem. Dostupnost modelů i nástrojů závisí na nastavení a aktuálním stavu běhu.', 'For example, when fixing an import, an agent can locate the relevant service, inspect callers and verify a change with a test. Model and tool availability depends on configuration and runtime state.')],
    ],
    'mutaui' => [
        [$lang->value('Rozhraní nad existujícím enginem', 'An interface over an existing engine'),
         $lang->value('MutaUI spravuje pohled na synchronizační a forwarding relace. Neobsahuje vlastní synchronizační algoritmus: komunikuje s odděleně nainstalovaným Mutagen CLI a jeho lokálním démonem.', 'MutaUI provides a view of synchronization and forwarding sessions. It does not implement its own sync algorithm: it communicates with a separately installed Mutagen CLI and its local daemon.'),
         $lang->value('Před prvním použitím dokumentace doporučuje dvě zkušební složky, kontrolu zvoleného režimu a náhledu operace. Obrázky aplikace pocházejí z offline dema se smyšlenými daty a vypnutými operacemi.', 'For first use, the documentation recommends two disposable folders and reviewing the selected mode and operation preview. Application screenshots come from an offline demo with synthetic data and disabled operations.')],
    ],
    'home-infrastructure' => [
        [$lang->value('Konfigurace a služby', 'Configuration and services'),
         $lang->value('Git uchovává požadovaný stav a jeho historii. Semaphore slouží ke spouštění úloh, zatímco Ansible provádí změny na cílových zařízeních; správa routeru je oddělená od běžné údržby hlavního serveru.', 'Git stores desired state and its history. Semaphore runs jobs while Ansible applies changes to target devices; router management is separate from routine maintenance of the primary server.'),
         $lang->value('Hlavní Raspberry Pi hostí mimo jiné DNS, mediální knihovnu a dokumentaci. NAS slouží jako zálohovací cíl a testovací server se používá podle potřeby. Diagram ukazuje role, nikoliv aktuální dostupnost.', 'The primary Raspberry Pi hosts DNS, a media library and documentation among other services. A NAS is a backup destination and a test server is used as needed. The diagram shows roles, not live availability.')],
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
         $lang->value('Symfony aplikace přijímá podklady, ukládá soubory a metadata a zapisuje úlohy do MySQL. Slicování provádí samostatný worker na Raspberry Pi; webová část nespouští PrusaSlicer přímo.', 'The Symfony application accepts submissions, stores files and metadata, and queues jobs in MySQL. A separate Raspberry Pi worker performs slicing; the web application does not run PrusaSlicer directly.'),
         $lang->value('Projekt je určen pro odhady tisku na Bambu Lab A1. Podle dokumentace nejde o automatickou platbu ani konečnou nabídku: cena je orientační a finální dohoda probíhá ručně. Přesnost konkrétního výpočtu jsme neověřovali.', 'The project targets Bambu Lab A1 print estimates. According to its documentation, there is no automatic payment or final offer: the price is indicative and the final agreement is manual. We have not verified the accuracy of a particular calculation.')],
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
         $lang->value('Dostupný zdroj obsahuje klienta pro Discord, registraci příkazů a modul údržby s kontrolou oprávněných uživatelů. Samostatný soubor nápovědy je zatím prázdný.', 'The available source includes a Discord client, command registration and a maintenance module with an authorized-user check. The separate help file is currently empty.'),
         $lang->value('Modul údržby má v aktuálně dostupném zdroji syntaktickou chybu, a proto nelze tvrdit, že daný checkout funguje. Příkladem zamýšlené činnosti je požadavek správce na restart služby.', 'The available maintenance module has a syntax error, so this checkout cannot be presented as working. One intended operation is an administrator’s request to restart the service.')],
    ],
    default => null,
};
$architecture = match ($project->slug) {
    'local-ai-hub' => [[
        ['Agent','Agent','Zadání úlohy','Task request'], ['MCP','MCP','Veřejné nástroje','Public tools'],
        ['Index','Repository index','Kód a vztahy','Code and links'], ['Model','Optional model','Pomocný návrh','Advisory draft'],
        ['Artefakty','Artifacts','Zdroje a výstup','Sources and output'], ['Kontrola','Review','Testy a rozhodnutí','Tests and decision'],
    ], [[0,1],[1,2],[1,3],[2,4],[3,4],[4,5]]],
    'mutaui' => [[
        ['Uživatel','User','Volí relaci','Chooses session'], ['MutaUI','MutaUI','Přehled a diagnostika','Overview and diagnostics'],
        ['CLI','CLI','Odděleně instalovaný','Installed separately'], ['Démon','Daemon','Zdroj stavu','State source'],
        ['Synchronizace','Sync endpoints','Soubory mezi cestami','Files across paths'], ['Forwarding','Forwarding','Předání spojení','Connection forwarding'],
    ], [[0,1],[1,2],[2,3],[3,4],[3,5]]],
    'home-infrastructure' => [[
        ['Git','Git','Požadovaný stav','Desired state'], ['Semaphore','Semaphore','Spuštění úloh','Job execution'],
        ['Ansible','Ansible','Playbooky','Playbooks'], ['Router','Router','Oddělená správa','Separate management'],
        ['Raspberry Pi','Raspberry Pi','DNS, média, dokumentace','DNS, media, docs'], ['NAS','NAS','Cíl záloh','Backup target'],
        ['Testovací host','Test host','Použití podle potřeby','Used when needed'],
    ], [[0,1],[1,2],[2,3],[2,4],[2,5],[2,6]]],
    'woodbound' => [[
        ['Návrh světa','World design','Oblasti a záměry','Regions and intents'], ['Body zájmu','Locations','Průmyslová místa','Industrial sites'],
        ['Terén','Terrain','Tvar a souš','Shape and land'], ['Biomy','Biomes','Typy prostředí','Environment types'],
        ['Umístění','Placement','Prostorové podmínky','Spatial constraints'], ['Trasy','Routes','Koridory a spojení','Corridors and links'],
        ['Validace','Validation','Kandidát musí projít','Candidate must pass'],
    ], [[0,1],[0,2],[0,3],[1,4],[2,4],[3,4],[4,5],[5,6]]],
    'scan2cad' => [[
        ['Vstupní sken','Input scan','Síť nebo mračno bodů','Mesh or point cloud'], ['Měřítko','Scale','Potvrzený rozměr','Confirmed dimension'],
        ['Očištění','Cleanup','Kontrola geometrie','Geometry review'], ['Fotografie','Photos','Odpovídající body','Matching points'],
        ['Kamera','Camera','Odhad ke schválení','Estimate to confirm'], ['CAD kandidát','CAD candidate','Model pro kontrolu','Reviewable model'],
        ['Výstup','Export','STEP nebo DXF','STEP or DXF'],
    ], [[0,1],[1,2],[2,5],[3,4],[4,5],[5,6]]],
    'tiskov' => [[
        ['Poptávka','Request','Model a údaje','Model and details'], ['Symfony web','Symfony app','Příjem a metadata','Intake and metadata'],
        ['Soubory','Files','Uložené podklady','Stored input'], ['MySQL fronta','MySQL queue','Úloha ke zpracování','Queued job'],
        ['Worker + slicer','Worker + slicer','PrusaSlicer na Pi','PrusaSlicer on Pi'], ['Ruční dohoda','Manual agreement','Orientační odhad','Indicative estimate'],
    ], [[0,1],[1,2],[1,3],[3,4],[4,5]]],
    'shpotify' => [[
        ['Účet','Account','Osobní nastavení','Personal settings'], ['Místnost','Room','Osobní či sdílená','Personal or shared'],
        ['Fronta','Queue','Pořadí a ochrana duplicit','Order and deduplication'], ['Doporučení','Recommendations','Osobní a místnostní kontext','Personal and room context'],
        ['Přehrávač','Player','Integrace rozpracovaná','Integration in progress'], ['Audio agent','Audio agent','Vzdálená integrace otevřená','Remote integration pending'],
    ], [[0,1],[1,2],[2,3],[2,4],[4,5]]],
    'softwareinc-multiplayer' => [[
        ['Herní událost','Game event','Vstup adaptéru','Adapter input'], ['Game adapter','Game adapter','Napojení na hru','Game integration'],
        ['C# jádro','C# core','Pravidla spolupráce','Collaboration rules'], ['Persistence','Persistence','Uložení a zabezpečení','Storage and security'],
        ['Rozhraní','UI','Zobrazení hráči','Player view'], ['Testy jádra','Core tests','Bez spuštěné hry','Without running the game'],
        ['Běh ve hře','In-game run','Nutné ověřit zvlášť','Requires separate verification'],
    ], [[0,1],[1,2],[2,3],[2,4],[2,5],[1,6],[2,6]]],
    'discord-bot' => [[
        ['Interakce','Interaction','Požadavek na bota','Bot request'], ['Discord klient','Discord client','Strom příkazů','Command tree'],
        ['Synchronizace','Command sync','Testovací nebo globální','Test or global'], ['Kontrola oprávnění','Permission check','Správce před akcí','Operator before action'],
        ['Údržba','Maintenance','Zdroj má syntaktickou chybu','Source has syntax error'], ['Nápověda','Help','Soubor je zatím prázdný','File is currently empty'],
    ], [[0,1],[1,2],[1,3],[3,4],[1,5]]],
    default => null,
};
?>
 <?php if ($detail !== null): [$copy] = $detail; [$architectureNodes, $architectureEdges] = $architecture;
$more = match ($project->slug) {
 'local-ai-hub' => $lang->value('Sedm veřejných MCP nástrojů pokrývá stav, práci s repozitářem, lokální úlohy, vyhledávání, koordinaci, pracovní předání a artefakty. Výsledky mohou nést identitu zdroje a důkazy; samotná konfigurace ale nedokazuje kvalitu odpovědi, dostupnost modelu ani úsporu tokenů.', 'Seven public MCP tools cover status, repository work, local tasks, retrieval, coordination, work handoffs and artifacts. Results can carry source identity and evidence; configuration alone does not prove answer quality, model availability or token savings.'),
 'mutaui' => $lang->value('Synchronizační relace řeší obsah mezi koncovými body; forwarding slouží k předání síťového spojení. Rozhraní nabízí přehled, seskupení a diagnostiku, ale stav zůstává odvozený od Mutagenu. Vytvořený instalační balíček sám o sobě nedokazuje, že prošel nativní zkouškou na všech podporovaných systémech.', 'Synchronization sessions handle content between endpoints; forwarding carries network connections. The interface offers overview, grouping and diagnostics, while Mutagen remains the source of state. A built installer alone does not prove native acceptance on every supported system.'),
 'home-infrastructure' => $lang->value('Názorný příklad je DNS: domácí klient pošle dotaz přes router, který za běžných podmínek používá lokální filtr a resolver. Dokumentace popisuje záložní resolver při jejich nedostupnosti. Zálohovací úložiště a testovací server mají odlišný režim dostupnosti; mapa není živé měření.', 'DNS offers a concrete example: a home client sends a query through the router, which normally uses a local filter and resolver. Documentation describes a fallback resolver if they are unavailable. Backup storage and the test server have different availability patterns; the map is not live telemetry.'),
 'woodbound' => $lang->value('Návrhová dokumentace popisuje několik oblastí, průmyslové body a různé typy dopravních spojení. Záměr trasy ovšem ještě není důkazem její fyzické průchodnosti: kontrola musí vyhodnotit skutečně vytvořený terén a konkrétní kandidátní svět.', 'Design documents describe several regions, industrial locations and different transport connections. A route intent is not proof of physical traversability: a check must evaluate the terrain and the specific generated candidate.'),
 'scan2cad' => $lang->value('Při práci s fotografií dokumentace popisuje alespoň šest odpovídajících dvojic bodů a odhad polohy kamery. Před projekcí označených oblastí uživatel odhad potvrdí; výsledek rozlišuje pozorované a nejisté plochy a sám geometrii neopravuje.', 'For a photograph, the documentation describes at least six matching point pairs and a camera-pose estimate. The user confirms the estimate before projecting marked regions; the result distinguishes observed and uncertain areas and does not itself repair geometry.'),
 'tiskov' => $lang->value('Provozní dokumentace popisuje přehled fronty a odezvy workeru, diagnostiku dostupnosti i postup pro obnovení zaseknutých úloh. Přehled nezakládá důkaz aktuálního nasazení nebo spolehlivosti; výsledky konkrétního běhu je nutné ověřit zvlášť.', 'Operational documentation describes queue and worker-heartbeat views, health diagnostics and recovery for stuck jobs. The documentation does not establish current deployment or reliability; results of a specific run require separate verification.'),
 'shpotify' => $lang->value('Ve zdrojovém popisu jsou uvedené osobní, dočasné a trvalé místnosti, práce s frontou a doporučování. Zároveň výslovně odlišuje tyto moduly od rozpracovaného přehrávání v prohlížeči a vzdáleného audio agenta. Příklad místnosti proto neznamená ověřený přenos zvuku.', 'The source description lists personal, temporary and persistent rooms, queue operations and recommendations. It explicitly distinguishes these modules from browser playback and the remote audio agent, which remain in progress. The room example therefore does not establish verified audio transmission.'),
 'softwareinc-multiplayer' => $lang->value('Projekt obsahuje také práci na perzistenci, zabezpečení a rozhraní. Rozdíl mezi testem samostatného jádra a ověřením ve hře je zásadní: teprve běh konkrétního kandidáta může ukázat, zda herní adaptér správně spolupracuje se skutečnou verzí hry.', 'The project also contains persistence, security and UI work. A core test differs from verification in the game: only running the specific candidate can show whether the adapter works with the actual game version.'),
 'discord-bot' => $lang->value('Klient vytváří strom aplikačních příkazů a při spuštění je synchronizuje buď pro testovací server, nebo globálně podle nastavení. Dostupný registr připojuje modul údržby pro hlavní instanci. Tento popis vychází ze zdrojového kódu a není potvrzením živého provozu.', 'The client creates an application command tree and synchronizes it for a test server or globally depending on configuration. The available registry attaches the maintenance module for the main instance. This describes source code, not verified live operation.'),
 default => '',
};
?>
<section class="section shell project-deepdive" data-project-deepdive aria-labelledby="project-deepdive-title">
 <div class="section-heading"><div><span class="eyebrow"><?= $lang->value('PODROBNĚJI', 'IN DEPTH') ?></span><h2 id="project-deepdive-title"><?= Html::escape($copy[0]) ?></h2></div></div>
 <div class="project-deepdive-copy"><p><?= Html::escape($copy[1]) ?></p><p><?= Html::escape($copy[2]) ?></p><p><?= Html::escape($more) ?></p></div>
 <figure class="architecture-figure" data-project-architecture>
  <figcaption><strong><?= $lang->value('Mapa architektury', 'Architecture map') ?></strong><span><?= $lang->value('Šipky ukazují návaznosti popsané v podkladech; diagram není snímek ani živý stav.', 'Arrows show relationships described in the source material; this is not a screenshot or live status.') ?></span></figcaption>
  <div class="architecture-canvas" data-architecture-canvas role="region" tabindex="0" aria-label="<?= $lang->value('Diagram lze na úzké obrazovce posouvat vodorovně', 'Scroll the diagram horizontally on a narrow screen') ?>">
   <svg viewBox="0 0 960 440" role="img" aria-label="<?= $lang->value('Komponenty projektu a doložené návaznosti; popisy jsou v seznamu pod schématem', 'Project components and documented relationships; descriptions follow below') ?>" xmlns="http://www.w3.org/2000/svg">
    <defs><marker id="architecture-arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M 0 0 L 10 5 L 0 10 z" fill="#49665f" /></marker></defs>
    <?php foreach ($architectureEdges as [$from, $to]): $sourceX = 20 + ($from % 3) * 310; $sourceY = 24 + intdiv($from, 3) * 142; $targetX = 20 + ($to % 3) * 310; $targetY = 24 + intdiv($to, 3) * 142; ?>
    <?php if (intdiv($to, 3) === intdiv($from, 3) && $to - $from > 1): ?>
    <path data-architecture-edge d="M <?= $sourceX + 140 ?> <?= $sourceY + 96 ?> V <?= $sourceY + 128 ?> H <?= $targetX + 140 ?> V <?= $targetY + 96 ?>" fill="none" stroke="#49665f" stroke-width="3" marker-end="url(#architecture-arrow)" />
    <?php elseif (intdiv($to, 3) > intdiv($from, 3) && ($to % 3) < ($from % 3)): ?>
    <path data-architecture-edge d="M <?= $sourceX + 280 ?> <?= $sourceY + 48 ?> H 940 V <?= $targetY - 16 ?> H <?= $targetX + 140 ?> V <?= $targetY ?>" fill="none" stroke="#49665f" stroke-width="3" marker-end="url(#architecture-arrow)" />
    <?php else: $dx = ($targetX + 140) - ($sourceX + 140); $dy = ($targetY + 48) - ($sourceY + 48); $ratio = min(140 / max(1, abs($dx)), 48 / max(1, abs($dy))); $x1 = $sourceX + 140 + $dx * $ratio; $y1 = $sourceY + 48 + $dy * $ratio; $x2 = $targetX + 140 - $dx * $ratio; $y2 = $targetY + 48 - $dy * $ratio; ?>
    <path data-architecture-edge d="M <?= $x1 ?> <?= $y1 ?> L <?= $x2 ?> <?= $y2 ?>" fill="none" stroke="#49665f" stroke-width="3" marker-end="url(#architecture-arrow)" />
    <?php endif; ?>
    <?php endforeach; ?>
    <?php foreach ($architectureNodes as $i => $node): $x = 20 + ($i % 3) * 310; $y = 24 + intdiv($i, 3) * 142; ?>
    <g data-architecture-node><rect x="<?= $x ?>" y="<?= $y ?>" width="280" height="96" rx="12" fill="#142f38" stroke="#3cdeb3" stroke-width="2" /><text x="<?= $x + 140 ?>" y="<?= $y + 40 ?>" text-anchor="middle" fill="#eff8f8" font-size="17" font-family="sans-serif"><?= Html::escape($lang->value($node[0], $node[1])) ?></text><text x="<?= $x + 140 ?>" y="<?= $y + 67 ?>" text-anchor="middle" fill="#b8d7d0" font-size="12" font-family="sans-serif"><?= Html::escape($lang->value($node[2], $node[3])) ?></text></g>
    <?php endforeach; ?>
   </svg>
  </div>
  <ol><?php foreach ($architectureNodes as $node): ?><li><strong><?= Html::escape($lang->value($node[0], $node[1])) ?></strong><span><?= Html::escape($lang->value($node[2], $node[3])) ?></span></li><?php endforeach; ?></ol>
 </figure>
</section>
<?php endif; ?>
