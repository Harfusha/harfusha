<?php
declare(strict_types=1);

use App\Domain\Project;
use App\Support\Language;

/** @var Project $project */
/** @var Language $lang */
$caseReading = match ($project->slug) {
    'local-ai-hub' => [
        $lang->value('Opakované hledání v repozitáři', 'Searching the same repository repeatedly'),
        $lang->value('Od indexu ke konkrétnímu řádku', 'From the index to a specific line'),
        $lang->value('Výsledek hledání lze dohledat ve zdroji', 'Lookup results lead back to source'),
        $lang->value('Přesný dotaz vrací soubor a výřez kódu. Širší kontext může využít stejný index a připojit další podklady.', 'An exact lookup returns a file and a source excerpt. A broader search can reuse the index and bring in related source material.'),
        '/playground#station-preprocessing',
        $lang->value('Sledovat přepočet po změně souboru', 'Change a file and trace what gets recomputed'),
    ],
    'mutaui' => [
        $lang->value('Přehled nad více relacemi', 'Keeping track of several sessions'),
        $lang->value('Správa relací v Mutagenu', 'Managing sessions in Mutagen'),
        $lang->value('Před spuštěním je vidět směr změn', 'See the direction of changes before running'),
        $lang->value('Rozhraní ukáže stav relace a její nastavení. Samotnou operaci provádí samostatně nainstalovaný Mutagen.', 'The interface shows a session’s state and settings. A separate Mutagen installation carries out the operation.'),
        '/playground#station-sync-preview',
        $lang->value('Porovnat režimy synchronizace', 'Compare sync modes'),
    ],
    'home-infrastructure' => [
        $lang->value('Změny napříč zařízeními', 'Changes across several devices'),
        $lang->value('Konfigurace rolí a datových cest', 'Configuring roles and data paths'),
        $lang->value('Výpadek hostu zasáhne konkrétní služby', 'A host outage affects specific services'),
        $lang->value('Dokumentované role ukazují, které služby sdílejí zařízení a kde jsou uložená data. Výpadek lze prozkoumat v modelu sítě.', 'Documented roles show which services share a device and where the data is stored. The network model lets you explore an outage.'),
        '/homelab#network-model',
        $lang->value('Nasimulovat výpadek v síti', 'Simulate an outage in the network map'),
    ],
    'woodbound' => [
        $lang->value('Terén, který ovlivňuje průjezd', 'Terrain that affects travel'),
        $lang->value('Návaznost generování a hledání tras', 'Connecting generation and routing'),
        $lang->value('Cesta se kontroluje ve vygenerované krajině', 'Routes are checked against generated terrain'),
        $lang->value('Reliéf, voda a mokřady omezují, kam se vejde průmyslové místo a kudy může vést spojení. Ukázka izoluje hledání trasy.', 'Terrain, water and wetlands constrain where industry can fit and how routes can connect it. The demo isolates the routing problem.'),
        '/playground#station-route-finder',
        $lang->value('Přepočítat trasu kolem překážky', 'Block a passage and recalculate the route'),
    ],
    'scan2cad' => [
        $lang->value('Kdy má sken použitelné rozměry', 'When a scan has usable dimensions'),
        $lang->value('Rekonstrukce s fyzickou referencí', 'Reconstruction with a physical reference'),
        $lang->value('Měřítko musí mít oporu v měření', 'Scale needs a measured reference'),
        $lang->value('Fotografie pomáhají při rekonstrukci tvaru. Zadaná reference určuje měřítko a výsledný model čeká na kontrolu skutečných rozměrů.', 'Photos help reconstruct the shape. A measured reference sets the scale, and the resulting model needs a physical dimension check.'),
        '/maker',
        $lang->value('Projít cestu od skenu k tisku', 'Follow the path from scanning to print preparation'),
    ],
    'tiskov' => [
        $lang->value('Příjem poptávek během analýzy modelu', 'Accepting requests while models are analyzed'),
        $lang->value('Oddělené zpracování a ruční kontrola', 'Separate processing and manual review'),
        $lang->value('Poptávka má stav i během čekání', 'A request has a visible status while it waits'),
        $lang->value('Návrh odděluje příjem, výpočet a ruční posouzení. Ukázka znázorňuje přechody úloh ve frontě.', 'The proposal separates intake, analysis and manual review. The demo illustrates how jobs move through the queue.'),
        '/playground#station-tiskov-queue',
        $lang->value('Změnit pořadí ve frontě', 'Compare queue order and waiting times'),
    ],
    'shpotify' => [
        $lang->value('Úpravy jedné fronty více lidmi', 'Several people editing one queue'),
        $lang->value('Verze fronty a opakované požadavky', 'Queue versions and repeated requests'),
        $lang->value('Zastaralá změna nepřepíše novější frontu', 'An outdated edit cannot overwrite a newer queue'),
        $lang->value('Při zápisu se kontroluje verze a totožnost požadavku. Ukázka představuje souběh změn ve frontě.', 'Writes check the queue version and request identity. The demo shows what happens when edits overlap.'),
        '/playground#station-queue-conflict',
        $lang->value('Vyzkoušet střet dvou změn', 'Try two edits against the same version'),
    ],
    'softwareinc-multiplayer' => [
        $lang->value('Pravidla pro dohody hráčů', 'Rules for agreements between players'),
        $lang->value('Rozhraní mezi pravidly a hrou', 'The boundary between rules and the game'),
        $lang->value('Dohoda prochází kontrolou hosta', 'An agreement goes through host checks'),
        $lang->value('Model odděluje návrh hráče od rozhodnutí hosta. Přijetí celého toku ve skutečné hře zůstává otevřené.', 'The model separates a player proposal from the host’s decision. Acceptance of the full flow in the game remains open.'),
        '/playground#station-mod-authority',
        $lang->value('Ověřit, kdo smí potvrdit dohodu', 'Check who can approve a change'),
    ],
    'discord-bot' => [
        $lang->value('Oprávnění k údržbovým příkazům', 'Permissions for maintenance commands'),
        $lang->value('Co popisují dostupné podklady', 'What the available material documents'),
        $lang->value('Údržbový příkaz potřebuje jasné oprávnění', 'A maintenance command needs a clear permission rule'),
        $lang->value('Katalog dokládá oddělené aplikační příkazy a modul údržby. Přesné chování kontrol nelze bez zdrojového kódu ověřit.', 'The catalog records separate application commands and a maintenance module. The exact checks cannot be verified without the source.'),
        '/connections#map-project-discord-bot',
        $lang->value('Porovnat míru ověření projektů', 'Compare its verification status with other projects'),
    ],
    default => [
        $lang->value('Problém', 'Problem'),
        $lang->value('Zvolené řešení', 'Approach'),
        $lang->value('Přehled projektu', 'Project overview'),
        $project->summary($lang),
        '/projects',
        $lang->value('Projít další projekty', 'Browse other projects'),
    ],
};
