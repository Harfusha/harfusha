(() => {
  'use strict';
  document.documentElement.classList.add('js');
  const $ = (selector, parent = document) => parent.querySelector(selector);
  const $$ = (selector, parent = document) => Array.from(parent.querySelectorAll(selector));
  const language = document.body.dataset.language === 'en' ? 'en' : 'cs';
  const local = (cs, en) => language === 'en' ? en : cs;
  const core = /** @type {any} */ (window).HarfushaLabCore;
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
      if (count) count.textContent = `${local('Počet záznamů:', 'Results:')} ${visible}`;
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
  function initAdmin() {
    const root = $('[data-admin-editor]');
    if (!root) return;
    const select = $('[data-admin-select]', root), cs = $('[data-admin-cs]', root), en = $('[data-admin-en]', root), status = $('[data-admin-status]', root), revision = $('[data-admin-revision]', root);
    const problems = [$('[data-admin-problem-cs]', root), $('[data-admin-problem-en]', root)];
    const solutions = [$('[data-admin-solution-cs]', root), $('[data-admin-solution-en]', root)];
    const update = () => {
      const option = select?.selectedOptions[0];
      if (!option) return;
      if (revision) revision.value = option.dataset.revision || '';
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
    const cards = $$('[data-map-project]', root).map(card => ({
      card,
      text: normalizeSearch(card.dataset.search || card.textContent || ''),
    }));
    const domains = $$('[data-map-domain]', root);
    const search = $('[data-map-search]', root);
    const theme = $('[data-map-category]', root);
    const state = $('[data-map-state]', root);
    const status = $('[data-map-status]', root);
    const empty = $('[data-map-empty]', root);
    const filter = () => {
      const query = normalizeSearch(search?.value || '').trim();
      let visible = 0;
      cards.forEach(({card, text}) => {
        const matches = (!query || text.includes(query))
          && (!theme || theme.value === 'all' || card.dataset.theme === theme.value)
          && (!state || state.value === 'all' || card.dataset.state === state.value);
        card.hidden = !matches;
        if (matches) visible++;
      });
      domains.forEach(domain => { domain.hidden = !$$('[data-map-project]', domain).some(card => !card.hidden); });
      if (status) status.textContent = `${status.dataset.label || ''} ${visible}`.trim();
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
      entry,
      id: entry.id || '',
      legacyId: entry.dataset.legacyId || '',
      text: normalizeSearch(entry.textContent || '').replace(/\s+/g, ' '),
    }));
    const buttons = $$('[data-journal-year]', root);
    const count = $('[data-journal-count]', root);
    const search = $('[data-journal-search]', root);
    const searchControl = $('[data-journal-search-control]', root);
    const projectControl = $('[data-journal-project-control]', root);
    const projectSelect = $('[data-journal-project]', root);
    const serverForm = $('.journal-server-filter', root);
    const empty = $('[data-journal-empty]', root);
    let year = buttons.find(b => b.classList.contains('active'))?.dataset.journalYear || 'all';
    if (searchControl) searchControl.hidden = false;
    if (projectControl) projectControl.hidden = false;
    serverForm?.addEventListener('submit', e => e.preventDefault());
    const activeDeepLinkId = () => window.location.hash.slice(1);
    const render = () => {
      const query = normalizeSearch(search?.value || '').trim().replace(/\s+/g, ' ');
      const proj = projectSelect?.value || 'all';
      const targetId = activeDeepLinkId();
      let visible = 0;
      entries.forEach(({entry, id, legacyId, text}) => {
        const isDeepLinked = targetId !== '' && (id === targetId || legacyId === targetId);
        const matches = isDeepLinked || (
          (year === 'all' || entry.dataset.year === year) &&
          (proj === 'all' || entry.dataset.project === proj) &&
          text.includes(query)
        );
        entry.hidden = !matches;
        if (matches) visible++;
      });
      buttons.forEach(button => {
        const active = button.dataset.journalYear === year;
        button.classList.toggle('active', active);
        if (active) {
          button.setAttribute('aria-current', 'page');
        } else {
          button.removeAttribute('aria-current');
        }
      });
      if (empty) empty.hidden = visible !== 0;
      if (count) count.textContent = `${local('Počet zápisů:', 'Entries:')} ${visible}`;
    };
    buttons.forEach(button => button.addEventListener('click', e => {
      e.preventDefault();
      year = button.dataset.journalYear || 'all';
      render();
    }));
    search?.addEventListener('input', render);
    projectSelect?.addEventListener('change', render);
    window.addEventListener('hashchange', render);
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
    const initialLabHash = window.location.hash.replace(/^#node-/, '').replace(/^#/, '');
    if (initialLabHash && nodeMap.has(initialLabHash)) {
      selected = initialLabHash;
    }
    window.addEventListener('hashchange', () => {
      const h = window.location.hash.replace(/^#node-/, '').replace(/^#/, '');
      if (h && nodeMap.has(h) && selected !== h) {
        selected = h;
        render();
      }
    });
    const edgeSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    edgeSvg.classList.add('lab-topology-edges');
    edgeSvg.setAttribute('viewBox', '0 0 1000 600');
    edgeSvg.setAttribute('preserveAspectRatio', 'none');
    edgeSvg.setAttribute('aria-hidden', 'true');
    const edges = [];
    nodes.forEach(node => (node.depends || []).forEach(parentId => {
      const parent = nodeMap.get(parentId);
      if (!parent) return;
      const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
      line.setAttribute('x1', parent.x);
      line.setAttribute('y1', parent.y);
      line.setAttribute('x2', node.x);
      line.setAttribute('y2', node.y);
      edgeSvg.append(line);
      edges.push({ line, parentId, nodeId: node.id });
    }));
    const countLabel = (count, cs, en) => language === 'en'
      ? `${count} ${en}`
      : `${count} ${count === 1 ? cs[0] : count < 5 && count > 1 ? cs[1] : cs[2]}`;
    const unavailable = () => new Set(core.affectedNodes(nodes, [...knockedOut]));
    const render = () => {
      const down = unavailable();
      const up = id => !down.has(id);
      const flowStatus = core.traceFlow(nodes, currentFlow, [...knockedOut]);
      const selectedNode = nodeMap.get(selected);
      edges.forEach(({line, parentId, nodeId}) => {
        const onFlow = view === 'flows' && currentFlow.path.includes(parentId) && currentFlow.path.includes(nodeId);
        const related = view === 'dependencies' && (selected === parentId || selected === nodeId);
        line.classList.toggle('is-in-path', onFlow || related);
        line.classList.toggle('is-down', !up(parentId) || !up(nodeId));
      });
      flowToolbar.classList.toggle('hidden', view !== 'flows');
      flowInspector.classList.toggle('hidden', view !== 'flows');
      devices.replaceChildren(edgeSvg, ...nodes.map(node => {
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
        button.id = 'node-' + node.id;
        button.style.left = `${node.x / 10}%`;
        button.style.top = `${node.y / 6}%`;
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
          if (window.location.hash !== '#node-' + node.id) {
            window.history.replaceState(null, '', '#node-' + node.id);
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
        const failedNodesStr = button.dataset.failNodes || button.dataset.failNode || '';
        const failedNodes = failedNodesStr.split(',').map(s => s.trim()).filter(Boolean);
        if (!flow || failedNodes.length === 0 || failedNodes.some(n => !nodeMap.has(n))) return;
        knockedOut.clear();
        failedNodes.forEach(n => knockedOut.add(n));
        selected = failedNodes[0];
        currentFlow = flow;
        view = 'flows';
        flowSelect.value = flow.id;
        failureMode.checked = true;
        render();
        flowResult.focus();
      }));
    }
    $('[data-lab-reset]', root)?.addEventListener('click', () => {
      knockedOut.clear();
      failureMode.checked = false;
      try {
        if (window.location.hash) {
          window.history.replaceState(null, '', '#');
        }
      } catch {}
      render();
    });
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

  // Manual theme switcher (Dark / Light toggle)
  const themeToggle = $('[data-theme-toggle]');
  if (themeToggle) {
    const updateToggleState = (theme) => {
      const isLight = theme === 'light';
      themeToggle.setAttribute('aria-pressed', isLight ? 'true' : 'false');
      const icon = $('.theme-toggle-icon', themeToggle);
      if (icon) icon.textContent = isLight ? '☼' : '◐';
    };
    let currentTheme = document.documentElement.dataset.theme;
    if (currentTheme !== 'light' && currentTheme !== 'dark') {
      try { currentTheme = localStorage.getItem('theme'); } catch (_) {}
      if (currentTheme !== 'light' && currentTheme !== 'dark') {
        currentTheme = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
      }
      document.documentElement.dataset.theme = currentTheme;
    }
    updateToggleState(currentTheme);
    const themeColor = document.querySelector('meta[name="theme-color"]');
    if (themeColor) themeColor.setAttribute('content', currentTheme === 'light' ? '#f4f0e6' : '#0d1019');

    themeToggle.addEventListener('click', () => {
      const current = document.documentElement.dataset.theme || 'light';
      const next = current === 'light' ? 'dark' : 'light';
      document.documentElement.dataset.theme = next;
      try { localStorage.setItem('theme', next); } catch (_) {}
      updateToggleState(next);
      const meta = document.querySelector('meta[name="theme-color"]');
      if (meta) meta.setAttribute('content', next === 'light' ? '#f4f0e6' : '#0d1019');
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

  function initLifecycleLab() {
    const root = $('[data-lifecycle-lab]');
    if (!root) return;
    const tabs = $$('[data-lifecycle-tab]', root);
    const panels = $$('[data-lifecycle-panel]', root);
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const targetId = tab.dataset.lifecycleTab;
        tabs.forEach(t => {
          const active = t.dataset.lifecycleTab === targetId;
          t.classList.toggle('active', active);
          t.setAttribute('aria-selected', String(active));
        });
        panels.forEach(p => {
          const active = p.dataset.lifecyclePanel === targetId;
          p.classList.toggle('active', active);
          p.hidden = !active;
        });
      });
    });
  }
  initLifecycleLab();

  function initConcurrencyLab() {
    const root = $('[data-concurrency-lab]');
    const dataEl = $('#concurrency-lab-data');
    if (!root || !dataEl) return;
    let scenarios;
    try { scenarios = JSON.parse(dataEl.textContent || '[]'); } catch { return; }
    if (!Array.isArray(scenarios) || !scenarios.length) return;

    const tabs = $$('[data-concurrency-tab]', root);
    const panels = $$('[data-concurrency-panel]', root);
    const prevBtn = $('[data-concurrency-step-prev]', root);
    const nextBtn = $('[data-concurrency-step-next]', root);
    const resetBtn = $('[data-concurrency-reset]', root);

    let activeScenarioId = tabs.find(t => t.classList.contains('active'))?.dataset.concurrencyTab || scenarios[0].id;
    let currentStepIdx = 0;

    const renderStep = () => {
      const scenario = scenarios.find(s => s.id === activeScenarioId);
      if (!scenario) return;
      const panel = $(`[data-concurrency-panel="${activeScenarioId}"]`, root);
      if (!panel) return;
      const steps = scenario.steps || [];
      if (!steps.length) return;
      if (currentStepIdx >= steps.length) currentStepIdx = steps.length - 1;
      if (currentStepIdx < 0) currentStepIdx = 0;

      const step = steps[currentStepIdx];
      const stepNum = $('[data-current-step-num]', panel);
      const actorA = $('[data-actor-a-state]', panel);
      const dbBox = $('[data-db-state]', panel);
      const actorB = $('[data-actor-b-state]', panel);
      const actionText = $('[data-action-text]', panel);
      const sqlStatus = $('[data-sql-status]', panel);
      const sqlCode = $('[data-sql-code]', panel);
      const markers = $$('.marker-item', panel);

      if (stepNum) stepNum.textContent = String(step.step_number);
      if (actorA) actorA.textContent = step.client_a_state;
      if (dbBox) dbBox.textContent = step.db_state;
      if (actorB) actorB.textContent = step.client_b_state;
      if (actionText) {
        const strong = $('strong', actionText);
        const span = $('span', actionText);
        if (strong) strong.textContent = step.actor + ': ';
        if (span) span.textContent = step.action[language] || step.action['cs'];
      }
      if (sqlStatus) sqlStatus.textContent = step.status;
      if (sqlCode) sqlCode.textContent = step.sql;

      markers.forEach((m, idx) => {
        m.classList.toggle('current', idx === currentStepIdx);
        m.classList.toggle('passed', idx < currentStepIdx);
      });

      if (prevBtn) prevBtn.disabled = currentStepIdx === 0;
      if (nextBtn) nextBtn.disabled = currentStepIdx >= steps.length - 1;
    };

    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        activeScenarioId = tab.dataset.concurrencyTab || '';
        currentStepIdx = 0;
        tabs.forEach(t => {
          const active = t.dataset.concurrencyTab === activeScenarioId;
          t.classList.toggle('active', active);
          t.setAttribute('aria-selected', String(active));
        });
        panels.forEach(p => {
          const active = p.dataset.concurrencyPanel === activeScenarioId;
          p.classList.toggle('active', active);
          p.hidden = !active;
        });
        renderStep();
      });
    });

    panels.forEach(panel => {
      $$('[data-step-jump]', panel).forEach(btn => {
        btn.addEventListener('click', () => {
          const idx = parseInt(btn.dataset.stepJump || '0', 10);
          if (!isNaN(idx)) {
            currentStepIdx = idx;
            renderStep();
          }
        });
      });
    });

    prevBtn?.addEventListener('click', () => {
      if (currentStepIdx > 0) {
        currentStepIdx--;
        renderStep();
      }
    });

    nextBtn?.addEventListener('click', () => {
      const scenario = scenarios.find(s => s.id === activeScenarioId);
      if (scenario && currentStepIdx < (scenario.steps || []).length - 1) {
        currentStepIdx++;
        renderStep();
      }
    });

    resetBtn?.addEventListener('click', () => {
      currentStepIdx = 0;
      renderStep();
    });

    renderStep();
  }
  initConcurrencyLab();

  function initTypeSafetyLab() {
    const root = $('[data-type-safety-lab]');
    if (!root) return;
    const tabs = $$('[data-ts-tab]', root);
    const panels = $$('[data-ts-panel]', root);
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const mode = tab.dataset.tsTab;
        tabs.forEach(t => {
          const active = t.dataset.tsTab === mode;
          t.classList.toggle('active', active);
          t.setAttribute('aria-selected', String(active));
        });
        panels.forEach(p => {
          const active = p.dataset.tsPanel === mode;
          p.classList.toggle('active', active);
          p.hidden = !active;
        });
      });
    });
  }
  initTypeSafetyLab();

  function initMobileMenu() {
    const menu = document.querySelector('details.mobile-menu');
    if (!menu) return;
    const summary = menu.querySelector('summary');
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && menu.hasAttribute('open')) {
        menu.removeAttribute('open');
        summary?.focus();
      }
    });
    document.addEventListener('click', (e) => {
      if (menu.hasAttribute('open') && e.target instanceof Node && !menu.contains(e.target)) {
        menu.removeAttribute('open');
      }
    });
  }
  initMobileMenu();

  function initLangToggle() {
    const toggle = document.querySelector('.lang-toggle');
    if (!toggle) return;
    const syncHash = () => {
      if (window.location.hash) {
        const base = toggle.getAttribute('href')?.split('#')[0] || '';
        toggle.setAttribute('href', base + window.location.hash);
      }
    };
    syncHash();
    window.addEventListener('hashchange', syncHash);
    toggle.addEventListener('click', syncHash);
  }
  initLangToggle();

  function initAnchorTargetAutoOpen() {
    const openTarget = () => {
      const hash = window.location.hash.slice(1);
      if (!hash) return;
      const target = document.getElementById(hash);
      if (!target) return;
      if (target instanceof HTMLDetailsElement) {
        target.open = true;
      }
      const parentDetails = target.closest('details');
      if (parentDetails instanceof HTMLDetailsElement) {
        parentDetails.open = true;
      }
    };
    openTarget();
    window.addEventListener('hashchange', openTarget);
  }
  initAnchorTargetAutoOpen();

})();
