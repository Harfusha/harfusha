<?php
declare(strict_types=1);

use App\Domain\Project;
use App\Support\Language;

/** @var Project $project */
/** @var Language $lang */
$caseReading = match ($project->slug) {
    'local-ai-hub' => [
        $lang->value('Najít zdroj v repozitáři bez opakovaného hledání', 'Find source in a repository without searching it again'),
        $lang->value('Nejdřív index, potom širší kontext', 'Search the index first, add context only when needed'),
        $lang->value('Veřejný zdroj', 'Public source'),
        $lang->value('Verze 6.1.0 je release candidate. Zlepšení latence ani spotřeby tokenů nejsou změřená.', 'Version 6.1.0 is a release candidate. Latency and token-usage gains have not been measured.'),
    ],
    'mutaui' => [
        $lang->value('Zobrazit stav relací bez přepínání mezi příkazy', 'See session status without switching between commands'),
        $lang->value('MutaUI ovládá relace přes Mutagen CLI; samotný přenos zajišťuje Mutagen.', 'MutaUI manages sessions through the Mutagen CLI; Mutagen performs the transfer.'),
        $lang->value('Snímky z offline dema', 'Offline demo screenshots'),
        $lang->value('Snímky používají ukázková data a mají vypnuté akce. Nejde o běžící relace.', 'Screenshots use sample data with actions disabled. They are not live sessions.'),
    ],
    'home-infrastructure' => [
        $lang->value('Zjistit, které služby závisejí na jednom hostu', 'See which services depend on a single host'),
        $lang->value('Propojit zařízení, služby a vybrané datové cesty', 'Connect devices, services and selected data paths'),
        $lang->value('Model vybraných částí domácí sítě', 'Selected home-network model'),
        $lang->value('Mapa vychází z dokumentovaných rolí a vazeb. Nečte živý stav zařízení, síťový provoz ani stav záloh.', 'The map follows documented roles and dependencies. It reads no live device, network or backup status.'),
    ],
    'woodbound' => [
        $lang->value('Udržet logistické trasy průjezdné i po generování světa', 'Keep logistics routes traversable after world generation'),
        $lang->value('Propojit generovací vrstvy s kontrolou jejich návaznosti', 'Connect world-generation layers and validate how they fit together'),
        $lang->value('Rozsah prototypu', 'Prototype scope'),
        $lang->value('Podklady popisují generátor a validátory, které kontrolují návaznost vrstev.', 'Project materials describe a generator and validators for how its layers fit together.'),
    ],
    'scan2cad' => [
        $lang->value('Vést rekonstrukci CAD modelu ze skenu', 'Guide scan-to-CAD reconstruction'),
        $lang->value('Vyžadovat známé měřítko a potvrzení před geometrickými kroky', 'Require a known scale and explicit review at geometry steps'),
        $lang->value('Co zatím není ověřeno', 'What remains unverified'),
        $lang->value('Rozměrovou přesnost je třeba ověřit fyzickou referencí; vyrobitelnost odborně posoudit.', 'Dimensional accuracy needs checking against a physical reference; an engineer must assess manufacturability.'),
    ],
    'tiskov' => [
        $lang->value('Přijmout poptávku, i když příprava tisku trvá', 'Accept a request while print preparation takes time'),
        $lang->value('Oddělit rychlé přijetí od pozdějšího zpracování', 'Separate request intake from later processing'),
        $lang->value('Dokumentovaný návrh', 'Documented proposal'),
        $lang->value('Jde o návrh služby. Veřejné podklady nedokládají běžící aplikaci, samostatné zpracování modelů ani vyřízené objednávky.', 'This is a service proposal. Public materials do not document a running app, a model-processing worker or completed orders.'),
    ],
    'shpotify' => [
        $lang->value('Zabránit starší změně v přepsání novější fronty', 'Prevent an older edit from overwriting a newer queue'),
        $lang->value('Kontrolovat verzi fronty a rozpoznat opakovaný požadavek', 'Check queue versions and recognise a retried request'),
        $lang->value('Rozsah prototypu', 'Prototype scope'),
        $lang->value('Zdroj popisuje verzování fronty a ID požadavků. Přehrávání Spotify ani vzdálený zvuk zde doložené nejsou.', 'The source describes queue versioning and request IDs. Spotify playback and remote audio are not documented here.'),
    ],
    'softwareinc-multiplayer' => [
        $lang->value('Odmítnout návrh bez oprávnění nebo aktuální verze', 'Reject proposals without authority or a current version'),
        $lang->value('Nechat hostitele ověřit roli, členství a verzi stavu', 'Have the host validate role, membership and state version'),
        $lang->value('Rozsah prototypu', 'Prototype scope'),
        $lang->value('Doménové kontroly jsou popsané v prototypu. Přijetí módu hrou ani multiplayerový provoz ověřené nejsou.', 'The prototype describes domain checks. In-game acceptance and multiplayer operation have not been verified.'),
    ],
    'discord-bot' => [
        $lang->value('Oddělit provoz od údržby', 'Separate runtime from maintenance'),
        $lang->value('Spravovat službu přes Ansible a systemd', 'Manage the service through Ansible and systemd'),
        $lang->value('Ověřený provozní rozsah', 'Verified operations scope'),
        $lang->value('Podklady popisují samostatný checkout, neprivilegovaný účet a omezené administrační wrappery. Příkazy bota ani jeho aktuální běh ověřené nejsou.', 'The available record documents a separate checkout, an unprivileged account and restricted maintenance wrappers. Bot commands and current runtime have not been verified.'),
    ],
    default => [
        $lang->value('Problém', 'Problem'),
        $lang->value('Přístup', 'Approach'),
        $lang->value('Dostupné podklady', 'Available evidence'),
        $project->summary($lang),
    ],
};
