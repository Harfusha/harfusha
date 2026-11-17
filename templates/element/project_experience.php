<?php
declare(strict_types=1);

use App\Support\Html;

$stories = [
    'local-ai-hub' => [
        'claim' => $lang->value('Agent nemá při každém dotazu začínat od nuly.', 'An agent should not have to start from zero for every question.'),
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
        'claim' => $lang->value('Mutagen už dělá těžkou práci. MutaUI mu dává ovládací panel.', 'Mutagen does the hard work. MutaUI gives it a control surface.'),
        'why' => $lang->value('Mutagen už synchronizuje, takže vlastní sync engine by jen dělal stejnou práci podruhé. MutaUI chce dát relace, diagnostiku a nastavení do jednoho okna a předem ukázat, co operace změní. Dokumentace doporučuje začít na dvou testovacích složkách; screenshoty jsou z offline dema a nic nespouštějí. Co mě přivedlo k úplně prvnímu commitu, zdrojový kód neříká.', 'Mutagen already handles synchronization, so a second sync engine would repeat its work. MutaUI puts sessions, diagnostics and settings in one window and previews what an operation will change. The docs recommend starting with two disposable folders; the screenshots come from an offline demo and execute nothing. The source does not say what triggered the very first commit.'),
        'alternatives' => [
            ['Mutagen CLI', 'https://mutagen.io/documentation/introduction/getting-started/', $lang->value('Nejkratší cesta, pokud mi vyhovují příkazy a výstupy v terminálu. MutaUI bez něj nefunguje.', 'The simplest route when terminal commands and output are enough. MutaUI still depends on it.')],
            ['Jiný Mutagen GUI klient', 'https://snapcraft.io/install/mutagen-sync-manager/kde-neon', $lang->value('Existují i další grafická rozhraní. Před volbou je potřeba porovnat platformu, údržbu a skutečné pokrytí funkcí.', 'Other graphical clients exist. Compare platform support, maintenance and actual feature coverage before choosing.')],
        ],
        'fit' => $lang->value('Jestli mi stačí Mutagen CLI nebo jiné udržované rozhraní, MutaUI nepotřebuji. Dává smysl jen tehdy, když mi náhled a diagnostika ušetří chyby, které se v terminálu snadno přehlédnou. Offline demo zatím nedokazuje, že reálné příkazy fungují.', 'If Mutagen CLI or another maintained interface is enough, I do not need MutaUI. It earns its place only if its previews and diagnostics prevent mistakes that are easy to miss in a terminal. The offline demo does not yet prove that real commands work.'),
        'source' => 'MutaUI README and setup docs; first source commit 9d50bfe',
        'destination' => '/playground#station-sync-preview',
        'interaction' => $lang->value('Prohlédni si náhled synchronizační operace.', 'Inspect a sync operation preview.'),
    ],
    'home-infrastructure' => [
        'claim' => $lang->value('Není to nový produkt. Je to popsaná konfigurace konkrétní domácnosti.', 'This is not a new product. It is configuration for one specific home.'),
        'why' => $lang->value('Tady nejde o jeden server. Router, hlavní Raspberry Pi, tiskové Pi, NAS a testovací Proxmox mají každý jiný úkol i režim provozu. První commit nevysvětluje, proč jsem s tím začal; pozdější audity ale ukazují, co mi ten popis dnes dává: v Gitu vidím, co patří na který host a co obnovit po výpadku. Mapa je dokumentace, ne živý monitoring.', 'This is not one server. The router, main Raspberry Pi, print Pi, NAS and test Proxmox each have a different job and operating pattern. The first commit does not explain why I started; later audits do show what the documentation gives me today: Git records what belongs on each host and what to restore after an outage. The map is documentation, not live monitoring.'),
        'alternatives' => [
            ['Ruční Ansible CLI', 'https://docs.ansible.com/ansible/latest/cli/ansible-playbook.html', $lang->value('Méně běžící infrastruktury; dobrá volba pro pár občasných změn.', 'Less infrastructure to maintain; a sensible choice for occasional changes.')],
            ['AWX', 'https://github.com/ansible/awx', $lang->value('Širší automatizační rozhraní; jeho provozní režii pro tuto síť jsem neměřil.', 'A broader automation interface; I have not measured its operating overhead for this network.')],
            ['Cockpit', 'https://cockpit-project.org/', $lang->value('Webová správa hostu a systemd služeb, ne náhrada za tuto sadu Ansible rolí.', 'Web-based host and systemd management, not a replacement for these Ansible roles.')],
        ],
        'fit' => $lang->value('Na pár občasných změn by mi stačilo Ansible CLI. Semaphore spouští stejné role, jen z přehledného místa; není to jiný konfigurační engine. Pro větší tým může dávat smysl AWX, ale nemám měření, které by dokazovalo, že se jeho provozní režie této domácí síti vyplatí.', 'For a few occasional changes, Ansible CLI would be enough. Semaphore runs the same roles from a convenient place; it is not another configuration engine. AWX may suit a larger team, but I have no measurements showing its operating overhead would pay off for this home network.'),
        'source' => 'README and four infrastructure audits, 16 Aug 2026; first commit c38db595',
        'destination' => '/homelab',
        'interaction' => $lang->value('Zvol hosta. Prohlédni jeho zdokumentované služby.', 'Choose a host. Inspect its documented services.'),
    ],
    'woodbound' => [
        'claim' => $lang->value('Les není dekorace; musí navazovat na vodu, terén i logistiku.', 'A forest is more than decoration; it must fit water, terrain and logistics.'),
        'why' => $lang->value('Pěkný terén sám o sobě nestačí. Podle návrhu má hráč projít od ruční práce v lese přes výrobu až k přepravě dřeva; proto se musí potkat reliéf, voda, regiony, průmyslová místa i průchozí trasy. Právě to propojení pravidel je důvod pro vlastní generovací pipeline. Je to cíl návrhu, ne důkaz, že už vznikl přijatý hratelný svět.', 'Attractive terrain is not enough. The design takes the player from manual forestry through production to timber transport, so terrain, water, regions, industrial sites and traversable routes have to fit together. Connecting those rules is the reason for a custom generation pipeline. That is the design goal, not proof of an accepted playable world.'),
        'alternatives' => [
            ['Unity Terrain Tools', 'https://docs.unity.cn/6000.0/Documentation/Manual/terrain-Tools.html', $lang->value('Přímo v Unity: ruční práce s terénem a jeho nástroji. Vhodná pro autorsky řízené prostředí.', 'Built into Unity: author terrain with its native tools. A fit for hand-directed environments.')],
            ['MapMagic 2', 'https://assetstore.unity.com/packages/tools/terrain/mapmagic-2-165180', $lang->value('Hotový uzlový generátor terénu a map. Je třeba ověřit, zda pokryje i pravidla a validace konkrétní hry.', 'An existing node-based terrain and map generator. Check whether it also covers this game’s specific rules and validation.')],
        ],
        'fit' => $lang->value('Kdyby Unity Terrain Tools nebo MapMagic pokryly i pravidla hry, trasy a jejich validaci, použil bych je. Dvacet vlastních modulů samo o sobě nic nedokazuje; musí spolu vytvořit průchozí svět. To zatím přijatelný herní běh nepotvrdil.', 'If Unity Terrain Tools or MapMagic also covered game rules, routes and their validation, I would use them. Twenty custom modules prove nothing by themselves; they have to produce a traversable world together. An accepted gameplay run has not confirmed that yet.'),
        'source' => 'Game vision, design pillars, world-generation docs; source revision 72c22cd',
        'interaction' => $lang->value('Změň vrstvu. Odhal pravidlo, které musí navazovat.', 'Change a layer. Reveal the rule that must follow.'),
    ],
    'scan2cad' => [
        'claim' => $lang->value('Tady už jsou dospělé nástroje. Vlastní prototyp musí přiznat, co ještě neumí.', 'Mature tools already exist. A custom prototype must say what it still cannot do.'),
        'why' => $lang->value('U skenu se dá snadno splést hezký tvar se správně změřeným modelem. Proto návrh nezačíná slibem automatického převodu: pracuje s lokálním PLY, OBJ nebo STL, potvrzením jednotek, referenčními fotkami a viditelnou nejistotou. Člověk má kandidát upravit a rozměry ověřit dřív, než ho použije v CADu. Přesnost se pořád musí měřit fyzicky.', 'A convincing shape can still be the wrong size. That is why the design does not promise an automatic conversion: it works with local PLY, OBJ or STL files, confirmed units, reference photos and visible uncertainty. A person should correct the candidate and verify its dimensions before using it in CAD. Accuracy still needs physical measurement.'),
        'alternatives' => [
            ['Autodesk ReCap Pro', 'https://help.autodesk.com/cloudhelp/ENU/Reality-Capture/files/Product_Overview/recap_product_tiers.html', $lang->value('Hotové nástroje pro import, čištění a zarovnání mračen bodů. Pro zavedený průmyslový proces může být správná odpověď koupit hotový produkt.', 'Established point-cloud import, cleanup and alignment tools. For an industrial workflow, buying a mature product may be the right answer.')],
            ['Geomagic Design X', 'https://support.geomagic.com/s/article/Using-Design-X-With-Scanners?language=en_US', $lang->value('Specializovaná reverzní konstrukce se skenery. Tento beta tok není náhradou metrologického řešení.', 'Specialized reverse engineering with scanners. This beta workflow is not a metrology replacement.')],
        ],
        'fit' => $lang->value('Pokud mi ReCap nebo Design X vyřeší práci i rozpočet, vzal bych hotový nástroj. Scan2CAD jim nemá konkurovat v průmyslové přesnosti. Vlastní tok má smysl jen pro konkrétní práci se skenem a fotkami, kde je nejistota vidět a člověk ji může opravit.', 'If ReCap or Design X meets the need and budget, I would use the established tool. Scan2CAD is not meant to compete on industrial accuracy. A custom workflow makes sense only for a specific scan-and-photo job where uncertainty is visible and a person can correct it.'),
        'source' => 'Committed scan-to-CAD design specification, 4 Nov 2026; repository revision bbe85d34b7',
        'destination' => '/playground#station-scale-check',
        'interaction' => $lang->value('Ověř měřítko na skutečném výpočtu.', 'Check scale with an actual calculation.'),
    ],
    'tiskov' => [
        'claim' => $lang->value('Pro kalkulaci tisku už existují služby. Tiskov míří na konkrétní místní postup.', 'Print quoting services already exist. Tiskov describes a specific local workflow.'),
        'why' => $lang->value('U Tiskova nemám zdrojový repozitář, jen návrh postupu. Ten posílá poptávku z webu do fronty v MySQL, dál na slicer na Raspberry Pi a nakonec k člověku, který potvrdí cenu. Původní důvod vzniku dostupné podklady neříkají, takže tu nebudu předstírat hotovou službu ani vymyšlenou vzpomínku.', 'I do not have Tiskov\'s source repository, only a proposed workflow. A web request goes to a MySQL queue, then to a slicer on Raspberry Pi, and finally to a person who confirms the price. The available material does not say why the project began, so I will not present a finished service or invent a memory.'),
        'alternatives' => [
            ['Craftcloud', 'https://craftcloud3d.com/en/upload', $lang->value('Nahraješ model, zvolíš technologii a porovnáš nabídky tiskových partnerů. Pro běžnou zakázku to může být dostatečné řešení.', 'Upload a model, select a process and compare printing-provider offers. That may be enough for an ordinary order.')],
        ],
        'fit' => $lang->value('Pro běžnou zakázku bych začal u Craftcloudu; pokud mi sedí jeho nabídky, vlastní aplikace nic nepřidá. Tiskov by měl důvod jen pro konkrétní místní tiskárnu a oddělené zpracování s lidskou kontrolou. Zatím nemám důkaz, že to tak v provozu funguje.', 'For an ordinary job, I would start with Craftcloud; if its offers fit, a custom app adds nothing. Tiskov would be justified only for a specific local printer and a separate workflow with human review. I have no evidence that this runs in production.'),
        'source' => 'Tiskov project record and decision notes; project source checkout not located',
        'interaction' => $lang->value('Tiskov zatím končí u návrhu toku.', 'Tiskov currently stops at a proposed flow.'),
    ],
    'shpotify' => [
        'claim' => $lang->value('Nejtěžší otázka: není vlastní aplikace zbytečná?', 'The hardest question: is another app unnecessary?'),
        'why' => $lang->value('V podkladech jsou místnosti a společná fronta, ale chybí původní rozhovor, který měl nápad vysvětlit. Nechci tedy tvrdit, že už řeší jasný problém: Spotify Jam nabízí společný poslech také. Otázka pro mě je, jestli existuje jiná potřeba než jen další tlačítko pro přidání skladby — a jestli jde přehrávání dovést od pozvánky až ke konci skladby.', 'The material shows rooms and a shared queue, but the original conversation behind the idea is missing. I do not want to claim it already solves a clear problem: Spotify Jam offers shared listening too. The question for me is whether there is a need beyond another add-track button, and whether playback can work from invitation through the end of a track.'),
        'alternatives' => [
            ['Spotify Jam', 'https://newsroom.spotify.com/2026-01-07/listening-activity-request-to-jam-messages-updates/', $lang->value('Už nabízí společnou frontu, sdílené poslechové sezení a návrhy podle vkusu skupiny. Pro většinu běžného poslechu může plně stačit.', 'Already offers a shared queue, joint listening and suggestions based on the group’s taste. It may fully meet ordinary listening needs.')],
        ],
        'fit' => $lang->value('Jestli Spotify Jam pokrývá to, co potřebujeme, není důvod stavět druhou hudební službu. Další fronta sama o sobě není výhoda. Než Shpotify označím za něco víc než prototyp, musí být jasný konkrétní rozdíl a fungovat přehrávání od začátku do konce.', 'If Spotify Jam covers the need, there is no reason to build another music service. A second queue is not an advantage by itself. Before I call Shpotify more than a prototype, it needs a concrete difference and end-to-end playback.'),
        'source' => 'Product design dated 13 Oct 2026 and first commit 03490878; playback acceptance not found',
        'interaction' => $lang->value('Přidávej do fronty. Zvuk zůstává mimo simulaci.', 'Add to the queue. Audio stays outside the simulation.'),
    ],
    'softwareinc-multiplayer' => [
        'claim' => $lang->value('Steam verze hry už má online kooperaci. Co má přidat další mod?', 'The Steam edition already has online co-op. What should another mod add?'),
        'why' => $lang->value('Tady je fér začít otázkou: proč další mod, když Steam verze hry už má online co-op? Podklady popisují sdílené projekty, hosting, distribuci a obchodní dohody. Jeden běh kandidáta po reloadu znovu našel testovací cíle; neověřil ale dohodu ve hře ani hotový multiplayer. Osobní důvod pro další mod se v dokumentaci nedochoval.', 'The fair starting question is: why another mod when the Steam edition already has online co-op? The docs describe shared projects, hosting, distribution and business agreements. One candidate run found its test targets after reload, but it did not verify an in-game agreement or finished multiplayer. The personal reason for another mod is not recorded.'),
        'alternatives' => [
            ['Software Inc. Multiplayer (SIMM)', 'https://github.com/software-inc-multiplayer/software-inc-multiplayer', $lang->value('Existující komunitní mod pro společný svět. Než stavět další, je třeba porovnat aktivitu, kompatibilitu a konkrétní chybějící funkci.', 'An existing community mod for a shared world. Compare maintenance, compatibility and the specific missing feature before building another.')],
            ['Software Inc. — nativní multiplayer', 'https://softwareinc.coredumping.com/', $lang->value('Steam edice nabízí online co-op i PvP. LAN-only omezení se týká DRM-free vydání; další mod musí obhájit konkrétní přínos nad nativní hrou.', 'The Steam edition offers online co-op and PvP. The LAN-only limitation applies to the DRM-free edition; another mod needs a concrete benefit beyond the native game.')],
            ['Steam Remote Play Together', 'https://store.steampowered.com/remoteplay', $lang->value('Sdílí obraz, zvuk a ovládání podporované lokální hry; nevytváří nový autoritativní multiplayerový datový model.', 'Streams a supported local game’s video, audio and input; it does not create a new authoritative multiplayer data model.')],
        ],
        'fit' => $lang->value('Nativní co-op už pokrývá společné hraní a SIMM má sdílený svět. Další mod potřebuje najít konkrétní pravidlo nebo dohodu, kterou tyto možnosti neumí. Dosavadní test to ještě neukázal.', 'Native co-op already covers playing together, and SIMM has a shared world. Another mod needs to identify a specific rule or agreement those options do not handle. The current test has not shown that yet.'),
        'source' => 'Project README and core/adapter docs; upstream game page and SIMM repository checked 16 Nov 2026',
        'interaction' => $lang->value('Co prokázal jediný běh kandidáta?', 'What did a single candidate run prove?'),
    ],
    'discord-bot' => [
        'claim' => $lang->value('Než přidám další bot příkaz, ověřím, že má uživatel opravdu důvod opustit SSH či správní rozhraní.', 'Before adding another bot command, check why an operator should leave SSH or an admin console.'),
        'why' => $lang->value('O Discord botu mám jen veřejný katalog: zmiňuje aplikační příkazy a oddělenou údržbovou větev. Zdrojový repozitář nemám, takže neznám přesné příkazy ani jejich oprávnění. Záznam o syntaktické chybě je stopa k ověření, ne důkaz dnešního chování.', 'I only have the public catalog for the Discord bot: it mentions application commands and a separate maintenance path. I do not have the source repository, so I cannot verify the exact commands or permissions. The syntax-error note is a lead to check, not proof of current behavior.'),
        'alternatives' => [
            ['SSH + systemd', 'https://www.freedesktop.org/software/systemd/man/latest/systemctl.html', $lang->value('Menší plocha útoku a přímé oprávnění správce. Pro vlastní údržbu bývá často lepší volba.', 'Smaller attack surface and direct operator permissions. Often the better choice for personal maintenance.')],
            ['Cockpit', 'https://cockpit-project.org/', $lang->value('Existující webové rozhraní umí práci se systemd službami a respektuje oprávnění hostu.', 'An existing web console can manage systemd services and follows host permissions.')],
            ['Discord application commands', 'https://docs.discord.com/developers/docs/interactions/slash-commands', $lang->value('Nativní vstupní bod pro příkaz neřeší bezpečnost ani správnou údržbu backendu za ním.', 'A native command entry point does not solve authorization or safe maintenance of the backend behind it.')],
        ],
        'fit' => $lang->value('Pro vlastní údržbu bývá SSH nebo Cockpit jednodušší a lépe ohraničená volba. Bot bych přidal jen tehdy, kdyby se opakovaný úkon opravdu hodil spouštět z Discordu, s úzkými právy a ověřeným backendem. Bez zdrojového kódu zatím nevím, jestli tu podmínku splňuje.', 'For personal maintenance, SSH or Cockpit is usually simpler and easier to constrain. I would add a bot only if a recurring task genuinely belongs in Discord, with narrow permissions and a verified backend. Without the source, I cannot tell whether it meets that bar.'),
        'source' => 'Curated portfolio catalog only; source repository and live runtime unavailable',
        'interaction' => $lang->value('Katalog je zatím jediná dostupná stopa.', 'The catalog is the only available trace for now.'),
    ],
];

