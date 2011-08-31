drop function if exists af_seq;

delimiter //
-- function used to implement sequences in appfuel
create function af_seq(seq_name char(30)) returns int(11)
begin
	declare trash	 text;
	declare is_table tinyint unsigned default 0;
	
	-- check to makesure the table exists	
	SELECT	count(tbl_name) 
	FROM	af_seq 
	WHERE	tbl_name = seq_name 
	INTO	is_table;

	if (1 = is_table) then
		UPDATE	af_seq 
		SET		seq_value = last_insert_id(seq_value+1) 
		WHERE	tbl_name = seq_name;
	
		return last_insert_id();
	end if;

    -- Ugly hack to throw an exception.
    SELECT `There is no sequence for this table!` 
	INTO	trash;
end
//
delimiter ;
