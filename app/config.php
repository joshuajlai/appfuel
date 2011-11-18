<?php

return array(
	'main' => array(
		'env'					=> 'local',
		'base-path'				=> AF_BASE_PATH,
		'db-connectors'			=> array('af-app'),
		'db-default-connector'	=> 'af-app',
		'uri-parse-token'		=> 'qx',
		'include-path-action'	=> 'replace',
		'include-path'			=> array(AF_BASE_PATH . '/lib'),	
		'enable-autoloader'		=> true,
		'error-reporting'		=> 'all, strict',
		'default-timezone'		=> 'America/Los_Angeles',
		'startup-tasks'	=> array(
			'Appfuel\Kernel\Startup\KernelInitTask',
		),
		'intercepting-filters'	=> array(
			'Appfuel\App\Filter\AuthFilter', 
			'Appfuel\App\Filter\OrgFilter', 
			'Appfuel\App\Filter\ThemeFilter',
			'Appfuel\App\Filter\OutputFilter',
		),
	),
	
	'test' => array(
		'env'					=> 'local',
		'base-path'				=> AF_BASE_PATH,
		'db-connectors'			=> array('af-test', 'af-app'),
		'db-default-connector'	=> 'af-test',
		'uri-parse-token'		=> 'qx',
		'include-path-action'	=> 'append',
		'include-path'			=> array(
			AF_BASE_PATH . '/test',
			AF_BASE_PATH . '/test/lib',
			AF_BASE_PATH . '/test/classes',
			AF_BASE_PATH . '/lib',
		),
		'enable-autoloader'		=> true,
		'display-errors'		=> 'on',
		'error-reporting'		=> 'all, strict',
		'default-timezone'		=> 'America/Los_Angeles',
		'init-tasks'			=> array('system', 'db', 'op-routes') 
	),
);
