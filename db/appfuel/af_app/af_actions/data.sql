insert into af_action_roots (root_id, root_ns) values
(1, 'Appfuel\\Action');

insert into af_action_modules(module_id, root_id, module_name) values
(1, 1, 'Error'),
(2, 1, 'Auth');

insert into af_action_submodules(submodule_id, module_id, submodule_name) 
values
(1, 1, 'Handler'),
(2, 2, 'System');

insert into af_action_controllers(
	controller_id
,	submodule_id
,	controller_name
,	builder_class
,	is_forward
) 
values
(1, 1, 'Invalid', DEFAULT, 1),
(2, 2, 'Login',	DEFAULT, 0),
(3, 2, 'Logout', DEFAULT, 1);

