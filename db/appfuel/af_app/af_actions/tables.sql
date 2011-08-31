-- holds a list of action controller, submodule, modules and root namespace
-- for the frameworks actions
create table if not exists af_action_roots (
	root_id		tinyint unsigned not null,
	root_ns char(64) not null,

	primary key (root_id),
	index (root_ns)
) engine = InnoDB default charset=utf8;

create table if not exists af_action_modules (
	module_id	smallint unsigned not null,
	root_id		tinyint unsigned not null,
	module_name	char(64) not null,

	primary key	(module_id),
	index		(module_name),
	foreign key (root_id) references af_action_roots (root_id)
) engine = InnoDB  default charset=utf8;

create table if not exists af_action_submodules (
	submodule_id	smallint unsigned not null,
	module_id		smallint unsigned not null,
	submodule_name	char(64),

	primary key (submodule_id),
	foreign key (module_id)	references af_action_modules (module_id),
	index (submodule_name)
) engine = InnoDB  default charset=utf8;

create table if not exists af_action_controllers (
	controller_id	smallint unsigned not null,
	submodule_id	smallint unsigned not null,
	controller_name	char(64),
	builder_class	char(128) not null default 'Appfuel\\Framework\\Action\\ActionBuilder',
	is_forward		boolean not null default 0,

	primary key (controller_id),
	foreign key (submodule_id)   references af_action_submodules (submodule_id),
	index (controller_name)
) engine = InnoDB  default charset=utf8;
