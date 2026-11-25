<?php
declare(strict_types=1);
use App\Support\Html;
use App\Support\Language;
/** @var Language $lang */
/** @var list<array<string,mixed>> $nodes */
/** @var list<App\Domain\NetworkFlow> $flows */
$localizedNodes = array_map(static function (array $node) use ($lang): array {
    foreach (['name', 'tech', 'services'] as $field) {
        if ($lang->code === 'cs') {
            $node[$field] = $node[$field . '_cs'];
        }
        unset($node[$field . '_cs']);
    }
    return $node;
}, $nodes);
?>
<section class="page-hero shell">
    <span class="eyebrow"><?= $lang->value('DOMÁCÍ SÍŤ · VEŘEJNÝ MODEL', 'HOME NETWORK · PUBLIC MODEL') ?></span>
    <h1><?= $lang->value('Co přestane fungovat, když vypadne jedno zařízení?', 'What stops when one device goes down?') ?></h1>
    <p><?= $lang->value('Model ukazuje, které služby závisejí na jednotlivých uzlech a kudy vedou vybrané datové cesty. Vychází z dokumentované topologie, ne z živého provozu.', 'The model shows which services depend on each node and how selected data paths connect. It follows documented topology, not live traffic.') ?></p>
</section>

<section class="shell section" aria-labelledby="homelab-topology-title">
    <div class="section-heading">
        <div>
            <span class="eyebrow"><?= $lang->value('SEDM UZLŮ · DOKUMENTOVANÉ VAZBY', 'SEVEN NODES · DOCUMENTED DEPENDENCIES') ?></span>
            <h2 id="homelab-topology-title"><?= $lang->value('Mapa závislostí služeb', 'Service dependency map') ?></h2>
            <p><?= $lang->value('Postup simulace: 1. Zvol pohled (služby, hardware, závislosti nebo cesty). 2. Zaškrtnutím „Vyber uzel pro simulaci výpadku“ aktivuj režim poruch a kliknutím vypni uzel (např. NAS). 3. V detailu vpravo ihned uvidíš, které navazující služby ztratily spojení. Tlačítko Reset vrátí celou síť do výchozího online stavu.', 'Simulation guide: 1. Choose a view (services, hardware, dependencies, or data paths). 2. Check "Select a node to simulate an outage" and click any node (e.g. NAS) to take it offline. 3. The inspector immediately shows which downstream services lose connection. The Reset button restores the baseline online topology.') ?></p>
        </div>
    </div>
</section>

<section class="shell lab-section" id="network-model"><div class="lab-shell" data-homelab data-lang="<?= Html::escape($lang->code) ?>">
<div class="lab-scenarios requires-js" data-lab-scenarios hidden>
  <div>
    <h2><?= $lang->value('Zkus výpadek', 'Start with an outage') ?></h2>
    <p><?= $lang->value('Vyber scénář. Model vypne jedno zařízení a ukáže, kam až požadavek dojde.', 'Pick a scenario to see which step fails when one device goes offline.') ?></p>
  </div>
  <div class="lab-scenario-actions">
    <button type="button" class="btn btn-ghost btn-sm" data-lab-scenario="media" data-fail-node="nas"><?= $lang->value('Video při výpadku NAS', 'Video with the NAS offline') ?></button>
    <button type="button" class="btn btn-ghost btn-sm" data-lab-scenario="printing" data-fail-node="print"><?= $lang->value('Tisk při výpadku tiskového uzlu', 'Printing with the print node offline') ?></button>
    <button type="button" class="btn btn-ghost btn-sm" data-lab-scenario="media" data-fail-nodes="nas,print"><?= $lang->value('Souběh: NAS + tisk', 'Multi-outage: NAS + print') ?></button>
    <button type="button" class="btn btn-ghost btn-sm" data-lab-scenario="media" data-fail-nodes="pi,nas"><?= $lang->value('Souběh: Pi + NAS (core)', 'Multi-outage: Pi + NAS (core)') ?></button>
  </div>
