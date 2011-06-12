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
	Appfuel\Db\Dsn,
	StdClass;

/**
 * The Dsn is a value object used to hold connection information, as well as
 * the adapter type and vendor. 
 */
class DnsTest extends ParentTestCase
{
	/**
	 * Dsn Value object
	 * @var string
	 */
	protected $dsn = null;

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
		$this->dsn = new Dsn($this->vendor, $this->adapter);
	}

	/**
	 * Restore the original registry data
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->dsn);
	}

	/**
	 * Vendor is an immutable member that can only be set through the 
	 * constructor.
	 *
	 * @return null
	 */
	public function testGetVendor()
	{
		$this->assertEquals($this->vendor, $this->dsn->getVendor());
	}

	/**
	 * Adapter is an immutable member that can only be set through the 
	 * constructor.
	 *
	 * @return null
	 */
	public function testGetAdapter()
	{
		$this->assertEquals($this->adapter, $this->dsn->getAdapter());
	}


	/**
	 * @return null
	 */
	public function testGetSetHost()
	{
		$this->assertNull($this->dsn->getHost());

		$host = 'appfuel.net';
		$this->assertSame(
			$this->dsn,
			$this->dsn->setHost($host),
			'must use a fluent interface'
		);
		$this->assertEquals($host, $this->dsn->getHost());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadHostEmptyString()
	{
		$this->dsn->setHost('');
	}

	/**
	 * @return null
	 */
	public function testGetSetUserName()
	{
		$this->assertNull($this->dsn->getUserName());

		$user = 'my-user-name';
		$this->assertSame(
			$this->dsn,
			$this->dsn->setUserName($user),
			'must use a fluent interface'
		);
		$this->assertEquals($user, $this->dsn->getUserName());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadUserNameEmptyString()
	{
		$this->dsn->setUserName('');
	}

	/**
	 * @return null
	 */
	public function testGetSetPassword()
	{
		$this->assertNull($this->dsn->getPassword());

		$pass = 'my-user-password';
		$this->assertSame(
			$this->dsn,
			$this->dsn->setPassword($pass),
			'must use a fluent interface'
		);
		$this->assertEquals($pass, $this->dsn->getPassword());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadPasswordEmptyString()
	{
		$this->dsn->setPassword('');
	}

	/**
	 * @return null
	 */
	public function testGetSetDbName()
	{
		$this->assertNull($this->dsn->getDbName());

		$name = 'my-db-name';
		$this->assertSame(
			$this->dsn,
			$this->dsn->setDbName($name),
			'must use a fluent interface'
		);
		$this->assertEquals($name, $this->dsn->getDbName());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadDbNameEmptyString()
	{
		$this->dsn->setHost('');
	}

	/**
	 * @return null
	 */
	public function testGetSetPort()
	{
		$this->assertNull($this->dsn->getPort());

		$port = 3306;
		$this->assertSame(
			$this->dsn,
			$this->dsn->setPort($port),
			'must use a fluent interface'
		);
		$this->assertEquals($port, $this->dsn->getPort());

		$port = '3306';
		$this->dsn->setPort($port);
		$this->assertEquals($port, $this->dsn->getPort());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadPortEmptyString()
	{
		$this->dsn->setPort('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadPortZero()
	{
		$this->dsn->setPort(0);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadPortNegativeNumber()
	{
		$this->dsn->setPort(-33);
	}

	/**
	 * @return null
	 */
	public function testGetSetSocket()
	{
		$this->assertNull($this->dsn->getSocket());

		$socket = 'tmp/mysql.sock';
		$this->assertSame(
			$this->dsn,
			$this->dsn->setSocket($socket),
			'must use a fluent interface'
		);
		$this->assertEquals($socket, $this->dsn->getSocket());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */	
	public function testBadSocketEmptyString()
	{
		$this->dsn->setSocket('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadVendorEmptyString()
	{
		$dsn = new Dsn('', 'some-adapter');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadVendorEmptyInt()
	{
		$dsn = new Dsn(12345, 'some-adapter');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadVendorEmptyArray()
	{
		$dsn = new Dsn(array(1,2,3), 'some-adapter');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadVendorEmptyObject()
	{
		$dsn = new Dsn(new StdClass(), 'some-adapter');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadAdapterEmptyString()
	{
		$dsn = new Dsn('some-vendor', '');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadAdapterEmptyInt()
	{
		$dsn = new Dsn('some-vendor', 12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadAdapterArray()
	{
		$dsn = new Dsn('some-vendor', array(1,2,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testConstructorBadAdapterObject()
	{
		$dsn = new Dsn('some-vendor', new StdClass());
	}
}
