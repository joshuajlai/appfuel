-- holds a list of appfuel users
drop table if exists role_paths;
drop table if exists roles;
create table roles (
	role_id			smallint unsigned not null auto_increment,
	role_code		char(64) not null,
	role_name		varchar(100) not null,
	role_desc		varchar(255) not null,

	primary key	(role_id),
	index		(role_code)
) engine = InnoDB  default charset=utf8 auto_increment=1;

create table role_paths (
	ancestor		smallint unsigned not null,
	descendant		smallint unsigned not null,
	length			smallint unsigned not null default 0,

	primary key	(ancestor, descendant),
	foreign key (ancestor)   references roles (role_id),
	foreign key (descendant) references roles (role_id)
) engine = InnoDB  default charset=utf8;


