-- holds a list of action controller, submodule, modules and root namespace
-- for the frameworks actions
drop table if exists action_controllers;
drop table if exists action_submodules;
drop table if exists action_modules;

create table action_modules (
	module_id	smallint unsigned not null,
	module_name	char(64) not null,

	primary key	(module_id),
	index		(module_name)
) engine = InnoDB  default charset=utf8;

create table action_submodules (
	submodule_id	smallint unsigned not null,
	module_id		smallint unsigned not null,
	submodule_name	char(64),

	primary key (submodule_id),
	foreign key (module_id)   references action_modules (module_id),
	index (submodule_name)
) engine = InnoDB  default charset=utf8;

create table action_controllers (
	controller_id	smallint unsigned not null,
	submodule_id	smallint unsigned not null,
	controller_name	char(64),

	primary key (submodule_id),
	foreign key (submodule_id)   references action_submodules (submodule_id),
	index (controller_name)
) engine = InnoDB  default charset=utf8;