</div><div class="lab-toolbar requires-js"><div class="lab-tabs" role="group" aria-label="<?= $lang->value('Pohled na domácí síť', 'Home network views') ?>"><button class="tab active" data-lab-view="services" aria-pressed="true"><?= $lang->value('Služby', 'Services') ?></button><button class="tab" data-lab-view="hardware" aria-pressed="false"><?= $lang->value('Zařízení', 'Hardware') ?></button><button class="tab" data-lab-view="dependencies" aria-pressed="false"><?= $lang->value('Závislosti','Dependencies') ?></button><button class="tab" data-lab-view="flows" aria-pressed="false"><?= $lang->value('Cesty dat','Data paths') ?></button></div><div class="lab-toolbar-right"><label class="toggle-label"><input type="checkbox" data-failure-mode><span><?= $lang->value('Vyber uzel pro simulaci výpadku','Select a node to simulate an outage') ?></span></label><button class="mini-button" type="button" data-lab-reset><?= $lang->value('Reset','Reset') ?></button></div></div><div class="lab-flow-toolbar hidden" data-flow-toolbar><label for="lab-flow-select"><?= $lang->value('Modelová cesta požadavku', 'Illustrative request path') ?></label><select id="lab-flow-select" data-flow-select><?php foreach ($flows as $flow): ?><option value="<?= Html::escape($flow->id) ?>"><?= Html::escape($lang->value($flow->titleCs, $flow->titleEn)) ?></option><?php endforeach; ?></select><span class="lab-flow-disclaimer"><?= $lang->value('Simulace nad připravenými daty — bez živého provozu.', 'Simulation uses prepared data — no live network traffic.') ?></span></div><div class="lab-content"><div class="lab-canvas-wrap"><div class="lab-canvas-status"><span class="status-indicator"></span><?= $lang->value('Uzly ve veřejném modelu','Nodes in the public model') ?></div><div class="lab-device-grid" data-lab-devices role="group" aria-label="<?= $lang->value('Zařízení v modelu sítě', 'Devices in the network model') ?>"><span id="node-pi" class="visually-hidden"></span></div><div class="lab-legend"><span><i class="legend-dot legend-online"></i><?= $lang->value('Dostupné v simulaci','Available in simulation') ?></span><span><i class="legend-dot legend-down"></i><?= $lang->value('Nedostupné','Unavailable') ?></span></div><div class="lab-flow-inspector hidden" data-flow-inspector><span class="eyebrow"><?= $lang->value('CESTA POŽADAVKU / POSTUP', 'REQUEST PATH / TRACE') ?></span><h3 data-flow-title></h3><p data-flow-description></p><p class="lab-flow-result" data-flow-result tabindex="-1" role="status" aria-live="polite"></p><ol class="lab-flow-steps" data-flow-steps></ol></div></div><div class="lab-inspector"><span class="eyebrow"><?= $lang->value('PODROBNOSTI / ', 'DETAILS / ') ?><span data-lab-index>01</span></span><h2 data-lab-name>—</h2><p data-lab-description></p><div class="lab-inspector-group"><span class="eyebrow"><?= $lang->value('TECHNOLOGIE', 'TECHNOLOGIES') ?></span><div class="stack-list" data-lab-tech></div></div><div class="lab-inspector-group"><span class="eyebrow"><?= $lang->value('SLUŽBY A ZÁVISLOSTI', 'SERVICES & DEPENDENCIES') ?></span><ul class="service-list" data-lab-services></ul></div><div class="lab-inspector-note" data-lab-note><?= $lang->value('Vyber uzel pro podrobnosti; zapni simulaci a kliknutím uzel vypni.','Select a node for details; enable simulation and click a node to take it offline.') ?></div></div></div></div>
 <script type="application/json" id="topology-flows"><?= Html::json(array_map(static fn (App\Domain\NetworkFlow $flow): array => $flow->toArray(), $flows)) ?></script>
 <script type="application/json" id="topology-data"><?= Html::json($localizedNodes) ?></script>