$sectionLabels = [
    'local-ai-hub' => ['why' => ['Proč Hub nezačíná modelem', 'Why the Hub does not start with a model'], 'fit' => ['KDY HUB DÁVÁ SMYSL', 'WHEN THE HUB EARNS ITS PLACE'], 'alternatives' => ['CO UŽ ZVLÁDNOU EDITORY', 'WHAT EDITORS ALREADY HANDLE'], 'exhibit' => ['NEJDŘÍV ROZSAH, PAK ZDROJE', 'SCOPE FIRST, SOURCES SECOND']],
    'mutaui' => ['why' => ['Co Mutagen nechává na obsluze', 'What Mutagen leaves to the operator'], 'fit' => ['KDY STAČÍ MUTAGEN CLI', 'WHEN MUTAGEN CLI IS ENOUGH'], 'alternatives' => ['CO UŽ UMÍ SAMOTNÝ MUTAGEN', 'WHAT MUTAGEN ALREADY DOES'], 'exhibit' => ['JEDNA ZMĚNA, TŘI STAVY', 'ONE CHANGE, THREE STATES']],
    'home-infrastructure' => ['why' => ['Domácí služby potřebují provozní pravidla', 'Home services need operating rules'], 'fit' => ['KDY STAČÍ ANSIBLE', 'WHEN ANSIBLE IS ENOUGH'], 'alternatives' => ['CO ZVLÁDNE ANSIBLE', 'WHAT ANSIBLE CAN HANDLE'], 'exhibit' => ['KTERÁ SLUŽBA ZÁVISÍ NA HOSTU?', 'WHICH SERVICE DEPENDS ON THIS HOST?']],
    'woodbound' => ['why' => ['Dřevo má projít celým světem hry', 'Wood must travel through the game world'], 'fit' => ['KDY STAČÍ HOTOVÁ HRA', 'WHEN AN EXISTING GAME IS ENOUGH'], 'alternatives' => ['CO UŽ ŘEŠÍ FACTORY HRY', 'WHAT FACTORY GAMES ALREADY SOLVE'], 'exhibit' => ['SLEDUJ TOK MATERIÁLU', 'FOLLOW THE MATERIAL FLOW']],
    'scan2cad' => ['why' => ['Sken ještě nezná konstrukční záměr', 'A scan does not know design intent'], 'fit' => ['KDY STAČÍ BĚŽNÝ CAD', 'WHEN STANDARD CAD IS ENOUGH'], 'alternatives' => ['CO UŽ NABÍZÍ CAD NÁSTROJE', 'WHAT CAD TOOLS ALREADY OFFER'], 'exhibit' => ['KDE KONČÍ JISTOTA MĚŘENÍ', 'WHERE MEASUREMENT CONFIDENCE ENDS']],
    'tiskov' => ['why' => ['Tiskov je zatím návrh fronty', 'Tiskov is still a queue proposal'], 'fit' => ['KDY STAČÍ TISKOVÁ SLUŽBA', 'WHEN A PRINT SERVICE IS ENOUGH'], 'alternatives' => ['CO ZVLÁDNE HOTOVÁ SLUŽBA', 'WHAT A PRINT SERVICE CAN HANDLE'], 'exhibit' => ['NÁVRH PRŮCHODU ZAKÁZKY', 'PROPOSED JOB FLOW']],
    'shpotify' => ['why' => ['Společný poslech je víc než další fronta', 'Shared listening is more than another queue'], 'fit' => ['KDY STAČÍ SPOTIFY JAM', 'WHEN SPOTIFY JAM IS ENOUGH'], 'alternatives' => ['CO UŽ ŘEŠÍ JAM', 'WHAT JAM ALREADY SOLVES'], 'exhibit' => ['JAK SE MĚNÍ SPOLEČNÝ STAV', 'HOW SHARED STATE CHANGES']],
    'softwareinc-multiplayer' => ['why' => ['Mod musí vyjasnit dohodu hráčů', 'The mod must make player agreements explicit'], 'fit' => ['KDY STAČÍ HRA SAMA', 'WHEN THE GAME ITSELF IS ENOUGH'], 'alternatives' => ['CO UŽ MÁ NATIVNÍ CO-OP', 'WHAT NATIVE CO-OP ALREADY HAS'], 'exhibit' => ['KDO SMÍ POTVRDIT ZMĚNU?', 'WHO MAY CONFIRM A CHANGE?']],
    'discord-bot' => ['why' => ['Katalog není důkaz chování bota', 'A catalog is not proof of bot behavior'], 'fit' => ['KDY STAČÍ SPRÁVA SERVERU', 'WHEN SERVER MANAGEMENT IS ENOUGH'], 'alternatives' => ['CO ZVLÁDNE BĚŽNÝ PROVOZ SERVERU', 'WHAT NORMAL SERVER OPERATIONS HANDLE'], 'exhibit' => ['CO JE DOLOŽENO A CO CHYBÍ', 'WHAT IS DOCUMENTED AND WHAT IS MISSING']],
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
 <?php if (isset($playgroundLinks[$project->slug])): ?>
 <aside class="project-lab-link">
   <span class="eyebrow"><?= $project->slug === 'tiskov' ? $lang->value('NÁVRHOVÁ SIMULACE', 'PROPOSAL SIMULATION') : $lang->value('PŘÍMO K FUNKČNÍMU MODELU', 'OPEN THE WORKING MODEL') ?></span>
   <p><?= Html::escape($story['interaction']) ?></p>
   <a class="btn btn-primary btn-sm" href="<?= $lang->path($playgroundLinks[$project->slug]) ?>"><?= $project->slug === 'tiskov' ? $lang->value('Otevřít návrhovou simulaci', 'Open proposal simulation') : $lang->value('Otevřít model', 'Open model') ?> →</a>
 </aside>
 <?php endif; ?>
</section>
