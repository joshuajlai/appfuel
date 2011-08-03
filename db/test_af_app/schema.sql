-- [script-order 1]
-- test_af_app is copy of the appfuel database that allows us provide
-- tables used to test domains or other code that would otherwise pollute 
-- the appfuel database

drop database if exists test_af_app;

create database test_af_app default character set utf8;
use test_af_app;

drop user 'tester_af_user';
create user 'tester_af_user'@'%' identified by 'w3bg33k';
grant all on test_af_app.* to 'tester_af_user'@'%';

