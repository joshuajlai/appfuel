create table if not exists af_user_sessions (
	session_id		int unsigned not null,
	user_assign_id	int unsigned not null,
	org_token		char(32) not null,
	session_token	char(32) not null,
	created_date	timestamp not null,
	expired_date	timestamp not null,
	user_ip			int unsigned not null default 0,

	primary key (session_id),
	foreign key (user_assign_id) references af_user_org_assign (user_assign_id),
	unique key (org_token),
	unique key (session_token)
) engine = InnoDB default charset=utf8;
