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
namespace Test\Appfuel\Db\Connection;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Connection\ConnectionDetail,
	Appfuel\Db\Mysql\Adapter\MysqliAdapter;

/**
 * Test the adapters ability to wrap mysqli
 */
class MysqliAdapterTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Adapter
	 */
	protected $adapter = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->connDetail = new ConnectionDetail('mysql', 'mysqli');
		$this->connDetail->setHost('localhost')
						 ->setUserName('appfuel_user')
						 ->setPassword('w3b_g33k')
						 ->setDbName('appfuel_unittest');

		$this->adapter = new MysqliAdapter($this->connDetail);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->adapter);
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
			$this->adapter->getConnectionDetail()
		);
	}

	/**
	 * This calls mysqli_init and returns it
	 * 
	 * @return	null
	 */
	public function testCreateHandle()
	{
		$this->assertInstanceOf('\Mysqli', $this->adapter->createHandle());
	}

	/**
	 * @return null
	 */
	public function testSetGetIsHandle()
	{
		$this->assertFalse($this->adapter->isHandle());
		$this->assertNull($this->adapter->getHandle());

		$handle = mysqli_init();
		$this->assertSame(
			$this->adapter,
			$this->adapter->setHandle($handle),
			'Must use a fluent interface'
		);

		$this->assertTrue($this->adapter->isHandle());
		$this->assertSame($handle, $this->adapter->getHandle());
	}	
}
