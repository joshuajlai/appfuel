-- start a transaction
BEGIN;

-- Plan the tests.
SELECT tap.plan(1);

-- Run the tests
SELECT tap.pass('My test passed, w00t!');

-- Finsih the tests and clean up
CALL tap.finish();

ROLLBACK;
