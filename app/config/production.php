<?php

return array(
	'common' => array(
		'env'					=> 'local',
		'base-path'				=> AF_BASE_PATH,
		'enable-autoloader'		=> true,
		'default-timezone'		=> 'America/Los_Angeles',
		'display-errors'		=> 'on',
		'error-reporting'		=> 'all, strict',
		'db'					=> array(),
		'env'					=> 'production',
	),

	'main' => array(
		'include-path'		=> array(AF_BASE_PATH . '/lib'),	
		'include-path-action'	=> 'replace',
		'error-reporting'	=> 'all, strict',
		'startup-tasks'		=> array(),
		'pre-filters'		=> array(),
		'post-filters'		=> array(),
	),
	
	'test' => array(
		'include-path-action'	=> 'append',
		'include-path'			=> array(
			'/usr/local/php/share/pear',
			AF_BASE_PATH . '/test',
			AF_BASE_PATH . '/test/classes',
			AF_LIB_PATH 
		),
		'startup-tasks'	=> array(
			'Appfuel\Db\DbStartup',
			'TestFuel\UnitTestStartup',
		),
		'db' => array()
	),
);
