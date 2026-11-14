'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const core = require('../../webroot/assets/js/lab-core.js');
const root = path.resolve(__dirname, '../..');
const graph = JSON.parse(fs.readFileSync(path.join(root,'data/topology.json'),'utf8'));

test('network dependency propagation matches reviewed infrastructure',()=>{
  assert.deepEqual(core.affectedNodes(graph, []), []);
  assert.deepEqual(core.affectedNodes(graph, ['nas']), ['nas']);
  assert.equal(core.affectedNodes(graph, ['router']).length, 6);
  assert.deepEqual(core.affectedNodes(graph, ['internet']).map(String), graph.map(n=>n.id));
});
test('network failure set is deterministic and rejects malformed input',()=>{
  assert.deepEqual(core.affectedNodes(graph,['router','pi']),core.affectedNodes(graph,['pi','router']));
  assert.throws(()=>core.affectedNodes(graph,['unknown']));
  assert.throws(()=>core.affectedNodes([{id:'a',depends:['not-real']}],[]));
  assert.throws(()=>core.affectedNodes([{id:'a',depends:['b']},{id:'b',depends:['a']}],[]));
});
test('world generator deterministic and seed-sensitive',()=>{
  for(const [x,y,seed] of [[0,0,0],[128,64,32768],[500,10,9]]){
    const result=core.terrain(x,y,seed);
    assert.ok(Number.isFinite(result));
    assert.equal(result,core.terrain(x,y,seed));
  }
  assert.notEqual(core.hash(12,39,1),core.hash(12,39,2));
  assert.ok(core.noise(2.5,4.75,99)>=0 && core.noise(2.5,4.75,99)<=1);
});
const initial={alpha:'a',beta:'a',dirtyA:false,dirtyB:false,revA:1,revB:1};
test('sync no-op does not mutate state',()=>{
  assert.deepEqual(core.syncStep(initial,'sync'),{state:initial,outcome:'unchanged'});
  assert.deepEqual(initial,{alpha:'a',beta:'a',dirtyA:false,dirtyB:false,revA:1,revB:1});
});
test('sync handles unilateral alpha/beta edits and versions',()=>{
  assert.deepEqual(core.syncStep({...initial,alpha:'new',dirtyA:true},'sync'),{state:{alpha:'new',beta:'new',dirtyA:false,dirtyB:false,revA:1,revB:2},outcome:'synchronized'});
  assert.deepEqual(core.syncStep({...initial,beta:'remote',dirtyB:true},'sync'),{state:{alpha:'remote',beta:'remote',dirtyA:false,dirtyB:false,revA:2,revB:1},outcome:'synchronized'});
});
test('sync conflicts preserve both edits until manual resolution',()=>{
  const conflict={...initial,alpha:'local',beta:'remote',dirtyA:true,dirtyB:true};
  assert.deepEqual(core.syncStep(conflict,'sync'),{state:conflict,outcome:'conflict'});
  assert.deepEqual(core.syncStep(conflict,'alpha'),{state:{...conflict,beta:'local',dirtyA:false,dirtyB:false,revB:2},outcome:'ALPHA → BETA'});
  assert.deepEqual(core.syncStep(conflict,'beta'),{state:{...conflict,alpha:'remote',dirtyA:false,dirtyB:false,revA:2},outcome:'BETA → ALPHA'});
  assert.throws(()=>core.syncStep(initial,'invalid'));
});
test('identical concurrent edits do not conflict',()=>{
  const result=core.syncStep({...initial,alpha:'new',beta:'new',dirtyA:true,dirtyB:true},'sync');
  assert.equal(result.outcome,'synchronized');
  assert.equal(result.state.dirtyA,false);
  assert.equal(result.state.dirtyB,false);
});
const ascii=`solid demo\nfacet normal 0 0 1\n outer loop\n vertex 0 0 0\n vertex 1 0 0\n vertex 0 1 0\n endloop\nendfacet\nendsolid demo\n`;
test('ASCII STL parser loads valid triangle and normalizes coordinates',()=>{
  const bytes=Buffer.from(ascii);
  const buffer=bytes.buffer.slice(bytes.byteOffset,bytes.byteOffset+bytes.byteLength);
  const faces=core.parseStl(buffer);
  assert.deepEqual(faces,[[[0,0,0],[1,0,0],[0,1,0]]]);
  const centered=core.normalizeMesh(faces);
  assert.equal(centered.length,1);
  assert.ok(centered.flat(2).every(Number.isFinite));
});
test('binary STL parser handles file regardless of leading solid header',()=>{
  const buf=new ArrayBuffer(84+50);const bytes=new Uint8Array(buf);
  bytes.set(Buffer.from('solid suspicious header'));
  const view=new DataView(buf);view.setUint32(80,1,true);
  const vertices=[0,0,0,1,0,0,0,1,0];
  vertices.forEach((n,i)=>view.setFloat32(84+12+i*4,n,true));
  assert.deepEqual(core.parseStl(buf),[[[0,0,0],[1,0,0],[0,1,0]]]);
});
test('STL importer fails closed for malformed geometry',()=>{
  assert.throws(()=>core.parseStl(new ArrayBuffer(0)));
  assert.throws(()=>core.parseStl(new ArrayBuffer(9*1024*1024)));
  assert.throws(()=>core.parseStl(new TextEncoder().encode('vertex 0 0 0').buffer));
  assert.throws(()=>core.normalizeMesh([[[0,0,0],[0,0,0],[0,0,0]]]));
  assert.throws(()=>core.normalizeMesh([[[NaN,0,0],[1,0,0],[0,1,0]]]));
});

