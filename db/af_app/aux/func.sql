-- function for sequence table access

delimiter //

drop function if exists af_seq;
create function af_seq(seq_name char(30)) returns int
begin
	
	declare is_tbl	tinyint unsigned default 0;

	-- check that the seq name exists
	update	af_seq 
	set		seq_value = last_insert_id(seq_value+1) 
	where	tbl_name = seq_name;
	
	return last_insert_id();
end
//
delimiter ;

