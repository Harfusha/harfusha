/* Harfusha Lab: deterministic, dependency-free browser simulation primitives.
 * No network, storage, document access or user data. Shared with Node regression tests.
 */
(function (root) {
  'use strict';

  /** @param {Array<{id:string,depends:string[]}>} nodes @param {string[]} failedIds @returns {string[]} */
  function affectedNodes(nodes, failedIds) {
    const byId = new Map(nodes.map(n => [n.id, n]));
    if (byId.size !== nodes.length || nodes.some(n => !Array.isArray(n.depends) || n.depends.some(d => !byId.has(d)))) {
      throw new TypeError('Invalid dependency graph');
    }
    const explicit = new Set(failedIds);
    if ([...explicit].some(id => !byId.has(id))) throw new TypeError('Unknown failed node');
    /** @type {Map<string,boolean>} */
    const state = new Map();
    const visiting = new Set();
    /** @param {string} id @returns {boolean} */
    function down(id) {
      if (state.has(id)) return state.get(id) === true;
      if (visiting.has(id)) throw new TypeError('Cyclic graph');
      visiting.add(id);
      const node = byId.get(id);
      if(!node) throw new TypeError('Unknown graph node');
      const value = explicit.has(id) || node.depends.some(d => down(d));
      visiting.delete(id);
      state.set(id, value);
      return value;
    }
    return nodes.filter(n => down(n.id)).map(n => n.id);
  }

  /**
   * Curated logical route, NOT a packet capture or a remote network probe.
   * Every hop must correspond to an undirected link in the public dependency graph.
   * @typedef {{id:string,path:string[]}} NetworkFlow
   * @param {Array<{id:string,depends:string[]}>} nodes
   * @param {NetworkFlow} flow
   * @param {string[]} failedIds
   * @returns {{available:boolean,firstFailed:string|null,steps:Array<{id:string,available:boolean}>}}
   */
  function traceFlow(nodes, flow, failedIds) {
    if (!flow || typeof flow.id !== 'string' || !Array.isArray(flow.path) ||
        flow.path.length < 2 || flow.path.length > 16) throw new TypeError('Invalid flow');
    const byId = new Map(nodes.map(n => [n.id,n]));
    if (byId.size !== nodes.length) throw new TypeError('Duplicate graph node');
    for (let i=0; i<flow.path.length; i++) {
      const nodeId=flow.path[i];
      const node=byId.get(nodeId);
      if (!node || !Array.isArray(node.depends)) throw new TypeError('Unknown flow node');
      if (i===0) continue;
      const previous=byId.get(flow.path[i-1]);
      if (!previous || !Array.isArray(previous.depends) || nodeId === previous.id ||
          !(node.depends.includes(previous.id) || previous.depends.includes(nodeId))) {
        throw new TypeError('Flow crosses an unconnected link');
      }
    }
    const down = new Set(affectedNodes(nodes,failedIds));
    const steps = flow.path.map(id=>({id,available:!down.has(id)}));
    const firstFailed = steps.find(step=>!step.available)?.id ?? null;
    return {available:firstFailed===null,firstFailed,steps};
  }

  /** @param {number} x @param {number} y @param {number} seed @returns {number} */
  function hash(x, y, seed) {
    let z = (Math.imul(x, 374761393) ^ Math.imul(y, 668265263) ^ Math.imul(seed, 1442695041)) | 0;
    z = Math.imul(z ^ (z >>> 13), 1274126177);
    return ((z ^ (z >>> 16)) >>> 0) / 4294967295;
  }
  /** @param {number} x @param {number} y @param {number} seed @returns {number} */
  function noise(x, y, seed) {
    const xi = Math.floor(x), yi = Math.floor(y), fx = x - xi, fy = y - yi;
    /** @param {number} v @returns {number} */
    const ease = v => v * v * (3 - 2 * v), a = ease(fx), b = ease(fy);
    /** @param {number} iy @returns {number} */
    const row = iy => hash(xi, iy, seed) * (1 - a) + hash(xi + 1, iy, seed) * a;
    return row(yi) * (1 - b) + row(yi + 1) * b;
  }
  /** @param {number} x @param {number} y @param {number} seed @returns {number} */
  function terrain(x, y, seed) {
    const f1 = noise(x * .02, y * .02, seed), f2 = noise(x * .075, y * .075, seed + 7), f3 = noise(x * .23, y * .23, seed + 17);
    const nx = x / 160 - .5, ny = y / 100 - .5, edge = Math.sqrt(nx * nx + ny * ny);
    return .29 + f1 * .48 + f2 * .19 + f3 * .08 - edge * .30;
  }

  /** @typedef {{ alpha:string,beta:string,dirtyA:boolean,dirtyB:boolean,revA:number,revB:number }} SyncState */
  /** @param {SyncState} state @param {'sync'|'alpha'|'beta'} operation @returns {{ state:SyncState, outcome:string }} */
  function syncStep(state, operation) {
    const s = {...state};
    if (operation === 'alpha' || operation === 'beta') {
      if (operation === 'alpha') { s.beta=s.alpha; s.revB++; }
      else { s.alpha=s.beta; s.revA++; }
      s.dirtyA=s.dirtyB=false;
      return {state:s, outcome:operation === 'alpha'?'ALPHA → BETA':'BETA → ALPHA'};
    }
    if (operation !== 'sync') throw new TypeError('Unknown sync action');
    if (!s.dirtyA && !s.dirtyB) return {state:s,outcome:'unchanged'};
    if (s.dirtyA && s.dirtyB && s.alpha !== s.beta) return {state:s,outcome:'conflict'};
    if (s.dirtyA && !s.dirtyB) {s.beta=s.alpha; s.revB++;}
    else if (s.dirtyB && !s.dirtyA) {s.alpha=s.beta; s.revA++;}
    s.dirtyA=s.dirtyB=false;
    return {state:s,outcome:'synchronized'};
  }

  /** @typedef {[number,number,number]} Point */
  /** @typedef {[Point,Point,Point]} Triangle */
  /** @param {ArrayBuffer} buffer @param {number} [maxTriangles] @returns {Triangle[]} */
  function parseStl(buffer, maxTriangles = 30000) {
    if (!(buffer instanceof ArrayBuffer) || buffer.byteLength < 25 || buffer.byteLength > 8 * 1024 * 1024) throw new TypeError('File exceeds allowed bounds');
    /** @type {Triangle[]} */
    const triangles = [];
    const view = new DataView(buffer);
    const binary = buffer.byteLength >= 84 && 84 + view.getUint32(80, true) * 50 === buffer.byteLength;
    if (binary) {
      const count = view.getUint32(80, true);
      if (count === 0 || count > 500000) throw new TypeError('Unsupported STL triangle count');
      const stride = Math.max(1, Math.ceil(count / maxTriangles));
      for (let i = 0; i < count; i += stride) {
        let offset = 84 + i * 50 + 12;
        const point = () => {
          /** @type {Point} */ const v = [view.getFloat32(offset,true),view.getFloat32(offset+4,true),view.getFloat32(offset+8,true)];
          offset += 12;
          return v;
        };
        triangles.push([point(),point(),point()]);
      }
    } else {
      const text = new TextDecoder('utf-8', {fatal:true}).decode(buffer);
      const regexp = /vertex\s+([-\d.eE+]+)\s+([-\d.eE+]+)\s+([-\d.eE+]+)/g;
      // Two bounded passes avoid materializing every regex match for large
      // ASCII meshes. Only up to maxTriangles faces become JavaScript objects.
      let vertices = 0;
      while (regexp.exec(text) !== null) vertices++;
      if (vertices === 0 || vertices % 3 !== 0) throw new TypeError('Malformed ASCII STL');
      const stride = Math.max(1, Math.ceil(vertices / 3 / maxTriangles));
      regexp.lastIndex = 0;
      let index = 0;
      /** @type {Point[]} */
      let face = [];
      let match;
      while ((match = regexp.exec(text)) !== null) {
        const triangleIndex = Math.floor(index / 3);
        if (triangleIndex % stride === 0) {
          face.push([Number(match[1]), Number(match[2]), Number(match[3])]);
          if (face.length === 3) {
            triangles.push(/** @type {Triangle} */ ([face[0], face[1], face[2]]));
            face = [];
          }
        }
        index++;
      }
    }
    if (triangles.length === 0 || triangles.some(face => face.some(p => p.length !== 3 || p.some(n => !Number.isFinite(n))))) throw new TypeError('Invalid STL coordinates');
    return triangles;
  }

  /** @param {Triangle[]} faces @returns {Triangle[]} */
  function normalizeMesh(faces) {
    if (!Array.isArray(faces) || faces.length === 0) throw new TypeError('No mesh faces');
    const minimum=[Infinity,Infinity,Infinity], maximum=[-Infinity,-Infinity,-Infinity];
    for(const face of faces) for(const point of face) for(let axis=0;axis<3;axis++) {
      if(!Number.isFinite(point[axis])) throw new TypeError('Invalid coordinate');
      minimum[axis]=Math.min(minimum[axis],point[axis]);maximum[axis]=Math.max(maximum[axis],point[axis]);
    }
    const extent=Math.max(...minimum.map((v,i)=>maximum[i]-v));
    if (!Number.isFinite(extent) || extent <= 0) throw new TypeError('Degenerate mesh');
    const center=minimum.map((v,i)=>(v+maximum[i])/2), scale=2.5/extent;
    /** @param {Point} point @returns {Point} */
    const transform = point=> [
      (point[0]-center[0])*scale,
      (point[1]-center[1])*scale,
      (point[2]-center[2])*scale,
    ];
    return faces.map(face=>[transform(face[0]),transform(face[1]),transform(face[2])]);
  }

  /**
   * Educational model of a CakePHP request, not a running HTTP request or a
   * claim about internal middleware guarantees. Values are deterministic and
   * never leave the browser. Real security is enforced server-side by PHP.
   * @typedef {{path:'/cs/projects'|'/admin/project',method:'GET'|'POST',hostValid:boolean,csrfValid:boolean,authenticated:boolean,payloadValid:boolean}} RequestScenario
   * @typedef {{name:string,result:'pass'|'block'|'skip',detail:string}} TraceStep
   * @param {RequestScenario} scenario
   * @returns {{status:number,steps:TraceStep[]}}
   */
  function requestTrace(scenario) {
    if (!scenario || !['/cs/projects', '/admin/project'].includes(scenario.path) ||
        !['GET','POST'].includes(scenario.method) ||
        !['hostValid','csrfValid','authenticated','payloadValid'].every(key => typeof scenario[/** @type {keyof RequestScenario} */ (key)] === 'boolean')) {
      throw new TypeError('Invalid request scenario');
    }
    /** @type {TraceStep[]} */
    const steps = [];
    /** @param {string} name @param {'pass'|'block'|'skip'} result @param {string} detail */
    const push = (name,result,detail) => steps.push({name,result,detail});
    if (!scenario.hostValid) {
      push('HostGuard', 'block', 'Host does not match the configured canonical origin');
      return {status:400,steps};
    }
    push('HostGuard','pass','Origin accepted');
    const requiredMethod = scenario.path === '/admin/project' ? 'POST' : 'GET';
    if (scenario.method !== requiredMethod) {
      push('RoutingMiddleware','block',`Route exists, but it requires ${requiredMethod}`);
      return {status:405,steps};
    }
    push('RoutingMiddleware','pass',`${requiredMethod} ${scenario.path}`);
    if (scenario.method === 'POST') {
      if (!scenario.csrfValid) {
        push('CsrfProtectionMiddleware','block','Missing or invalid CSRF token');
        return {status:403,steps};
      }
      push('CsrfProtectionMiddleware','pass','CSRF token verified');
      if (!scenario.authenticated) {
        push('AdminController','block','Not authenticated');
        return {status:403,steps};
      }
      push('AdminController','pass','Authenticated session');
      if (!scenario.payloadValid) {
        push('EditorialInput','block','Invalid typed editorial payload');
        return {status:422,steps};
      }
      push('EditorialInput','pass','Typed payload accepted');
      push('CakePHP ORM','pass','Illustrative transactional save and audit receipt (not executed)');
      return {status:303,steps};
    }
    push('CsrfProtectionMiddleware','skip','Safe read-only GET');
    push('PagesController','pass','Collect reviewed project case studies');
    push('ProjectCatalog','pass','Build typed read-only project view');
    return {status:200,steps};
  }

  const API = Object.freeze({affectedNodes,traceFlow,hash,noise,terrain,syncStep,parseStl,normalizeMesh,requestTrace});
  if(typeof module !== 'undefined' && typeof module.exports !== 'undefined') module.exports=API;
  else /** @type {typeof globalThis & {HarfushaLabCore?: typeof API}} */ (root).HarfushaLabCore=API;
})(globalThis);
