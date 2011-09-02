<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Test\Db\Connection;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Connection\ConnectionDetail;

/**
 * The ConnectionDetail is a value object used to hold connection information, 
 * as well as the adapter type and vendor. 
 */
class ConnectionDetailTest extends BaseTestCase
{
	public function testOne()
	{
		$this->assertTrue(true);
	}
}
