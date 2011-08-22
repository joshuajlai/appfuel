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
namespace TestFuel\Test\Db;

use StdClass,
	Appfuel\Db\DbResponse,
	TestFuel\TestCase\BaseTestCase;

/**
 * Test the adapters ability to wrap mysqli
 */
class DbResponseTest extends BaseTestCase
{

	public function testResponseSuccessfulNoDataReturned()
	{
		$response = new DbResponse();
		$this->assertTrue($response->getStatus());
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isError());
		$this->assertNull($response->getError());
		$this->assertEquals(0, $response->getRowCount());
		
		/* was was no resultset given */
		$this->assertFalse($response->getCurrentResult());
		$this->assertNull($response->getResultset());

	}

	/**
	 * Status is an immutable property only set by the constructor
	 *
	 * @return null
	 */
	public function testGetStatus()
	{
		$response = new DbResponse();
		$this->assertTrue($response->getStatus());
	}

}
