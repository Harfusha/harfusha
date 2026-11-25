const test = require('node:test');
const assert = require('node:assert/strict');

test('concurrency transitions: lost update simulates data loss', () => {
  const initial = 100;
  const incA = 10;
  const incB = 10;

  // Read-modify-write without version check
  const readA = initial;
  const readB = initial;
  const writeA = readA + incA; // 110
  let db = writeA;
  const writeB = readB + incB; // 110 (overwrites A)
  db = writeB;

  assert.equal(db, 110);
  assert.notEqual(db, initial + incA + incB);
});

test('concurrency transitions: optimistic locking retries on conflict', () => {
  let dbVal = 100;
  let dbVer = 1;

  const clientAVal = dbVal;
  const clientAVer = dbVer;

  const clientBVal = dbVal;
  const clientBVer = dbVer;

  // Client A commits
  if (clientAVer === dbVer) {
    dbVal = clientAVal + 10;
    dbVer++;
  }
  assert.equal(dbVal, 110);
  assert.equal(dbVer, 2);

  // Client B attempts commit with stale ver 1 -> fails
  let clientBConflicts = 0;
  if (clientBVer !== dbVer) {
    clientBConflicts++;
  }
  assert.equal(clientBConflicts, 1);

  // Client B refetches and retries
  const freshVal = dbVal;
  const freshVer = dbVer;
  if (freshVer === dbVer) {
    dbVal = freshVal + 10;
    dbVer++;
  }

  assert.equal(dbVal, 120);
  assert.equal(dbVer, 3);
});

test('concurrency transitions: idempotency prevents duplicate mutations', () => {
  let dbVal = 100;
  const receipts = new Map();

  function handleRequest(key, increment) {
    if (receipts.has(key)) {
      return { status: 200, cached: true, val: receipts.get(key) };
    }
    dbVal += increment;
    receipts.set(key, dbVal);
    return { status: 200, cached: false, val: dbVal };
  }

  const res1 = handleRequest('req_001', 10);
  assert.equal(res1.cached, false);
  assert.equal(res1.val, 110);

  // Duplicate replay
  const res2 = handleRequest('req_001', 10);
  assert.equal(res2.cached, true);
  assert.equal(res2.val, 110);
  assert.equal(dbVal, 110);
});
