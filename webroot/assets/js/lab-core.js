/* Curated home-network graph calculations; no network access or device telemetry. */
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

  const API = Object.freeze({affectedNodes,traceFlow});
  if(typeof module !== 'undefined' && typeof module.exports !== 'undefined') module.exports=API;
  else /** @type {typeof globalThis & {HarfushaLabCore?: typeof API}} */ (root).HarfushaLabCore=API;
})(globalThis);
