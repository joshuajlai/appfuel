delete from role_paths;
delete from roles;

insert into roles(role_id, role_code, role_name, role_desc) values 
(1, 'public', 'public vistor', 'Authorization level given to vistors'),
(2, 'super-user', 'system admin', 'Controls the whole system');

-- add the root node 
insert into role_paths(role_ancestor, role_descendant, role_depth) 
values (1, 1, 0);

-- add admin node
insert into role_paths(role_ancestor, role_descendant, role_depth)
select	t.role_ancestor, 2, t.role_depth + 1
from	role_paths as t
where	t.role_descendant = 1
union all 
select 2, 2, 0;
