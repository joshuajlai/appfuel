create table if not exists tbl_int (
	col_1	tinyint not null primary key,
	col_2	float(3,2) zerofill not null
) engine = InnoDB;
