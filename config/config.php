<?php 
 /* generated config file */ 
 return array(
'common' => array(
	'php-include-path' => array(
		0 => '/Users/rsb/dev/php/appfuel/package', 
	),
	'php-include-path-action' => 'replace', 
	'base-path' => '/Users/rsb/dev/php/appfuel', 
	'fault-handler-class' => 'Appfuel\Kernel\FaultHandler', 
	'php-autoloader' => 'Appfuel\ClassLoader\StandardAutoLoader', 
	'php-default-timezone' => 'America/Los_Angeles', 
	'php-display-errors' => 'on', 
	'php-error-level' => 'all, strict', 
	'db' => array(
	),
	'env' => 'production', 
),
'main' => array(
	'startup-tasks' => array(
		1 => 'Appfuel\View\ViewStartupTask', 
	),
	'pre-filters' => array(
	),
	'post-filters' => array(
	),
),
'test' => array(
	'php-include-path-action' => 'append', 
	'php-include-path' => array(
		0 => '/Users/rsb/dev/php/appfuel/test', 
	),
	'php-display-errors' => 'on', 
	'php-error-level' => 'all, strict', 
	'fault-handler-class' => 'Appfuel\Kernel\FaultHandler', 
	'startup-tasks' => array(
		0 => 'Appfuel\DataSource\Db\DbStartupTask', 
		1 => 'TestFuel\UnitTestStartup', 
	),
	'db-scope' => array(
		0 => 'af-tester', 
	),
	'db' => array(
		'use' => array(
			0 => 'af-tester', 
		),
		'default-connector' => 'af-tester', 
		'connectors' => array(
			'af-tester' => array(
				'adapter' => 'Appfuel\DataSource\Db\Mysql\Mysqli\MysqliConn', 
				'conn-params' => array(
					'host' => 'localhost', 
					'user' => 'af_tester', 
					'pass' => 'w3bg33k3r', 
					'name' => 'af_unittest', 
				),
			),
		),
	),
),
);
?>