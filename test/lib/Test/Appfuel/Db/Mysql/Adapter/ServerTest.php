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
namespace Test\Appfuel\Db\Mysql\Adapter;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Connection\ConnectionDetail,
	Appfuel\Db\Mysql\Adapter\Server;

/**
 * Test the adapters ability to wrap mysqli
 */
class ServerTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Server
	 */
	protected $server = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->connDetail = new ConnectionDetail('mysql', 'mysqli');
		$this->connDetail->setHost('localhost')
						 ->setUserName('appfuel_user')
						 ->setPassword('w3b_g33k')
						 ->setDbName('af_unittest');

		$this->server = new Server($this->connDetail);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->server);
	}

	/**
	 * The connection detail is an immutable object that can only be set in 
	 * the constructor.
	 *
	 * @return	null
	 */
	public function testGetConnectionDetail()
	{
		$this->assertSame(
			$this->connDetail, 
			$this->server->getConnectionDetail()
		);
	}

	/**
	 * This calls mysqli_init and returns it
	 * 
	 * @return	null
	 */
	public function testCreateHandle()
	{
		$this->assertInstanceOf('\Mysqli', $this->server->createHandle());
	}

	/**
	 * @return null
	 */
	public function testSetGetIsClearHandle()
	{
		$this->assertFalse($this->server->isHandle());
		$this->assertNull($this->server->getHandle());

		$handle = mysqli_init();
		$this->assertSame(
			$this->server,
			$this->server->setHandle($handle),
			'Must use a fluent interface'
		);

		$this->assertTrue($this->server->isHandle());
		$this->assertSame($handle, $this->server->getHandle());

		$this->assertSame(
			$this->server,
			$this->server->clearHandle(),
			'must use a fluent interface'
		);

		$this->assertFalse($this->server->isHandle());
		$this->assertNull($this->server->getHandle());
	}

	/**
	 * @return null
	 */
	public function testConnectClose()
	{
		$this->server->initialize();
		$this->assertTrue($this->server->connect());
		$this->assertTrue($this->server->isConnected());
		$this->assertTrue($this->server->close());
		$this->assertFalse($this->server->isConnected());
	}

	/**
	 * Test the behavior of what happens when you clear the handle while
	 * a connection is open
	 *
	 * @return null
	 */
	public function testConnectClearHandle()
	{
		$this->server->initialize();
		$this->assertTrue($this->server->connect());
		$this->assertTrue($this->server->isConnected());
	
		$this->assertSame(
			$this->server,
			$this->server->clearHandle(),
			'must use a fluent interface'
		);

		$this->assertFalse($this->server->isConnected());
		$this->assertFalse($this->server->isHandle());
		$this->assertNull($this->server->getHandle());
	}

	/**
	 * @return	null
	 */
	public function testConnectBadConnectionDetail()
	{
		$connDetail = new ConnectionDetail('mysql', 'mysqli');
		$connDetail->setHost('localhost')
				   ->setUserName('_not_likely_to_exist_apfuel__')
				   ->setPassword('no-pass')
				   ->setDbName('no-db');


		$adapter = new Server($connDetail);
		
		$adapter->initialize();
		$this->assertFalse($adapter->connect());
	}

	/**
	 * In order to test the getLastConnectNbr and getLastConnectText we
	 * must first create an connection error
	 *
	 * @return	null
	 */
	public function testGetLastConnectErrorNbrAndText()
	{
		$connDetail = new ConnectionDetail('mysql', 'mysqli');
		$connDetail->setHost('localhost')
				   ->setUserName('_not_likely_to_exist_apfuel__')
				   ->setPassword('no-pass')
				   ->setDbName('no-db');


		$server = new Server($connDetail);
		$server->initialize();
		$this->assertFalse($server->connect());
		$this->assertFalse($server->isConnected());
	
		$this->assertInstanceOf(
			'Appfuel\Db\Mysql\Adapter\Error',
			$server->getConnectionError()
		);	
	}

	/**
	 * Test the behavior of getLastError(Nbr|Text) when no error is present.
	 *
	 * @return null
	 */
	public function testGetLastConnectErrorNbTextNoError()
	{
		$this->server->initialize();
		$this->assertTrue($this->server->isHandle());

		$this->assertTrue($this->server->connect());
		$this->assertNull($this->server->getConnectionError());
	
		$this->assertTrue($this->server->close());	
	}

	/**
	 * This method returns the version of the mysql client. No connection is
	 * needed for this
	 *
	 * @return null
	 */
	public function testGetClientInfo()
	{
		$this->assertFalse($this->server->isHandle());
		$info = $this->server->getClientInfo();
		$this->assertInternalType('string', $info);
	}

	/**
	 * This method requires a handle, when no handle is present it returns null
	 *
	 * @return null
	 */
	public function testGetClientVersion()
	{
		$this->server->initialize();
		$this->assertTrue($this->server->isHandle());
		$version = $this->server->getClientVersion();
		$this->assertInternalType('int', $version);
		$this->assertGreaterThan(10000, $version);
		
		$this->server->clearHandle();
		$this->assertFalse($this->server->isHandle());
	}

	/**
	 * @return null
	 */
	public function testGetClientStats()
	{
		$this->server->initialize();
		$this->server->connect();
			
		$stats = $this->server->getClientStats();
		$this->assertInternalType('array', $stats);
		$this->assertNotEmpty($stats);

		$this->server->close();
		$this->assertFalse($this->server->isConnected());

		/* no connection but handle is still valid */
		$stats = $this->server->getClientStats();
		$this->assertInternalType('array', $stats);
		$this->assertNotEmpty($stats);

		/* no handle */
		$this->server->clearHandle();
		$this->assertFalse($this->server->isConnected());
		$this->assertFalse($this->server->isHandle());

		$stats = $this->server->getClientStats();
		$this->assertNull($stats);
	}

	/**
	 * @return null
	 */
	public function testGetConnectionStats()
	{
		$this->server->initialize();
		$this->server->connect();
			
		$stats = $this->server->getConnectionStats();
		$this->assertInternalType('array', $stats);
		$this->assertNotEmpty($stats);

		$this->server->close();
		$this->assertFalse($this->server->isConnected());

		/* no connection but handle is still valid */
		$stats = $this->server->getConnectionStats();
		$this->assertNull($stats);

		/* no handle */
		$this->server->clearHandle();
		$this->assertFalse($this->server->isConnected());
		$this->assertFalse($this->server->isHandle());

		$stats = $this->server->getConnectionStats();
		$this->assertNull($stats);
	}

	/**
	 * @return null
	 */
	public function testGetHostInfo()
	{
		$this->server->initialize();
		$this->server->connect();
		
		$info = $this->server->getHostInfo();
		$this->assertInternalType('string', $info);
		$this->assertNotEmpty($info);

		$this->server->close();
		$info = $this->server->getHostInfo();
		$this->assertNull($info);

		$this->server->clearHandle();
		$info = $this->server->getHostInfo();
		$this->assertNUll($info);	
	}

	/**
	 * @return null
	 */
	public function testGetProtocolVersion()
	{
		$this->server->initialize();
		$this->server->connect();
		
		$version = $this->server->getProtocolVersion();
		$this->assertInternalType('int', $version);
		$this->assertGreaterThan(0, $version);

		$this->server->close();
		$version = $this->server->getProtocolVersion();
		$this->assertNull($version);

		$this->server->clearHandle();
		$version = $this->server->getProtocolVersion();
		$this->assertNull($version);	
	}

	/**
	 * @return null
	 */
	public function testGetServerInfo()
	{
		$this->server->initialize();
		$this->server->connect();
		
		$info = $this->server->getServerInfo();
		$this->assertInternalType('string', $info);
		$this->assertNotEmpty($info);

		$this->server->close();
		$info = $this->server->getServerInfo();
		$this->assertNull($info);

		$this->server->clearHandle();
		$info = $this->server->getServerInfo();
		$this->assertNull($info);	
	}

	/**
	 * @return null
	 */
	public function testGetServerVersion()
	{
		$this->server->initialize();
		$this->server->connect();
		
		$version = $this->server->getServerVersion();
		$this->assertInternalType('int', $version);
		$this->assertGreaterThan(10000, $version);

		$this->server->close();
		$version = $this->server->getServerVersion();
		$this->assertNull($version);

		$this->server->clearHandle();
		$version = $this->server->getServerVersion();
		$this->assertNull($version);	
	}

	/**
	 * @return null
	 */
	public function testGetServerStatus()
	{
		$this->server->initialize();
		$this->server->connect();
		
		$status = $this->server->getServerStatus();
		$this->assertInternalType('string', $status);
		$this->assertNotEmpty($status);

		$this->server->close();
		$status = $this->server->getServerStatus();
		$this->assertNull($status);

		$this->server->clearHandle();
		$status = $this->server->getServerStatus();
		$this->assertNull($status);
	
	}

	/**
	 * @return null
	 */
	public function testGetDefaultCharset()
	{
		$this->server->initialize();
		$this->server->connect();
	
		$charset = $this->server->getDefaultCharset();
		$this->assertInternalType('string', $charset);
		$this->assertNotEmpty($charset);

		$this->server->close();
		$charset = $this->server->getDefaultCharset();
		$this->assertNull($charset);

		$this->server->clearHandle();
		$charset = $this->server->getDefaultCharset();
		$this->assertNull($charset);
	}

	/**
	 * @return	null
	 */
	public function testSetDefaultCharset()
	{
		$this->server->initialize();
		$this->server->connect();
		
		$charset = 'utf8';
		$result = $this->server->setDefaultCharset($charset);
		$this->assertTrue($result);
		$this->assertEquals($charset, $this->server->getDefaultCharset());

		$this->server->close();

		$charset = $this->server->setDefaultCharset($charset);
		$this->assertFalse($charset);

		$this->server->clearHandle();
		$charset = $this->server->setDefaultCharset($charset);
		$this->assertFalse($charset);
	}
}
