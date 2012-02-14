<?php

return array(
	'common' => array(
		'env'					=> 'local',
		'base-path'				=> AF_BASE_PATH,
		'enable-autoloader'		=> true,
		'display-errors'		=> 'on',
	),

	'main' => array(
		'include-path'		=> array(AF_BASE_PATH . '/lib'),	
		'startup-tasks'		=> array(),
		'post-filters'		=> array(),
	),
	
	'test' => array(),
);
