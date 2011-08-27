-- holds a list of appfuel users
drop table if exists users;
create table users (
	user_id			int unsigned not null,
	system_name		char(64) not null,
	first_name		varchar(100) not null,
	last_name		varchar(100) not null,
	primary_email	varchar(100) not null,
	activity_code	enum('active', 'inactive', 'deleted'),
	create_date		timestamp not null default CURRENT_TIMESTAMP,
	password		char(64) not null,

	primary key	(user_id),
	index		(system_name)
) engine = InnoDB  default charset=utf8;
