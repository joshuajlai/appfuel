-- The table is designed specificly to test appfuels lowlevel db code. It
-- allows the unit tests to have a standard place to run meaningless queries
-- that prove the database code is functioning at a basic level

drop table if exists test_queries;
create table test_queries (
	query_id	tinyint unsigned not null auto_increment,
	param_1		tinyint unsigned not null,
	param_2		char(10) not null,
	param_3		tinyint unsigned not null,
	result		varchar(50) not null,

	primary key	(query_id),
) engine = InnoDB  default charset=utf8 auto_increment=1;
