-- holds a list of appfuel users
create table if not exists af_users (
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

create table if not exists af_user_org_assign (
	user_assign_id	int unsigned not null,
	user_id			int unsigned not null,
	org_id			smallint unsigned not null,

	primary key (user_assign_id),
	foreign key (user_id) references af_users (user_id),
	foreign key (org_id)  references af_organizations(org_id)
) engine = InnoDB  default charset=utf8;
