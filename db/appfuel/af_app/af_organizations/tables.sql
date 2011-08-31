create table if not exists af_organizations (
	org_id			smallint unsigned not null,
	org_name		varchar(128) not null,
	org_token		char(32) not null default '',
	org_status		enum('active', 'pending', 'deleted') not null default 'pending',

	primary key (org_id),
	index (org_name)
) engine = InnoDB default charset=utf8;
