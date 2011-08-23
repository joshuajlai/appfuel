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
	Appfuel\Db\Connection\DetailFactory;

/**
 * The detail factory is used to create connection detail objects
 */
class DetailFactoryTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Parser
	 */
	protected $factory = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->factory = new DetailFactory();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->parser);
	}

	/**
	 * @return	array
	 */
	public function providerConnString()
	{
		return array(

		);
	}

	/**
	 * @return	null
	 */
	public function testConstructorGetParser()
	{
		$this->assertInstanceOf(
			'Appfuel\Db\Connection\Parser',
			$this->factory->getParser(),
			'default parser must be Appfuel\Db\Connection\Parser'
		);

		$interface ='Appfuel\Framework\Db\Connection\ParserInterface';
		$parser    = $this->getMock($interface);
		$factory = new DetailFactory($parser);
		$this->assertEquals($parser, $factory->getParser());	
	}

	/**
	 * @return null
	 */
	public function testCreateConnectionDetail()
	{
		$string = 'type=master;vendor=mysql;adapter=mysqli;host=my-host;' .
				  'username=me;dbname=my-db;password=pass;port=3306;'     .
				  'socket=tmp/mysql.sock';

		$result = $this->factory->createConnectionDetail($string);
		$this->assertInstanceOf(
			'Appfuel\Db\Connection\ConnectionDetail',
			$result
		);
		
		$this->assertEquals('mysql',			$result->getVendor());
		$this->assertEquals('mysqli',			$result->getAdapter());
		$this->assertEquals('my-host',			$result->getHost());
		$this->assertEquals('me',				$result->getUserName());
		$this->assertEquals('pass',				$result->getPassword());
		$this->assertEquals(3306,				$result->getPort());
		$this->assertEquals('tmp/mysql.sock',	$result->getSocket());
		$this->assertEquals('master',			$result->getType());
		
	}

	/**
	 * Port is an optional connection field
	 * @return null
	 */
	public function testCreateConnectionDetailNoPort()
	{
		$string = 'type=slave;vendor=mysql;adapter=mysqli;host=my-host;' .
				  'username=me;dbname=my-db;password=pass;' .
				  'socket=tmp/mysql.sock';

		$result = $this->factory->createConnectionDetail($string);
		$this->assertInstanceOf(
			'Appfuel\Db\Connection\ConnectionDetail',
			$result
		);
		
		$this->assertEquals('mysql',			$result->getVendor());
		$this->assertEquals('mysqli',			$result->getAdapter());
		$this->assertEquals('my-host',			$result->getHost());
		$this->assertEquals('me',				$result->getUserName());
		$this->assertEquals('pass',				$result->getPassword());
		$this->assertEquals('tmp/mysql.sock',	$result->getSocket());
		$this->assertEquals('slave',			$result->getType());
		
		$this->assertNull($result->getPort());
	}

	/**
	 * Socket is an optional connection field
	 * @return null
	 */
	public function testCreateConnectionDetailNoSocket()
	{
		$string = 'vendor=mysql;adapter=mysqli;host=my-host;username=me;' .
				  'dbname=my-db;password=pass;port=3306';

		$result = $this->factory->createConnectionDetail($string);
		$this->assertInstanceOf(
			'Appfuel\Db\Connection\ConnectionDetail',
			$result
		);
		
		$this->assertEquals('mysql',			$result->getVendor());
		$this->assertEquals('mysqli',			$result->getAdapter());
		$this->assertEquals('my-host',			$result->getHost());
		$this->assertEquals('me',				$result->getUserName());
		$this->assertEquals('pass',				$result->getPassword());
		$this->assertEquals(3306,				$result->getPort());
		
		$this->assertNull($result->getSocket());
	}

	/**
	 * @return null
	 */
	public function testCreateConnectionDetailNoPortSocket()
	{
		$string = 'vendor=mysql;adapter=mysqli;host=my-host;username=me;' .
				  'dbname=my-db;password=pass';

		$result = $this->factory->createConnectionDetail($string);
		$this->assertInstanceOf(
			'Appfuel\Db\Connection\ConnectionDetail',
			$result
		);
		
		$this->assertEquals('mysql',			$result->getVendor());
		$this->assertEquals('mysqli',			$result->getAdapter());
		$this->assertEquals('my-host',			$result->getHost());
		$this->assertEquals('me',				$result->getUserName());
		$this->assertEquals('pass',				$result->getPassword());
		
		$this->assertNull($result->getSocket());
		$this->assertNull($result->getPort());
	}

	/**
	 * @return null
	 */
	public function testCreateConnectionDetailEmptyString()
	{
		$this->assertFalse($this->factory->createConnectionDetail(''));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testCreateConnectionDetailNoVendor()
	{
		$string = 'adapter=mysqli;host=my-host;username=me;' .
				  'dbname=my-db;password=pass;port=3306;socket=tmp/mysql.sock';

		$result = $this->factory->createConnectionDetail($string);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testCreateConnectionDetailNoAdapter()
	{
		$string = 'vendor=mysql;host=my-host;username=me;' .
				  'dbname=my-db;password=pass;port=3306;socket=tmp/mysql.sock';

		$result = $this->factory->createConnectionDetail($string);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testCreateConnectionDetailNoHost()
	{
		$string = 'vendor=mysql;adapter=mysqli;username=me;' .
				  'dbname=my-db;password=pass;port=3306;socket=tmp/mysql.sock';

		$result = $this->factory->createConnectionDetail($string);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testCreateConnectionDetailNoUserName()
	{
		$string = 'vendor=mysql;adapter=mysqli;host=my-host;' .
				  'dbname=my-db;password=pass;port=3306;socket=tmp/mysql.sock';

		$result = $this->factory->createConnectionDetail($string);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testCreateConnectionDetailNoDbName()
	{
		$string = 'vendor=mysql;adapter=mysqli;host=my-host;' .
				  'username=me;password=pass;port=3306;socket=tmp/mysql.sock';

		$result = $this->factory->createConnectionDetail($string);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testCreateConnectionDetailNoPassword()
	{
		$string = 'vendor=mysql;adapter=mysqli;host=my-host;' .
				  'dbname=my-db;userName=me;port=3306;socket=tmp/mysql.sock';

		$result = $this->factory->createConnectionDetail($string);
	}



}
