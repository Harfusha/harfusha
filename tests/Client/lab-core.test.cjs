'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const core = require('../../webroot/assets/js/lab-core.js');
const root = path.resolve(__dirname, '../..');
const graph = JSON.parse(fs.readFileSync(path.join(root, 'data/topology.json'), 'utf8'));
const flows = JSON.parse(fs.readFileSync(path.join(root, 'data/flows.json'), 'utf8'));

test('outage propagation follows the curated network dependencies', () => {
  assert.deepEqual(core.affectedNodes(graph, []), []);
  assert.deepEqual(core.affectedNodes(graph, ['nas']), ['nas']);
  assert.deepEqual(core.affectedNodes(graph, ['nas', 'print']).sort(), ['nas', 'print'].sort());
  assert.deepEqual(core.affectedNodes(graph, ['pi', 'nas']).sort(), ['nas', 'pi'].sort());
  assert.equal(core.affectedNodes(graph, ['router']).length, 6);
  assert.deepEqual(core.affectedNodes(graph, ['internet']).map(String), graph.map(node => node.id));
  assert.throws(() => core.affectedNodes(graph, ['unknown']));
  assert.throws(() => core.affectedNodes([{ id: 'a', depends: ['b'] }, { id: 'b', depends: ['a'] }], []));
});

test('curated data paths use graph links and report the first unavailable hop', () => {
  for (const flow of flows) {
    const trace = core.traceFlow(graph, flow, []);
    assert.equal(trace.available, true);
    assert.equal(trace.firstFailed, null);
    assert.equal(trace.steps.length, flow.path.length);
  }
  const media = flows.find(flow => flow.id === 'media');
  assert.equal(core.traceFlow(graph, media, ['nas']).firstFailed, 'nas');
  assert.equal(core.traceFlow(graph, media, ['router']).firstFailed, 'clients');
  assert.equal(core.traceFlow(graph, flows.find(flow => flow.id === 'printing'), ['nas']).available, true);
  assert.throws(() => core.traceFlow(graph, { id: 'invalid', path: ['clients', 'nas'] }, []));
  assert.throws(() => core.traceFlow(graph, { id: 'invalid', path: ['none', 'router'] }, []));
});
