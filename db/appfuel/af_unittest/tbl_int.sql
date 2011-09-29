create table if not exists tbl_int (
	col_1	tinyint not null primary key,
	col_2	float(7,4) unsigned zerofill not null
) engine = InnoDB;
