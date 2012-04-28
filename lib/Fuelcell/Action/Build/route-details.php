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
	'cs-build' => array(
		'is-public'   => true,
		'action-name' => 'BuildClientsideResources',
		'default-format' => 'text'
	),
	'cs-buildtree' => array('action-name' => 'BuildClientsideTree'),
	'config-build' => array(
		'is-public' => true,
		'action-name' => array(
			'cli' => 'BuildConfig'
		),
	)
);

