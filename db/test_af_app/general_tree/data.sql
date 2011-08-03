insert into general_tree (
	node_id
,	parent_id
,	node_label
,	node_type
,	node_left
,	node_right
) values 
(1, 0, 'level 0: A', 'root',	1, 16),
(2, 1, 'level 1: A', 'folder',	2, 11),
(3, 2, 'level 2: A', 'file',	3, 4),
(4, 2, 'level 2: B', 'folder',	5, 10),
(5, 4, 'level 3: A', 'file',	6, 7),
(6, 4, 'level 3: B', 'file',	8, 9),
(7, 1, 'level 1: B', 'folder',	12, 13),
(8, 1, 'level 1: C', 'file',	14, 15); 
