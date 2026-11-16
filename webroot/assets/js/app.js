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
    };
    input?.addEventListener('input', run);
    filters.forEach(({element}) => element?.addEventListener('change', run));
    run();
  }

  initFilter($('[data-maker-browser]'), '[data-maker]', [['[data-maker-status]', 'status']]);
  initFilter($('[data-project-browser]'), '[data-project]', [['[data-filter]', 'category']]);
  const categoryFilter = $('[data-project-browser] [data-filter]');
  $$('[data-category-shortcut]').forEach(button => button.addEventListener('click', () => {
    if (!categoryFilter) return;
    categoryFilter.value = button.dataset.categoryShortcut || 'all';
    categoryFilter.dispatchEvent(new Event('change', {bubbles: true}));
    $$('[data-category-shortcut]').forEach(item => item.setAttribute('aria-pressed', String(item === button)));
    $('[data-project-browser]').scrollIntoView({behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start'});
  }));
  initFilter($('[data-activity-browser]'), '[data-activity]', [['[data-state]', 'state'], ['[data-kind]', 'kind']]);
  initFilter($('[data-decision-browser]'), '[data-decision]', [['[data-decision-status]', 'status']]);
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

  /** Project relationship explorer: fully local, deterministic, keyboard-accessible. */
  function initConnections() {
    const root = $('[data-connections]');
    const source = $('#connections-data');
    if (!root || !source) return;
    let graph;
    try { graph = JSON.parse(source.textContent); } catch { return; }
    if (!Array.isArray(graph.nodes) || !Array.isArray(graph.edges) || graph.nodes.length === 0) return;
    const svg = $('[data-connections-svg]', root);
    const input = $('[data-connection-search]', root);
    const name = $('[data-connections-name]', root);
    const description = $('[data-connections-description]', root);
    const links = $('[data-connections-links]', root);
    const open = $('[data-connections-open]', root);
    const nodes = graph.nodes;
    const byId = new Map(nodes.map(node => [node.id, node]));
    const positions = new Map();
    const columns = 4, rowHeight = 150;
    const rows = Math.ceil(nodes.length / columns);
    svg.setAttribute('viewBox', `0 0 1060 ${Math.max(680, rows * rowHeight + 80)}`);
    nodes.forEach((node, index) => {
      const row = Math.floor(index / columns);
      const inRow = Math.min(columns, nodes.length - row * columns);
      positions.set(node.id, {x:530 + (index % columns - (inRow - 1) / 2) * 245, y:100 + row * rowHeight});
    });
    const make = (tag, attributes = {}, text = null) => {
      const el = document.createElementNS('http://www.w3.org/2000/svg', tag);
      for (const [key, value] of Object.entries(attributes)) el.setAttribute(key, String(value));
      if (text !== null) el.textContent = text;
      return el;
    };
    let selected = nodes[0].id;
    const render = () => {
      const query = input.value.trim().toLocaleLowerCase();
      const matching = new Set(nodes.filter(node => !query || `${node.name} ${node.category}`.toLocaleLowerCase().includes(query)).map(n => n.id));
      svg.replaceChildren();
      const edges = make('g', {'aria-hidden':'true',class:'connections-edges'});
      graph.edges.forEach(edge => {
        const a = positions.get(edge.from), b = positions.get(edge.to);
        if (!a || !b) return;
        const strong = selected === null || selected === edge.from || selected === edge.to;
        edges.append(make('line',{x1:a.x,y1:a.y,x2:b.x,y2:b.y,class:strong?'':'muted'}));
      });
      svg.append(edges);
      nodes.forEach(node => {
        const pos = positions.get(node.id);
        const highlighted = selected === node.id;
        const dimmed = !matching.has(node.id) && selected !== node.id;
        const g=make('g',{class:`connection-node${highlighted?' selected':''}${dimmed?' muted':''}`,role:'button',tabindex:'0','aria-pressed':String(highlighted),'aria-label':`${node.name} ${node.category}`,'data-node-id':node.id});
        g.append(make('rect',{x:pos.x-91,y:pos.y-31,width:182,height:62,rx:11}));
        g.append(make('text',{x:pos.x,y:pos.y-2,class:'node-title',...(node.name.length > 22 ? {textLength:170,lengthAdjust:'spacingAndGlyphs'} : {})},node.name));
        g.append(document.createTextNode(' '));
        g.append(make('text',{x:pos.x,y:pos.y+15,class:'node-type'},node.category));
        const select = ()=>{selected=node.id;render();
          svg.querySelectorAll('[data-node-id]').forEach(candidate=>{if(candidate.getAttribute('data-node-id')===node.id)candidate.focus();});};
        g.addEventListener('click', select);
        g.addEventListener('keydown', event=>{if(event.key==='Enter' || event.key===' '){event.preventDefault();select();}});
        svg.append(g);
      });
      const node=selected ? byId.get(selected) : null;
      name.textContent = node?.name || (language==='en'?'Project map':'Mapa projektů');
      description.textContent = node ? (language==='en'?node.description_en:node.description_cs) :
        local('Klikni na uzel grafu a prozkoumej související projekty.','Select a graph node to inspect related projects.');
      links.replaceChildren();
      if (node) {
        graph.edges.filter(edge=>edge.from===node.id || edge.to===node.id).forEach(edge=>{
          const other=byId.get(edge.from===node.id?edge.to:edge.from);
          if (!other) return;
          const li=document.createElement('li');
          const button=document.createElement('button');button.type='button';button.className='inline-link';
          button.textContent=`${other.name} · ${edge.label}`;
          button.addEventListener('click',()=>{selected=other.id;render();});
          li.append(button);links.append(li);
        });
        open.href=`${graph.base}/${encodeURIComponent(node.id)}`;
        open.classList.remove('hidden');
      } else {
        open.classList.add('hidden');
      }
    };
    input.addEventListener('input',render);
    $('[data-connections-reset]',root).addEventListener('click',()=>{selected=nodes[0].id;input.value='';render();});
    render();
  }
  initConnections();

  function initHomelab() {
    const root = $('[data-homelab]'), json = $('#topology-data'), flowsJson = $('#topology-flows');
    if (!root || !json || !flowsJson) return;
    let nodes, flows;
    try { nodes = JSON.parse(json.textContent); flows = JSON.parse(flowsJson.textContent); } catch { return; }
    if (!Array.isArray(nodes) || nodes.length < 2 || !Array.isArray(flows) || !flows.length) return;
    const svg = $('[data-lab-svg]', root), modeButtons = $$('[data-lab-view]', root), failureMode = $('[data-failure-mode]', root);
    const inspectorName = $('[data-lab-name]', root), description = $('[data-lab-description]', root), tech = $('[data-lab-tech]', root), services = $('[data-lab-services]', root), note = $('[data-lab-note]', root), index = $('[data-lab-index]', root);
    const flowToolbar = $('[data-flow-toolbar]', root), flowSelect = $('[data-flow-select]', root);
    const flowInspector = $('[data-flow-inspector]', root), flowTitle = $('[data-flow-title]', root);
    const flowDescription = $('[data-flow-description]', root), flowSteps = $('[data-flow-steps]', root), flowResult = $('[data-flow-result]', root);
    let currentFlow = flows[0];
    let selected = 'pi', view = 'hardware';
    const knockedOut = new Set();
    const nodeMap = new Map(nodes.map(n => [n.id, n]));
    const svgEl = (name, attrs = {}, contents = null) => {
      const el = document.createElementNS('http://www.w3.org/2000/svg', name);
      Object.entries(attrs).forEach(([key, value]) => el.setAttribute(key, String(value)));
      if (contents !== null) el.textContent = contents;
      return el;
    };
    const unavailable = () => new Set(core.affectedNodes(nodes, [...knockedOut]));
    const render = () => {
      svg.replaceChildren();
      const down = unavailable();
      const up = id => !down.has(id);
      const flowStatus = core.traceFlow(nodes, currentFlow, [...knockedOut]);
      const flowLinks = new Set();
      if (view === 'flows') {
        for (let i = 1; i < currentFlow.path.length; i++) {
          flowLinks.add([currentFlow.path[i-1], currentFlow.path[i]].sort().join('|'));
        }
      }
      flowToolbar.classList.toggle('hidden', view !== 'flows');
      flowInspector.classList.toggle('hidden', view !== 'flows');
      const edges = svgEl('g', {'aria-hidden':'true'});
      nodes.forEach(n => (n.depends || []).forEach(dep => {
        const parent = nodeMap.get(dep);
        if (!parent) return;
        const highlight = (view === 'dependencies' && (n.id === selected || parent.id === selected)) ||
          (view === 'flows' && flowLinks.has([n.id, parent.id].sort().join('|')));
        const failed = !up(n.id) || !up(parent.id);
        const edge = svgEl('path', {
          d: `M${parent.x} ${parent.y+38} Q${(parent.x+n.x)/2} ${(parent.y+n.y)/2} ${n.x} ${n.y-38}`,
          class:`lab-edge${highlight ? ' highlight':''}${failed ? ' failed':''}`,
        });
        edges.append(edge);
      }));
      svg.append(edges);
      nodes.forEach((node, i) => {
        const alive = up(node.id);
        const detail = view==='services' ? `${(node.services||[]).length} services` : view==='dependencies' ? `${(node.depends||[]).length} dependencies` : view==='flows' && currentFlow.path.includes(node.id) ? local('NA CESTĚ','IN PATH') : node.kind.toUpperCase();
        const group = svgEl('g',{class:`lab-node${node.id===selected?' selected':''}${alive?'':' failed'}`,role:'button',tabindex:'0', 'aria-label': `${node.name} ${detail}${alive ? '' : local(' – nedostupné v simulaci', ' – unavailable in simulation')}`, 'aria-pressed': String(node.id===selected), 'data-lab-node-id': node.id});
        group.append(svgEl('rect',{x:node.x-105,y:node.y-38,width:210,height:76,rx:13}));
        group.append(svgEl('circle',{cx:node.x-82,cy:node.y-15,r:5}));
        group.append(svgEl('text',{x:node.x-68,y:node.y-9},node.name));
        group.append(document.createTextNode(' '));
        group.append(svgEl('text',{x:node.x-80,y:node.y+18,class:'sub'},detail));
        const select = () => {
          selected = node.id;
          if (failureMode.checked && node.id !== 'internet') {
            if (knockedOut.has(node.id)) knockedOut.delete(node.id);
            else knockedOut.add(node.id);
          }
          render();
          svg.querySelectorAll('[data-lab-node-id]').forEach(candidate=>{if(candidate.getAttribute('data-lab-node-id')===node.id)candidate.focus();});
        };
        group.addEventListener('click', select);
        group.addEventListener('keydown', event => {if(event.key==='Enter' || event.key===' '){event.preventDefault();select();}});
        svg.append(group);
      });
      const node = nodeMap.get(selected);
      inspectorName.textContent = node.name;
      description.textContent = (language === 'en' ? node.description_en : node.description_cs) || '';
      index.textContent = String(nodes.indexOf(node)+1).padStart(2,'0');
      tech.replaceChildren(...(node.tech||[]).map(label => { const e=document.createElement('span');e.textContent=label;return e; }));
      services.replaceChildren(...(node.services||[]).map(label => {const e=document.createElement('li');e.textContent=label;e.className=up(node.id)?'':'down';return e;}));
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
          li.textContent = `${i+1}. ${name}: ${description} (${state})`;
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

  function initContextWorkbench() {
    const root = $('[data-context-workbench]');
    if (!root) return;
    const form = $('[data-context-form]', root);
    form.addEventListener('submit', event => {
      event.preventDefault();
      const values = new FormData(form);
      const indexed = values.get('context-root') === 'indexed';
      const exact = values.get('context-question') === 'exact';
      const model = values.has('context-model');
      const steps = [];
      if (!indexed) steps.push(local('Jednou zaregistrovat stabilní absolutní kořen.', 'Register the stable absolute root once.'));
      if (!indexed) steps.push(local('Asynchronně připravit inventář, code index a deterministické karty.', 'Asynchronously prepare inventory, code index and deterministic cards.'));
      if (exact) {
        steps.push(local('Code indexem najít přesný symbol a jeho vztahy.', 'Find the exact symbol and its relationships in the code index.'));
        steps.push(local('Načíst pouze zdrojový výřez potřebný pro odpověď.', 'Load only the source slice needed for the answer.'));
      } else {
        steps.push(local('Začít levnějšími indexovanými zdroji a mapou repozitáře.', 'Start with cheaper indexed sources and the repository map.'));
        steps.push(local('Složit širší kontext z více dohledatelných důkazů.', 'Compose broader context from multiple traceable sources.'));
        steps.push(local('RAG přidat až tehdy, když index nestačí.', 'Add RAG only when indexed evidence is insufficient.'));
      }
      if (model) steps.push(local('Volitelně přidat lokální syntézu; návrh zůstává poradní.', 'Optionally add local synthesis; its output remains advisory.'));
      steps.push(local('Změnu ověřit příkazem spuštěným hostitelským prostředím.', 'Verify the change with a command run by the host environment.'));
      $('[data-context-title]', root).textContent = exact
        ? local(indexed ? 'Přesný dotaz, připravený index' : 'Nejdřív připravit stabilní kořen', indexed ? 'Exact query, prepared index' : 'Prepare a stable root first')
        : local(indexed ? 'Širší změna, dohledatelné zdroje' : 'Příprava před širším dotazem', indexed ? 'Broader change, traceable sources' : 'Prepare before a broad query');
      const list = $('[data-context-steps]', root);
      list.replaceChildren();
      for (const step of steps) {
        const item = document.createElement('li');
        item.textContent = step;
        list.append(item);
      }
      $('[data-context-boundary]', root).textContent = local(
        'Tento model ukazuje pořadí práce, ne běžící Hub. Hostitelský test a kontrola zdroje zůstávají rozhodující.',
        'This model shows the order of work, not a running Hub. Host tests and source review remain decisive.'
      );
    });
  }

  function initProjectExhibits() {
    const root = $('[data-project-exhibit]');
    if (!root) return;
    const kind = root.dataset.projectExhibit;
    if (kind === 'hub') {
      let mode = 'exact';
      const output = $('[data-hub-result]', root);
      $$('[data-hub-mode]', root).forEach(button => button.addEventListener('click', () => {
        mode = button.dataset.hubMode;
        $$('[data-hub-mode]', root).forEach(item => item.setAttribute('aria-pressed', String(item === button)));
      }));
      $('[data-hub-run]', root).addEventListener('click', () => {
        const question = $('[data-hub-question]', root).value.trim();
        output.replaceChildren();
        const title = document.createElement('strong');
        const detail = document.createElement('span');
        title.textContent = question || local('Zadej otázku k repozitáři.', 'Enter a repository question.');
        detail.textContent = mode === 'exact'
          ? local('Přesný tok: code index → zdrojový výřez → hostitelský test. Bez modelového odhadu.', 'Exact route: code index → source slice → host validation. No model guess.')
          : local('Širší tok: poskládat více ověřitelných zdrojů; RAG až po levnějších indexovaných cestách. Výstup stále ověřuje host.', 'Broader route: combine traceable sources; use RAG after cheaper indexed routes. The host still verifies the result.');
        output.append(title, detail);
      });
    } else if (kind === 'mutaui') {
      const output = $('[data-mutaui-result]', root), arrow = $('.mutaui-endpoints i', root);
      $$('[data-mutaui-mode]', root).forEach(button => button.addEventListener('click', () => {
        const forward = button.dataset.mutauiMode === 'forward';
        $$('[data-mutaui-mode]', root).forEach(item => item.setAttribute('aria-pressed', String(item === button)));
        arrow.textContent = forward ? '→' : '⇄';
        output.textContent = forward
          ? local('Forward: Mutagen předává provoz mezi dvěma endpointy. Nejde o kopírování souborů.', 'Forward: Mutagen forwards traffic between two endpoints. It is not file synchronization.')
          : local('Sync: Mutagen porovnává oba endpointy podle zvoleného režimu.', 'Sync: Mutagen compares both endpoints under the selected mode.');
      }));
    } else if (kind === 'homelab') {
      const roles = {
        router: ['Router', 'Router', 'Routing, firewall a WireGuard VPN. Zásahy do routeru jsou v playboocích zvlášť hlídané.', 'Routing, firewall and WireGuard VPN. Playbooks guard router changes separately.'],
        'main-pi': ['Hlavní Pi', 'Main Pi', 'Pi-hole, Unbound, Jellyfin, BookStack a Semaphore.', 'Pi-hole, Unbound, Jellyfin, BookStack and Semaphore.'],
        'print-pi': ['Tiskové Pi', 'Print Pi', 'CUPS a síťová tisková fronta. Samostatná role od hlavního hostu.', 'CUPS and the network print queue. A role separate from the primary host.'],
        nas: ['NAS', 'NAS', 'Úložiště a cíl záloh; provozní dostupnost se z mapy nečte.', 'Storage and backup target; runtime availability is not read by this map.'],
        proxmox: ['Proxmox', 'Proxmox', 'Testovací host zapínaný podle potřeby, ne stálá veřejná služba.', 'On-demand test host, not an always-on public service.'],
      };
      const detail = $('[data-host-detail]', root);
      $$('[data-host-role]', root).forEach(button => button.addEventListener('click', () => {
        const [cs, en, descCs, descEn] = roles[button.dataset.hostRole];
        $$('[data-host-role]', root).forEach(item => item.setAttribute('aria-pressed', String(item === button)));
        detail.querySelector('.eyebrow').textContent = `HOST ROLE / ${button.dataset.hostRole.toUpperCase()}`;
        detail.querySelector('h4').textContent = language === 'en' ? en : cs;
        detail.querySelector('p').textContent = language === 'en' ? descEn : descCs;
      }));
    } else if (kind === 'woodbound') {
      const map = $('.world-map', root), output = $('[data-world-result]', root);
      const copy = {
        terrain: local('Reliéf určuje, kde další fáze může umístit oblasti a průchody.', 'Terrain constrains where later stages can place regions and traversable paths.'),
        water: local('Voda musí navazovat na terén a povodí; je to pravidlo k validaci, ne dekorace.', 'Water must agree with terrain and watershed rules; it needs validation, not just decoration.'),
        regions: local('Regionální hranice mají dávat smysl vůči reliéfu a herním záměrům.', 'Region boundaries should fit terrain and game design intent.'),
        routes: local('Trasa má spojit určené body. Pathfinder ověří kandidáta, ne pocit z hezké mapy.', 'A route must connect its intended endpoints. The pathfinder checks a candidate, not whether the map looks pretty.'),
      };
      $$('[data-world-layer]', root).forEach(button => button.addEventListener('click', () => {
        const layer = button.dataset.worldLayer;
        map.dataset.layer = layer;
        output.textContent = copy[layer];
        $$('[data-world-layer]', root).forEach(item => item.setAttribute('aria-pressed', String(item === button)));
      }));
    } else if (kind === 'scan2cad') {
      $$('[data-scan-scale]', root).forEach(button => button.addEventListener('click', () => {
        const confirmed = button.dataset.scanScale === 'confirmed';
        $$('[data-scan-scale]', root).forEach(item => item.setAttribute('aria-pressed', String(item === button)));
        $('[data-scan-result]', root).textContent = confirmed
          ? local('Měřítko potvrzeno v modelu. Další krok: člověk kontroluje zarovnání a lokální odchylky; rozměry pořád nejsou certifikované.', 'Scale marked confirmed in this model. Next: a person reviews alignment and local deviation; dimensions are still not certified.')
          : local('STOP: jednotky nejsou potvrzené. Rekonstrukce nesmí hádat rozměr.', 'STOP: units are not confirmed. Reconstruction must not guess dimensions.');
      }));
    } else if (kind === 'tiskov') {
      const inbox = $('[data-print-inbox]', root), worker = $('[data-print-worker]', root), review = $('[data-print-review]', root);
      let next = 0;
      $('[data-print-next]', root).addEventListener('click', event => {
        if (next >= 2) return;
        const job = inbox.firstElementChild;
        job?.remove();
        next++;
        worker.textContent = local(`Case ${next === 1 ? 'A' : 'B'} · worker zpracoval dávku`, `Case ${next === 1 ? 'A' : 'B'} · worker processed the batch`);
        review.textContent = local('Výsledek čeká na lidskou kontrolu. Cena se nespočítala.', 'Result awaits human review. No price was calculated.');
        if (next === 2) event.currentTarget.disabled = true;
      });
    } else if (kind === 'shpotify') {
      let count = 0;
      $('[data-music-add]', root).addEventListener('click', () => {
        if (count >= 2) return;
        count++;
        const item = document.createElement('li');
        item.append(document.createTextNode(`0${count + 2} · ${count === 1 ? 'Paper Satellites' : 'Blue Hour'} `));
        const who = document.createElement('small');
        who.textContent = count === 1 ? 'Adam' : 'Eva';
        item.append(who);
        $('[data-music-queue]', root).append(item);
        $('[data-music-note]', root).textContent = local('Ukázková skladba přidána do lokální fronty; žádné audio se nepřehrává.', 'Sample track added to the local queue; no audio is played.');
      });
    } else if (kind === 'softwareinc') {
      const state = $('[data-agreement-state]', root), copy = $('[data-agreement-copy]', root);
      let step = 0;
      const render = () => {
        $$('li', state).forEach((item, index) => item.classList.toggle('active', index === step));
        copy.textContent = [
          local('Návrh změny existuje. Zatím nic neukládá do hry.', 'A change proposal exists. Nothing is written to the game yet.'),
          local('Druhý hráč návrh kontroluje. Doménové pravidlo se může zamítnout.', 'The other player reviews the proposal. A domain rule may reject it.'),
          local('Model dohody přijal návrh. To ještě nedokazuje aplikaci ve hře.', 'The agreement model accepted the proposal. This does not prove the game applied it.'),
          local('Adaptér hry je samostatná hranice. Herní přijetí zde není spuštěno ani ověřeno.', 'The game adapter is a separate boundary. In-game acceptance is not run or verified here.'),
        ][step];
      };
      $('[data-agreement-next]', root).addEventListener('click', () => { step = Math.min(3, step + 1); render(); });
      $('[data-agreement-reset]', root).addEventListener('click', () => { step = 0; render(); });
    } else if (kind === 'discord') {
      $('[data-discord-check]', root).addEventListener('click', () => {
        const role = $('[data-discord-role]', root).value, action = $('[data-discord-action]', root).value, allowed = role === 'operator';
        const output = $('[data-discord-result]', root);
        output.dataset.state = allowed ? 'allowed' : 'denied';
        output.textContent = allowed
          ? local(`Model: ${action} může pokračovat ke skutečnému backendu. Tady se nic nespustí.`, `Model: ${action} may proceed to a real backend. Nothing runs here.`)
          : local(`Odmítnuto: běžný člen nemá oprávnění k akci ${action}.`, `Denied: a regular member is not authorized for ${action}.`);
      });
    }
  }

  initContextWorkbench();
  initProjectExhibits();
})();
