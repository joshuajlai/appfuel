-- sequence table used to hold the sequence value instead of directly relying
-- on mysql's auto_increment
create table af_seq (
  tbl_name		char(30) not null,
  seq_value		int(10) unsigned not null,

  primary key (tbl_name)
) engine=MyISAM default charset=latin1 
