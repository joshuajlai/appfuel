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
namespace TestFuel\Unit\DataSource\Db\Mysql\Mysqli;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataSource\Db\DbRegistry,
	Appfuel\DataSource\Db\Mysql\Mysqli\MysqliConn;

/**
 */
class MysqliConnTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var MysqliConn
	 */
	protected $conn = null;

	/**
	 * Parameters used to connect to the database
	 * @var array
	 */
	protected $params = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->runDbStartupTask();
		$params = DbRegistry::getConnectionParams('af-tester');
		$this->params = $params->get('conn-params');
		$this->conn = new MysqliConn($this->params);
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->params = null;
		$this->conn   = null;
	}

	/**	
	 * @return	MysqliConnInterface
	 */
	public function getMysqliConnector()
	{
		return $this->conn;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$conn = $this->getMysqliConnector();
		$this->assertInstanceof('Appfuel\DataSource\Db\DbConnInterface',$conn);
		$this->assertInstanceof(
			'Appfuel\DataSource\Db\Mysql\Mysqli\MysqliConnInterface',
			$conn
		);

		$params = $conn->getConnectionParams();
		$this->assertInstanceOf(
			'Appfuel\DataStructure\Dictionary',
			$params
		);
		$this->assertSame($this->params, $params->getAll());
		$this->assertEquals(3306, $conn->getDefaultPort());
		$this->assertFalse($conn->isDriver());
		$this->assertNull($conn->getDriver());
		$this->assertFalse($conn->isError());
		$this->assertEquals(array(), $conn->getError());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testDefaultPort()
	{
		$conn = $this->getMysqliConnector();
		
		$port = 5505;
		$this->assertSame($conn, $conn->setDefaultPort($port));
		$this->assertEquals($port, $conn->getDefaultPort());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetDefaultPortString_Failure()
	{
		$conn = $this->getMysqliConnector();
		$conn->setDefaultPort('12345');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetDefaultPortNegativePort_Failure()
	{
		$conn = $this->getMysqliConnector();
		$conn->setDefaultPort(-1234);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetDefaultPortZeroPort_Failure()
	{
		$conn = $this->getMysqliConnector();
		$conn->setDefaultPort(0);
	}

	/**
	 * @depends	testInitialState
	 * @return
	 */
	public function testCreateDriver()
	{
		$conn = $this->getMysqliConnector();
		$this->assertInstanceOf('mysqli', $conn->createDriver());
	}

	/**
	 * @depends	testInitialState
	 * @return
	 */
	public function testSetGetIsDriver()
	{
		$conn = $this->getMysqliConnector();
		$this->assertFalse($conn->isDriver());
		
		$driver = $this->getMock('mysqli');
		$this->assertSame($conn, $conn->setDriver($driver));
		$this->assertSame($driver, $conn->getDriver());
		$this->assertTrue($conn->isDriver());
	}

	/**
	 * @depends	testInitialState
	 * @return
	 */
	public function testConnect()
	{
		$conn = $this->getMysqliConnector();
		$this->assertTrue($conn->connect());
		$this->assertTrue($conn->isConnected());
		$this->assertFalse($conn->isError());
		$this->assertEquals(array(), $conn->getError());
	}

	/**
	 * @depends	testInitialState
	 * @return
	 */
	public function testConnectBadUser_Failure()
	{
		$params = array(
			'host' => 'localhost',
			'user' => 'bad_tester',
			'pass' => 'w3bg33k3r',
			'name' => 'af_unittest'
		);

		$conn = new MysqliConn($params);
		$this->assertFalse($conn->connect());
		$this->assertFalse($conn->isConnected());
		$this->assertTrue($conn->isError());

		$error = $conn->getError();
		$this->assertEquals(1045, $error['error-nbr']);

		$msg = "Access denied for user 'bad_tester'@'localhost'";
		$this->assertContains($msg, $error['error-text']);	
	}	

	/**
	 * @depends	testInitialState
	 * @return
	 */
	public function testConnectBadPass_Failure()
	{
		$params = array(
			'host' => 'localhost',
			'user' => 'af_tester',
			'pass' => 'w3k3r',
			'name' => 'af_unittest'
		);

		$conn = new MysqliConn($params);
		$this->assertFalse($conn->connect());
		$this->assertFalse($conn->isConnected());
		$this->assertTrue($conn->isError());

		$error = $conn->getError();
		$this->assertEquals(1045, $error['error-nbr']);

		$msg = "Access denied for user 'af_tester'@'localhost'";
		$this->assertContains($msg, $error['error-text']);	
	}

	/**
	 * @depends	testInitialState
	 * @return
	 */
	public function testConnectBadDbName_Failure()
	{
		$params = array(
			'host' => 'localhost',
			'user' => 'af_tester',
			'pass' => 'w3k3r',
			'name' => 'asdasdad'
		);

		$conn = new MysqliConn($params);
		$this->assertFalse($conn->connect());
		$this->assertFalse($conn->isConnected());
		$this->assertTrue($conn->isError());

		$error = $conn->getError();
		$this->assertEquals(1045, $error['error-nbr']);
		$msg = "Access denied for user 'af_tester'@'localhost'";
		$this->assertContains($msg, $error['error-text']);	
	}

	/**
	 * @depends	testInitialState
	 * @return
	 */
	public function testConnectBadDbPort_Failure()
	{
		$params = array(
			'host' => 'localhost',
			'user' => 'af_tester',
			'pass' => 'w3k3r',
			'name' => 'af_unittest',
			'port' => -100
		);

		$conn = new MysqliConn($params);
		$this->assertFalse($conn->connect());
		$this->assertFalse($conn->isConnected());
		$this->assertTrue($conn->isError());

		$error = $conn->getError();
		$this->assertEquals(1045, $error['error-nbr']);
		$msg = "Access denied for user 'af_tester'@'localhost'";
		$this->assertContains($msg, $error['error-text']);	
	}

	/**
	 * @depends	testInitialState
	 * @return
	 */
	public function testConnectBadDbSocket_Failure()
	{
		$params = array(
			'host' => 'localhost',
			'user' => 'af_tester',
			'pass' => 'w3k3r',
			'name' => 'af_unittest',
			'socket' => 'blah'
		);

		$conn = new MysqliConn($params);
		$this->assertFalse($conn->connect());
		$this->assertFalse($conn->isConnected());
		$this->assertTrue($conn->isError());

		$error = $conn->getError();
		$this->assertEquals(2002, $error['error-nbr']);
		$msg = "No such file or directory";
		$this->assertContains($msg, $error['error-text']);	
	}
}
