<?php

return array(
	'common' => array(
		'include-path'		    => array(AF_BASE_PATH . '/lib'),
		'include-path-action'	=> 'replace',
		'base-path'				=> AF_BASE_PATH,
		'enable-autoloader'		=> true,
		'default-timezone'		=> 'America/Los_Angeles',
		'display-errors'		=> 'on',
		'error-reporting'		=> 'all, strict',
		'db'					=> array(),
		'env'					=> 'production',
	),

	'main' => array(
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
			'Appfuel\DataSource\Db\DbStartupTask',
			'TestFuel\UnitTestStartup',
		),
        'db-scope' => array('af-tester'),
        'db' => array(
            'af-tester' => array(
                'adapter' => 'Appfuel\DataSource\Db\Mysql\Mysqli\MysqliConn',
            ),
        ),
	),
);
