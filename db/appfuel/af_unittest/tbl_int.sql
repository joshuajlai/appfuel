
drop table if exists mytable;
create table if not exists mytable (
	col_1	tinyint not null,
	col_2	int unsigned not null, 

	constraint my_con unique key my_key (col_1),
	key (col_2)
) engine = InnoDB;
