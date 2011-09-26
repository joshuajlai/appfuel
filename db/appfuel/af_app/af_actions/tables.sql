create table if not exists af_apps (
	app_id					tinyint unsigned not null,
	app_ns					char(64) not null,
	action_root_ns			char(128) not null,
	action_class			char(64) not null default 'Controller',
	action_builder_class	char(64) not null default 'ActionBuilder',

	primary_key (app_id),
	index		(app_namespace),
	index		(action_namespace)
) engine = InnoDB default charset=utf8;

-- holds a list of action controller, submodule, modules and root namespace
-- for the frameworks actions
create table if not exists af_actions (
	action_id				smallint unsigned not null,
	app_id					tinyint unsigned not null,
	action_ns				char(255) not null,
	override_builder_class	char(128) not null default '',
	override_action_class	char(128) not null default '',,
	
	primary key (action_id),
	foreign key (app_id) references af_apps (app_id),
	index		(action_ns)
) engine = InnoDB default charset=utf8;