<noscript><div class="lab-fallback"><h2><?= $lang->value('Textový přehled datových cest', 'Text-only data path overview') ?></h2><p><?= $lang->value('Bez JavaScriptu jsou cesty uvedeny níže. Simulace výpadků vyžaduje JavaScript a používá pouze připravená data.', 'Without JavaScript, the paths remain available here. Failure simulation requires JavaScript and uses prepared data only.') ?></p><?php foreach ($flows as $flow): ?><h3><?= Html::escape($lang->value($flow->titleCs, $flow->titleEn)) ?></h3><p><?= Html::escape($lang->value($flow->descriptionCs, $flow->descriptionEn)) ?></p><ol><?php foreach ($flow->path as $i => $nodeId): ?><li><?= Html::escape($lang->value($flow->stepsCs[$i], $flow->stepsEn[$i])) ?></li><?php endforeach; ?></ol><?php endforeach; ?></div></noscript>
</section>

<section class="section shell" aria-labelledby="homelab-impact-title">
    <div class="section-heading">
        <div>
            <span class="eyebrow"><?= $lang->value('ANALÝZA SPOLEHLIVOSTI', 'RELIABILITY ANALYSIS') ?></span>
            <h2 id="homelab-impact-title"><?= $lang->value('Matice dopadů výpadků a redundance', 'Outage Impact & Redundancy Matrix') ?></h2>
            <p><?= $lang->value('Přehled závislostí upstreamu, dopadů na klientské služby a dostupných záložních mechanismů při výpadku jednotlivých zařízení.', 'Summary of upstream dependencies, downstream service impacts, and redundancy fallbacks during node outages.') ?></p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table-fallback impact-matrix-table">
            <thead>
                <tr>
                    <th><?= $lang->value('Zařízení / Uzel', 'Device / Node') ?></th>
                    <th><?= $lang->value('Primární role', 'Primary Role') ?></th>
                    <th><?= $lang->value('Dopad při výpadku', 'Outage Impact') ?></th>
                    <th><?= $lang->value('Redundance / Záloha', 'Redundancy / Fallback') ?></th>
                    <th><?= $lang->value('Priorita obnovy', 'Recovery Priority') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Router (MikroTik)</strong></td>
                    <td><?= $lang->value('L3 routing, firewall, WireGuard VPN', 'L3 routing, firewall, WireGuard VPN') ?></td>
                    <td><?= $lang->value('Celá síť ztratí spojení s internetem i mezi VLANy.', 'All network traffic to internet and inter-VLAN halts.') ?></td>
                    <td><?= $lang->value('Konfigurace zálohována v Gitu, lokální LTE záloha při selhání WAN.', 'Git-backed config, local LTE fallback on WAN failure.') ?></td>
                    <td><span class="badge badge-error">P1 (<?= $lang->value('Kritická', 'Critical') ?>)</span></td>
                </tr>
                <tr>
                    <td><strong>Switch (CRS326)</strong></td>
                    <td><?= $lang->value('L2 distribuce, 10GbE SFP+ páteř', 'L2 distribution, 10GbE SFP+ backbone') ?></td>
                    <td><?= $lang->value('Ztráta kabelového spojení mezi servery a NASem.', 'Loss of wired connectivity between hosts and NAS.') ?></td>
                    <td><?= $lang->value('Agregované linky (LACP) na páteři.', 'LACP bonded links on core backbone.') ?></td>
                    <td><span class="badge badge-error">P1 (<?= $lang->value('Kritická', 'Critical') ?>)</span></td>
                </tr>
                <tr>
                    <td><strong>Raspberry Pi (Core)</strong></td>
                    <td><?= $lang->value('DNS filtr (Pi-hole), Unbound, Discord bot', 'DNS filter (Pi-hole), Unbound, Discord bot') ?></td>
                    <td><?= $lang->value('Lokální DNS filtrace vypadne; interní jména nedostupná.', 'Local DNS filtering down; local hostnames unreachable.') ?></td>
                    <td><?= $lang->value('Router automaticky přepíná na záložní veřejný DNS resolver.', 'Router automatically falls back to secondary public DNS resolver.') ?></td>
                    <td><span class="badge badge-warning">P2 (<?= $lang->value('Vysoká', 'High') ?>)</span></td>
                </tr>
                <tr>
                    <td><strong>NAS (Synology)</strong></td>
                    <td><?= $lang->value('NFS/SMB úložiště, BookStack DB, mediální archiv', 'NFS/SMB storage, BookStack DB, media archive') ?></td>
                    <td><?= $lang->value('Jellyfin ztratí mediální data; BookStack je v režimu offline.', 'Jellyfin loses media pool; BookStack drops offline.') ?></td>
                    <td><?= $lang->value('SHR (RAID-1/5) redundance disků; denní šifrovaná C2 záloha.', 'SHR (RAID-1/5) disk tolerance; daily encrypted C2 backup.') ?></td>
                    <td><span class="badge badge-warning">P2 (<?= $lang->value('Vysoká', 'High') ?>)</span></td>
                </tr>
                <tr>
                    <td><strong>Print Node (OctoPrint)</strong></td>
                    <td><?= $lang->value('3D tiskový uzel, OctoPrint daemon', '3D printing node, OctoPrint daemon') ?></td>
                    <td><?= $lang->value('Není možný vzdálený tisk ani monitoring.', 'Remote print dispatch and camera feed unavailable.') ?></td>
                    <td><?= $lang->value('Tiskárna podporuje autonomní tisk z lokální SD karty.', 'Printer supports standalone printing from local SD card.') ?></td>
                    <td><span class="badge badge-info">P3 (<?= $lang->value('Střední', 'Medium') ?>)</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<section class="section shell homelab-services" data-service-catalog>
 <div class="section-heading"><div><span class="eyebrow"><?= $lang->value('KATALOG SLUŽEB', 'SERVICE CATALOG') ?></span><h2><?= $lang->value('Kde jednotlivé služby běží', 'Where each service runs') ?></h2><p><?= $lang->value('Rozklikni službu: role, návaznost i konkrétní hranice. Katalog vychází z konfiguračních podkladů; neukazuje živé zdraví hostů.', 'Open a service to see its role, dependencies and limits. This catalog follows configuration sources; it does not show live host health.') ?></p></div></div>
 <div class="service-groups">
  <section class="service-group"><header><span class="eyebrow">01 / NETWORK EDGE</span><h3><?= $lang->value('Síť a DNS', 'Network and DNS') ?></h3><p><?= $lang->value('Vstup do sítě, filtrování dotazů a vzdálený přístup.', 'Network entry, query filtering and remote access.') ?></p></header><div class="service-records">
   <details class="service-record"><summary><strong>RouterOS</strong><span>MikroTik · routing / firewall</span></summary><div><p><?= $lang->value('Směruje provoz mezi internetem, lokálními zařízeními a VPN; v konfiguraci se hlídají zásahy do síťové vrstvy.', 'Routes traffic between the internet, local devices and VPN; playbooks guard network-level changes.') ?></p><p class="service-caveat"><?= $lang->value('Router používá záložní veřejné resolvery. DoT/DoH v podkladech zapnuté není.', 'The router has public resolver fallbacks. Source does not enable DoT/DoH.') ?></p></div></details>
   <details class="service-record"><summary><strong>Pi-hole</strong><span><?= $lang->value('DNS filtr', 'DNS filtering') ?></span></summary><div><p><?= $lang->value('Přijímá DNS dotazy klientů a aplikuje síťové filtry. V modelu běží na hlavním Raspberry Pi.', 'Receives client DNS queries and applies network filters. The model places it on the main Raspberry Pi.') ?></p></div></details>
   <details class="service-record"><summary><strong>Unbound</strong><span><?= $lang->value('Rekurzivní resolver', 'Recursive resolver') ?></span></summary><div><p><?= $lang->value('Navazuje na DNS vrstvu za Pi-hole. Jeho přítomnost v konfiguraci neznamená šifrovaný DNS transport.', 'Resolves queries behind Pi-hole. Its presence in configuration does not mean encrypted DNS transport.') ?></p></div></details>
   <details class="service-record"><summary><strong>WireGuard</strong><span><?= $lang->value('Vzdálený přístup', 'Remote access') ?></span></summary><div><p><?= $lang->value('VPN končí na routeru. Veřejný model schválně neukazuje peer adresy, porty ani klíče.', 'VPN terminates at the router. The public model omits peer addresses, ports and keys.') ?></p></div></details>
  </div></section>
  <section class="service-group"><header><span class="eyebrow">02 / APPLICATIONS</span><h3><?= $lang->value('Aplikace a automatizace', 'Applications and automation') ?></h3><p><?= $lang->value('Služby pro média, dokumentaci, správu a každodenní úlohy.', 'Services for media, documentation, administration and daily tasks.') ?></p></header><div class="service-records">
   <details class="service-record"><summary><strong>Jellyfin</strong><span><?= $lang->value('Mediální knihovna', 'Media library') ?></span></summary><div><p><?= $lang->value('Zpřístupňuje domácí video a další média. Datová cesta v modelu pokračuje z aplikace na NAS.', 'Serves home video and other media. The modeled data path continues from the application to the NAS.') ?></p></div></details>
    <details class="service-record"><summary><strong>BookStack + MariaDB</strong><span><?= $lang->value('Dokumentace a její stav', 'Documentation and state') ?></span></summary><div><p><?= $lang->value('BookStack je uživatelská znalostní báze. MariaDB drží strukturovaný obsah a persistentní data musí přežít restart aplikačního kontejneru.', 'BookStack is the human-facing knowledge base. MariaDB stores structured content, and persistent data must survive an application-container restart.') ?></p><p class="service-caveat"><?= $lang->value('Výpadek aplikace a ztráta persistentního úložiště jsou dvě různé poruchy.', 'An application outage and loss of persistent storage are two different failure modes.') ?></p></div></details>
   <details class="service-record"><summary><strong>Semaphore + Ansible</strong><span><?= $lang->value('Spouštění automatizace', 'Automation runner') ?></span></summary><div><p><?= $lang->value('Semaphore poskytuje rozhraní pro úlohy, které popisuje Ansible. Git je zdrojem požadovaného nastavení.', 'Semaphore provides an interface for Ansible jobs. Git is the source of desired configuration.') ?></p></div></details>
    <details class="service-record"><summary><strong>Nginx + PHP dashboard</strong><span><?= $lang->value('Proxy a domácí přehled', 'Proxy and home dashboard') ?></span></summary><div><p><?= $lang->value('Nginx směruje webový provoz a PHP obsluhuje dashboard. Proxy, aplikace a služby za ní jsou oddělené části cesty; dostupný dashboard sám o sobě nepotvrzuje zdraví cílových služeb.', 'Nginx routes web traffic and PHP serves the dashboard. The proxy, application and services behind it are separate parts of the path; a reachable dashboard alone does not prove that target services are healthy.') ?></p></div></details>
   <details class="service-record"><summary><strong>Discord bot · systemd</strong><span><?= $lang->value('Samostatně nasazovaná aplikace', 'Separately deployed application') ?></span></summary><div><p><?= $lang->value('Ansible spravuje samostatný checkout pod vyhrazeným neprivilegovaným účtem a omezenými administračními wrappery. To popisuje provozní konfiguraci, ne chování bota.', 'Ansible manages a separate checkout under a dedicated unprivileged account with restricted maintenance wrappers. This describes deployment configuration, not the bot’s behavior.') ?></p><p class="service-caveat"><?= $lang->value('Zdrojový checkout aplikace není k dispozici; příkazy, oprávnění ani aktuální běh proto nejsou ověřené.', 'The application source checkout is unavailable, so its commands, permissions and current runtime are unverified.') ?></p></div></details>
   <details class="service-record"><summary><strong>Transmission</strong><span><?= $lang->value('Správa přenosů', 'Transfer management') ?></span></summary><div><p><?= $lang->value('Stav aplikace zůstává na trvalém úložišti, místo aby závisel na dočasném systémovém prostoru.', 'Application state stays on persistent storage instead of depending on temporary system space.') ?></p></div></details>
  </div></section>
  <section class="service-group"><header><span class="eyebrow">03 / DATA & RECOVERY</span><h3><?= $lang->value('Data, tisk a testování', 'Data, printing and testing') ?></h3><p><?= $lang->value('Co má přežít výpadek, kam se ukládá a kde se zkouší změny.', 'What should survive an outage, where it is stored and where changes are tested.') ?></p></header><div class="service-records">
    <details class="service-record"><summary><strong>QNAP NAS · SMB3</strong><span><?= $lang->value('Sdílená data a zálohy', 'Shared data and backups') ?></span></summary><div><p><?= $lang->value('SMB3 zpřístupňuje síťové úložiště a Restic do něj posílá zálohy. NAS se používá jen v plánovaném zálohovacím okně; stránka neukazuje jeho aktuální dostupnost.', 'SMB3 exposes network storage and Restic sends backups there. The NAS is used only during its scheduled backup window; this page does not show its live availability.') ?></p><p class="service-caveat"><?= $lang->value('Nedostupné úložiště může přerušit práci se soubory nebo médii. Stránka neověřuje stav poslední zálohy ani to, zda ji lze obnovit.', 'An unavailable share can interrupt access to files or media. This page does not verify the latest backup or whether it can be restored.') ?></p></div></details>
    <details class="service-record"><summary><strong>Restic</strong><span><?= $lang->value('Zálohovací úlohy', 'Backup jobs') ?></span></summary><div><p><?= $lang->value('Playbook spouští zálohovací úlohy a ukládá kopie do NAS cíle přes SMB3. Úspěšný playbook ale není totéž jako pravidelně otestovaná obnova.', 'The playbook runs backup jobs and stores copies on the NAS target over SMB3. A successful playbook is not the same as a regularly tested restore.') ?></p></div></details>
    <details class="service-record"><summary><strong>CUPS · Raspberry Pi Zero</strong><span><?= $lang->value('Samostatná tisková fronta', 'Separate print queue') ?></span></summary><div><p><?= $lang->value('CUPS připojuje Brother tiskárnu do sítě bez závislosti na hlavním Pi. Systém používá read-only overlay, takže údržba vyžaduje vědomý zapisovatelný režim.', 'CUPS connects the Brother printer to the network without depending on the main Pi. The system uses a read-only overlay, so maintenance requires a deliberate writable mode.') ?></p><p class="service-caveat"><?= $lang->value('Výpadek této větve znamená ztrátu síťového tisku, ne automaticky výpadek ostatních služeb.', 'Failure on this branch means network printing is unavailable; it does not imply all other services are down.') ?></p></div></details>
   <details class="service-record"><summary><strong>Proxmox</strong><span><?= $lang->value('Testovací virtuální stroje', 'Test virtual machines') ?></span></summary><div><p><?= $lang->value('Samostatný testovací host se zapíná podle potřeby. Není to stálá produkční služba.', 'A separate test host is used as needed. It is not an always-on production service.') ?></p></div></details>
  </div></section>
 </div>
 <section class="homelab-contracts" aria-labelledby="homelab-contracts-title"><div class="section-heading"><div><span class="eyebrow"><?= $lang->value('PROVOZNÍ HRANICE', 'OPERATIONAL BOUNDARIES') ?></span><h2 id="homelab-contracts-title"><?= $lang->value('Co obnovit po výpadku', 'What to restore after an outage') ?></h2><p><?= $lang->value('Právě tyhle rozdíly dělají z domácího labu systém. Ne všechny služby jsou pořád zapnuté a ne každá porucha znamená totéž.', 'These differences are what make the home lab a system. Not every service is always on, and outages do not all mean the same thing.') ?></p></div></div><div class="homelab-contract-grid">
   <article><span>01 / CORE</span><h3><?= $lang->value('Hlavní Raspberry Pi', 'Main Raspberry Pi') ?></h3><p><?= $lang->value('Sdílí DNS, dashboard, Semaphore, Jellyfin, Transmission, BookStack s MariaDB i systemd službu Discord bota. Je to hlavní společný uzel a případný jediný bod selhání.', 'Shares DNS, the dashboard, Semaphore, Jellyfin, Transmission, BookStack with MariaDB and the Discord bot systemd service. It is the main shared dependency and a potential single point of failure.') ?></p></article>
  <article><span>02 / PRINT</span><h3>Pi Zero + CUPS</h3><p><?= $lang->value('Tisková větev je oddělená. Systém používá read-only overlay; údržba proto musí počítat s řízeným přepnutím do zapisovatelného režimu.', 'Printing is on a separate branch. The system uses a read-only overlay, so maintenance must deliberately enable a writable mode.') ?></p></article>
  <article><span>03 / STORAGE</span><h3>QNAP + SMB3 + Restic</h3><p><?= $lang->value('NAS přijímá zálohy přes SMB3. Mapa neověřuje dostupnost úložiště ani obnovitelnost záloh.', 'The NAS is a backup target over SMB3. The map does not verify storage availability or backup recovery.') ?></p></article>
  <article><span>04 / TEST</span><h3>Proxmox</h3><p><?= $lang->value('Testovací host se zapíná podle potřeby. Není součástí trvalé cesty běžných služeb; jeho výpadek má jiný dopad než výpadek hlavního Pi.', 'The test host is powered on when needed. It is not part of the always-on service path; its outage has a different impact from losing the main Pi.') ?></p></article>
 </div></section>
 <p class="homelab-source-note"><?= $lang->value('Názvy a role popisují dokumentovaný stav. Přesné adresy, přístupové údaje a živá dostupnost zůstávají mimo veřejnou stránku.', 'Names and roles describe documented state. Exact addresses, credentials and live availability stay off this public page.') ?></p>
