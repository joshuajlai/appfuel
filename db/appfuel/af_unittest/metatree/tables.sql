create table symbols (
	symbol_id		smallint unsigned not null auto_increment,
	symbol_text		char(255) not null,

	primary key (symbol_id),
	index		(symbol_text)
) engine = InnoDB  default charset=utf8 auto_increment=1;

create table metadata (
	meta_id			smallint unsigned not null auto_increment,
	symbol_id		smallint unsigned not null,
	client_id		smallint unsigned not null,
	meta_type		enum('group', 'category', 'tag') not null,
	meta_desc		varchar(255) not null default '',
	meta_class		varchar(128) not null default '',
	is_pres_visable	boolean not null default 1,
	is_default		boolean not null default 0,

	primary key	(meta_id),
	index		(client_id),
	index		(symbol_id),
	foreign key (symbol_id)   references symbols (symbol_id)
) engine = InnoDB  default charset=utf8 auto_increment=1;
	

create table metadata_closure (
	meta_ancestor		smallint unsigned not null,
	meta_descendant		smallint unsigned not null,
	depth				smallint unsigned not null default 0,

	primary key	(meta_ancestor, meta_descendant),
	foreign key (meta_ancestor)   references metadata (meta_id),
	foreign key (meta_descendant) references metadata (meta_id)
) engine = InnoDB  default charset=utf8;
