<?php

return array(
	'servers' => array(
		'af-master' => array(
			'prod'	=> array('hostname' => 'db-master'),
			'qa'	=> array('hostname' => 'db-master'),
			'dev'	=> array('hostname' => 'db-master'),
			'local' => array('hostname' => 'localhost')

		),
		'af-slave' => array(
			'prod'	=> array('hostname' => 'db-slave'),
			'qa'	=> array('hostname' => 'db-slave'),
			'dev'	=> array('hostname' => 'db-slave'),
			'local' => array('hostname' => 'localhost')
		),
	),

	'databases' => array(
		'app-db' => array(
			'dbname'			=> 'af_app',
			'default-charset'	=> 'utf8',
			'default-collate'	=> 'utf8_general_ci',
			'master'			=> 'af-master',
			'slave'				=> 'af-slave',
			'users'				=> array('app-user', 'app-admin'),
		),
		'test-db' => array(
			'dbname'			=> 'af_unittest',
			'host'				=> 'localhost',
			'default-charset'	=> 'utf8',
			'default-collate'	=> 'utf8_general_ci',
			'master'			=> 'af-master', 
			'slave'				=> 'af-slave',
			'users' => array('app-user', 'app-admin'),
		),
	),

	'privilege-groups' => array(
		'app-user' => array(
			'select',
			'insert',
			'delete',
			'update',
			'execute'
		),
		'app-admin' => array(
			'select',
			'insert',
			'delete',
			'update',
			'execute',
			'alter',
			'alter routine',
			'create',
			'create routine',
			'create temporary tables',
			'create view',
			'index',
			'lock tables',
			'process',
			'reload',
			'show databases',
			'show view',
		),
	),

	'users' => array(
		'af-app-user' => array(
			'privilege' => 'app-user',
			'prod' => array(
				'username'  => 'af_app_user',
				'hostname'  => 'localhost',
				'password'	=> 'w3bG33k3R'
			),
			'qa' => array(
				'username'  => 'af_app_user',
				'hostname'  => 'localhost',
				'password'	=> 'w3bG33k3R'
			),
			'dev' => array(
				'username'  => 'af_app_user',
				'hostname'  => 'localhost',
				'password'	=> 'w3bG33k3R'
			),
			'local' => array(
				'username'  => 'appfuel_user',
				'hostname'  => 'localhost',
				'password'	=> 'w3b_g33k'
			),

		),
		'af-admin-user' => array(
			'privilege' => 'admin-user',
			'prod' => array(
				'username'  => 'af_admin_user',
				'hostname'  => 'localhost',
				'password'	=> 'adminG33K3r'
			),
			'qa' => array(
				'username'  => 'af_admin_user',
				'hostname'  => 'localhost',
				'password'	=> 'adminG33K3r'
			),
			'dev' => array(
				'username'  => 'af_admin_user',
				'hostname'  => 'localhost',
				'password'	=> 'adminG33K3r'
			),
			'local' => array(
				'username'  => 'af_admin_user',
				'hostname'  => 'localhost',
				'password'	=> 'adminG33K3r'
			),
		),
	),

	'connectors' => array(
		'af-app' => array(
			'php-conn-class' => 'Appfuel\Db\Mysql\AfMysqli\Connection',
			'db-key'		 => 'app-db',
			'user-key'		 => 'af-app-user'
		),
		'af-test' => array(
			'php-conn-class' => 'Appfuel\Db\Mysql\AfMysqli\Connection',
			'db-key'		 => 'test-db',
			'user-key'		 => 'af-app-user'
		),
	)
);
