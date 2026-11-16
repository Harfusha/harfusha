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

  function initTextDiff() {
    const root = $('[data-diff-tool]');
    if (!root) return;
    const before = $('[data-diff-before]', root), after = $('[data-diff-after]', root);
    const result = $('[data-diff-result]', root);
    $('[data-diff-run]', root).addEventListener('click', () => {
      const lines = value => value === '' ? [] : value.replace(/\r\n?/g, '\n').split('\n');
      const a = lines(before.value), b = lines(after.value);
      if (a.length > 200 || b.length > 200) {
        result.textContent = local('Limit je 200 řádků na každé straně.', 'The limit is 200 lines per side.');
        return;
      }
      const dp = Array.from({length:a.length + 1}, () => new Uint16Array(b.length + 1));
      for (let i = a.length - 1; i >= 0; i--) {
        for (let j = b.length - 1; j >= 0; j--) {
          dp[i][j] = a[i] === b[j] ? dp[i + 1][j + 1] + 1 : Math.max(dp[i + 1][j], dp[i][j + 1]);
        }
      }
      const output = []; let i = 0, j = 0, changes = 0;
      while (i < a.length || j < b.length) {
        if (i < a.length && j < b.length && a[i] === b[j]) { output.push('  ' + a[i]); i++; j++; }
        else if (j < b.length && (i === a.length || dp[i][j + 1] > dp[i + 1][j])) { output.push('+ ' + b[j++]); changes++; }
        else { output.push('- ' + a[i++]); changes++; }
      }
      result.textContent = changes ? output.join('\n') : local('Texty jsou shodné.', 'The texts are identical.');
    });
    $('[data-diff-run]').click();
  }
  initTextDiff();

  function initPlayground() {
    const root = $('[data-playground]');
    if (!root) return;
    const tabs = $$('[data-demo-tab]',root), panels = $$('[data-demo-panel]',root);
    const valid = new Set(['ai','sync','world','cad','request']);
    const show = (id, updateHash) => {
      if (!valid.has(id)) id='ai';
      tabs.forEach(t=>{t.setAttribute('aria-selected',String(t.dataset.demoTab===id));t.tabIndex=t.dataset.demoTab===id?0:-1;});
      panels.forEach(p=>p.classList.toggle('hidden',p.dataset.demoPanel!==id));
      if (updateHash) history.replaceState(null,'',`#${id}`);
      if (id==='world') renderWorld();
      if (id==='cad') renderCad();
    };
    tabs.forEach((t,i)=>{
      t.addEventListener('click',()=>show(t.dataset.demoTab,true));
      t.addEventListener('keydown',event=>{
        let next=i;
        if(event.key==='ArrowRight')next=(i+1)%tabs.length;
        else if(event.key==='ArrowLeft')next=(i-1+tabs.length)%tabs.length;
        else if(event.key==='Home')next=0;
        else if(event.key==='End')next=tabs.length-1;
        else return;
        event.preventDefault();
        show(tabs[next].dataset.demoTab,true);
        tabs[next].focus();
      });
    });
    window.addEventListener('hashchange',()=>show(location.hash.slice(1),false));
    show(location.hash.slice(1) || 'ai',false);
    initAi(root);
    initSync(root);
    initWorld(root);
    initCad(root);
    initRequest(root);
  }

  function initAi(root) {
    const area = $('[data-ai-steps]',root);
    if (!area) return;
    const selector = $('[data-ai-scenario]',root), note = $('[data-ai-scenario-note]',root);
    const detail = $('[data-ai-title]',root), desc = $('[data-ai-description]',root), code = $('[data-ai-code]',root), index = $('[data-ai-index]',root);
    let current = 0, active = AI_SCENARIOS[selector?.value] || AI_SCENARIOS['first-use'], buttons = [];
    const draw = () => {
      const step=active.steps[current];
      detail.textContent=language==='en'?step.en:step.cs;
      desc.textContent=language==='en'?step.descEn:step.descCs;
      code.textContent=step.trace;
      index.textContent=`${String(current+1).padStart(2,'0')}/${String(active.steps.length).padStart(2,'0')}`;
      note.textContent=language==='en'?active.noteEn:active.noteCs;
      const progress=current/Math.max(active.steps.length-1,1);
      area.style.setProperty('--ai-progress',`${Math.round(progress*100)}%`);
      area.style.setProperty('--ai-progress-ratio',String(progress));
      buttons.forEach((b,i)=>{b.classList.toggle('complete',i<current);b.classList.toggle('active',i===current);b.classList.toggle('pending',i>current);b.setAttribute('aria-current',i===current?'step':'false');});
      $('[data-ai-prev]',root).disabled=current===0;
      $('[data-ai-next]',root).disabled=current===active.steps.length-1;
    };
    const load = () => {
      active=AI_SCENARIOS[selector.value] || AI_SCENARIOS['first-use'];
      current=0;
      area.replaceChildren();
      buttons=active.steps.map((step,i)=>{
        const btn=document.createElement('button');btn.className='ai-step';btn.type='button';
        const num=document.createElement('span');num.textContent=String(i+1).padStart(2,'0');
        const title=document.createElement('strong');title.textContent=language==='en'?step.en:step.cs;
        btn.append(num,title);
        btn.addEventListener('click',()=>{current=i;draw();});
        area.append(btn);
        return btn;
      });
      draw();
    };
    selector.addEventListener('change',load);
    $('[data-ai-prev]',root).addEventListener('click',()=>{current=Math.max(0,current-1);draw();});
    $('[data-ai-next]',root).addEventListener('click',()=>{current=Math.min(active.steps.length-1,current+1);draw();});
    $('[data-ai-reset]',root).addEventListener('click',()=>{current=0;draw();});
    load();
  }

  function initSync(root) {
    const alpha = $('[data-sync-alpha]',root), beta = $('[data-sync-beta]',root), log=$('[data-sync-log]',root);
    if (!alpha || !beta) return;
    const initial='{"theme":"dark","version":1}';
    let revA=1,revB=1,dirtyA=false,dirtyB=false;
    const push = message => {
      const li=document.createElement('li');li.textContent=message;
      log.prepend(li);
      while(log.children.length>8)log.lastChild.remove();
    };
    const paint = () => {
      $('[data-alpha-version]',root).textContent=`rev ${revA}${dirtyA?' · modified':''}`;
      $('[data-beta-version]',root).textContent=`rev ${revB}${dirtyB?' · modified':''}`;
    };
    alpha.addEventListener('input',()=>{dirtyA=true;paint();});
    beta.addEventListener('input',()=>{dirtyB=true;paint();});
    const transition = operation => {
      const outcome = core.syncStep({alpha:alpha.value,beta:beta.value,dirtyA,dirtyB,revA,revB},operation);
      const next = outcome.state;
      alpha.value=next.alpha;beta.value=next.beta;dirtyA=next.dirtyA;dirtyB=next.dirtyB;revA=next.revA;revB=next.revB;
      const messages = {unchanged:local('Žádné změny ke zpracování.','No changes to sync.'),
        conflict:local('Konflikt: obě strany byly změněny. Vyber vítěznou kopii.','Conflict: both sides changed. Choose the winning copy.'),
        synchronized:local('Změny byly synchronizovány.','Changes synchronized.')};
      push(messages[outcome.outcome]||`${outcome.outcome}: manual resolution`);
      paint();
    };
    $('[data-sync-run]',root).addEventListener('click',()=>transition('sync'));
    $$('[data-sync-resolve]',root).forEach(btn=>btn.addEventListener('click',()=>transition(btn.dataset.syncResolve)));
    $('[data-sync-reset]',root).addEventListener('click',()=>{alpha.value=beta.value=initial;revA=revB=1;dirtyA=dirtyB=false;log.replaceChildren();push('Sandbox reset.');paint();});
    push('Demo initialized: synchronized.');paint();
  }

  let renderWorld = () => {};
  function initWorld(root) {
    const canvas = $('[data-world-canvas]',root);
    if (!canvas) return;
    const seedInput=$('[data-world-seed]',root),density=$('[data-world-density]',root),relief=$('[data-world-relief]',root);
    const ctx=canvas.getContext('2d',{alpha:false});
    if(!ctx)return;
    const hash = core.hash;
    const terrain = core.terrain;
    renderWorld=()=>{
      if($('#world')?.classList.contains('hidden'))return;
      const seed=Math.max(0,Math.min(99999999,Number(seedInput.value)||0))|0;
      const trees=Number(density.value)/100,elevation=Number(relief.value)/100;
      $('[data-density-value]',root).textContent=`${density.value}%`;
      $('[data-relief-value]',root).textContent=`${relief.value}%`;
      const w=canvas.width,h=canvas.height;ctx.fillStyle='#10242a';ctx.fillRect(0,0,w,h);
      const cell=5,cols=Math.ceil(w/cell),rows=Math.ceil(h/cell);
      for(let y=0;y<rows;y++)for(let x=0;x<cols;x++){
        const z=terrain(x,y,seed),n=hash(x,y,seed+92);
        let fill;
        if(z<.36){fill=n>.6?'#164554':'#1a5160';}
        else if(z<.42){fill='#b7a87c';}
        else if(z>.64+(.44-elevation)*.19){fill=n>.65?'#a4b6ac':'#7f948e';}
        else if(n<trees*(z>.48?.57:.30)){fill=n<trees*.15?'#215d40':'#286a47';}
        else fill=z>.52?'#779473':'#8faa74';
        ctx.fillStyle=fill;ctx.fillRect(x*cell,y*cell,cell+1,cell+1);
      }
      ctx.strokeStyle='#d7ba8b';ctx.lineWidth=3;ctx.setLineDash([8,5]);ctx.beginPath();
      for(let x=80;x<w-60;x+=6){const y=235+Math.sin((x+seed%600)/95)*80+Math.sin(x/47)*14;if(x===80)ctx.moveTo(x,y);else ctx.lineTo(x,y);}ctx.stroke();ctx.setLineDash([]);
      ctx.fillStyle='rgba(10,28,32,.72)';ctx.fillRect(16,16,205,38);
      ctx.fillStyle='#ddedeb';ctx.font='13px monospace';ctx.fillText(`SEED ${seed} · TOY TERRAIN`,28,40);
    };
    $('[data-world-generate]',root).addEventListener('click',renderWorld);
    [density,relief,seedInput].forEach(e=>e.addEventListener('input',renderWorld));
    renderWorld();
  }

  let renderCad=()=>{};
  function initCad(root) {
    const canvas=$('[data-cad-canvas]',root),file=$('[data-cad-file]',root),wire=$('[data-cad-wire]',root);
    if(!canvas)return;
    const ctx=canvas.getContext('2d',{alpha:false});if(!ctx)return;
    const sample=()=>{
      const V=[[-1,-.8,-1],[1,-.8,-1],[1,.8,-1],[-1,.8,-1],[-1,-.8,1],[1,-.8,1],[1,.8,1],[-1,.8,1]];
      const F=[[0,2,1],[0,3,2],[4,5,6],[4,6,7],[0,1,5],[0,5,4],[3,7,6],[3,6,2],[1,2,6],[1,6,5],[0,4,7],[0,7,3]];
      const base=F.map(([a,b,c])=>[V[a],V[b],V[c]]);
      const n=24;
      for(let i=0;i<n;i++){
        const a=i*Math.PI*2/n,b=(i+1)*Math.PI*2/n;
        const p=[.35*Math.cos(a),.81,.35*Math.sin(a)],q=[.35*Math.cos(b),.81,.35*Math.sin(b)],v=[0,1.45,0];
        base.push([p,q,v]);
      }
      return base;
    };
    let mesh=sample(),ax=-.3,ay=.65,zoom=1,drag=false,previous=[0,0],loadGeneration=0,renderQueued=false;
    const normMesh = core.normalizeMesh;
    mesh=normMesh(mesh);
    renderCad=()=>{
      if($('#cad')?.classList.contains('hidden'))return;
      const w=canvas.width,h=canvas.height;ctx.fillStyle='#0c1d25';ctx.fillRect(0,0,w,h);
      ctx.strokeStyle='rgba(83,139,141,.11)';ctx.lineWidth=1;
      for(let i=0;i<w;i+=37){ctx.beginPath();ctx.moveTo(i,0);ctx.lineTo(i,h);ctx.stroke();}
      for(let i=0;i<h;i+=37){ctx.beginPath();ctx.moveTo(0,i);ctx.lineTo(w,i);ctx.stroke();}
      const sinY=Math.sin(ay),cosY=Math.cos(ay),sinX=Math.sin(ax),cosX=Math.cos(ax);
      const project=p=>{const x=p[0]*cosY+p[2]*sinY,z=-p[0]*sinY+p[2]*cosY,y=p[1]*cosX-z*sinX,depth=p[1]*sinX+z*cosX;return [w/2+x*125*zoom,h/2-y*125*zoom,depth];};
      const tris=mesh.map(t=>t.map(project)).map(points=>({points,depth:points.reduce((a,p)=>a+p[2],0)/3})).sort((a,b)=>a.depth-b.depth);
      for(const {points} of tris){
        const [a,b,c]=points;
        const cross=(b[0]-a[0])*(c[1]-a[1])-(b[1]-a[1])*(c[0]-a[0]);
        if(!wire.checked && cross>0)continue;
        const shade=Math.min(1,Math.abs(cross)/50000);
        ctx.beginPath();ctx.moveTo(a[0],a[1]);ctx.lineTo(b[0],b[1]);ctx.lineTo(c[0],c[1]);ctx.closePath();
        if(!wire.checked){ctx.fillStyle=`rgba(44, ${Math.round(126+shade*80)}, ${Math.round(140+shade*56)}, 0.87)`;ctx.fill();}
        ctx.strokeStyle=wire.checked?'rgba(103,238,194,.55)':'rgba(163,247,225,.3)';ctx.lineWidth=wire.checked?1.1:.5;ctx.stroke();
      }
    };
    // Render at most once per animation frame even if pointer events arrive faster.
    const queueCadRender=()=>{
      if(renderQueued)return;
      renderQueued=true;
      requestAnimationFrame(()=>{renderQueued=false;renderCad();});
    };
    const info=$('[data-cad-info]',root);
    const setInfo=msg=>info.textContent=msg;
    const loadStl = async input => {
      if(input.size > 8 * 1024 * 1024) throw new Error('File exceeds 8 MB');
      // A bounded display sample keeps rotation responsive even for large STL files.
      return normMesh(core.parseStl(await input.arrayBuffer(),6000));
    };
    file.addEventListener('change',async()=>{
      const input=file.files?.[0];
      if(!input)return;
      const generation=++loadGeneration;
      setInfo(local('Načítám STL…','Loading STL…'));
      try{
        const nextMesh=await loadStl(input);
        if(generation!==loadGeneration)return;
        mesh=nextMesh;ax=-.3;ay=.65;zoom=1;
        setInfo(`${mesh.length.toLocaleString()} displayed triangles · local STL sample`);
        queueCadRender();
      }catch(e){
        if(generation===loadGeneration)setInfo(`Cannot open STL: ${e instanceof Error?e.message:'Invalid file'}`);
      }
    });
    wire.addEventListener('change',queueCadRender);
    $('[data-cad-reset]',root).addEventListener('click',()=>{loadGeneration++;mesh=normMesh(sample());ax=-.3;ay=.65;zoom=1;file.value='';setInfo(`${mesh.length} triangles · sample geometry`);queueCadRender();});
    canvas.addEventListener('pointerdown',e=>{drag=true;previous=[e.clientX,e.clientY];canvas.setPointerCapture(e.pointerId);});
    canvas.addEventListener('pointermove',e=>{if(!drag)return;ay+=(e.clientX-previous[0])*.009;ax+=(e.clientY-previous[1])*.009;ax=Math.max(-1.5,Math.min(1.5,ax));previous=[e.clientX,e.clientY];queueCadRender();});
    canvas.addEventListener('pointerup',()=>{drag=false;});
    canvas.addEventListener('pointercancel',()=>{drag=false;});
    canvas.addEventListener('keydown',e=>{
      if(e.key==='ArrowLeft')ay-=.15;
      else if(e.key==='ArrowRight')ay+=.15;
      else if(e.key==='ArrowUp')ax=Math.max(-1.5,ax-.15);
      else if(e.key==='ArrowDown')ax=Math.min(1.5,ax+.15);
      else if(e.key==='+'||e.key==='=')zoom=Math.min(3,zoom*1.12);
      else if(e.key==='-'||e.key==='_')zoom=Math.max(.28,zoom*.88);
      else return;
      e.preventDefault();queueCadRender();
    });
    canvas.addEventListener('wheel',e=>{e.preventDefault();zoom=Math.max(.28,Math.min(3,zoom*(e.deltaY>0?.88:1.12)));queueCadRender();},{passive:false});
    setInfo(`${mesh.length} triangles · sample geometry`);
    renderCad();
  }

  function initRequest(root) {
    const panel=$('[data-request-lab]',root);
    if(!panel || !core?.requestTrace) return;
    const route=$('[data-request-route]',panel), method=$('[data-request-method]',panel);
    const host=$('[data-request-host]',panel), csrf=$('[data-request-csrf]',panel);
    const auth=$('[data-request-auth]',panel), payload=$('[data-request-payload]',panel);
    const response=$('[data-request-response]',panel), steps=$('[data-request-steps]',panel);
    const render=()=>{
      const trace=core.requestTrace({path:route.value,method:method.value,hostValid:host.checked,
        csrfValid:csrf.checked,authenticated:auth.checked,payloadValid:payload.checked}, language);
      response.textContent=`HTTP ${trace.status} · ${({200:'OK',303:'See Other',400:'Bad Request',403:'Forbidden',405:'Method Not Allowed',422:'Unprocessable Content'})[trace.status] || 'Result'}`;
      response.dataset.status=String(trace.status);
      steps.replaceChildren();
      for(const step of trace.steps){
        const li=document.createElement('li');li.className=`request-step request-${step.result}`;
        const name=document.createElement('strong');name.textContent=step.name;
        const detail=document.createElement('span');detail.textContent=step.detail;
        const badge=document.createElement('b');badge.textContent=language === 'cs' ? ({pass:'PROŠLO',block:'ZASTAVENO',skip:'PŘESKOČENO'}[step.result]) : step.result.toUpperCase();
        li.append(name,detail,badge);steps.append(li);
      }
      const admin=route.value==='/admin/project';
      $$('[data-request-admin-only]',panel).forEach(element=>{element.classList.toggle('request-muted',!admin);});
    };
    $$('input,select',panel).forEach(input=>input.addEventListener('change',render));
    $('[data-request-reset]',panel).addEventListener('click',()=>{
      route.value='/cs/projects';method.value='GET';
      host.checked=csrf.checked=auth.checked=payload.checked=true;render();
    });
    render();
  }

  initPlayground();
})();
