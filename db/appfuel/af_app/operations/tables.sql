drop table if exists operations;
create table operations (
	op_id		smallint unsigned not null,
	op_name		char(64) not null,
	op_class	enum('business', 'infra', 'ui') not null default 'business',
	op_desc		varchar(255) not null,

	primary key	(op_id),
	index		(op_name)
) engine = InnoDB  default charset=utf8;
