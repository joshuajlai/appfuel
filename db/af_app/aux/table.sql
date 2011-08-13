drop table if exists af_seq;
create table af_seq (
	tbl_name	char(30) not null,
	seq_value	int unsigned not null,
	
	primary key (tbl_name)
) engine=myisam default charset=latin1
