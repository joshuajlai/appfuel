-- The general_tree is a table used to implement a nested set model to hold
-- hierarchical data based on left and right nodes
drop table if exists general_tree;
create table general_tree (
	node_id		int unsigned not null auto_increment,
	parent_id	int unsigned not null,
	node_label	char(50) not null,
	node_type	enum('root', 'folder', 'file'),
	node_left	int unsigned not null,
	node_right	int unsigned not null,

	primary key	(node_id),
	index		(parent_id),
	index		(node_left),
	index		(node_right) 
) engine = InnoDB  default charset=utf8 auto_increment=1;

