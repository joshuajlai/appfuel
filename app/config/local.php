<?php

return array(
	'common' => array(
		'php-display-errors' => 'on',
	),
	'main' => array(
		'startup-tasks'		=> array(),
		'post-filters'		=> array(),
	),
	'test' => array(
		'db' => array(
			'connectors' => array(
				'af-tester' => array(
					'conn-params' => array(
						'host' => 'localhost',
						'user' => 'af_tester',
						'pass' => 'w3bg33k3r',
						'name' => 'af_unittest'
					) 
				),
			),
		),
	)
);
