<?php

return array(
	'common' => array(
		'php-include-path'			=> array(AF_CODE_PATH),
		'php-include-path-action'	=> 'replace',
		'base-path'					=> AF_BASE_PATH,
		'fault-handler-class'	=> 'Appfuel\Kernel\FaultHandler',
		'php-autoloader'		=> 'Appfuel\ClassLoader\StandardAutoLoader',
		'php-default-timezone'	=> 'America/Los_Angeles',
		'php-display-errors'	=> 'off',
		'php-error-level'		=> 'all, strict',
		'db'					=> array(),
		'env'					=> 'production',
	),

	'main' => array(
		'startup-tasks'		=> array(
			1 => 'Appfuel\View\ViewStartupTask',
		),
		'pre-filters'		=> array(),
		'post-filters'		=> array(),
	),
	
	'test' => array(
		'php-include-path-action'	=> 'append',
		'php-include-path'			=> array(AF_BASE_PATH . '/test'),
		'php-display-errors'	=> 'on',
		'php-error-level'		=> 'all, strict',
		'fault-handler-class'	=> 'Appfuel\Kernel\FaultHandler',
		'startup-tasks'	=> array(
			'Appfuel\DataSource\Db\DbStartupTask',
			'TestFuel\UnitTestStartup',
		),
        'db-scope' => array('af-tester'),
        'db' => array(
			'use'		=> array('af-tester'),
			'default-connector' => 'af-tester',
			'connectors' => array(
				'af-tester' => array(
					'adapter' => 'Appfuel\DataSource\Db\Mysql\Mysqli\MysqliConn',
				),
			),
		),
	),
);
