create table if not exists af_operations (
	op_id		smallint unsigned not null,
	op_name		char(64) not null,
	op_class	enum('business', 'infra', 'ui') not null default 'business',
	op_desc		varchar(255) not null,

	primary key	(op_id),
	index		(op_name)
) engine = InnoDB  default charset=utf8;

create table if not exists af_operation_role_assign (
	role_id		smallint unsigned not null,
	op_id		smallint unsigned not null,
	permission	ENUM('allow', 'deny') not null,
	
	primary key (role_id, op_id),
	foreign	key	(role_id) references af_roles (role_id),
	foreign key (op_id)   references af_operations(op_id)
) engine = InnoDB  default charset=utf8;