</section>
<section class="section shell"><div class="section-heading"><div><h2><?= $lang->value('Rozhodnutí za topologií','Decisions behind the topology') ?></h2><p><?= $lang->value('Infrastruktura se vyvíjí průběžně. Veřejná mapa je abstrahovaný model, ne návod ke vstupu do domácí sítě.', 'The infrastructure evolves. The public map is an abstract model, not a route into a private network.') ?></p></div></div><div class="triptych"><article class="info-card"><span>01 /</span><h3>Infrastructure as Code</h3><p><?= $lang->value('Deklarativní konfigurace Ansible, explicitní playbooky a kontrolované změny s možností návratu.', 'Declarative Ansible configuration, explicit playbooks and guarded changes.') ?></p></article><article class="info-card"><span>02 /</span><h3>Failure-aware Design</h3><p><?= $lang->value('Rozlišení stálých služeb, zařízení zapínaných podle potřeby a jejich závislostí.', 'Differentiate always-on services, on-demand devices and their dependencies.') ?></p></article><article class="info-card"><span>03 /</span><h3>Privacy by Design</h3><p><?= $lang->value('Žádné skutečné adresy, přihlašovací údaje nebo přímé odkazy na interní správu.', 'No real addresses, credentials or direct internal administration links.') ?></p></article></div></section>
