-- list of all available intercepting filters. These filters are run by the 
-- framework before and after the action controller is executed
create table if not exists af_filters (
	filter_id		smallint unsigned not null,
	filter_key		char(64) not null,
	filter_type		enum('pre', 'post') not null,
	filter_desc		varchar(255) not null default '',

	primary key (filter_id),
	index (filter_key)
) engine = InnoDB default charset=utf8;

-- holds a list of all filters to be applied regardless of what operation route
-- the application is currently executing
create table if not exists af_global_filters (
	global_filter_id	tinyint unsigned not null,
	filter_id			smallint unsigned not null,

	primary key (global_filter_id),
	foreign key (filter_id) references af_filters (filter_id)
) engine = InnoDB default charset=utf8;

-- holds a list of filters specific to a particual operation route
create table if not exists af_route_filter_assign (
	route_id	smallint unsigned not null,
	filter_id	smallint unsigned not null,
	module_name	char(64) not null,

	primary key	(route_id, filter_id),
	foreign key (route_id)  references af_registered_routes (route_id),
	foreign key (filter_id) references af_filters (filter_id)
) engine = InnoDB  default charset=utf8;
