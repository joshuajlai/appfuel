-- tables used to test a programming languages ability to perform queries
-- the table is designed to have a known structure that unit test can 
-- rely on in order to make assertions
drop table if exists test_queries;
create table test_queries (
	query_id	tinyint unsigned not null,
	param_1		boolean  not null,
	param_2		char(10) not null,
	param_3     tinyint  not null,
	result		varchar(50) not null,

	primary key	(query_id)
) engine=InnoDB charset=utf8 collate=utf8_unicode_ci;
