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
namespace Test\Appfuel\Db;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Db\ConnectionDetail,
	StdClass;

/**
 * The ConnectionDetail is a value object used to hold connection information, as well as
 * the adapter type and vendor. 
 */
class ConnectionDetailTest extends ParentTestCase
{
	/**
	 * ConnectionDetail Value object
	 * @var string
	 */
	protected $connDetail = null;

	/**
	 * Names the database vendor, first parameter used in constructor
	 * @var string
	 */
	protected $vendor = null;

	/**
	 * Names the database vendor's adapter like mysqli
	 * @var string
	 */
	protected $adapter = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->vendor  = 'mysql';
		$this->adapter = 'mysqli'; 
		$this->connDetail = new ConnectionDetail($this->vendor, $this->adapter);
	}

	/**
	 * Restore the original registry data
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->connDetail);
	}

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\ConnectionDetailInterface',
			$this->connDetail,
			'must use a connection detail interface'
		);
	}

	/**
	 * Vendor is an immutable member that can only be set through the 
	 * constructor.
	 *
	 * @return null
	 */
	public function testGetVendor()
	{
		$this->assertEquals($this->vendor, $this->connDetail->getVendor());
	}

	/**
	 * Adapter is an immutable member that can only be set through the 
	 * constructor.
	 *
	 * @return null
	 */
	public function testGetAdapter()
	{
		$this->assertEquals($this->adapter, $this->connDetail->getAdapter());
	}


	/**
	 * @return null
	 */
	public function testGetSetHost()
	{
		$this->assertNull($this->connDetail->getHost());

		$host = 'appfuel.net';
		$this->assertSame(
			$this->connDetail,
			$this->connDetail->setHost($host),
			'must use a fluent interface'
		);
		$this->assertEquals($host, $this->connDetail->getHost());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadHostEmptyString()
	{
		$this->connDetail->setHost('');
	}

	/**
	 * @return null
	 */
	public function testGetSetUserName()
	{
		$this->assertNull($this->connDetail->getUserName());

		$user = 'my-user-name';
		$this->assertSame(
			$this->connDetail,
			$this->connDetail->setUserName($user),
			'must use a fluent interface'
		);
		$this->assertEquals($user, $this->connDetail->getUserName());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadUserNameEmptyString()
	{
		$this->connDetail->setUserName('');
	}

	/**
	 * @return null
	 */
	public function testGetSetPassword()
	{
		$this->assertNull($this->connDetail->getPassword());

		$pass = 'my-user-password';
		$this->assertSame(
			$this->connDetail,
			$this->connDetail->setPassword($pass),
			'must use a fluent interface'
		);
		$this->assertEquals($pass, $this->connDetail->getPassword());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadPasswordEmptyString()
	{
		$this->connDetail->setPassword('');
	}

	/**
	 * @return null
	 */
	public function testGetSetDbName()
	{
		$this->assertNull($this->connDetail->getDbName());

		$name = 'my-db-name';
		$this->assertSame(
			$this->connDetail,
			$this->connDetail->setDbName($name),
			'must use a fluent interface'
		);
		$this->assertEquals($name, $this->connDetail->getDbName());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadDbNameEmptyString()
	{
		$this->connDetail->setHost('');
	}

	/**
	 * @return null
	 */
	public function testGetSetPort()
	{
		$this->assertNull($this->connDetail->getPort());

		$port = 3306;
		$this->assertSame(
			$this->connDetail,
			$this->connDetail->setPort($port),
			'must use a fluent interface'
		);
		$this->assertEquals($port, $this->connDetail->getPort());

		$port = '3306';
		$this->connDetail->setPort($port);
		$this->assertEquals($port, $this->connDetail->getPort());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadPortEmptyString()
	{
		$this->connDetail->setPort('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadPortZero()
	{
		$this->connDetail->setPort(0);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadPortNegativeNumber()
	{
		$this->connDetail->setPort(-33);
	}

	/**
	 * @return null
	 */
	public function testGetSetSocket()
	{
		$this->assertNull($this->connDetail->getSocket());

		$socket = 'tmp/mysql.sock';
		$this->assertSame(
			$this->connDetail,
			$this->connDetail->setSocket($socket),
			'must use a fluent interface'
		);
		$this->assertEquals($socket, $this->connDetail->getSocket());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadSocketEmptyString()
	{
		$this->connDetail->setSocket('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadVendorEmptyString()
	{
		$connDetail = new ConnectionDetail('', 'some-adapter');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadVendorEmptyInt()
	{
		$connDetail = new ConnectionDetail(12345, 'some-adapter');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadVendorEmptyArray()
	{
		$connDetail = new ConnectionDetail(array(1,2,3), 'some-adapter');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadVendorEmptyObject()
	{
		$connDetail = new ConnectionDetail(new StdClass(), 'some-adapter');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadAdapterEmptyString()
	{
		$connDetail = new ConnectionDetail('some-vendor', '');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadAdapterEmptyInt()
	{
		$connDetail = new ConnectionDetail('some-vendor', 12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadAdapterArray()
	{
		$connDetail = new ConnectionDetail('some-vendor', array(1,2,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadAdapterObject()
	{
		$connDetail = new ConnectionDetail('some-vendor', new StdClass());
	}
}
