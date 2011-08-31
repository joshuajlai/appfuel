insert into af_roles(role_id, role_code, role_name, role_desc) values 
(1, 'visitor', 'public vistor', 'Authorization level given to vistors'),
(2, 'system-admin', 'system admin', 'Controls the whole system');

-- add the root node 
insert into af_role_closure(role_ancestor, role_descendant, role_depth) 
values (1, 1, 0);

-- add admin node
insert into af_role_closure(role_ancestor, role_descendant, role_depth)
select	t.role_ancestor, 2, t.role_depth + 1
from	role_paths as t
where	t.role_descendant = 1
union all 
select 2, 2, 0;
