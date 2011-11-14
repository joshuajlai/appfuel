drop table if exists comments;
DROP table if exists comment_closure;

create table  comments (
    comment_id		smallint unsigned not null auto_increment,
	comment_text	varchar(128) not null,

    primary key (comment_id),
    index my_text (comment_text)
) ENGINE=InnoDB default charset=utf8;

create table comment_closure(
    ancestor	smallint unsigned not null,
    descendant	smallint unsigned not null,
    depth		smallint unsigned not null,

    primary key (ancestor, descendant),
    foreign key (ancestor)   references comments (comment_id),
    foreign key (descendant) references comments (comment_id)
) ENGINE=InnoDB default charset=utf8;

INSERT INTO comments (comment_id, comment_text) VALUES
(1, '[1] Fran: Whats the cause of this bug'),
(2, '[2] Ollie: I think its a null pointer'),
(3, '[3] Fran: No I checked for that'),
(4, '[4] Kukla: We need to check for valid input'),
(5, '[5] Ollie: Yes, thats a bug '),
(6, '[6] Fran: Yes, please add a check'),
(7, '[7] Kukla: Thats fixed it');


INSERT INTO comment_closure (ancestor, descendant, depth) VALUES
(1, 1, 0),
(1, 2, 1),
(1, 3, 2),
(1, 4, 1),
(1, 5, 2),
(1, 6, 2),
(1, 7, 3),

(2, 2, 0),
(2, 3, 1), 
(3, 3, 0),

(4, 4, 0),
(4, 5, 1), 
(4, 6, 1),
(4, 7, 2),

(5, 5, 0),

(6, 6, 0),
(6, 7, 1),

(7, 7, 0);
