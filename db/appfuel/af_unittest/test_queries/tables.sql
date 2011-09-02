create table if not exists test_queries (
	query_id	tinyint unsigned not null auto_increment,
	param_1		tinyint unsigned not null,
	param_2		char(10) not null,
	param_3		tinyint unsigned not null,
	result		varchar(50) not null,

	primary key(query_id),
	index (param_1),
	index (param_2),
	index (param_3)
)engine = InnoDB  default charset=utf8;
