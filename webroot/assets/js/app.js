(() => {
  'use strict';
  const $ = (selector, parent = document) => parent.querySelector(selector);
  const $$ = (selector, parent = document) => [...parent.querySelectorAll(selector)];
  const language = document.body.dataset.language === 'en' ? 'en' : 'cs';
  const local = (cs, en) => language === 'en' ? en : cs;
  const core = window.HarfushaLabCore;
  // Match Czech and English text even when visitors omit accents.
  const normalizeSearch = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase();

  function initFilter(root, itemSelector, extraFilters = []) {
    if (!root) return;
    const input = $('[data-search]', root);
    const count = $('[data-count]', root);
    const empty = $('[data-empty]', root);
    const items = $$(itemSelector, root);
    const groups = $$('[data-filter-group]', root);
    // Search text comes from immutable server-rendered cards: normalize once,
    // rather than repeating Unicode decomposition on every keystroke.
    const indexed = items.map(item => ({item, text: normalizeSearch(item.dataset.searchText || '')}));
    const filters = extraFilters.map(([selector, attr]) => ({ element: $(selector, root), attr }));
    const run = () => {
      const q = normalizeSearch((input?.value ?? '').trim());
      let visible = 0;
      for (const {item, text} of indexed) {
        const matched = text.includes(q) && filters.every(({element, attr}) => !element || element.value === 'all' || item.dataset[attr] === element.value);
        item.classList.toggle('hidden', !matched);
        if (matched) visible++;
      }
      if (count) count.textContent = `${visible} ${local(visible === 1 ? 'záznam' : visible >= 2 && visible <= 4 ? 'záznamy' : 'záznamů', visible === 1 ? 'result' : 'results')}`;
      empty?.classList.toggle('hidden', visible > 0);
      groups.forEach(group => { group.hidden = !$$(`${itemSelector}:not(.hidden)`, group).length; });
    };
    input?.addEventListener('input', run);
    filters.forEach(({element}) => element?.addEventListener('change', run));
    const reset = $('[data-filter-reset]', root);
    if (reset) {
      reset.hidden = false;
      reset.addEventListener('click', () => {
        if (input) input.value = '';
        filters.forEach(({element}) => { if (element) element.value = 'all'; });
        run();
        input?.focus();
      });
    }
    run();
  }

  initFilter($('[data-maker-browser]'), '[data-maker]', [['[data-maker-status]', 'status']]);
  initFilter($('[data-project-browser]'), '[data-project]', [['[data-filter]', 'category']]);
  initFilter($('[data-activity-browser]'), '[data-activity]', [['[data-year]', 'year'], ['[data-state]', 'state'], ['[data-kind]', 'kind']]);
  initFilter($('[data-decision-browser]'), '[data-decision]', [['[data-decision-status]', 'status'], ['[data-decision-project]', 'project']]);
  const contributionDialog = $('[data-activity-dialog]');
  if (contributionDialog) {
    $$('[data-activity-inspect]').forEach(button => button.addEventListener('click', () => {
      $('[data-activity-title]', contributionDialog).textContent = button.dataset.inspectTitle || '';
      $('[data-activity-repo]', contributionDialog).textContent = `${button.dataset.inspectRepo || ''} · ${button.dataset.inspectState || ''}`;
      const anchor = $('[data-activity-url]', contributionDialog);
      anchor.href = button.dataset.inspectUrl || '#';
      if (typeof contributionDialog.showModal === 'function') contributionDialog.showModal();
      else anchor.click();
    }));
    contributionDialog.addEventListener('click', e => {
      if (e.target === contributionDialog) contributionDialog.close();
    });
  }


  function initAdmin() {
    const root = $('[data-admin-editor]');
    if (!root) return;
    const select = $('[data-admin-select]', root), cs = $('[data-admin-cs]', root), en = $('[data-admin-en]', root), status = $('[data-admin-status]', root);
    const problems = [$('[data-admin-problem-cs]', root), $('[data-admin-problem-en]', root)];
    const solutions = [$('[data-admin-solution-cs]', root), $('[data-admin-solution-en]', root)];
    const update = () => {
      const option = select?.selectedOptions[0];
      if (!option) return;
      cs.value = option.dataset.cs || '';
      en.value = option.dataset.en || '';
      status.value = option.dataset.status || 'prototype';
      problems[0].value = option.dataset.problemCs || '';
      problems[1].value = option.dataset.problemEn || '';
      solutions[0].value = option.dataset.solutionCs || '';
      solutions[1].value = option.dataset.solutionEn || '';
    };
    select?.addEventListener('change', update);
    update();
  }
  initAdmin();

  function initProjectMap() {
    const root = $('[data-project-map-filter]');
    if (!root) return;
    const cards = $$('[data-map-project]', root);
    const domains = $$('[data-map-domain]', root);
    const search = $('[data-map-search]', root);
    const theme = $('[data-map-category]', root);
    const state = $('[data-map-state]', root);
    const status = $('[data-map-status]', root);
    const empty = $('[data-map-empty]', root);
    const filter = () => {
      const query = (search?.value || '').trim().toLocaleLowerCase();
      let visible = 0;
      cards.forEach(card => {
        const matches = (!query || card.dataset.search?.toLocaleLowerCase().includes(query))
          && (!theme || theme.value === 'all' || card.dataset.theme === theme.value)
          && (!state || state.value === 'all' || card.dataset.state === state.value);
        card.hidden = !matches;
        if (matches) visible++;
      });
      domains.forEach(domain => { domain.hidden = !$$('[data-map-project]', domain).some(card => !card.hidden); });
      if (status) status.textContent = `${visible} ${status.dataset.label || ''}`.trim();
      if (empty) empty.hidden = visible !== 0;
    };
    search?.addEventListener('input', filter);
    theme?.addEventListener('change', filter);
    state?.addEventListener('change', filter);
  }
  initProjectMap();

  function initJournalFilter() {
    const root = $('[data-journal-filter]');
    if (!root) return;
    const entries = $$('[data-journal-entry]', root).map(entry => ({
      entry, text: normalizeSearch(entry.textContent || '').replace(/\s+/g, ' '),
    }));
    const buttons = $$('[data-journal-year]', root);
    const count = $('[data-journal-count]', root);
    const search = $('[data-journal-search]', root);
    const searchControl = $('[data-journal-search-control]', root);
    const projectControl = $('[data-journal-project-control]', root);
    const projectSelect = $('[data-journal-project]', root);
    const empty = $('[data-journal-empty]', root);
    let year = 'all';
    if (searchControl) searchControl.hidden = false;
    if (projectControl) projectControl.hidden = false;
    const render = () => {
      const query = normalizeSearch(search?.value || '').trim().replace(/\s+/g, ' ');
      const proj = projectSelect?.value || 'all';
      let visible = 0;
      entries.forEach(({entry, text}) => {
        const matches = (year === 'all' || entry.dataset.year === year)
          && (proj === 'all' || entry.dataset.project === proj)
          && text.includes(query);
        entry.hidden = !matches;
        if (matches) visible++;
      });
      buttons.forEach(button => {
        const active = button.dataset.journalYear === year;
        button.classList.toggle('active', active);
        button.setAttribute('aria-pressed', String(active));
      });
      if (empty) empty.hidden = visible !== 0;
      if (count) count.textContent = `${visible} ${local(visible === 1 ? 'zápisek' : visible >= 2 && visible <= 4 ? 'zápisky' : 'zápisků', visible === 1 ? 'entry' : 'entries')}`;
    };
    buttons.forEach(button => button.addEventListener('click', () => {
      year = button.dataset.journalYear || 'all';
      render();
    }));
    search?.addEventListener('input', render);
    projectSelect?.addEventListener('change', render);
    render();
  }
  initJournalFilter();

  /** Filter the evidence map without implying project dependencies. */
  function initProjectEvidence() {
    const root = $('[data-evidence-map]');
    if (!root) return;
    const cards = $$('[data-evidence-stage]', root);
    const columns = $$('[data-evidence-column]', root);
    const filters = $$('[data-evidence-filter]', root);
    const count = $('[data-evidence-count]', root);
    const empty = $('[data-evidence-empty]', root);
    const render = stage => {
      let visible = 0;
      cards.forEach(card => {
        const matches = stage === 'all' || card.dataset.evidenceStage === stage;
        card.hidden = !matches;
        if (matches) visible++;
      });
      columns.forEach(column => {
        column.hidden = !$$('[data-evidence-stage]', column).some(card => !card.hidden);
      });
      if (count) count.textContent = `${visible} ${local('projektů', 'projects')}`;
      empty?.classList.toggle('hidden', visible > 0);
    };
    filters.forEach(button => button.addEventListener('click', () => {
      filters.forEach(item => item.setAttribute('aria-pressed', String(item === button)));
      render(button.dataset.evidenceFilter || 'all');
    }));
    render('all');
  }
  initProjectEvidence();
  function initHomelab() {
    const root = $('[data-homelab]'), json = $('#topology-data'), flowsJson = $('#topology-flows');
    if (!root || !json || !flowsJson) return;
    let nodes, flows;
    try { nodes = JSON.parse(json.textContent); flows = JSON.parse(flowsJson.textContent); } catch { return; }
    if (!Array.isArray(nodes) || nodes.length < 2 || !Array.isArray(flows) || !flows.length) return;
    const devices = $('[data-lab-devices]', root), modeButtons = $$('[data-lab-view]', root), failureMode = $('[data-failure-mode]', root);
    const inspectorName = $('[data-lab-name]', root), description = $('[data-lab-description]', root), tech = $('[data-lab-tech]', root), services = $('[data-lab-services]', root), note = $('[data-lab-note]', root), index = $('[data-lab-index]', root);
    const flowToolbar = $('[data-flow-toolbar]', root), flowSelect = $('[data-flow-select]', root);
    const flowInspector = $('[data-flow-inspector]', root), flowTitle = $('[data-flow-title]', root);
    const flowDescription = $('[data-flow-description]', root), flowSteps = $('[data-flow-steps]', root), flowResult = $('[data-flow-result]', root);
    let currentFlow = flows[0];
    let selected = 'pi', view = 'services';
    const knockedOut = new Set();
    const nodeMap = new Map(nodes.map(n => [n.id, n]));
    const countLabel = (count, cs, en) => language === 'en'
      ? `${count} ${en}`
      : `${count} ${count === 1 ? cs[0] : count < 5 && count > 1 ? cs[1] : cs[2]}`;
    const unavailable = () => new Set(core.affectedNodes(nodes, [...knockedOut]));
    const render = () => {
      const down = unavailable();
      const up = id => !down.has(id);
      const flowStatus = core.traceFlow(nodes, currentFlow, [...knockedOut]);
      const selectedNode = nodeMap.get(selected);
      flowToolbar.classList.toggle('hidden', view !== 'flows');
      flowInspector.classList.toggle('hidden', view !== 'flows');
      devices.replaceChildren(...nodes.map(node => {
        const active = node.id === selected;
        const alive = up(node.id);
        const inPath = view === 'flows' && currentFlow.path.includes(node.id);
        const upstream = (selectedNode.depends || []).includes(node.id);
        const downstream = (node.depends || []).includes(selected);
        const related = view === 'dependencies' && (upstream || downstream);
        const detail = view === 'services'
          ? countLabel((node.services || []).length, ['služba', 'služby', 'služeb'], 'services')
          : view === 'dependencies'
            ? active
              ? local('Vybraný uzel', 'Selected node')
              : upstream
                ? local('Vyžadováno uzlem', 'Required by node')
                : downstream
                  ? local('Závisí na uzlu', 'Depends on node')
                  : countLabel((node.depends || []).length, ['závislost', 'závislosti', 'závislostí'], 'dependencies')
            : inPath ? local('Na trase', 'On the route') : (node.tech || []).slice(0, 2).join(' · ') || node.kind;

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'lab-device';
        button.classList.toggle('is-selected', active);
        button.classList.toggle('is-in-path', inPath || related);
        button.classList.toggle('is-down', !alive);
        button.dataset.labNodeId = node.id;
        button.setAttribute('aria-pressed', String(active));
        button.setAttribute('aria-label', node.name + ' ' + detail + (alive ? '' : local(' · nedostupné v simulaci', ' · unavailable in simulation')));

        const name = document.createElement('strong');
        name.textContent = node.name;
        const sub = document.createElement('span');
        sub.textContent = detail;
        const availability = document.createElement('span');
        availability.className = 'lab-device-state';
        availability.textContent = alive ? local('Dostupné', 'Available') : local('Nedostupné', 'Unavailable');
        button.append(name, sub, availability);

        button.addEventListener('click', () => {
          selected = node.id;
          if (failureMode.checked && node.id !== 'internet') {
            if (knockedOut.has(node.id)) knockedOut.delete(node.id);
            else knockedOut.add(node.id);
          }
          render();
          [...devices.children].find(candidate => candidate.dataset.labNodeId === selected)?.focus();
        });
        return button;
      }));
      const node = nodeMap.get(selected);
      inspectorName.textContent = node.name;
      description.textContent = (language === 'en' ? node.description_en : node.description_cs) || '';
      index.textContent = String(nodes.indexOf(node)+1).padStart(2,'0');
      tech.replaceChildren(...(node.tech||[]).map(label => { const e=document.createElement('span');e.textContent=label;return e; }));
      if (view === 'dependencies') {
        const depItems = [];
        const upstreamNodes = nodes.filter(n => (node.depends || []).includes(n.id));
        const downstreamNodes = nodes.filter(n => (n.depends || []).includes(node.id));

        const upHeader = document.createElement('li');
        upHeader.className = 'dependency-header';
        upHeader.innerHTML = `<strong>${local('Vyžaduje (upstream):', 'Requires (upstream):')}</strong>`;
        depItems.push(upHeader);

        if (upstreamNodes.length === 0) {
          const none = document.createElement('li');
          none.className = 'dependency-item-muted';
          none.textContent = local('— žádné další závislosti v modelu', '— no further dependencies in model');
          depItems.push(none);
        } else {
          upstreamNodes.forEach(un => {
            const li = document.createElement('li');
            li.className = 'dependency-item';
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'inline-dep-btn';
            btn.textContent = `→ ${un.name}`;
            btn.title = local(`Přepnout na ${un.name}`, `Switch to ${un.name}`);
            btn.addEventListener('click', () => {
              selected = un.id;
              render();
              devices.querySelector(`[data-lab-node-id="${un.id}"]`)?.focus();
            });
            li.append(btn);
            depItems.push(li);
          });
        }

        const downHeader = document.createElement('li');
        downHeader.className = 'dependency-header';
        downHeader.innerHTML = `<strong>${local('Závislá zařízení (downstream):', 'Depended on by (downstream):')}</strong>`;
        depItems.push(downHeader);

        if (downstreamNodes.length === 0) {
          const none = document.createElement('li');
          none.className = 'dependency-item-muted';
          none.textContent = local('— žádná závislá zařízení', '— no dependent devices');
          depItems.push(none);
        } else {
          downstreamNodes.forEach(dn => {
            const li = document.createElement('li');
            li.className = 'dependency-item';
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'inline-dep-btn';
            btn.textContent = `← ${dn.name}`;
            btn.title = local(`Přepnout na ${dn.name}`, `Switch to ${dn.name}`);
            btn.addEventListener('click', () => {
              selected = dn.id;
              render();
              devices.querySelector(`[data-lab-node-id="${dn.id}"]`)?.focus();
            });
            li.append(btn);
            depItems.push(li);
          });
        }

        const srvHeader = document.createElement('li');
        srvHeader.className = 'dependency-header';
        srvHeader.innerHTML = `<strong>${local('Běžící služby na uzlu:', 'Hosted services on node:')}</strong>`;
        depItems.push(srvHeader);
        (node.services || []).forEach(label => {
          const e = document.createElement('li');
          e.textContent = label;
          e.className = up(node.id) ? '' : 'down';
          depItems.push(e);
        });

        services.replaceChildren(...depItems);
      } else {
        services.replaceChildren(...(node.services||[]).map(label => {const e=document.createElement('li');e.textContent=label;e.className=up(node.id)?'':'down';return e;}));
      }
      const failed = nodes.filter(n=>down.has(n.id)).map(n=>n.name);
      note.textContent = failed.length ? local(`Simulace: nedostupné uzly: ${failed.join(', ')}.`, `Simulation: unavailable nodes: ${failed.join(', ')}.`) : local('Všechny modelované uzly jsou dostupné. Zapni simulaci výpadku a klikni na zařízení.', 'All modeled nodes are available. Enable outage simulation and select a device.');
      if (view === 'flows') {
        flowTitle.textContent = language === 'en' ? currentFlow.title_en : currentFlow.title_cs;
        flowDescription.textContent = language === 'en' ? currentFlow.description_en : currentFlow.description_cs;
        flowSteps.replaceChildren();
        flowStatus.steps.forEach((step, i) => {
          const li = document.createElement('li');
          li.className = step.available ? 'available' : 'unavailable';
          const name = nodeMap.get(step.id).name;
          const description = language === 'en' ? currentFlow.steps_en[i] : currentFlow.steps_cs[i];
          const state = step.available ? local('dostupný','available') : local('nedostupný','unavailable');
          li.textContent = `${name}: ${description} (${state})`;
          flowSteps.append(li);
        });
        flowResult.textContent = flowStatus.available
          ? local('Modelová cesta je dostupná.','Modeled path is available.')
          : local(`Cesta přerušena: ${nodeMap.get(flowStatus.firstFailed).name}.`, `Path interrupted: ${nodeMap.get(flowStatus.firstFailed).name}.`);
        flowResult.classList.toggle('blocked', !flowStatus.available);
      }
      modeButtons.forEach(b=>{const active=b.dataset.labView===view;b.classList.toggle('active',active);b.setAttribute('aria-pressed',String(active));});
    };
    modeButtons.forEach(btn=>btn.addEventListener('click',()=>{view=btn.dataset.labView;render();}));
    flowSelect.addEventListener('change', () => {currentFlow=flows.find(flow=>flow.id===flowSelect.value) || flows[0];render();});
    const scenarioGroup = $('[data-lab-scenarios]', root);
    if (scenarioGroup) {
      scenarioGroup.hidden = false;
      $$('[data-lab-scenario]', scenarioGroup).forEach(button => button.addEventListener('click', () => {
        const flow = flows.find(item => item.id === button.dataset.labScenario);
        const failedNode = button.dataset.failNode;
        if (!flow?.path.includes(failedNode) || !nodeMap.has(failedNode)) return;
        knockedOut.clear();
        knockedOut.add(failedNode);
        selected = failedNode;
        currentFlow = flow;
        view = 'flows';
        flowSelect.value = flow.id;
        failureMode.checked = true;
        render();
        flowResult.focus();
      }));
    }
    $('[data-lab-reset]',root)?.addEventListener('click',()=>{knockedOut.clear();failureMode.checked=false;render();});
    failureMode.addEventListener('change',render);
    render();
  }
  initHomelab();

  const AI_SCENARIOS = {
    'first-use': {
      noteCs:'První registrace je idempotentní. Preprocessing běží na pozadí; volitelné backendy mohou zůstat nedostupné nebo se přeskočit.',
      noteEn:'First registration is idempotent. Preprocessing runs in the background; optional backends may be unavailable or skipped.',
      steps:[
        {cs:'Zaregistruj stabilní kořen',en:'Register a stable root',descCs:'Agent odešle jednorázový preprocess požadavek s absolutní cestou repozitáře.',descEn:'The agent sends a one-time preprocess request with the repository’s absolute path.',trace:'local_ai_repo(action="preprocess", root=ABSOLUTE_REPOSITORY_ROOT)'},
        {cs:'Ověř projektovou hranici',en:'Confirm the project boundary',descCs:'Hub naváže projekt na povolený kořen a zachová vlastní filtraci souborů a cest.',descEn:'The Hub binds the project to an authorized root and applies its file and path boundaries.',trace:'root policy → exclusions → eligible files'},
        {cs:'Proveď inventář',en:'Inventory eligible files',descCs:'Preprocessing najde vhodné zdrojové soubory podle konfigurace projektu.',descEn:'Preprocessing identifies source files eligible under project configuration.',trace:'inventory → bounded file selection'},
        {cs:'Spočítej změnové hashe',en:'Hash source changes',descCs:'Hashování rozliší změněné vstupy, které potřebují aktualizovat navazující evidenci.',descEn:'Hashing identifies changed inputs that need downstream evidence refreshed.',trace:'source bytes → content hashes'},
        {cs:'Postav code index',en:'Build the code index',descCs:'Vestavěný index poskytne přesné symboly, importy a vztahy bez načtení celého repozitáře do promptu.',descEn:'The built-in index exposes exact symbols, imports and relationships without loading the whole repository into a prompt.',trace:'code_index → definitions / callers / imports'},
        {cs:'Vytvoř deterministické karty',en:'Build deterministic cards',descCs:'Karty popisují to, co lze bezpečně vyčíst z parsovaných zdrojů; nejde o modelový odhad.',descEn:'Cards describe facts that can be derived from parsed sources; they are not model guesses.',trace:'AST + source facts → bounded cards'},
        {cs:'Doplň dostupné symboly',en:'Enrich available symbols',descCs:'Serena přidává jazykové symboly, pokud je dostupná. Při chybě může Hub pokračovat jinou cestou.',descEn:'Serena adds language-aware symbols when available. The Hub can continue through another path if it fails.',trace:'Serena → optional language-aware symbols'},
        {cs:'Připrav textové hledání',en:'Prepare text retrieval',descCs:'Lexikální index zpřístupní textové shody; RAG je doplňková cesta, nikoli podmínka každé otázky.',descEn:'The lexical index enables text matches; RAG is an additional path, not a requirement for every question.',trace:'lexical → optional RAG enrichment'},
        {cs:'Slož mapy kontextu',en:'Compose context maps',descCs:'Pipeline doplní souborové, modulové a projektové mapy a záznamy často používaných dotazů.',descEn:'The pipeline adds file, module and project maps plus records of frequently used queries.',trace:'files → modules → project → hot queries'},
        {cs:'Prohlédni dokončení',en:'Inspect completion',descCs:'Diagnostika zaznamená stav průchodu. Complete neznamená, že model běžel nebo že každá odpověď je správná.',descEn:'Diagnostics record the pipeline state. Complete does not mean a model ran or every answer is correct.',trace:'diagnostics → complete\nnext: query index → fetch exact source → verify change'},
      ]
    },
    'warm-query': {
      noteCs:'Začni nejlevnější přesnou cestou. Rozšiř kontext jen když nalezené zdroje nestačí.',
      noteEn:'Start with the cheapest exact path. Widen context only when the returned sources are insufficient.',
      steps:[
        {cs:'Vymez konkrétní otázku',en:'Scope the question',descCs:'Příklad: najdi všechny volající metody po přesunu třídy.',descEn:'Example: find all callers after moving a class.',trace:'question → symbol: SomeNamespace.SomeType::method'},
        {cs:'Dotazuj code index',en:'Query the code index',descCs:'Přesná symbolická otázka patří nejdřív do indexu, ne do obecného jazykového odhadu.',descEn:'An exact symbol question belongs in the index first, not a general language-model guess.',trace:'local_ai_repo(action="code_index")'},
        {cs:'Načti úzký výřez',en:'Fetch a narrow source slice',descCs:'Otevři deklaraci nebo konkrétní volající a zachovej cestu a řádky jako původ.',descEn:'Open the declaration or specific callers and retain their path and lines as provenance.',trace:'local_ai_artifact(action="slice", evidence_id=...)'},
        {cs:'Rozšiř jen při mezeře',en:'Widen only if evidence is missing',descCs:'Pokud přesné zdroje nestačí, použij jednou adaptive context; nevracej se k širokému hledání bez důvodu.',descEn:'If exact sources are insufficient, use adaptive context once; do not restart broad search without a reason.',trace:'local_ai_repo(action="context", mode="adaptive")'},
        {cs:'Ověř v hostu',en:'Verify on the host',descCs:'Test nebo build spouští prostředí agenta. Model může vysvětlovat, ale test rozhoduje o chování kódu.',descEn:'The agent host runs tests or builds. A model can explain, but tests decide code behavior.',trace:'native command → result → diff review'},
      ]
    },
    'model-offline': {
      noteCs:'Indexované a deterministické cesty mohou fungovat dál. Nedostupný model se nikdy nesmí změnit na vymyšlený důkaz.',
      noteEn:'Indexed and deterministic paths can continue. An unavailable model must never be replaced with invented evidence.',
      steps:[
        {cs:'Zkontroluj readiness',en:'Check readiness',descCs:'Stav odlišuje Hub, model a dostupné kapacity. Konfigurace sama o sobě není provozní důkaz.',descEn:'Status distinguishes the Hub, model and available capacity. Configuration alone is not runtime evidence.',trace:'local_ai_status(detail="brief")'},
        {cs:'Použij přesný index',en:'Use the exact index',descCs:'Hledání definic a volajících nepotřebuje generativní model, pokud je code index připravený.',descEn:'Definition and caller lookup does not need a generative model when the code index is ready.',trace:'code_index / deterministic / source slice'},
        {cs:'Přiznej meze kontextu',en:'Expose context limits',descCs:'Pokud volitelná Serena nebo RAG chybí, výsledek může být užší. Stav se označí; chybějící část se nedoplňuje domněnkou.',descEn:'If optional Serena or RAG is missing, results may be narrower. State the limit; do not fill the gap with a guess.',trace:'available evidence only → limitation visible'},
        {cs:'Ověř změnu deterministicky',en:'Verify the change deterministically',descCs:'Testy, diff a příkazy zůstávají v hostitelském prostředí a nejsou závislé na lokálním modelu.',descEn:'Tests, diffs and commands remain in the host environment and do not depend on the local model.',trace:'host command → actual exit status'},
        {cs:'Vrať výsledek bez přikrášlení',en:'Report without embellishment',descCs:'Nahlas stav, přesné zdroje a co zůstalo neověřené.',descEn:'Report the state, exact sources and what remains unverified.',trace:'evidence + limitation → honest handoff'},
      ]
    }
  };

  function initScale(root) {
    if (!root) return;
    const scaleForm = $('[data-scale-form]', root);
    if (!scaleForm) return;
    const quickControls = $('[data-quick-scale-controls]', scaleForm);
    if (quickControls) quickControls.disabled = false;
    const renderScale = () => {
      const mm = Number($('[data-scale-mm]', root).value);
      const pixels = Number($('[data-scale-pixels]', root).value);
      const probeInput = $('[data-scale-probe]', root).value;
      const probe = Number(probeInput);
      const valid = probeInput !== '' && Number.isFinite(mm) && mm > 0 && Number.isFinite(pixels) && pixels > 0 && Number.isFinite(probe) && probe >= 0;
      $('[data-scale-result]', root).textContent = valid ? local(`${(pixels / mm).toFixed(2)} px/mm`, `${(pixels / mm).toFixed(2)} px/mm`) : local('Zadej platné kladné měření.', 'Enter a valid positive measurement.');
      $('[data-scale-probe-result]', root).textContent = valid ? local(`${probe} px odpovídá ${(probe * mm / pixels).toFixed(2)} mm`, `${probe} px corresponds to ${(probe * mm / pixels).toFixed(2)} mm`) : '';
    };
    scaleForm.addEventListener('submit', event => event.preventDefault());
    scaleForm.addEventListener('input', renderScale);
    renderScale();
  }
  initScale($('[data-quick-scale]'));

  function initPlayground() {
    const root = $('[data-playground]');
    if (!root) return;
    const preprocessForm = $('[data-preprocess-form]', root);
    const preprocessPath = $('[data-preprocess-path]', root);
    const preprocessContent = $('[data-preprocess-content]', root);
    const baselinePath = preprocessPath.value;
    const baselineContent = preprocessContent.value;
    const digest = async value => {
      const bytes = new Uint8Array(await crypto.subtle.digest('SHA-256', new TextEncoder().encode(value)));
      return Array.from(bytes, byte => byte.toString(16).padStart(2, '0')).join('');
    };
    const renderPreprocess = async () => {
      const path = preprocessPath.value.trim().replaceAll('\\', '/').replace(/^\.\//, '');
      const kind = $('[data-preprocess-kind]', root).value;
      const title = $('[data-preprocess-title]', root);
      const hash = $('[data-preprocess-hash]', root);
      const list = $('[data-preprocess-steps]', root);
      const note = $('[data-preprocess-note]', root);
      hash.textContent = '';
      if (!path) {
        title.textContent = local('Doplň cestu k souboru.', 'Enter a file path.');
        list.replaceChildren();
        note.textContent = '';
        return;
      }
      const excluded = path.split('/').some(part => part === '.git' || part === 'node_modules');
      if (excluded) {
        title.textContent = local('Cesta je vyloučená před čtením obsahu.', 'The path is excluded before its content is read.');
        list.replaceChildren(...[local(`Vyřazovací pravidlo odpovídá části cesty „${path}“.`, `The exclusion rule matches “${path}”.`), local('Soubor se nehashuje a žádný navazující index se kvůli němu neobnovuje.', 'The file is not hashed and no downstream index is rebuilt for it.')].map(text => { const li = document.createElement('li'); li.textContent = text; return li; }));
        note.textContent = local('Jde o demonstrační pravidlo této stránky.', 'This is the page’s demonstration rule.');
        return;
      }
      let currentHash = null;
      let baselineHash = null;
      try {
        if (crypto.subtle) [currentHash, baselineHash] = await Promise.all([digest(preprocessContent.value), digest(baselineContent)]);
      } catch { /* Offline previews may not provide Web Crypto; exact text comparison still models change detection. */ }
      const unchanged = currentHash === null ? preprocessContent.value === baselineContent : currentHash === baselineHash;
      hash.textContent = currentHash ?? local('SHA-256 vyžaduje HTTPS', 'SHA-256 requires HTTPS');
      if (unchanged) {
        title.textContent = local('Obsah beze změny — použijí se připravené podklady.', 'Content unchanged — reuse prepared evidence.');
        list.replaceChildren(...[local('Fingerprint odpovídá uloženému základu.', 'The fingerprint matches the stored baseline.'), local('Code index, textové hledání i další odvozené podklady zůstávají použitelné.', 'The code index, text search and other derived evidence remain reusable.')].map(text => { const li = document.createElement('li'); li.textContent = text; return li; }));
        note.textContent = local('Uprav obsah a přepočítej fingerprint; data neopustí tento prohlížeč.', 'Edit the content and recalculate; no data leaves this browser.');
        return;
      }
      const downstream = kind === 'code'
        ? [local('Obnovit code index, strukturu a lexikální hledání.', 'Refresh the code index, structure and lexical search.'), local('Volitelné RAG a další analýzy znovu použít jen podle potřeby.', 'Reuse optional RAG and other analysis only where needed.')]
        : kind === 'doc'
          ? [local('Obnovit textový index dokumentace.', 'Refresh the documentation text index.')]
          : [local('Zachovat inventární metadata; obsah předat jen povolenému specializovanému zpracování.', 'Keep inventory metadata; send content only to an enabled specialized processor.')];
      title.textContent = local('Obsah se změnil — navazující podklady je třeba obnovit.', 'Content changed — dependent evidence needs refreshing.');
      list.replaceChildren(...[local('SHA-256 se liší od uloženého základu.', 'SHA-256 differs from the stored baseline.'), ...downstream].map(text => { const li = document.createElement('li'); li.textContent = text; return li; }));
      note.textContent = local('V reálném Hubu se změna zapisuje do obnovitelné dávky. Tady se pouze počítá otisk ukázkového textu.', 'The real Hub checkpoints changes in resumable batches. Here we only fingerprint sample text.');
    };
    $('[data-preprocess-run]', root).addEventListener('click', renderPreprocess);
    $('[data-preprocess-reset]', root).addEventListener('click', () => {
      preprocessPath.value = baselinePath;
      preprocessContent.value = baselineContent;
      renderPreprocess();
    });
    renderPreprocess();
    const syncForm = $('[data-sync-form]', root);
    const parseManifest = selector => {
      const manifest = new Map();
      for (const line of $(selector, root).value.split(/\r?\n/).map(value => value.trim()).filter(Boolean)) {
        const divider = line.indexOf('|');
        const path = line.slice(0, divider).trim();
        const revision = line.slice(divider + 1).trim();
        if (divider < 1 || !revision || manifest.has(path)) throw new Error('manifest');
        manifest.set(path, revision);
      }
      return manifest;
    };
    const renderSync = () => {
      const title = $('[data-sync-title]', root);
      const summary = $('[data-sync-summary]', root);
      const operations = [];
      const conflicts = [];
      try {
        const base = parseManifest('[data-sync-base]');
        const localCopy = parseManifest('[data-sync-local]');
        const remoteCopy = parseManifest('[data-sync-remote]');
        const mode = $('[data-sync-mode]', root).value;
        for (const path of new Set([...base.keys(), ...localCopy.keys(), ...remoteCopy.keys()])) {
          const before = base.get(path);
          const localRevision = localCopy.get(path);
          const remoteRevision = remoteCopy.get(path);
          if (mode === 'one-way') {
            if (localRevision !== remoteRevision) operations.push(localRevision === undefined
              ? local(`Odstranit ${path} z druhé kopie.`, `Remove ${path} from the other copy.`)
              : local(`Kopírovat ${path}: druhá kopie → moje (${remoteRevision ?? 'nový soubor'} → ${localRevision}).`, `Copy ${path}: other copy → mine (${remoteRevision ?? 'new file'} → ${localRevision}).`));
            continue;
          }
          const localChanged = localRevision !== before;
          const remoteChanged = remoteRevision !== before;
          if (localChanged && remoteChanged && localRevision !== remoteRevision) conflicts.push(path);
          else if (localChanged && localRevision !== remoteRevision) operations.push(localRevision === undefined
            ? local(`Odstranit ${path} z druhé kopie.`, `Remove ${path} from the other copy.`)
            : local(`Poslat ${path}: moje kopie → druhá kopie (${localRevision}).`, `Send ${path}: my copy → other copy (${localRevision}).`));
          else if (remoteChanged && localRevision !== remoteRevision) operations.push(remoteRevision === undefined
            ? local(`Odstranit ${path} z mojí kopie.`, `Remove ${path} from my copy.`)
            : local(`Přijmout ${path}: druhá kopie → moje (${remoteRevision}).`, `Receive ${path}: other copy → mine (${remoteRevision}).`));
        }
        title.textContent = conflicts.length
          ? local(`${conflicts.length} konflikt${conflicts.length === 1 ? '' : 'y'} — rozhodni před synchronizací.`, `${conflicts.length} conflict${conflicts.length === 1 ? '' : 's'} — decide before syncing.`)
          : local(`${operations.length} změn v plánu.`, `${operations.length} change${operations.length === 1 ? '' : 's'} in the plan.`);
        summary.textContent = local(`${operations.length} přenosů · ${conflicts.length} konfliktů · beze zápisu na disk`, `${operations.length} transfers · ${conflicts.length} conflicts · no disk writes`);
      } catch {
        title.textContent = local('Manifest musí mít na každém řádku cestu|revizi.', 'Each manifest line must contain path|revision.');
        summary.textContent = local('Prázdné řádky jsou povolené; duplicitní cesty nebo prázdné revize ne.', 'Blank lines are allowed; duplicate paths and empty revisions are not.');
      }
      $('[data-sync-operations]', root).replaceChildren(...operations.map(text => { const li = document.createElement('li'); li.textContent = text; return li; }));
      $('[data-sync-conflicts]', root).replaceChildren(...conflicts.map(path => { const li = document.createElement('li'); li.textContent = local(`${path}: obě kopie se změnily od společného základu.`, `${path}: both copies changed since the common base.`); return li; }));
    };
    $('[data-sync-preview]', syncForm).addEventListener('click', renderSync);
    renderSync();

    initScale(root);

    const grid = $('[data-route-grid]', root);
    const gridSize = 8;
    const start = 0;
    const goal = gridSize * gridSize - 1;
    let walls = new Set([10, 17, 18, 26, 34, 42, 50, 51, 52]);
    const findRoute = blocked => {
      const previous = Array(gridSize * gridSize).fill(-1);
      const queue = [start];
      previous[start] = start;
      for (let head = 0; head < queue.length && previous[goal] === -1; head++) {
        const current = queue[head];
        for (const next of [current - gridSize, current + gridSize, current % gridSize ? current - 1 : -1, current % gridSize < gridSize - 1 ? current + 1 : -1]) {
          if (next < 0 || next >= previous.length || blocked.has(next) || previous[next] !== -1) continue;
          previous[next] = current;
          queue.push(next);
        }
      }
      if (previous[goal] === -1) return null;
      const path = [];
      for (let cell = goal; cell !== start; cell = previous[cell]) path.push(cell);
      path.push(start);
      return path.reverse();
    };
    if (findRoute(new Set()).length !== gridSize * 2 - 1) throw new Error('Route finder self-check failed');
    const renderRoute = () => {
      const path = findRoute(walls);
      grid.replaceChildren();
      for (let cell = 0; cell < gridSize * gridSize; cell++) {
        const button = document.createElement('button');
        const isStart = cell === start;
        const isGoal = cell === goal;
        button.type = 'button';
        button.dataset.routeCell = String(cell);
        button.className = [isStart ? 'is-start' : '', isGoal ? 'is-goal' : '', walls.has(cell) ? 'is-wall' : '', path?.includes(cell) ? 'is-route' : ''].filter(Boolean).join(' ');
        button.setAttribute('aria-pressed', String(walls.has(cell)));
        button.setAttribute('aria-label', isStart ? local('Start', 'Start') : isGoal ? local('Cíl', 'Goal') : walls.has(cell) ? local('Překážka; kliknutím odstranit', 'Obstacle; activate to remove') : local('Volná buňka; kliknutím přidat překážku', 'Open cell; activate to add obstacle'));
        button.textContent = isStart ? 'S' : isGoal ? 'C' : walls.has(cell) ? '×' : path?.includes(cell) ? '·' : '';
        if (!isStart && !isGoal) button.addEventListener('click', () => { walls.has(cell) ? walls.delete(cell) : walls.add(cell); renderRoute(); });
        grid.append(button);
      }
      $('[data-route-status]', root).textContent = path ? local('Trasa existuje', 'Route found') : local('Bez průchodu', 'No route');
      $('[data-route-length]', root).textContent = path ? local(`${path.length - 1} kroků · 4směrný pohyb`, `${path.length - 1} steps · four-direction movement`) : local('Zablokováno překážkami; uprav mřížku.', 'Blocked by obstacles; change the grid.');
    };
    $('[data-route-reset]', root).addEventListener('click', () => { walls = new Set([10, 17, 18, 26, 34, 42, 50, 51, 52]); renderRoute(); });
    renderRoute();

    const queueList = $('[data-queue-list]', root);
    const queueVersion = $('[data-queue-version]', root);
    const queueResult = $('[data-queue-result]', root);
    let version = 12;
    let queueItems = ['Night Drive', 'Quiet Signal'];
    const renderQueue = () => {
      queueVersion.textContent = String(version);
      queueList.replaceChildren(...queueItems.map(title => { const li = document.createElement('li'); li.textContent = title; return li; }));
    };
    $$('[data-queue-add]', root).forEach(button => button.addEventListener('click', () => {
      const client = button.dataset.queueAdd;
      if (version !== 12) {
        queueResult.textContent = local(`Klient ${client.toUpperCase()} poslal verzi 12, aktuální je ${version}; změna odmítnuta.`, `Client ${client.toUpperCase()} sent version 12, current is ${version}; edit rejected.`);
        return;
      }
      queueItems = [...queueItems, client === 'a' ? 'Client A · Blue Hour' : 'Client B · Slow Motion'];
      version++;
      queueResult.textContent = local(`Klient ${client.toUpperCase()} uložil změnu jako verzi ${version}. Druhý klient má zastaralou verzi.`, `Client ${client.toUpperCase()} saved as version ${version}. The other client is stale.`);
      renderQueue();
    }));
    $('[data-queue-reset]', root).addEventListener('click', () => {
      version = 12;
      queueItems = ['Night Drive', 'Quiet Signal'];
      queueResult.textContent = '';
      renderQueue();
    });
    renderQueue();

    const tiskovForm = $('[data-tiskov-form]', root);
    if (tiskovForm) {
      const jobsInput = $('[data-tiskov-jobs]', tiskovForm);
      const workersInput = $('[data-tiskov-workers]', tiskovForm);
      const minutesInput = $('[data-tiskov-minutes]', tiskovForm);
      const queue = $('[data-tiskov-queue]', root);
      const summary = $('[data-tiskov-summary]', root);
      const renderTiskov = () => {
        const jobs = jobsInput.valueAsNumber;
        const workers = workersInput.valueAsNumber;
        const minutes = minutesInput.valueAsNumber;
        const batches = Math.ceil(jobs / workers);
        const queued = Math.max(0, jobs - workers);
        const lastStart = (batches - 1) * minutes;
        const finish = batches * minutes;
        $('[data-tiskov-jobs-value]', tiskovForm).textContent = String(jobs);
        $('[data-tiskov-workers-value]', tiskovForm).textContent = String(workers);
        $('[data-tiskov-minutes-value]', tiskovForm).textContent = `${minutes} min`;
        summary.textContent = local(
          `${jobs} poptávek se uloží hned; ${queued} čeká na volného workera. Poslední začne asi za ${lastStart} min a celý model doběhne za ${finish} min.`,
          `${jobs} requests are stored immediately; ${queued} wait for a free worker. The final request starts in about ${lastStart} min and the batch finishes in ${finish} min.`
        );
        queue.replaceChildren(...Array.from({length: jobs}, (_, index) => {
          const item = document.createElement('li');
          item.className = 'tiskov-job';
          item.dataset.jobState = index < workers ? 'running' : 'waiting';
          const number = document.createElement('span');
          number.className = 'tiskov-job-number';
          number.textContent = String(index + 1).padStart(2, '0');
          const detail = document.createElement('span');
          detail.textContent = index < workers ? local('Zahájeno ihned', 'Starts now') : local('Čeká ve frontě', 'Waiting in queue');
          item.append(number, detail);
          return item;
        }));
      };
      [jobsInput, workersInput, minutesInput].forEach(input => input.addEventListener('input', renderTiskov));
      renderTiskov();
    }

    const outageForm = $('[data-outage-form]', root);
    const renderOutage = () => {
      const host = $('[data-outage-host]', root).value;
      const cases = {
        'main-pi': [local('Hlavní Raspberry Pi je společný bod několika služeb.', 'The main Raspberry Pi is shared by several services.'), [local('Pi-hole a Unbound: lokální DNS filtr a resolver.', 'Pi-hole and Unbound: local DNS filtering and resolution.'), 'Nginx + PHP dashboard, Semaphore, Jellyfin, Transmission, BookStack + MariaDB.', local('systemd spravuje také nasazení Discord bota; jeho aplikace a chování zde ověřené nejsou.', 'systemd also manages the Discord bot deployment; its application and behavior are not verified here.'), local('Přístup k připojeným úložištím může ovlivnit média a soubory.', 'Access to mounted storage can affect media and files.')], local('Router má v konfiguraci veřejné DNS fallbacky; dopad na jmenné dotazy proto nemusí znamenat úplný výpadek internetu.', 'The router has public DNS fallbacks configured, so name resolution may degrade without implying a total internet outage.')],
        router: [local('Bez brány selže přístup mezi domácí sítí, internetem a VPN.', 'Without the gateway, access between the home network, internet and VPN is affected.'), [local('RouterOS zajišťuje směrování, DHCP, firewall a WireGuard.', 'RouterOS provides routing, DHCP, firewall and WireGuard.'), local('Interní aplikace mohou dál běžet na hostech, ale cesta k nim se přeruší.', 'Internal applications may still run on their hosts, but the route to them is interrupted.')], local('Model odděluje běžící službu od dostupnosti cesty k ní.', 'The model separates a running service from whether there is a route to it.')],
        nas: [local('Výpadek QNAPu se týká úložiště a cíle záloh.', 'A QNAP outage affects storage and the backup target.'), ['SMB3 sdílení a připojené datové cesty.', local('Restic nemůže zapsat novou zálohu do nedostupného cíle.', 'Restic cannot write a new backup to an unavailable target.'), local('Jellyfin může ztratit média uložená na tomto úložišti.', 'Jellyfin may lose access to media stored on that volume.')], local('Konfigurace budí NAS v plánovaném okně; živý stav a poslední záloha se zde nekontrolují.', 'Configuration wakes the NAS on a schedule; live state and the latest backup are not checked here.')],
        'print-pi': [local('Samostatná tisková větev se oddělí od hlavního serveru.', 'The separate print branch is isolated from the main server.'), ['CUPS fronta na Raspberry Pi Zero.', local('Tiskárna není dostupná přes tuto síťovou frontu.', 'The printer is unavailable through this network queue.')], local('Ostatní služby hlavního Pi ani NAS tímto modelem přímo zasažené nejsou.', 'Other main Pi and NAS services are not directly affected in this model.')],
        proxmox: [local('Testovací prostředí je vypnuté.', 'The test environment is offline.'), [local('Proxmox a jeho testovací virtuální stroje nejsou dostupné.', 'Proxmox and its test virtual machines are unavailable.'), local('Běžné domácí služby jsou oddělené od tohoto hostu.', 'Everyday home services are separate from this host.')], local('Proxmox je podle podkladů zapínán podle potřeby, ne provozován jako stálá služba.', 'Sources describe Proxmox as on-demand, not an always-on service.')]
      };
      const [title, effects, note] = cases[host];
      $('[data-outage-title]', root).textContent = title;
      $('[data-outage-effects]', root).replaceChildren(...effects.map(text => { const li = document.createElement('li'); li.textContent = text; return li; }));
      $('[data-outage-note]', root).textContent = note;
    };
    outageForm.addEventListener('change', renderOutage);
    renderOutage();

    const authorityForm = $('[data-authority-form]', root);
    const renderAuthority = () => {
      const role = $('[data-authority-role]', root).value;
      const stale = $('[data-authority-version]', root).value === 'stale';
      const roster = $('[data-authority-roster]', root).checked;
      const result = !roster
        ? [local('Zamítnout: hráč není v aktuální sestavě.', 'Reject: player is not in the current roster.'), local('Kontrola rosteru selže dřív, než se dohoda změní.', 'Roster validation fails before the agreement can change.')]
        : stale
          ? [local('Zamítnout: základní verze už není aktuální.', 'Reject: the base version is stale.'), local('Klient musí načíst aktuální stav místo přepsání novější dohody.', 'The client must reload current state instead of overwriting a newer agreement.')]
          : role === 'guest'
            ? [local('Předat hostiteli k ověření.', 'Send to the host for validation.'), local('Zpráva hráče sama o sobě neukládá změnu do stavu relace.', 'A player message does not save a change into session state by itself.')]
            : [local('Host může po validaci změnu potvrdit.', 'The host may commit the change after validation.'), local('Hostitel vlastní relaci a save; přijetí zprávy samo neprokazuje platnost všech herních pravidel.', 'The host owns the session and save; receiving a message does not prove every game rule is valid.')];
      $('[data-authority-title]', root).textContent = result[0];
      $('[data-authority-detail]', root).textContent = result[1];
    };
    authorityForm.addEventListener('change', renderAuthority);
    renderAuthority();
  }

  // Manual theme switcher (Dark / Light toggle)
  const themeToggle = $('[data-theme-toggle]');
  if (themeToggle) {
    const updateToggleState = (theme) => {
      const isLight = theme === 'light';
      themeToggle.setAttribute('aria-pressed', isLight ? 'true' : 'false');
      const icon = $('.theme-toggle-icon', themeToggle);
      if (icon) icon.textContent = isLight ? '☼' : '◐';
    };
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light' || savedTheme === 'dark') {
      document.documentElement.dataset.theme = savedTheme;
      updateToggleState(savedTheme);
    } else {
      const systemLight = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches;
      updateToggleState(systemLight ? 'light' : 'dark');
    }
    themeToggle.addEventListener('click', () => {
      const current = document.documentElement.dataset.theme || (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
      const next = current === 'light' ? 'dark' : 'light';
      document.documentElement.dataset.theme = next;
      try { localStorage.setItem('theme', next); } catch (_) {}
      updateToggleState(next);
    });
  }

  // Anchor copy buttons
  $$('[data-copy-anchor]').forEach(button => {
    button.addEventListener('click', async (e) => {
      e.preventDefault();
      e.stopPropagation();
      const targetId = button.dataset.copyAnchor;
      if (!targetId) return;
      const url = new URL(window.location.href);
      url.hash = targetId;
      try {
        if (navigator.clipboard && navigator.clipboard.writeText) {
          await navigator.clipboard.writeText(url.toString());
        } else {
          window.location.hash = targetId;
        }
        button.classList.add('is-copied');
        const originalTitle = button.getAttribute('title') || '';
        button.setAttribute('title', local('Zkopírováno!', 'Copied!'));
        setTimeout(() => {
          button.classList.remove('is-copied');
          button.setAttribute('title', originalTitle);
        }, 1500);
      } catch (_) {
        window.location.hash = targetId;
      }
    });
  });

  initPlayground();
})();
