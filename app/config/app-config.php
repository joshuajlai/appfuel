<?php 
 /* generated config file */ 
 return array(
'common' => array(
	'env' => 'local', 
	'base-path' => '/Users/rsb/dev/php/appfuel', 
	'enable-autoloader' => true,
	'default-timezone' => 'America/Los_Angeles', 
	'display-errors' => 'on', 
	'error-reporting' => 'all, strict', 
	'db' => array(
	),
),
'main' => array(
	'include-path' => array(
		0 => '/Users/rsb/dev/php/appfuel/lib', 
	),
	'include-path-action' => 'replace', 
	'error-reporting' => 'all, strict', 
	'startup-tasks' => array(
	),
	'pre-filters' => array(
	),
	'post-filters' => array(
	),
),
'test' => array(
	'include-path-action' => 'append', 
	'include-path' => array(
		0 => '/usr/local/php/share/pear', 
		1 => '/Users/rsb/dev/php/appfuel/test', 
		2 => '/Users/rsb/dev/php/appfuel/test/classes', 
		3 => '/Users/rsb/dev/php/appfuel/lib', 
	),
	'startup-tasks' => array(
		0 => 'Appfuel\Db\DbStartup', 
		1 => 'TestFuel\UnitTestStartup', 
	),
	'db' => array(
	),
),
);?>