-- a registered route binds what the user wants to do (the operation)
-- and the code that actually preforms that operation (the controller)
-- because of this binding the framework knows how to preform any request
-- operation by the user. The route_string is responsible for decoupling 
-- the knowledge of the mvc system in the url. All routes are referred to in
-- the url or cli params by the route string.
create table if not exists af_registered_routes (
	route_id		smallint unsigned not null,
	controller_id	smallint unsigned not null,
	op_id			smallint unsigned not null,
	route_string	char(255) not null,
	default_format	char(25) not null,
	access_policy	enum('public', 'private') not null default 'private',
	request_type	enum('html', 'ajax', 'cli') not null default 'ajax',

	primary key (route_id),
	foreign key (controller_id) references af_action_controllers(controller_id),
	foreign key (op_id) references af_operations (op_id)
) engine = InnoDB  default charset=utf8;

-- table used to hold a list of routes an particular route has access to
create table if not exists af_available_routes (
	parent_route	smallint unsigned not null,
	available_route	smallint unsigned not null,

	primary key (parent_route, available_route),
	foreign key (parent_route)	  references af_registered_routes (route_id),
	foreign key (available_route) references af_registered_routes (route_id)
) engine = InnoDB  default charset=utf8;
