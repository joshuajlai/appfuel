<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Testfuel\Unit\Console;

use StdClass,
	Appfuel\Console\ArgSpec,
	Testfuel\TestCase\BaseTestCase;

class FieldSpecTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideInvalidStringsWithEmpty()
	{
		$result = $this->provideInvalidStrings();
		$result[] = array('');
		return $result;
	}

	/**
	 * @return	array	
	 */
	public function provideInvalidStrings()
	{
		return array(
			array(12345),
			array(1.234),
			array(0),
			array(true),
			array(false),
			array(array(1,2,3)),
			array(new StdClass()),
		);
	}
}
