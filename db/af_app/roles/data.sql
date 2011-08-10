delete from role_paths;
delete from roles;

insert into roles(role_id, role_code, role_name, role_desc) values 
(1, 'public', 'public vistor', 'Authorization level given to vistors'),
(2, 'admin', 'cms admin', 'Allowed to admin users in the cms'),
(3, 'content-producer', 'cms content producer', 'produce web pages etc ...'),
(4, 'content-publisher', 'cms content publisher', 'produce and publishes'),
(5, 'content-editor', 'cms editor', 'manages all content'),
(6, 'super-user', 'system admin', 'has control over the whole system');

-- add the root node 
insert into role_paths(ancestor, descendant, length) values (1, 1, 0);

-- add admin node
insert into role_paths(ancestor, descendant, length)
select	t.ancestor, 2, t.length + 1
from	role_paths as t
where	t.descendant = 1
union all 
select 2, 2, 0;

-- add content producer
insert into role_paths(ancestor, descendant, length)
select	t.ancestor, 3, t.length + 1
from	role_paths as t
where	t.descendant = 1
union all 
select 3, 3, 0;

-- add content producer
insert into role_paths(ancestor, descendant, length)
select	t.ancestor, 4, t.length + 1
from	role_paths as t
where	t.descendant = 3
union all 
select 4, 4, 0;

-- add content producer
insert into role_paths(ancestor, descendant, length)
select	t.ancestor, 5, t.length + 1
from	role_paths as t
where	t.descendant = 4
union all 
select 5, 5, 0;

-- add content producer
insert into role_paths(ancestor, descendant, length)
select	t.ancestor, 6, t.length + 1
from	role_paths as t
where	t.descendant = 1
union all 
select 6, 6, 0;
