delete from action_modules;
delete from action_submodules;
delete from action_controllers;

insert into action_modules(module_id, module_name) 
values 
(1, 'Error'),
(2, 'Auth');

insert into action_submodules(submodule_id, module_id, module_name) 
values 
(1, 1, 'Handler'),
(2, 2,	'App');

insert into action_controllers(action_id, submodule_id, controller_name)
values
(1, 1, 'Invalid'),
(2, 2, 'Login'),
(3, 2, 'Logout');



