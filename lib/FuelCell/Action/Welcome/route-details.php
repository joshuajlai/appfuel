<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     FuelCell
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
return array(
	'build-test' => array(
		'is-public'   => true,
		'action-name' => 'MyAction',
		'startup'     => array(
			'exclude' => array(
				'Wdl\Startup\Task\LegacyDbStartup',
				'Wdl\Startup\Task\SmartyStartup',
				'Wdl\Startup\Task\UrlStartup',
			),
		),
		'intercept'   => array(
			'is-skip-pre' => true,
		),

		'context-params' => array(
			'foo' => 'bar',
		),

		'view-pkg' => "appfuel:page.my-test",
	),
	'test-a' => null,
	'test-b' => 'build-test',
	'test-c' => array(
		'is-public'   => true,
		'action-name' => 'MyActionForC'
	)
);

