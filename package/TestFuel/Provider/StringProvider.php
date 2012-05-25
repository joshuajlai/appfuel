<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Provider;

use StdClass,
	SplFileInfo;

/**
 * Encapsulates on the general cases for getting sets string values to be
 * used with phpunits dataProvider
 */
class StringProvider
{
	static public function getInvalidStrings($includeEmpty = false)
	{
		$result = array(
			array(true),
			array(false),
			array(12345),
			array(1.2345),
			array(array(1,2,3,4)),
			array(new StdClass()),
		);

		if (true === $includeEmpty) {
			$result[] = array('');
		}

		return $result;
	}
}	
