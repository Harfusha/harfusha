<?php
declare(strict_types=1);

use App\Domain\Project;
use App\Support\Language;

/** @var Project $project */
/** @var Language $lang */
$caseReading = match ($project->slug) {
    'local-ai-hub' => [
        $lang->value('Dohledat kontext v repozitáři bez plýtvání tokeny', 'Retrieve repository context without wasting token budget'),
        $lang->value('Nejdřív deterministický index, potom širší kontext', 'Query the deterministic index first, expand context only when needed'),
        $lang->value('Architektura a veřejný kód', 'Architecture and public source'),
        $lang->value('Verze 6.1.0 je release candidate s deterministickým AST indexem, SQLite WAL koordinací a MCP rozhraním. Výkonnostní parametry závisí na hardwaru.', 'Version 6.1.0 is a release candidate featuring deterministic AST indexing, SQLite WAL coordination, and MCP interfaces. Benchmarks vary by host hardware.'),
    ],
    'mutaui' => [
        $lang->value('Zobrazit stav relací bez přepínání mezi příkazy', 'See session status without switching between commands'),
        $lang->value('MutaUI ovládá relace přes Mutagen CLI; samotný přenos zajišťuje Mutagen.', 'MutaUI manages sessions through the Mutagen CLI; Mutagen performs the transfer.'),
        $lang->value('Desktopové rozhraní a profily', 'Desktop interface and profiles'),
        $lang->value('PySide6 rozhraní pro správu synchronizačních profilů Mutagen CLI a diagnostiku démona na testovacích datech.', 'PySide6 interface for Mutagen CLI sync profile orchestration and daemon diagnostics with test fixtures.'),
    ],
    'home-infrastructure' => [
        $lang->value('Zjistit, které služby závisejí na jednom hostu', 'See which services depend on a single host'),
        $lang->value('Propojit zařízení, služby a vybrané datové cesty', 'Connect devices, services and selected data paths'),
        $lang->value('Topologický model domácí sítě', 'Home network topology model'),
        $lang->value('Deterministický model rolí uzlů, Ansible orchestrace a vzájemných závislostí služeb v domácí infrastruktuře.', 'Deterministic model of node roles, Ansible orchestration, and service dependencies across the home infrastructure.'),
    ],
    'woodbound' => [
        $lang->value('Udržet logistické trasy průjezdné i po generování světa', 'Keep logistics routes traversable after world generation'),
        $lang->value('Propojit generovací vrstvy s kontrolou jejich návaznosti', 'Connect world-generation layers and validate how they fit together'),
        $lang->value('Generátor a validace světa', 'Generator and world validation'),
        $lang->value('Prototyp v Unity spojující dvacet generovacích modulů s fázovými validátory terénu, vodotečí a průjezdnosti tras.', 'Unity prototype integrating twenty generation modules with phased validators for terrain, waterways, and traversable routes.'),
    ],
    'scan2cad' => [
        $lang->value('Vést rekonstrukci CAD modelu ze skenu', 'Guide scan-to-CAD reconstruction'),
        $lang->value('Vyžadovat známé měřítko a potvrzení před geometrickými kroky', 'Require a known scale and explicit review at geometry steps'),
        $lang->value('Algoritmický postup rekonstrukce', 'Reconstruction pipeline'),
        $lang->value('Postup rekonstrukce CAD těles z mesh dat s kontrolou měřítka a geometrických kroků před exportem do výrobních formátů.', 'CAD reconstruction pipeline from mesh data requiring scale verification and geometric review prior to manufacturing export.'),
    ],
    'tiskov' => [
        $lang->value('Přijmout poptávku, i když příprava tisku trvá', 'Accept a request while print preparation takes time'),
        $lang->value('Oddělit rychlé přijetí od pozdějšího zpracování', 'Separate request intake from later processing'),
        $lang->value('Návrhová architektura služby', 'Service architecture proposal'),
        $lang->value('Koncept tiskové služby s asynchronním oddělením příjmu požadavků od plánovaného zpracování tiskových úloh a modelů.', 'Print service concept decoupling asynchronous request intake from downstream job scheduling and 3D model processing.'),
    ],
    'shpotify' => [
        $lang->value('Zabránit starší změně v přepsání novější fronty', 'Prevent an older edit from overwriting a newer queue'),
        $lang->value('Kontrolovat verzi fronty a rozpoznat opakovaný požadavek', 'Check queue versions and recognise a retried request'),
        $lang->value('Správa stavu distribuované fronty', 'Distributed queue state management'),
        $lang->value('Prototyp koordinace sdílené fronty přehrávání s optimistickým zamykáním, verzováním stavu a idempotentními identifikátory požadavků.', 'Distributed queue coordination prototype with optimistic locking, state version checks, and idempotent request identifiers.'),
    ],
    'softwareinc-multiplayer' => [
        $lang->value('Odmítnout návrh bez oprávnění nebo aktuální verze', 'Reject proposals without authority or a current version'),
        $lang->value('Nechat hostitele ověřit roli, členství a verzi stavu', 'Have the host validate role, membership and state version'),
        $lang->value('Autoritativní síťový model', 'Authoritative networking model'),
        $lang->value('Prototyp autoritativního hostitelského modelu pro síťovou synchronizaci s doménovou validací rolí a verzováním herního stavu.', 'Authoritative host prototype for network synchronization with domain role validation and game-state version checks.'),
    ],
    'discord-bot' => [
        $lang->value('Oddělit provoz od údržby', 'Separate runtime from maintenance'),
        $lang->value('Spravovat službu přes Ansible a systemd', 'Manage the service through Ansible and systemd'),
        $lang->value('Provozní konfigurace a izolace', 'Operations setup and isolation'),
        $lang->value('Ansible spravuje samostatný checkout se systémovou službou v systemd a vyhrazeným neprivilegovaným účtem s omezenými administračními wrappery.', 'Ansible manages a separate checkout with a systemd service, a dedicated unprivileged account, and restricted maintenance wrappers.'),
    ],
    default => [
        $lang->value('Problém', 'Problem'),
        $lang->value('Přístup', 'Approach'),
        $lang->value('Dostupné podklady', 'Available evidence'),
        $project->summary($lang),
    ],
};
