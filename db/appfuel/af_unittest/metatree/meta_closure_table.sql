drop table if exists asset_tag_assign;
drop table if exists assets;

drop table if exists metadata_closure;
drop table if exists metadata;

create table  metadata (
    meta_id		smallint unsigned not null auto_increment,
	client_id	smallint unsigned not null,
	node_type	enum('group', 'category', 'tag'),
	meta_class	varchar(50) not null default '',
	is_pres		boolean not null default 1,
	is_default	boolean not null default 0,
	meta_text	varchar(128) not null,

    primary key (meta_id),
    index my_text (meta_text)
) ENGINE=InnoDB default charset=utf8;

create table metadata_closure(
    ancestor		smallint unsigned not null,
    descendant		smallint unsigned not null,
    depth			smallint unsigned not null,
	asset_count		mediumint unsigned not null default 0,

    primary key (ancestor, descendant),
    foreign key (ancestor)   references metadata (meta_id),
    foreign key (descendant) references metadata (meta_id)
) ENGINE=InnoDB default charset=utf8;

create table assets (
	node_id	smallint unsigned not null,
	asset_label varchar(128) not null,

	primary key (node_id)
) engine=innodb default charset=utf8;

create table asset_tag_assign (
    node_id		smallint    unsigned not null,
	cat_id		smallint	unsigned not null,	
    tag_id		smallint    unsigned not null,
	group_id	smallint	unsigned not null,
    rank        tinyint     unsigned not null,

    PRIMARY KEY (node_id, tag_id),
    foreign key (node_id)	references assets   (node_id),
    foreign key (tag_id)	references metadata (meta_id),
	foreign key (cat_id)	references metadata (meta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO metadata (
	meta_id
,	client_id
,	node_type
,	meta_class
,	is_pres
,	is_default
,	meta_text
) VALUES
(1,  1,	'group',	'',	default, default, '[1] Group A'),
(2,  1,	'category',	'', default, default, '[2] Category A'),
(3,  1,	'category',	'', default, default, '[3] Category B'),
(4,  1,	'category',	'', default, default, '[4] Category C'),
(5,  1,	'tag',		'', default, default, '[5]  Tag 1'),
(6,  1,	'tag',		'', default, default, '[6]  Tag 2'),
(7,  1,	'tag',		'', default, default, '[7]  Tag 3'),
(8,  1,	'tag',		'', default, default, '[8]  Tag 4'),
(9,  1,	'tag',		'', default, default, '[9]  Tag 5'),
(10, 1,	'tag',		'', default, default, '[10] Tag 6'),
(11, 1,	'tag',		'', default, default, '[11] Tag 7'),
(12, 1,	'tag',		'', default, default, '[12] Tag 8'),
(13, 1,	'tag',		'', default, default, '[13] Tag 9'),
(14, 1,	'tag',		'', default, default, '[14] Tag 10');


INSERT INTO metadata_closure (ancestor, descendant, depth) VALUES
(1,  1, 0),
(1,  2, 1),
(1,  3, 2),
(1,  4, 1),
(1,  5, 2),
(1,  6, 2),
(1,  7, 2),
(1,  8, 2),
(1,  9, 2),
(1, 10, 2),
(1, 11, 2),
(1, 12, 2),
(1, 13, 2),
(1, 14, 2),

(2, 2,   0),
(2, 5,   1),
(2, 14,  1), 
(5, 5,   0),
(14, 14, 0),

(4, 4, 0),
(4, 5, 1), 
(4, 6, 1),
(4, 7, 1),
(4, 8, 1),
(4, 9, 1),

(6, 6, 0),
(7, 7, 0),
(8, 8, 0),
(9, 9, 0);


insert into assets(node_id, asset_label) values
(1, 'asset 1'), (2, 'asset 2'), (3, 'asset 3'), (4, 'asset 4'), 
(5, 'asset 5');

insert into asset_tag_assign(node_id, cat_id, tag_id, rank) values
(1, 2,  5,	1),
(2, 2,  5,	1),
(2, 2,  14, 2);
