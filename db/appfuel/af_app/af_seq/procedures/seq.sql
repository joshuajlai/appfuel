-- function used to implement sequences in appfuel
delimiter //

create function af_seq(seq_name char(30)) returns int(11)
begin
	update	af_seq 
	set		seq_value = last_insert_id(seq_value+1) 
	where	tbl_name = seq_name;
	
	return last_insert_id();
end
//
delimiter ;
