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
	Appfuel\Db\Mysql\Adapter\Server,
	Appfuel\Db\Mysql\Adapter\MysqliAdapter;

/**
 */
class MysqliAdapterTest extends ParentTestCase
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

		$this->server  = new Server($this->connDetail);
		$this->adapter = new MysqliAdapter($this->server);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->server);
		unset($this->adapter);
	}

	/**
	 * Immutable object set in the constructor
	 *
	 * @return null
	 */
	public function testGetServer()
	{
		$this->assertSame($this->server, $this->adapter->getServer()); 
	}

	/**
	 * @return null
	 */
	public function testIsConnectedConnectClose()
	{
		$this->assertFalse($this->adapter->isError());
		$this->assertFalse($this->adapter->isConnected());
		$this->assertTrue($this->adapter->connect());
		$this->assertTrue($this->adapter->isConnected());
		$this->assertFalse($this->adapter->isError());
	
		/* will return true when already connected */
		$this->assertTrue($this->adapter->connect());	
		
		$this->assertTrue($this->adapter->close());
		$this->assertFalse($this->adapter->isConnected());
		$this->assertFalse($this->adapter->isError());
	}

	/**
	 * @return null
	 */
	public function testBadConnection()
	{
        $connDetail = new ConnectionDetail('mysql', 'mysqli');
        $connDetail->setHost('localhost')
                   ->setUserName('_not_likely_to_exist_apfuel__')
                   ->setPassword('no-pass')
                   ->setDbName('no-db');


        $server  = new Server($connDetail);
		$adapter = new MysqliAdapter($server);
		$this->assertFalse($adapter->isConnected());
		$this->assertFalse($adapter->connect());
		$this->assertTrue($adapter->isError());

		$error = $adapter->getError();
		$this->assertInstanceof(
			'Appfuel\Db\Mysql\Adapter\Error',
			$error
		);
		/* mysql access denied error code */
		$this->assertEquals(1045, $error->getCode());
	}
}
