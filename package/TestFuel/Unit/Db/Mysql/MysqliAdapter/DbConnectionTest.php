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
namespace TestFuel\Test\Db\Mysql\MysqliAdapter;

use mysqli,
	TestFuel\TestCase\DbTestCase,
	Appfuel\Db\Mysql\MysqliAdapter\DbConnection,
	Appfuel\Db\ConnectionDetail;

/**
 * Test the adapters ability to wrap mysqli
 */
class ConnectionTest extends DbTestCase
{
	/**
	 * System under test
	 * @var Connection
	 */
	protected $conn = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->conn = new Connection($this->getConnectionDetail());
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->conn);
	}

	/**	
	 * @return null
	 */
	public function testInitialStatus()
	{
		$this->assertTrue($this->conn->isDriver());
		$this->assertInstanceOf(
			'mysqli', 
			$this->conn->getDriver()
		);

		$this->assertEquals('initialized', $this->conn->getStatus());
		$this->assertFalse($this->conn->isError());
		$this->assertFalse($this->conn->isConnected());
		$this->assertEquals(0, $this->conn->getErrorCode());
		$this->assertNull($this->conn->getErrorText());
	}

	/**
	 * @return null
	 */
	public function testConnectClose()
	{
		$this->assertTrue($this->conn->connect());
		$this->assertEquals('connected', $this->conn->getStatus());
		$this->assertTrue($this->conn->isConnected());
		$this->assertFalse($this->conn->isError());

		$this->assertTrue($this->conn->close());
		$this->assertEquals('closed', $this->conn->getStatus());
		
		$this->assertFalse($this->conn->isDriver());
		$this->assertFalse($this->conn->isConnected());
		$this->assertFalse($this->conn->isError());

		/* lets see if we can connect again */
		$this->assertFalse($this->conn->connect());
		$this->assertEquals('closed', $this->conn->getStatus());
		$this->assertFalse($this->conn->isConnected());
		$this->assertTrue($this->conn->isError());
	}
}
