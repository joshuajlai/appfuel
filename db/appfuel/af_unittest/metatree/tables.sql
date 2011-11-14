DROP TABLE if exists node_tag_assign;
DROP TABLE if exists node_group_assign;
DROP TABLE if exists metadata_closure;
DROP TABLE if exists nodes;
DROP TABLE if exists metadata;
DROP TABLE if exists symbols;

CREATE TABLE symbols (
    symbol_id		smallint unsigned NOT NULL AUTO_INCREMENT,
    symbol_text		char(255) NOT NULL,

    PRIMARY KEY			(symbol_id),
    INDEX	symbol_text (symbol_text)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE metadata (
    meta_id			smallint unsigned NOT NULL AUTO_INCREMENT,
    symbol_id		smallint unsigned NOT NULL,
    client_id		smallint unsigned NOT NULL,
    meta_type		enum('group','category','tag') NOT NULL,
    meta_desc		varchar(255) NOT NULL,
    meta_class		varchar(128) NOT NULL,
    is_pres_visable tinyint NOT NULL default 1,
    is_default		tinyint NOT NULL default 0,

    PRIMARY KEY (meta_id),
    INDEX client_id (client_id),
    INDEX symbol_id (symbol_id),
    FOREIGN KEY (symbol_id) REFERENCES symbols (symbol_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE metadata_closure (
    meta_ancestor	smallint unsigned NOT NULL,
    meta_descendant smallint unsigned NOT NULL,
    depth smallint	unsigned NOT NULL,

    PRIMARY KEY (meta_ancestor,meta_descendant),
    index meta_descendant (meta_descendant),
    FOREIGN KEY (meta_ancestor) REFERENCES metadata (meta_id),
    FOREIGN KEY (meta_descendant) REFERENCES metadata (meta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE nodes (
    node_id     int     unsigned not null auto_increment,
    node_label  varchar(128)    not null,

    primary key (node_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- assignment of node to tag
CREATE TABLE node_tag_assign (
    node_id     int         unsigned not null,
    meta_id     smallint    unsigned not null,
    rank        tinyint     unsigned not null,

    PRIMARY KEY (node_id, meta_id),
    foreign key (node_id) references nodes (node_id),
    foreign key (meta_id) references metadata (meta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- assignment of node to group
CREATE TABLE node_group_assign (
    node_id     int         unsigned not null,
    meta_id     smallint    unsigned not null,

    PRIMARY KEY (node_id, meta_id),
    foreign key (node_id) references nodes (node_id),
    foreign key (meta_id) references metadata (meta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- values
INSERT INTO nodes (node_id, node_label) VALUES
(1, 'good music video'),
(2, 'commercial'),
(3, 'award winning video'),
(4, 'bad music video'),
(5, 'really crappy video');

INSERT INTO symbols (symbol_id, symbol_text) VALUES
(1, 'Group A'),
(2, 'Group B'),
(3, 'Group C'),

(4,  'Cat A'),
(5,  'Cat B'),
(6,  'Cat C'),

(7,  'Tag 1'),
(8,  'Tag 2'),
(9,  'Tag 3'),
(10, 'Tag 4'),
(11, 'Tag 5'),
(12, 'Tag 6'),
(13, 'Tag 7');


INSERT INTO metadata (
	meta_id
, symbol_id
, client_id
, meta_type
, meta_desc
, meta_class
, is_pres_visable
, is_default
)
VALUES
(1, 1, 1, 'group', '', 'text', DEFAULT, DEFAULT),
(2, 2, 1, 'group', '', 'text', DEFAULT, DEFAULT),
(3, 3, 1, 'group', '', 'text', DEFAULT, DEFAULT),

(4, 4, 1, 'category', '', 'text', DEFAULT, DEFAULT),
(5, 5, 1, 'category', '', 'text', DEFAULT, DEFAULT),
(6, 6, 1, 'category', '', 'text', DEFAULT, DEFAULT),

(7, 7, 1,	'tag', '', 'text', DEFAULT, DEFAULT),
(8, 8, 1,	'tag', '', 'text', DEFAULT, DEFAULT),
(9, 9, 1,	'tag', '', 'text', DEFAULT, DEFAULT),
(10, 10, 1, 'tag', '', 'text', DEFAULT, DEFAULT),
(11, 11, 1, 'tag', '', 'text', DEFAULT, DEFAULT),
(12, 12, 1, 'tag', '', 'text', DEFAULT, DEFAULT),
(13, 13, 1, 'tag', '', 'text', DEFAULT, DEFAULT);


INSERT INTO metadata_closure (meta_ancestor, meta_descendant, depth) VALUES
(1, 1, 0),
(2, 2, 0),
(3, 3, 0);


-- asset group assign
INSERT INTO node_group_assign (node_id, meta_id) VALUES
(4, 1);

-- asset tag assign
INSERT INTO node_tag_assign (node_id, meta_id, rank) VALUES
(4, 12, 1);

