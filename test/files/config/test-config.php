<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * Used in unit test to ensure the Appfuel\Kernel\Startup\ConfigLoader is 
 * working correctly
 */
return array(
	'common' => array(
		'key-a' => 'common-a',
		'key-z' => 'value-z'
	),
	'section-a' => array(
		'key-a' => 'value-a',
		'key-b' => 12345,
		'key-c' => 1.2345,
		'key-d' => true,
		'key-e' => false,
		'key-f' => array(1,2,3),
		'key-g' => array(
			'sub-key-a' => 'value-a',
			'sub-key-b' => 'value-b'
		)
	),
	'section-b' => array(
		'key-b' => 'value-b',
		'key-x' => 'value-x',
		'key-y' => array('a', 'b','c')
	)
);