test('CakePHP educational request pipeline: safe read and fail-closed middleware',()=>{
  const valid={path:'/cs/projects',method:'GET',hostValid:true,csrfValid:true,authenticated:true,payloadValid:true};
  const ok=core.requestTrace(valid);
  assert.equal(ok.status,200);
  assert.equal(ok.steps[2].result,'skip');
  assert.equal(core.requestTrace({...valid,hostValid:false}).status,400);
  assert.equal(core.requestTrace({...valid,method:'POST'}).status,405);
  assert.throws(()=>core.requestTrace({...valid,path:'/private/secrets'}));
  assert.throws(()=>core.requestTrace({...valid,csrfValid:'yes'}));
});

test('CakePHP educational request pipeline: CSRF, session and typed payload boundaries',()=>{
  const valid={path:'/admin/project',method:'POST',hostValid:true,csrfValid:true,authenticated:true,payloadValid:true};
  assert.equal(core.requestTrace(valid).status,303);
  assert.equal(core.requestTrace({...valid,csrfValid:false}).status,403);
  assert.equal(core.requestTrace({...valid,authenticated:false}).status,403);
  assert.equal(core.requestTrace({...valid,payloadValid:false}).status,422);
  assert.equal(core.requestTrace({...valid,method:'GET'}).status,405);
  assert.equal(core.requestTrace({...valid,payloadValid:false}).steps.at(-1).name,'EditorialInput');
});


test('large ASCII STL uses bounded sampling instead of storing all regex matches',()=>{
  const facet='facet normal 0 0 1\n outer loop\n vertex 0 0 0\n vertex 1 0 0\n vertex 0 1 0\n endloop\nendfacet\n';
  const content='solid many\n'+facet.repeat(1250)+'endsolid many\n';
  const bytes=new TextEncoder().encode(content);
  const mesh=core.parseStl(bytes.buffer,100);
  assert.equal(mesh.length,Math.ceil(1250/Math.ceil(1250/100)));
  assert.ok(mesh.every(face=>face.length===3));
});

test('request paths traverse only actual model links',()=>{
  const flows=JSON.parse(fs.readFileSync(path.join(root,'data/flows.json'),'utf8'));
  assert.equal(flows.length,4);
  for (const flow of flows) {
    const trace=core.traceFlow(graph,flow,[]);
    assert.equal(trace.available,true);
    assert.equal(trace.firstFailed,null);
    assert.equal(trace.steps.length,flow.path.length);
  }
});
test('request paths propagate simulated outages and reject invalid edges',()=>{
  const flows=JSON.parse(fs.readFileSync(path.join(root,'data/flows.json'),'utf8'));
  const video=flows.find(f=>f.id==='media');
  assert.equal(core.traceFlow(graph,video,['nas']).available,false);
  assert.equal(core.traceFlow(graph,video,['nas']).firstFailed,'nas');
  assert.equal(core.traceFlow(graph,video,['router']).firstFailed,'clients');
  assert.equal(core.traceFlow(graph,flows.find(f=>f.id==='printing'),['nas']).available,true);
  assert.throws(()=>core.traceFlow(graph,{id:'invalid',path:['clients','nas']},[]));
  assert.throws(()=>core.traceFlow(graph,{id:'invalid',path:['none','router']},[]));
  assert.throws(()=>core.traceFlow(graph,video,['nonexistent']));
});
