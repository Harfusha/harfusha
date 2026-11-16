<?php
declare(strict_types=1);

use App\Support\Html;

$example = match ($project->slug) {
    'local-ai-hub' => [
        $lang->value('Hledání příčiny chyby v repozitáři', 'Finding a repository bug'),
        $lang->value('Agent dostane hlášení o chybě importu. Hub mu pomůže dohledat související kód a podklady; agent pak může navrhnout opravu a spustit odpovídající test.', 'An agent receives a report of a failed import. The Hub helps locate relevant code and evidence; the agent can then propose a fix and run a relevant test.'),
        $lang->value('Výstup modelu je podklad k posouzení; změnu je nutné ověřit v kódu a testech.', 'Model output is advisory; the change must be checked against code and tests.'),
    ],
    'mutaui' => [
        $lang->value('Kontrola dvou složek před synchronizací', 'Checking two folders before synchronization'),
        $lang->value('Uživatel chce sladit pracovní složku s druhou kopií. V MutaUI může zkontrolovat nastavení relace a následně sledovat stav nebo nahlédnout do diagnostiky.', 'A user wants to synchronize a working folder with a second copy. MutaUI can display the session settings and then its status and diagnostics.'),
        $lang->value('Synchronizaci zajišťuje samostatně nainstalovaný Mutagen; chování na každé platformě vyžaduje ověření.', 'Synchronization is performed by separately installed Mutagen; behavior on each platform requires verification.'),
    ],
    'home-infrastructure' => [
        $lang->value('Údržba domácí služby', 'Maintaining a home service'),
        $lang->value('Změna konfigurace se zaznamená do Gitu. Úloha v Semaphore může spustit příslušný Ansible playbook a správce následně zkontroluje výsledek na zařízení.', 'A configuration change is recorded in Git. A Semaphore job can run the relevant Ansible playbook, after which the operator checks the result on the device.'),
        $lang->value('Veřejný popis je model podle dokumentace, nikoliv živé sledování sítě.', 'The public description is a documented model, not live network monitoring.'),
    ],
    'woodbound' => [
        $lang->value('Vznik průchodného světa', 'Creating a traversable world'),
        $lang->value('Terén, oblasti a cesty musí v generovaném světě dávat smysl společně. Kontroly mohou upozornit na trasu, která nedosáhne cíle, ještě před ručním hraním.', 'Terrain, regions and routes must work together in a generated world. Checks can flag a route that cannot reach its destination before manual playtesting.'),
        $lang->value('Jde o příklad práce s prototypem; přijatý hratelný svět zatím není doložen.', 'This illustrates a prototype; an accepted playable world has not yet been demonstrated.'),
    ],
    'scan2cad' => [
        $lang->value('Od skenu součástky k CAD návrhu', 'From a part scan to a CAD candidate'),
        $lang->value('Ze skenu součástky lze připravit návrh editovatelného modelu: zkontrolovat měřítko, očistit vstup, rekonstruovat geometrii a vyexportovat kandidáta pro další práci.', 'A part scan can be used to prepare an editable model candidate: check scale, clean the input, reconstruct geometry and export it for further work.'),
        $lang->value('Kandidát vyžaduje kontrolu; výpočetní porovnání nenahrazuje fyzické měření.', 'The candidate requires review; computational comparison does not replace physical measurement.'),
    ],
    'tiskov' => [
        $lang->value('Poptávka na 3D tisk', 'A 3D-print request'),
        $lang->value('Zákazník zadá podklady pro tisk a web je předá ke zpracování. Samostatný worker může náročnější úlohu řešit mimo webový požadavek, zatímco stav zůstává ve frontě.', 'A customer submits print request details and the website queues them. A separate worker can handle longer work outside the web request while the queue tracks its state.'),
        $lang->value('Je to zamýšlený postup prototypu, nikoliv příslib přesné ceny nebo hotové služby.', 'This is the prototype’s intended workflow, not a promise of an accurate quote or a finished service.'),
    ],
    'shpotify' => [
        $lang->value('Hudba pro společnou místnost', 'Music for a shared room'),
        $lang->value('Skupina má vlastní místnost, do které lidé přidávají skladby. Sdílená fronta a návrhy pomáhají udržet přehled o tom, co má přijít na řadu.', 'A group has a room where people add tracks. A shared queue and suggestions help everyone see what comes next.'),
        $lang->value('Přehrávání a integrace se vyvíjejí; příklad nepotvrzuje jejich kompletní provoz.', 'Playback and integrations remain in development; this example does not establish complete operational support.'),
    ],
    'softwareinc-multiplayer' => [
        $lang->value('Spolupráce na společném projektu', 'Collaborating on a shared project'),
        $lang->value('Dva hráči by mohli spolupracovat na společné práci ve hře. Pravidla žijí v odděleném jádru, zatímco adaptér má na starosti vazbu na prostředí hry.', 'Two players could collaborate on shared work in the game. Rules live in a separate core, while an adapter connects it to the game.'),
        $lang->value('Jde o zamýšlený scénář; plně hratelné vydání zatím není ověřené.', 'This is an intended scenario; a fully playable release has not yet been verified.'),
    ],
    'discord-bot' => [
        $lang->value('Údržba služby přes příkaz', 'Service maintenance by command'),
        $lang->value('Zamýšlený postup je, že oprávněný správce zadá příkaz údržby a bot předá požadavek odpovídajícímu modulu.', 'The intended workflow is for an authorized administrator to issue a maintenance command which the bot passes to the relevant module.'),
        $lang->value('Aktuálně dostupný modul obsahuje syntaktickou chybu; funkční provoz není ověřen.', 'The currently available module contains a syntax error; working operation has not been verified.'),
    ],
    default => null,
};
?>
<?php if ($example !== null): ?>
<section class="section shell" data-project-example aria-labelledby="project-example-title">
 <div class="notice note-info"><span class="eyebrow"><?= $lang->value('ILUSTRATIVNÍ PŘÍKLAD', 'ILLUSTRATIVE EXAMPLE') ?></span><h2 id="project-example-title"><?= Html::escape($example[0]) ?></h2><p><?= Html::escape($example[1]) ?></p><p><strong><?= $lang->value('Co je potřeba vědět:', 'What to keep in mind:') ?></strong> <?= Html::escape($example[2]) ?></p></div>
</section>
<?php endif; ?>
