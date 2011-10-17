
drop table if exists mytable;
create table if not exists mytable (
	col_1	tinyint(1) not null default	99,
	col_2	varchar(123) not null default "a, \', b, c",
	col_3   enum('small', 'medium'),

	constraint my_con unique key my_key (col_1)
) engine = InnoDB;
