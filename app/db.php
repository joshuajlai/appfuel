<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
return array(
	'databases' => array(
		'af-unittest' => array(
			'dbname'			=> 'af_unittest',
			'host'				=> 'localhost',
			'default-charset'	=> 'utf8',
			'default-collate'	=> 'utf8_general_ci',
			'users'				=> array('af-testuser', 'af-testadmin')
		),
	),

	'privilege-groups' => array(
		'app-user' => array('select','insert','delete','update','execute'),
		'app-admin' => array(
			'select','insert','delete','update','execute',
			'alter','alter routine',
			'create','create routine','create temporary tables','create view',
			'index','lock tables','process','reload','show databases',
			'show view',
		),
	),

	'users' => array(
		'af-testuser'  => 'app-user',
		'af-testadmin' => 'admin-user',
	),

	'connectors' => array(
		'default-conn-class' => 'Appfuel\Db\Mysql\MysqliAdapter\DbConnection',
		'local' => array(
			'af-test' => array(
				'host' => 'localhost',
				'name' => 'af-unittest',
				'user' => 'af-test-user',
				'pass' => 'w3B_G33k3r'
			),
		)
	)
);
